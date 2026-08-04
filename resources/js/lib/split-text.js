/**
 * Minimal line/word splitter.
 *
 * GSAP's own SplitText is a paid Club plugin, so this covers what we actually
 * need: wrap each visual line (or word) in an overflow-hidden shell with an
 * inner element that can be translated up from behind it.
 *
 * Lines are detected by measuring word offsets after layout, which means it has
 * to re-run on resize — `revert()` restores the original markup first.
 */
export class SplitText {
    constructor(element, { type = 'lines' } = {}) {
        this.el = element;
        this.type = type;
        this.original = element.innerHTML;
        this.parts = [];
        this.split();
    }

    split() {
        const words = this.el.textContent.trim().split(/\s+/);

        this.el.innerHTML = words
            .map((word) => `<span class="split-word" style="display:inline-block">${word}</span>`)
            .join(' ');

        const wordEls = Array.from(this.el.querySelectorAll('.split-word'));

        if (this.type === 'words') {
            this.parts = wordEls.map((word) => this.#wrap(word));
            return;
        }

        // Group words by their vertical offset to recover visual lines.
        const lines = [];
        let currentTop = null;

        wordEls.forEach((word) => {
            const top = Math.round(word.offsetTop);
            if (currentTop === null || Math.abs(top - currentTop) > 4) {
                currentTop = top;
                lines.push([]);
            }
            lines[lines.length - 1].push(word);
        });

        this.el.innerHTML = '';
        this.parts = lines.map((lineWords) => {
            const outer = document.createElement('span');
            outer.className = 'split-line';

            const inner = document.createElement('span');
            inner.className = 'split-inner';
            inner.innerHTML = lineWords.map((w) => w.textContent).join(' ');

            outer.appendChild(inner);
            this.el.appendChild(outer);
            return inner;
        });
    }

    #wrap(word) {
        const outer = document.createElement('span');
        outer.className = 'split-line';
        outer.style.display = 'inline-block';

        const inner = document.createElement('span');
        inner.className = 'split-inner';
        inner.textContent = word.textContent;

        outer.appendChild(inner);
        word.replaceWith(outer);
        return inner;
    }

    revert() {
        this.el.innerHTML = this.original;
        this.parts = [];
    }
}
