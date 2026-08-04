import { gsap, ScrollTrigger, prefersReducedMotion } from './motion';

/**
 * The about figure: a point travelling the unit circle, its height plotted to
 * the right as a sine wave.
 *
 * The curve is already in the markup with the correct shape — this only reveals
 * it in step with the point, so the figure still reads if the script never runs.
 * The loop is paused whenever the figure is off screen; there is no reason to
 * run a timer for something nobody is looking at.
 */
export function initMathFigure() {
    document.querySelectorAll('[data-math-figure]').forEach((svg) => {
        const wave = svg.querySelector('[data-figure-wave]');
        const dot = svg.querySelector('[data-figure-dot]');
        const tip = svg.querySelector('[data-figure-tip]');
        const radius = svg.querySelector('[data-figure-radius]');
        const height = svg.querySelector('[data-figure-height]');
        const link = svg.querySelector('[data-figure-link]');
        if (!wave || !dot) return;

        // Read the geometry back off the markup so the two stay in step.
        const cx = Number(svg.querySelector('circle[data-figure-dot]').getAttribute('cx'));
        const cy = Number(dot.getAttribute('cy'));
        const centreX = Number(radius.getAttribute('x1'));
        const centreY = Number(radius.getAttribute('y1'));
        const r = cx - centreX;

        const length = wave.getTotalLength();
        const plotStart = Number(link.getAttribute('x2'));
        const plotEnd = wave.getPointAtLength(length).x;

        wave.style.strokeDasharray = `${length}`;

        const draw = (turn) => {
            const theta = turn * Math.PI * 2;
            const px = centreX + r * Math.cos(theta);
            const py = centreY - r * Math.sin(theta);

            dot.setAttribute('cx', px);
            dot.setAttribute('cy', py);

            radius.setAttribute('x2', px);
            radius.setAttribute('y2', py);

            // The vertical leg is the value being plotted.
            height.setAttribute('x1', px);
            height.setAttribute('y1', centreY);
            height.setAttribute('x2', px);
            height.setAttribute('y2', py);

            const tipX = plotStart + (plotEnd - plotStart) * turn;
            link.setAttribute('x1', px);
            link.setAttribute('y1', py);
            link.setAttribute('x2', tipX);
            link.setAttribute('y2', py);

            tip.setAttribute('cx', tipX);
            tip.setAttribute('cy', py);

            wave.style.strokeDashoffset = `${length * (1 - turn)}`;
        };

        // Someone who has asked for less motion still gets the whole idea:
        // the finished curve, with the point at its peak.
        if (prefersReducedMotion) {
            wave.style.strokeDasharray = 'none';
            wave.style.strokeDashoffset = '0';
            draw(0.25);
            wave.style.strokeDasharray = 'none';
            wave.style.strokeDashoffset = '0';
            return;
        }

        const state = { turn: 0 };
        const loop = gsap.to(state, {
            turn: 1,
            duration: 7,
            ease: 'none',
            repeat: -1,
            paused: true,
            onUpdate: () => draw(state.turn),
        });

        draw(0);

        ScrollTrigger.create({
            trigger: svg,
            start: 'top bottom',
            end: 'bottom top',
            onToggle: (self) => (self.isActive ? loop.play() : loop.pause()),
        });
    });
}
