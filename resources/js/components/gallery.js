import { stopScroll, startScroll, gsap } from '../lib/motion';

/** Filterable gallery grid with a keyboard-navigable lightbox. */
export default (items = []) => ({
    items,
    filter: 'all',
    open: false,
    index: 0,

    get filtered() {
        return this.filter === 'all' ? this.items : this.items.filter((i) => i.category === this.filter);
    },

    get current() {
        return this.filtered[this.index] ?? null;
    },

    setFilter(value) {
        this.filter = value;
        this.$nextTick(() => {
            gsap.fromTo(
                this.$el.querySelectorAll('[data-gallery-item]'),
                { opacity: 0, y: 24 },
                { opacity: 1, y: 0, duration: 0.6, stagger: 0.05, ease: 'power3.out' }
            );
        });
    },

    show(index) {
        this.index = index;
        this.open = true;
        stopScroll();
    },

    close() {
        this.open = false;
        startScroll();
    },

    next() {
        this.index = (this.index + 1) % this.filtered.length;
    },

    previous() {
        this.index = (this.index - 1 + this.filtered.length) % this.filtered.length;
    },

    onKey(event) {
        if (!this.open) return;
        if (event.key === 'Escape') this.close();
        if (event.key === 'ArrowRight') this.next();
        if (event.key === 'ArrowLeft') this.previous();
    },
});
