import { gsap, ScrollTrigger, prefersReducedMotion } from './motion';
import { SplitText } from './split-text';

/**
 * The hero: a full-bleed muted video behind a masked headline.
 *
 * The video is decorative, so if autoplay is refused (Low Power Mode, some
 * mobile browsers) we quietly fall back to the poster image instead of showing
 * a dead player.
 */
export function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const video = hero.querySelector('[data-hero-video]');
    const poster = hero.querySelector('[data-hero-poster]');

    if (video) {
        video.muted = true;
        video.playsInline = true;

        // The poster carries its own CSS opacity transition. Driving the fade
        // through a class rather than GSAP means it still resolves when frames
        // are throttled — a background tab, or a device under load — instead of
        // leaving the poster stranded on top of a playing video.
        const revealVideo = () => poster?.classList.add('opacity-0');
        const showPosterOnly = () => {
            video.classList.add('hidden');
            poster?.classList.remove('opacity-0');
        };

        // Playback can already be under way by the time this runs on a warm cache,
        // in which case the `playing` event has been and gone.
        if (video.readyState >= 3 && ! video.paused) {
            revealVideo();
        }

        video.addEventListener('playing', revealVideo, { once: true });
        video.addEventListener('error', showPosterOnly, { once: true });

        const play = video.play();
        if (play?.catch) {
            // Autoplay refused (Low Power Mode, some mobile browsers) — fall back
            // to the still rather than showing a dead player.
            play.catch(showPosterOnly);
        }
    }

    if (prefersReducedMotion) {
        gsap.set(hero.querySelectorAll('[data-reveal], [data-hero-mask]'), {
            opacity: 1,
            clipPath: 'none',
        });
        return;
    }

    const tl = gsap.timeline({ defaults: { ease: 'power4.out' } });

    // Curtain lift
    tl.fromTo(
        hero.querySelector('[data-hero-mask]'),
        { clipPath: 'inset(100% 0 0 0)', scale: 1.14 },
        { clipPath: 'inset(0% 0 0 0)', scale: 1, duration: 1.6, ease: 'power3.inOut' }
    );

    const heading = hero.querySelector('[data-hero-title]');
    if (heading) {
        const split = new SplitText(heading);
        gsap.set(heading, { opacity: 1 });
        tl.from(split.parts, { yPercent: 118, duration: 1.35, stagger: 0.1 }, '-=1.05');
    }

    tl.fromTo(
        hero.querySelectorAll('[data-hero-fade]'),
        { opacity: 0, y: 26 },
        { opacity: 1, y: 0, duration: 1, stagger: 0.12 },
        '-=0.85'
    );

    // Slow drift on the video as the user scrolls away.
    //
    // The copy used to fade and lift on the same scrub. It was removed: the
    // headline, the buttons and the countdown all thinned out while still on
    // screen, which read as the page failing to render rather than as an
    // effect. The hero now simply scrolls away, fully legible until it goes.
    gsap.to(hero.querySelector('[data-hero-media]'), {
        yPercent: 18,
        scale: 1.1,
        ease: 'none',
        scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true },
    });
}

/**
 * Sections tagged data-theme="dark" flip the header to its light treatment as
 * they pass under it.
 */
export function initHeaderTheme() {
    const header = document.querySelector('[data-header]');
    if (!header) return;

    ScrollTrigger.create({
        start: 'top -80',
        end: 99999,
        onUpdate: (self) => {
            header.classList.toggle('is-stuck', self.scroll() > 80);
            header.classList.toggle('is-hidden', self.direction === 1 && self.scroll() > 420);
        },
    });

    document.querySelectorAll('[data-theme="dark"]').forEach((section) => {
        ScrollTrigger.create({
            trigger: section,
            start: 'top 72px',
            end: 'bottom 72px',
            onToggle: (self) => header.classList.toggle('is-over-dark', self.isActive),
        });
    });
}
