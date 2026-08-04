import { gsap } from '../lib/motion';
import { scrollTo } from '../lib/motion';

/**
 * Multi-step reunion registration.
 *
 * Step 1 picks a registration category — the association prices by category
 * rather than a flat fee, and only two of the four may bring guests. Everything
 * downstream (the running total, the guest repeater, the payable amount) follows
 * from that choice.
 *
 * RegistrationRequest on the server is the source of truth; this only gates
 * navigation so people aren't told about a missing field five steps later.
 * Progress is mirrored to localStorage because the form is long and losing it to
 * an accidental refresh is the worst possible outcome.
 */
const STORAGE_KEY = 'rcmaa.registration.draft';

export default (config = {}) => ({
    step: 1,
    totalSteps: 7,
    submitting: false,
    errors: {},

    // Supplied by the server so the price list lives in exactly one place.
    categories: config.categories ?? {},
    guestFee: config.guestFee ?? 0,
    photoPreview: null,
    receiptPreview: null,
    receiptName: null,

    form: {
        category: '',

        full_name_en: '',
        full_name_bn: '',
        blood_group: '',
        mobile: '',
        whatsapp: '',
        email: '',
        present_address: '',
        permanent_address: '',

        session: '',
        degree: '',
        class_roll: '',
        registration_no: '',
        passing_year: '',

        employment_status: '',
        profession: '',
        designation: '',
        organization: '',

        tshirt_size: '',
        cultural_program: '',
        guest_count: '0',
        guests: [],

        memories: '',

        payment_method: '',
        transaction_id: '',
        sender_number: '',
        amount_paid: '',
        terms: false,
    },

    init() {
        this.restore();

        if (config.serverErrors && Object.keys(config.serverErrors).length) {
            this.errors = config.serverErrors;
            this.step = this.stepForField(Object.keys(config.serverErrors)[0]);
        }

        if (config.old) Object.assign(this.form, config.old);

        this.$watch('form', () => this.persist(), { deep: true });
        this.$watch('form.guest_count', (value) => this.syncGuests(value));
        // Switching to a category that cannot bring guests must clear any it had.
        this.$watch('form.category', () => {
            if (! this.allowsGuests) {
                this.form.guest_count = '0';
                this.form.guests = [];
            }
        });
        this.$watch('step', () => this.animateStep());
    },

    // --- Category ---------------------------------------------------------
    get category() {
        return this.categories[this.form.category] ?? null;
    },

    get allowsGuests() {
        return !! this.category?.allows_guests;
    },

    get categoryFee() {
        return this.category?.fee ?? 0;
    },

    choose(key) {
        this.form.category = key;
        this.errors.category = null;
    },

    // --- Persistence ------------------------------------------------------
    persist() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.form));
        } catch {
            // Private browsing / quota — a lost draft is not worth throwing over.
        }
    },

    restore() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (raw) Object.assign(this.form, JSON.parse(raw));
        } catch {
            localStorage.removeItem(STORAGE_KEY);
        }
    },

    clearDraft() {
        localStorage.removeItem(STORAGE_KEY);
    },

    // --- Guests -----------------------------------------------------------
    get guestTotal() {
        if (! this.allowsGuests) return 0;

        return this.form.guest_count === '3+'
            ? this.form.guests.length
            : parseInt(this.form.guest_count || 0, 10);
    },

    syncGuests(value) {
        if (! this.allowsGuests) {
            this.form.guests = [];
            return;
        }

        const target = value === '3+' ? Math.max(3, this.form.guests.length) : parseInt(value || 0, 10);

        while (this.form.guests.length < target) {
            this.form.guests.push({ name: '', relation: '', occupation: '' });
        }
        this.form.guests.length = target;
    },

    addGuest() {
        if (this.form.guests.length >= 8) return;
        this.form.guests.push({ name: '', relation: '', occupation: '' });
    },

    removeGuest(index) {
        this.form.guests.splice(index, 1);
        if (this.form.guests.length < 3) {
            this.form.guest_count = String(this.form.guests.length);
        }
    },

    // --- Money ------------------------------------------------------------
    get computedFee() {
        return this.categoryFee + this.guestTotal * this.guestFee;
    },

    get formattedFee() {
        return new Intl.NumberFormat('en-BD').format(this.computedFee);
    },

    money(value) {
        return new Intl.NumberFormat('en-BD').format(value ?? 0);
    },

    // --- Photo ------------------------------------------------------------
    handlePhoto(event) {
        const file = event.target.files?.[0];
        this.errors.photo = null;

        if (! file) {
            this.photoPreview = null;
            return;
        }

        if (file.size > 1024 * 1024) {
            this.errors.photo = 'Photo must be 1 MB or smaller.';
            event.target.value = '';
            this.photoPreview = null;
            return;
        }

        if (! ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.errors.photo = 'Please upload a JPG, PNG or WebP image.';
            event.target.value = '';
            this.photoPreview = null;
            return;
        }

        this.photoPreview = URL.createObjectURL(file);
    },

    // --- Payment receipt --------------------------------------------------
    handleReceipt(event) {
        const file = event.target.files?.[0];
        const reject = (message) => {
            this.errors.payment_receipt = message;
            event.target.value = '';
            this.receiptPreview = null;
            this.receiptName = null;
        };

        this.errors.payment_receipt = null;

        if (! file) {
            this.receiptPreview = null;
            this.receiptName = null;
            return;
        }

        if (file.size > 4 * 1024 * 1024) {
            return reject('Receipt must be 4 MB or smaller.');
        }

        const allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (! allowed.includes(file.type)) {
            return reject('Please upload a JPG, PNG, WebP or PDF file.');
        }

        this.receiptName = file.name;
        // A PDF has nothing to show inline; the filename is the confirmation.
        this.receiptPreview = file.type === 'application/pdf' ? null : URL.createObjectURL(file);
    },

    // --- Navigation -------------------------------------------------------
    stepForField(field) {
        const map = {
            1: ['category'],
            2: ['full_name_en', 'full_name_bn', 'blood_group', 'mobile', 'whatsapp', 'email', 'present_address', 'permanent_address'],
            3: ['session', 'degree', 'class_roll', 'registration_no', 'passing_year'],
            4: ['employment_status', 'profession', 'designation', 'organization'],
            5: ['tshirt_size', 'cultural_program', 'guest_count', 'guests'],
            6: ['memories', 'photo'],
            7: ['payment_method', 'transaction_id', 'sender_number', 'amount_paid', 'payment_receipt', 'terms'],
        };

        const root = field.split('.')[0];
        for (const [step, fields] of Object.entries(map)) {
            if (fields.includes(root)) return parseInt(step, 10);
        }
        return 1;
    },

    required(step) {
        return {
            1: ['category'],
            2: ['full_name_en', 'mobile', 'email', 'present_address'],
            3: ['session', 'degree', 'passing_year'],
            4: ['employment_status'],
            5: ['tshirt_size', 'cultural_program'],
            6: [],
            7: ['payment_method', 'transaction_id', 'sender_number', 'amount_paid', 'terms'],
        }[step] ?? [];
    },

    validateStep() {
        this.errors = {};

        this.required(this.step).forEach((field) => {
            const value = this.form[field];
            if (value === '' || value === null || value === false || value === undefined) {
                this.errors[field] = field === 'category'
                    ? 'Please choose the category that applies to you.'
                    : 'This field is required.';
            }
        });

        if (this.step === 2) {
            if (this.form.email && ! /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(this.form.email)) {
                this.errors.email = 'Enter a valid email address.';
            }
            if (this.form.mobile && ! /^(\+?88)?01[3-9]\d{8}$/.test(this.form.mobile.replace(/[\s-]/g, ''))) {
                this.errors.mobile = 'Enter a valid Bangladeshi mobile number (e.g. 01712345678).';
            }
        }

        if (this.step === 3 && this.form.passing_year) {
            const year = parseInt(this.form.passing_year, 10);
            const now = new Date().getFullYear();
            if (Number.isNaN(year) || year < 1873 || year > now + 1) {
                this.errors.passing_year = `Enter a year between 1873 and ${now + 1}.`;
            }
        }

        if (this.step === 4 && ['employed', 'self_employed'].includes(this.form.employment_status)) {
            if (! this.form.profession) this.errors.profession = 'This field is required.';
            if (! this.form.organization) this.errors.organization = 'This field is required.';
        }

        if (this.step === 5 && this.guestTotal > 0) {
            this.form.guests.forEach((guest, index) => {
                if (! guest.name) this.errors[`guests.${index}.name`] = 'Guest name is required.';
            });
        }

        return Object.keys(this.errors).length === 0;
    },

    next() {
        if (! this.validateStep()) return this.focusFirstError();
        if (this.step < this.totalSteps) this.step += 1;
        this.toTop();
    },

    previous() {
        this.errors = {};
        if (this.step > 1) this.step -= 1;
        this.toTop();
    },

    goTo(step) {
        if (step > this.step && ! this.validateStep()) return this.focusFirstError();
        this.step = step;
        this.toTop();
    },

    submit(event) {
        if (! this.validateStep()) {
            event.preventDefault();
            return this.focusFirstError();
        }
        this.submitting = true;
        this.clearDraft();
    },

    focusFirstError() {
        this.$nextTick(() => {
            const el = this.$el.querySelector('[aria-invalid="true"]');
            el?.focus({ preventScroll: true });
            if (el) scrollTo(el, { offset: -160, duration: 0.8 });
        });
    },

    toTop() {
        this.$nextTick(() => scrollTo(this.$el, { offset: -110 }));
    },

    animateStep() {
        const panel = this.$el.querySelector('[data-step-panel]');
        if (! panel) return;

        gsap.fromTo(panel, { opacity: 0, y: 18 }, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' });
    },

    get progress() {
        return Math.round(((this.step - 1) / (this.totalSteps - 1)) * 100);
    },
});
