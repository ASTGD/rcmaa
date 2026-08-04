/**
 * Live countdown to the reunion — days, hours, minutes and seconds.
 *
 * The target is rendered server-side as an ISO string so the page is correct on
 * first paint; this only keeps it ticking. Seconds mean a wakeup every second,
 * so the timer is stopped whenever the page is hidden and restarted — with an
 * immediate tick, so no stale figure is ever shown — when it comes back.
 */
export default (target) => ({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
    passed: false,
    timer: null,

    init() {
        this.start();
        document.addEventListener('visibilitychange', () => {
            document.hidden ? this.stop() : this.start();
        });
    },

    start() {
        this.tick();
        if (! this.timer) this.timer = setInterval(() => this.tick(), 1000);
    },

    stop() {
        if (this.timer) clearInterval(this.timer);
        this.timer = null;
    },

    destroy() {
        this.stop();
    },

    tick() {
        const remaining = new Date(target).getTime() - Date.now();

        if (remaining <= 0) {
            this.passed = true;
            this.days = this.hours = this.minutes = this.seconds = 0;
            this.stop();
            return;
        }

        const second = 1000;
        const minute = 60 * second;
        this.days = Math.floor(remaining / (1440 * minute));
        this.hours = Math.floor((remaining % (1440 * minute)) / (60 * minute));
        this.minutes = Math.floor((remaining % (60 * minute)) / minute);
        this.seconds = Math.floor((remaining % minute) / second);
    },

    /** Two digits for hours and minutes; days can run to three. */
    pad(value) {
        return String(value).padStart(2, '0');
    },
});
