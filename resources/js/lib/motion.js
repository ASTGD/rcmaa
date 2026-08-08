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

/*
 * How many deliberate jumps are in flight.
 *
 * ScrollTrigger.refresh() restores the scroll position it captured when it
 * started, which during a step change is the foot of the page the reader just
 * left. Anything that refreshes on a timer — images finishing, fonts landing —
 * would therefore quietly undo a jump. Callers check this and wait.
 */
let jumping = 0;

// The page's own overflow-anchor, saved once when the first jump starts. Jumps
// overlap, and saving per-jump meant the second one recorded the value the
// first had already replaced, then "restored" that — leaving anchoring off for
// the rest of the visit.
let anchorBeforeJump = null;

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

/** True while jumpTo is holding the page somewhere on purpose. */
export function isJumping() {
    return jumping > 0;
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
 * Deliberately not getBoundingClientRect() + scrollY. That pair has to be read
 * in the same frame to agree, and a step change is the worst moment for it: the
 * page is being re-laid-out and the scroll is being clamped as the document
 * shrinks. offsetTop is measured from the document and does not care where we
 * currently are, so repeated calls converge instead of chasing themselves.
 */
function documentTop(el) {
    let y = 0;
    for (let node = el; node; node = node.offsetParent) y += node.offsetTop;
    return y;
}

/**
 * Put an element at the top of the viewport, now, and keep it there.
 *
 * Used for moving between steps of the registration form, where landing
 * reliably matters far more than gliding.
 *
 * Scheduling a handful of setTimeout passes was not enough. Changing step blocks
 * the main thread for the best part of a second — Alpine rebuilds a long panel,
 * x-collapse runs, GSAP animates it — so the timers land late, against a layout
 * that has moved, and whatever ran last won. Meanwhile Lenis eases toward its
 * own target and a ScrollTrigger refresh restores the scroll it captured before
 * any of this began. Measured on a laptop: the reader ends at the foot of the
 * page with the form 1462px above them. A phone never showed it, because with
 * syncTouch off Lenis is not driving the scroll there at all.
 *
 * So this converges rather than firing and hoping. It re-reads the target every
 * frame and re-applies whenever the page has drifted, until the position has
 * held still for a moment. The target is computed as scrollY + rect.top, which
 * is invariant under scrolling — scrolling by d moves rect.top by exactly -d —
 * so re-applying cannot chase itself. That invariance is what the old
 * offsetTop-only approach was protecting against, and it is why an earlier
 * one-shot rect correction was a bug: it measured once, mid-reflow, and
 * believed a drift that was really its own doing.
 *
 * It gives way the moment the reader takes over.
 */
export function jumpTo(target, offset = -110) {
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (! el) return;

    /*
     * Scroll anchoring is the thing that actually drags the page away.
     *
     * The reader presses Continue from the foot of the page, so the footer is
     * what is on screen. The step that opens is taller than the one that closed,
     * the page grows above that footer, and the browser faithfully scrolls down
     * to hold it still — measured at +901px, arriving after the jump has already
     * landed correctly. Marking the form alone was not enough: the anchor is the
     * footer, which is outside it.
     *
     * Suspended on the scrolling element for the length of the jump, and put
     * back exactly as it was afterwards, so anchoring keeps working everywhere
     * else — it is the right behaviour in every case except this one.
     */
    const root = document.documentElement;
    if (jumping === 0) anchorBeforeJump = root.style.overflowAnchor;
    root.style.overflowAnchor = 'none';

    jumping += 1;

    const targetFor = () => Math.max(0, Math.round(window.scrollY + el.getBoundingClientRect().top + offset));

    const apply = (top) => {
        try {
            if (lenis) {
                // Lenis caches the scrollable height, and the step that just
                // closed changed it underneath.
                lenis.resize();
                lenis.scrollTo(top, { immediate: true, force: true, lock: true });
            }
        } catch {
            // Lenis is not driving this page; the native scroll below stands.
        }

        window.scrollTo(0, top);
    };

    apply(targetFor());

    /*
     * Corrected from two clocks, because neither is dependable on its own.
     *
     * requestAnimationFrame is the natural fit and is measurably not delivered
     * here: through a cold step change it produced zero frames in 2.6 seconds.
     * Alpine's $nextTick is built on it, so that misses too, and the single
     * timeout that did fire landed mid-reflow and was then overtaken. Timers
     * keep running when frames do not, and frames are smoother when they do —
     * so both drive the same check, and whichever arrives first wins.
     */
    const deadline = performance.now() + 2000;
    let done = false;

    const finish = () => {
        if (done) return;
        done = true;
        jumping = Math.max(0, jumping - 1);

        // Only the last one out puts it back.
        if (jumping === 0) root.style.overflowAnchor = anchorBeforeJump ?? '';

        events.forEach((e) => window.removeEventListener(e, release));
    };

    // The reader's own scrolling always wins.
    const release = () => finish();
    const events = ['wheel', 'touchstart', 'keydown'];
    events.forEach((e) => window.addEventListener(e, release, { passive: true }));

    /*
     * Deliberately no early exit once the position looks right.
     *
     * An earlier version stopped as soon as it had held still for 300ms, and
     * that was wrong here: the expensive part of a step change — Alpine building
     * the panel, x-collapse, the reflow — arrives *after* that. The loop would
     * congratulate itself at 400ms and shut down, and the page was dragged away
     * at 900ms with nothing left watching. It now keeps watch for the whole
     * window and only the reader can end it early.
     */
    const check = () => {
        if (done) return;

        const top = targetFor();
        if (Math.abs(window.scrollY - top) > 2) apply(top);

        if (performance.now() > deadline) finish();
    };

    const frame = () => {
        if (done) return;
        check();
        requestAnimationFrame(frame);
    };
    requestAnimationFrame(frame);

    // The same check on a timer, for when frames are not being delivered at all.
    [40, 90, 160, 260, 400, 600, 850, 1150, 1450, 1750].forEach((ms) => window.setTimeout(check, ms));

    // Always ends, even when no frame is ever delivered. Without this the
    // deadline was only ever tested by callers that arrived before it, so
    // finish() never ran: anchoring stayed disabled and the listeners leaked.
    window.setTimeout(finish, 2100);
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
