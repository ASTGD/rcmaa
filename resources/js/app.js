import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

import { initSmoothScroll, scrollTo, ScrollTrigger, prefersReducedMotion } from './lib/motion';
import {
    initReveals,
    initParallax,
    initCounters,
    initMarquees,
    initLazyImages,
    refreshOnMediaLoad,
    revealStranded,
} from './lib/reveals';
import { initHero, initHeaderTheme } from './lib/hero';
import { initMathFigure } from './lib/figure';
import registrationForm from './components/registration-form';
import gallery from './components/gallery';
import countdown from './components/countdown';

// Guards the pre-animation hidden state in app.css — set before first paint so
// there is no flash of visible-then-hidden content.
document.documentElement.classList.add('js-ready');

Alpine.plugin(collapse);
Alpine.data('registrationForm', registrationForm);
Alpine.data('gallery', gallery);
Alpine.data('countdown', countdown);
window.Alpine = Alpine;

function boot() {
    initSmoothScroll();
    initHero();
    initHeaderTheme();
    initReveals();
    initParallax();
    initCounters();
    initMarquees();
    initMathFigure();

    // In-page anchors go through Lenis so they inherit the same easing.
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach((link) => {
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;
            event.preventDefault();
            scrollTo(target);
        });
    });

    // Fonts land after first paint and reflow every measured line.
    document.fonts?.ready.then(() => ScrollTrigger.refresh());

    // Native loading="lazy" never fires under Lenis, so we decide it ourselves.
    initLazyImages();

    // Lazy images land later still, and move everything below them.
    refreshOnMediaLoad();

    // Last resort: nothing marked for reveal may stay invisible just because a
    // trigger missed. Cheap — it only reads opacity for what is actually on screen.
    let sweep;
    window.addEventListener(
        'scroll',
        () => {
            window.clearTimeout(sweep);
            sweep = window.setTimeout(revealStranded, 300);
        },
        { passive: true }
    );
    window.addEventListener('load', () => {
        ScrollTrigger.refresh();
        revealStranded();
    });

    // Background tabs suspend requestAnimationFrame, so a page opened in one
    // never runs its reveals. Recompute once it is actually looked at.
    document.addEventListener('visibilitychange', () => {
        if (! document.hidden) ScrollTrigger.refresh();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

Alpine.start();

export { scrollTo, prefersReducedMotion };
