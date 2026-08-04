import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

export const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Lenis is set up with syncTouch: false, so on a touch screen the browser keeps
// its own momentum scrolling and Lenis is not the thing moving the page. The
// test is deliberately broad — a phone that reports itself unusually still must
// not end up on the branch that cannot scroll it.
const nativeScrolling =
    window.matchMedia('(hover: none), (pointer: coarse)').matches ||
    navigator.maxTouchPoints > 0 ||
    'ontouchstart' in window;

let lenis = null;

/**
 * Lenis drives the scroll position and ScrollTrigger reads from it, so the two
 * have to share a single RAF loop — otherwise triggers fire against the native
 * scroll offset and lag one frame behind the smoothed one.
 */
export function initSmoothScroll() {
    if (prefersReducedMotion) return null;

    lenis = new Lenis({
        duration: 1.1,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        // Native momentum on touch feels better than an emulated one.
        syncTouch: false,
        touchMultiplier: 1.6,
    });

    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    // Handy when debugging scroll position from the console.
    window.__lenis = lenis;

    return lenis;
}

export function getLenis() {
    return lenis;
}

export function scrollTo(target, options = {}) {
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return;

    const offset = options.offset ?? -90;

    // Deliberately not routed through Lenis, and deliberately not branching on
    // whether the device looks like a phone.
    //
    // Lenis is configured with syncTouch: false, so on touch the browser keeps
    // its own scrolling and lenis.scrollTo() moves nothing at all — which left
    // every "Continue" on the registration form stranded at the bottom of the
    // step just finished. Guessing which devices those are got it wrong twice.
    // The native call works everywhere, and `smooth` is smooth; Lenis still
    // eases ordinary wheel scrolling, it just is not asked to make the jumps.
    //
    // scrollIntoView cannot express the offset that clears the sticky header,
    // so the position is computed — and recomputed, because opening a step
    // changes the page height and the browser clamps the scroll while that
    // settles, silently undoing the jump. Arrival is judged by where the
    // element ended up, never by whether scrollY changed: the clamp moves the
    // page on its own, so a change in scrollY proves nothing.
    const go = (behavior) => window.scrollTo({
        top: Math.max(0, window.scrollY + el.getBoundingClientRect().top + offset),
        behavior,
    });

    go(prefersReducedMotion ? 'auto' : 'smooth');

    // The correction is instant, not smooth, and that is the point: a smooth
    // scroll is an animation, and an animation that never runs leaves the
    // reader exactly where they were. Landing abruptly is a far smaller cost
    // than not landing at all.
    window.setTimeout(() => {
        if (Math.abs(el.getBoundingClientRect().top + offset) > 12) go('auto');
    }, 320);
}

/**
 * The document position of an element, walking the offset chain.
 *
 * Deliberately not getBoundingClientRect() + scrollY: that pair is only
 * meaningful at the instant it is read, and a step change is exactly when it is
 * not — the page is being re-laid-out and the browser is clamping the scroll
 * because the document just got shorter. offsetTop is measured from the
 * document, so it does not care where we currently are.
 */
function documentTop(el) {
    let y = 0;
    for (let node = el; node; node = node.offsetParent) y += node.offsetTop;
    return y;
}

/**
 * Put an element at the top of the viewport, now, without animating.
 *
 * Used for moving between steps of the registration form, where landing
 * reliably matters far more than gliding. Every animated route to this —
 * Lenis, CSS smooth scrolling — depends on a frame loop that may be throttled,
 * suspended, or simply not driving the page, and each failure leaves the reader
 * stranded at the bottom of the step they just finished. This cannot fail that
 * way. It repeats because the step being closed shortens the page, and the
 * clamp that follows would otherwise undo the jump.
 */
export function jumpTo(target, offset = -110) {
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return;

    const go = () => {
        const top = Math.max(0, documentTop(el) + offset);
        window.scrollTo(0, top);
        // Lenis keeps its own idea of where the page is. Left unsynced it will
        // ease back to that on the next frame and quietly undo the jump.
        lenis?.scrollTo(top, { immediate: true, force: true });
    };

    // Land it, then check where it actually landed. offsetTop can disagree with
    // the rendered position while a step is still opening, so the last word goes
    // to the element itself rather than to any arithmetic about it.
    const settle = () => {
        go();
        const drift = el.getBoundingClientRect().top - -offset;
        if (Math.abs(drift) > 8) window.scrollBy(0, drift);
        lenis?.scrollTo(window.scrollY, { immediate: true, force: true });
    };

    go();
    window.setTimeout(go, 120);
    window.setTimeout(settle, 340);
    // Leaving step 1 collapses four tall category cards at once, and the page is
    // still shrinking when the first correction runs. One more pass, after the
    // largest reflow on the form has finished.
    window.setTimeout(settle, 700);
}

export function stopScroll() {
    lenis?.stop();
    document.documentElement.classList.add('lenis-stopped');
}

export function startScroll() {
    lenis?.start();
    document.documentElement.classList.remove('lenis-stopped');
}

export { gsap, ScrollTrigger };
