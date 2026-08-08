import { gsap, ScrollTrigger, prefersReducedMotion, isJumping } from './motion';
import { SplitText } from './split-text';

const DEFAULTS = {
    start: 'top 85%',
    once: true,
};

/**
 * Scroll-linked reveals, driven entirely by data attributes so Blade templates
 * stay declarative:
 *
 *   data-reveal="up|fade|scale|mask|split"
 *   data-reveal-delay="0.15"
 *   data-reveal-stagger="0.08"   (on a parent, animates [data-reveal-item])
 */
export function initReveals(scope = document) {
    if (prefersReducedMotion) {
        scope.querySelectorAll('[data-reveal]').forEach((el) => gsap.set(el, { opacity: 1 }));
        return;
    }

    scope.querySelectorAll('[data-reveal]').forEach((el) => {
        const kind = el.dataset.reveal || 'up';
        const delay = parseFloat(el.dataset.revealDelay || 0);
        const stagger = parseFloat(el.dataset.revealStagger || 0);

        const trigger = { trigger: el, ...DEFAULTS };

        if (stagger > 0) {
            const items = el.querySelectorAll('[data-reveal-item]');
            gsap.set(el, { opacity: 1 });
            gsap.from(items, {
                y: 34,
                opacity: 0,
                duration: 1,
                delay,
                stagger,
                ease: 'power3.out',
                scrollTrigger: trigger,
            });
            return;
        }

        switch (kind) {
            case 'fade':
                gsap.fromTo(
                    el,
                    { opacity: 0 },
                    { opacity: 1, duration: 1.1, delay, ease: 'power2.out', scrollTrigger: trigger }
                );
                break;

            case 'scale':
                gsap.fromTo(
                    el,
                    { opacity: 0, scale: 1.08 },
                    {
                        opacity: 1,
                        scale: 1,
                        duration: 1.4,
                        delay,
                        ease: 'power3.out',
                        scrollTrigger: trigger,
                    }
                );
                break;

            case 'mask':
                gsap.fromTo(
                    el,
                    { clipPath: 'inset(0 0 100% 0)', opacity: 1 },
                    {
                        clipPath: 'inset(0 0 0% 0)',
                        duration: 1.3,
                        delay,
                        ease: 'power3.inOut',
                        scrollTrigger: trigger,
                    }
                );
                break;

            case 'split': {
                const split = new SplitText(el);
                gsap.set(el, { opacity: 1 });
                gsap.from(split.parts, {
                    yPercent: 115,
                    duration: 1.15,
                    delay,
                    stagger: 0.08,
                    ease: 'power4.out',
                    scrollTrigger: trigger,
                });
                break;
            }

            default:
                gsap.fromTo(
                    el,
                    { opacity: 0, y: 42 },
                    {
                        opacity: 1,
                        y: 0,
                        duration: 1.1,
                        delay,
                        ease: 'power3.out',
                        scrollTrigger: trigger,
                    }
                );
        }
    });
}

/**
 * Decide for ourselves when a lazy image should load.
 *
 * Native loading="lazy" does not fire on this site. Lenis drives the window
 * scroll, and Chrome's own deferral heuristic never concludes these images have
 * come near enough — the committee portraits sat at naturalWidth 0 with their
 * cards fully on screen and 195px from the top of the viewport, indefinitely.
 * Flipping one to `eager` loads it immediately, so nothing is wrong with the
 * files or the markup; only the trigger is missing.
 *
 * The attribute stays in the HTML for crawlers and for the no-JS case. This
 * only promotes an image once it is within a screen of the viewport, which is
 * what the browser was supposed to be doing.
 */
export function initLazyImages(scope = document) {
    const lazy = [...scope.querySelectorAll('img[loading="lazy"]')];
    if (! lazy.length) return;

    if (! ('IntersectionObserver' in window)) {
        lazy.forEach((img) => { img.loading = 'eager'; });
        return;
    }

    const io = new IntersectionObserver(
        (entries) => entries.forEach((entry) => {
            if (! entry.isIntersecting) return;
            entry.target.loading = 'eager';
            io.unobserve(entry.target);
        }),
        // A screenful of lead time, so an image is decoded before it is reached
        // rather than popping in under the reader.
        { rootMargin: '600px 0px' }
    );

    lazy.forEach((img) => io.observe(img));
}

