{{-- Closing call to action, shared by most inner pages. --}}
<section class="relative overflow-hidden bg-parchment py-20 md:py-24">
    <div class="container-rc">
        <div data-theme="dark" class="relative overflow-hidden rounded-3xl bg-ink-950 px-8 py-16 text-center md:px-16 md:py-20">
            <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
            <div class="pointer-events-none absolute -top-24 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-brass-700/25 blur-[100px]"></div>

            <div class="relative">
                <p class="eyebrow eyebrow-light justify-center" data-reveal>Grand Reunion 2026</p>

                <h2 class="heading-display mx-auto mt-6 max-w-2xl text-[clamp(1.8rem,3.8vw,2.9rem)] text-parchment"
                    data-reveal="split">
                    Reunite with the people who sat beside you
                </h2>

                <p class="prose-rc mx-auto mt-5 max-w-xl !text-ink-200" data-reveal data-reveal-delay="0.15">
                    Registration for Math Nexus is open. Secure your seat, bring your family, and be part
                    of this milestone at Rajshahi College.
                </p>

                <div class="mt-9 flex flex-wrap justify-center gap-3" data-reveal data-reveal-delay="0.25">
                    <a href="{{ route('register.create') }}" class="btn btn-primary">Register Now</a>
                    <a href="{{ route('help-center') }}" class="btn btn-outline-light">Need Help?</a>
                </div>
            </div>
        </div>
    </div>
</section>
