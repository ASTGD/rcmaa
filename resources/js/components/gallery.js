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

    /*
     * Keyed by the item's id, never by the loop position.
     *
     * The x-for iterator used to be named `index` — the same name as this
     * component's own `index` property. Whichever scope won the lookup decided
     * what show() received, and when the component's own property won, every
     * click opened image 0. The client saw exactly that: any photo tapped, the
     * first one opens. An id cannot collide with anything and survives
     * filtering besides.
     */
    show(id) {
        const at = this.filtered.findIndex((item) => item.id === id);
        this.index = at === -1 ? 0 : at;
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
