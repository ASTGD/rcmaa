<section id="about" class="bg-grid relative overflow-hidden bg-parchment py-24 md:py-32" data-parallax-scope>
    <div class="container-rc">
        <div class="grid gap-14 lg:grid-cols-[1fr_1.05fr] lg:items-center lg:gap-20">

            {{-- Image stack --}}
            <div class="relative">
                <div class="relative overflow-hidden rounded-3xl" data-reveal="mask">
                    <div class="aspect-4/5 w-full" data-parallax="-0.06">
                        @include('partials.home.about-figure')
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-ink-950/50 to-transparent"></div>
                </div>

                {{-- Founding-year seal --}}
                <div class="absolute -bottom-8 -right-4 w-44 rounded-2xl bg-ink-900 p-6 text-center shadow-[0_30px_70px_-30px_rgba(7,14,27,.6)] md:-right-10 md:w-52"
                     data-reveal="scale" data-reveal-delay="0.35">
                    <p class="font-mono text-[0.6rem] uppercase tracking-[0.2em] text-brass-400">Established since</p>
                    <p class="heading-display mt-2 text-5xl text-parchment" data-count="{{ config('rcmaa.college_founded') }}">0</p>
                    <div class="rule-brass mt-3"></div>
                    <p class="mt-3 text-[0.7rem] leading-snug text-ink-300">Rajshahi College, one of the subcontinent's oldest institutions</p>
                </div>
            </div>

            {{-- Copy --}}
            <div>
                <x-section-heading
                    eyebrow="About"
                    title="Welcome to the Rajshahi College Mathematics Alumni Association"
                    size="md"/>

                <div class="prose-rc mt-6 max-w-xl" data-reveal data-reveal-delay="0.2">
                    <p>
                        Founded with a legacy of academic excellence, the Rajshahi College Mathematics
                        Alumni Association brings together decades of graduates under one global
                        community. RCMAA is a space to reconnect, collaborate, and inspire the next
                        generation of mathematicians.
                    </p>
                    <p>
                        Together, we celebrate our shared journey, create meaningful opportunities,
                        and shape the future of our alma mater.
                    </p>
                </div>

                <div class="mt-9 flex flex-wrap gap-4" data-reveal data-reveal-delay="0.3">
                    <a href="{{ route('about') }}" class="btn btn-ink">Learn More</a>
                    <a href="{{ route('our-goal') }}" class="btn btn-outline">Our Goal</a>
                </div>
            </div>
        </div>

        {{-- Live counters --}}
        <div class="mt-24 grid gap-px overflow-hidden rounded-3xl bg-ink-900/10 md:grid-cols-3"
             data-reveal data-reveal-stagger="0.12">
            @foreach ($stats as $stat)
                <div class="group bg-parchment p-8 transition-colors duration-500 hover:bg-white" data-reveal-item>
                    <p class="heading-display flex items-baseline gap-1 text-[3.2rem] leading-none text-ink-950">
                        <span data-count="{{ $stat['value'] }}">0</span>
                        <span class="text-brass-600">{{ $stat['suffix'] }}</span>
                    </p>
                    <p class="mt-4 text-sm font-semibold tracking-tight text-ink-800">{{ $stat['label'] }}</p>
                    <p class="prose-rc mt-2 text-[0.82rem]">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
