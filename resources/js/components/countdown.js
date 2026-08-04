/**
 * Live countdown to the reunion — days, hours and minutes, as specified.
 *
 * The target is rendered server-side as an ISO string so the page is correct on
 * first paint; this only keeps it ticking. It updates once a minute rather than
 * once a second: nothing below minutes is displayed, so a faster timer would
 * just burn wakeups on mobile.
 */
export default (target) => ({
    days: 0,
    hours: 0,
    minutes: 0,
    passed: false,
    timer: null,

    init() {
        this.tick();
        this.timer = setInterval(() => this.tick(), 60_000);
        // Coming back from a background tab should not show a stale figure.
        document.addEventListener('visibilitychange', () => {
            if (! document.hidden) this.tick();
        });
    },

    destroy() {
        if (this.timer) clearInterval(this.timer);
    },

    tick() {
        const remaining = new Date(target).getTime() - Date.now();

        if (remaining <= 0) {
            this.passed = true;
            this.days = this.hours = this.minutes = 0;
            return;
        }

        const minute = 60_000;
        this.days = Math.floor(remaining / (1440 * minute));
        this.hours = Math.floor((remaining % (1440 * minute)) / (60 * minute));
        this.minutes = Math.floor((remaining % (60 * minute)) / minute);
    },

    /** Two digits for hours and minutes; days can run to three. */
    pad(value) {
        return String(value).padStart(2, '0');
    },
});
