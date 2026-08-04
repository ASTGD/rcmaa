import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

export const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Lenis is set up with syncTouch: false, so on a touch screen the browser keeps
// its own momentum scrolling and Lenis is not the thing moving the page.
const nativeScrolling = window.matchMedia('(pointer: coarse)').matches;

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

    // Only hand this to Lenis where Lenis actually drives the scroll. On touch
    // it does not, and lenis.scrollTo() there simply never moves the page —
    // which left every "Continue" on the registration form stranded halfway
    // down the previous step on a phone.
    if (lenis && !nativeScrolling) {
        lenis.scrollTo(el, { offset: -90, duration: 1.2, ...options });
        return;
    }

    // scrollIntoView cannot express the offset that clears the sticky header,
    // so the position is worked out directly.
    const offset = options.offset ?? -90;
    window.scrollTo({
        top: Math.max(0, window.scrollY + el.getBoundingClientRect().top + offset),
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
    });
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
