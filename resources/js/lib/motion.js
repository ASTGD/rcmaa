import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

export const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
    if (lenis) {
        lenis.scrollTo(target, { offset: -90, duration: 1.2, ...options });
        return;
    }

    const el = typeof target === 'string' ? document.querySelector(target) : target;
    el?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