/**
 * Lazy images finish loading long after `load` — that is the whole point of
 * them — and each one that lands can change the page height. Every ScrollTrigger
 * below it is then measuring against a stale position, and a trigger that never
 * fires leaves its section stuck at the `opacity: 0` the CSS guard applied. The
 * images had loaded; the section around them simply never became visible.
 *
 * Refresh once a burst of loads settles rather than once per image.
 */
export function refreshOnMediaLoad(scope = document) {
    let pending;

    const schedule = () => {
        window.clearTimeout(pending);
        pending = window.setTimeout(() => {
            // ScrollTrigger.refresh() restores the scroll position it captured
            // when it began. Called in the middle of a deliberate jump — a step
            // change, where images are loading at exactly that moment — it puts
            // the reader back at the foot of the step they just left. Wait.
            if (isJumping()) return schedule();

            ScrollTrigger.refresh();
            revealStranded(scope);
        }, 200);
    };

    scope.querySelectorAll('img').forEach((img) => {
        if (img.complete) return;
        img.addEventListener('load', schedule, { once: true });
        img.addEventListener('error', schedule, { once: true });
    });
}

/**
 * The reveal is decoration; the content under it is not. Because the CSS guard
 * hides every [data-reveal] the moment JS boots, anything whose trigger fails to
 * fire would stay invisible for good — a worse outcome than no animation at all.
 * Show whatever is on screen and still hidden, and let the rest animate normally.
 */
export function revealStranded(scope = document) {
    if (prefersReducedMotion) return;

    const onScreen = (el) => {
        const rect = el.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    };

    const show = (el) => gsap.to(el, {
        opacity: 1, y: 0, duration: 0.4, ease: 'power2.out', overwrite: 'auto',
    });

    /*
     * Both selectors, checked independently.
     *
     * A staggered group sets its own opacity to 1 immediately and animates only
     * its children, so testing the container told us nothing about them — the
     * container read 1 while every card under it sat at 0, and this sweep
     * skipped the whole group. That is precisely the case it exists to catch.
     */
    scope.querySelectorAll('[data-reveal], [data-reveal-item]').forEach((el) => {
        if (! onScreen(el)) return;
        if (getComputedStyle(el).opacity !== '0') return;

        show(el);
        el.querySelectorAll('[data-reveal-item]').forEach(show);
    });
}

/** Elements with data-parallax="0.2" drift against the scroll direction. */
export function initParallax(scope = document) {
    if (prefersReducedMotion) return;

    scope.querySelectorAll('[data-parallax]').forEach((el) => {
        const strength = parseFloat(el.dataset.parallax || 0.2);

        gsap.to(el, {
            yPercent: strength * 100,
            ease: 'none',
            scrollTrigger: {
                trigger: el.closest('[data-parallax-scope]') || el.parentElement,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
            },
        });
    });
}

/** data-count="1873" animates from 0 when scrolled into view. */
export function initCounters(scope = document) {
    scope.querySelectorAll('[data-count]').forEach((el) => {
        const target = parseFloat(el.dataset.count);
        const decimals = parseInt(el.dataset.countDecimals || 0, 10);

        if (prefersReducedMotion) {
            el.textContent = target.toFixed(decimals);
            return;
        }

        const state = { value: 0 };

        gsap.to(state, {
            value: target,
            duration: 2.2,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 88%', once: true },
            onUpdate: () => {
                el.textContent = state.value.toFixed(decimals);
            },
        });
    });
}

/** Horizontal marquee that pauses on hover (sponsor + alumni strips). */
export function initMarquees(scope = document) {
    scope.querySelectorAll('[data-marquee]').forEach((el) => {
        const track = el.querySelector('[data-marquee-track]');
        if (!track) return;

        // Duplicate the row so the loop has no visible seam.
        track.innerHTML += track.innerHTML;

        if (prefersReducedMotion) return;

        const speed = parseFloat(el.dataset.marquee || 40);
        const tween = gsap.to(track, {
            xPercent: -50,
            duration: speed,
            ease: 'none',
            repeat: -1,
        });

        el.addEventListener('mouseenter', () => tween.timeScale(0.15));
        el.addEventListener('mouseleave', () => tween.timeScale(1));
    });
}

export { ScrollTrigger };
