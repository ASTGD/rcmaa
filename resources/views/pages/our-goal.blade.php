{{--
    Content on this page is the association's own, taken verbatim from the
    Bangla "Our goal" page of rcmaa.bd (page 389). The Bangla is authoritative;
    the English beneath each section is a translation for non-Bangla readers.
--}}
<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Our Goal"
        title="History, aims and objectives"
        lead="How RCMAA began, what it exists to do, and the people leading it."
        :breadcrumbs="['About' => route('about'), 'Our Goal' => null]"/>

    {{-- ── History ─────────────────────────────────────────────────────── --}}
    <section class="bg-grid bg-parchment py-20 md:py-28">
        <div class="container-narrow">
            <x-section-heading eyebrow="ইতিহাস · History" title="Where the association came from"/>

            <div class="prose-rc mt-8 space-y-5 text-[1.02rem]" data-reveal>
                <p lang="bn" class="font-bangla">
                    রাজশাহী কলেজের গণিত বিভাগ দীর্ঘদিন ধরে দেশের বিভিন্ন প্রান্তে অসংখ্য মেধাবী শিক্ষার্থী,
                    শিক্ষক, গবেষক, সরকারি ও বেসরকারি প্রতিষ্ঠানের কর্মকর্তা, পেশাজীবী ও সুপ্রতিষ্ঠিত নাগরিক
                    গড়ে তুলতে গুরুত্বপূর্ণ ভূমিকা পালন করে আসছে। কিন্তু সময়ের পরিক্রমায় বিভাগের প্রাক্তন ও
                    বর্তমান শিক্ষার্থীদের মধ্যে যোগাযোগ ধীরে ধীরে বিচ্ছিন্ন হয়ে যাওয়ার উপক্রম হয়। এই বাস্তবতা
                    থেকে কিছু উদ্যোগী প্রাক্তন ও বর্তমান শিক্ষার্থীর মধ্যে এমন একটি প্ল্যাটফর্ম গড়ে তোলার চিন্তা
                    জন্ম নেয়, যা গণিত বিভাগের সকল সদস্যকে একটি অভিন্ন বন্ধনে আবদ্ধ করবে। সেই ভাবনা থেকেই
                    Rajshahi College Mathematics Alumni Association (RCMAA)-এর সূচনা।
                </p>

                <p class="border-l-2 border-brass-500 pl-5 !text-ink-500">
                    For many years the Department of Mathematics at Rajshahi College has helped shape
                    countless able students, teachers, researchers, officers of public and private
                    institutions, professionals and established citizens across the country. But over
                    time, contact between the department's past and present students began to fall away.
                    Out of that reality, a group of enterprising former and current students conceived a
                    platform that would bind every member of the department in a common bond — and from
                    that thought, RCMAA began.
                </p>
            </div>
        </div>
    </section>

    {{-- ── Timeline ────────────────────────────────────────────────────── --}}
    <section id="journey" data-theme="dark" class="scroll-mt-28 relative overflow-hidden bg-ink-900 py-20 md:py-28">
        <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute top-1/3 -right-40 h-[28rem] w-[28rem] rounded-full bg-brass-700/12 blur-[130px]"></div>

        <div class="container-rc relative">
            <x-section-heading light eyebrow="আমাদের পথচলা · Our Journey"
                title="From an idea to Math Nexus 2026"/>

            <ol class="mt-16 space-y-0" data-reveal data-reveal-stagger="0.08">
                @foreach ([
                    ['16 December 2025', 'RCMAA begins', 'The association\'s journey starts.', '২০২৫ সালের ১৬ ডিসেম্বর RCMAA-এর যাত্রা শুরু হয়।'],
                    ['3 January 2026', 'Formally constituted', 'At a meeting in front of the Political Science building at Rajshahi College, the initiative takes formal shape with students from the 2011-12 through 2024-25 sessions taking part.', 'রাজশাহী কলেজের রাষ্ট্রবিজ্ঞান ভবনের সামনে অনুষ্ঠিত এক সভার মাধ্যমে ২০১১-১২ থেকে ২০২৪-২৫ সেশন পর্যন্ত শিক্ষার্থীদের অংশগ্রহণে উদ্যোগটি আনুষ্ঠানিক রূপ লাভ করে।'],
                    ['10 January 2026', 'Leadership connected', 'Through Mst. Mafruha Mustari (session 1996-97) — a current teacher of the department and herself a former student — contact is established with Convenor Md. Rofikul Islam (1995-96) and Member Secretary Md. Mahabub Khan Murad (1996-97).', '১০ জানুয়ারি ২০২৬ তারিখে রাজশাহী কলেজ গণিত বিভাগের বর্তমান শিক্ষক এবং বিভাগেরই প্রাক্তন শিক্ষার্থী মোছাঃ মাফরুহা মুস্তারি (সেশন: ১৯৯৬-৯৭)-এর মাধ্যমে বর্তমান আহ্বায়ক ও সদস্য সচিবের সঙ্গে যোগাযোগ স্থাপিত হয়।'],
                    ['January 2026', 'Reunion roadmap drafted', 'A meeting at Shaheed A.H.M. Kamaruzzaman Government Degree College sets out the roadmap and overall plan for a possible reunion.', 'শহীদ এ.এইচ.এম. কামারুজ্জামান সরকারি ডিগ্রি কলেজে অনুষ্ঠিত এক সভায় সম্ভাব্য রিইউনিয়নের রোডম্যাপ ও সার্বিক পরিকল্পনা নিয়ে বিস্তারিত আলোচনা অনুষ্ঠিত হয়।'],
                    ['18 January 2026', 'Department consulted', 'Formal discussions are completed with the teachers of the department and the head of department.', '১৮ জানুয়ারি ২০২৬ তারিখে বিভাগের শিক্ষকবৃন্দ এবং বিভাগীয় প্রধানের সঙ্গে আনুষ্ঠানিক আলোচনা সম্পন্ন হয়।'],
                    ['25 January 2026', 'Principal\'s approval', 'Written approval is received from the Principal of Rajshahi College, and 19 or 25 December 2026 is set as the probable reunion date.', '২৫ জানুয়ারি ২০২৬ তারিখে রাজশাহী কলেজের অধ্যক্ষ মহোদয়ের লিখিত অনুমোদন গ্রহণ করা হয়।'],
                    ['Ramadan 2026', 'Iftar Mahfil', 'An Iftar gathering brings together students from batches going back to 1993. The partial convening committee and the full sub-committee for Reunion 2026 are announced there.', 'পবিত্র রমজান মাসে একটি ইফতার মাহফিলের আয়োজন করা হয়, যেখানে ১৯৯৩ সাল পর্যন্ত বিভিন্ন ব্যাচের শিক্ষার্থীরা অংশগ্রহণ করেন।'],
                    ['19 December 2026', 'Math Nexus — Reunion 2026', 'The association\'s largest undertaking: not merely a reunion, but an attempt to build a strong bridge of relationships, memories, experience and cooperation from one generation to the next.', 'RCMAA-এর সবচেয়ে বড় উদ্যোগ হলো Math Nexus – RCMAA Reunion 2026।'],
                ] as $i => [$date, $heading, $body, $bn])
                    <li class="group relative flex gap-6 pb-10 sm:gap-10" data-reveal-item>
                        {{-- Rail --}}
                        <div class="relative flex flex-none flex-col items-center">
                            <span class="mt-1.5 h-3 w-3 rounded-full bg-brass-500 ring-4 ring-brass-500/15"></span>
                            @unless ($loop->last)
                                <span class="mt-2 w-px flex-1 bg-white/12"></span>
                            @endunless
                        </div>

                        <div class="min-w-0 pb-2">
                            <p class="font-mono text-[0.66rem] uppercase tracking-[0.18em] text-brass-400">{{ $date }}</p>
                            <h3 class="heading-display mt-2 text-xl text-parchment">{{ $heading }}</h3>
                            <p class="mt-2.5 max-w-2xl text-sm leading-relaxed text-ink-300">{{ $body }}</p>
                            <p lang="bn" class="mt-2 max-w-2xl text-[0.82rem] leading-relaxed text-ink-400">{{ $bn }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-4 rounded-2xl border border-white/10 bg-ink-800/50 p-6" data-reveal>
                <p class="text-sm leading-relaxed text-ink-300">
                    Active in establishing that first contact were
                    <span class="text-parchment">Md. Shafiul Islam</span> (2011-12),
                    <span class="text-parchment">Md. Abdul Bari</span> (2014-15),
                    <span class="text-parchment">Md. Samiul Islam</span> (2017-18),
                    <span class="text-parchment">Rafawat Ahmed</span> (2019-20),
                    <span class="text-parchment">Mst. Taslima Khatun</span> (2019-20) and
                    <span class="text-parchment">Md. Toufiqul Islam</span> (2019-20).
                </p>
                <p class="mt-3 text-sm leading-relaxed text-ink-400">
                    In the same period the association's official Facebook page was launched, followed by a
                    continuing series of meetings, planning sessions, a logo competition, the naming process,
                    videography and the development of this website.
                </p>
            </div>
        </div>
    </section>

    {{-- ── Aims ────────────────────────────────────────────────────────── --}}
    <section id="aims" class="scroll-mt-28 bg-parchment py-20 md:py-28">
        <div class="container-rc">
            <x-section-heading eyebrow="লক্ষ্য · Aims" title="What we are working towards"/>

            <div class="mt-14 grid gap-x-14 gap-y-9 lg:grid-cols-2">
                @foreach ([
                    ['Build a lasting network', 'To build a permanent, effective and strong network among the past and present students of the Department of Mathematics.', 'গণিত বিভাগের প্রাক্তন ও বর্তমান শিক্ষার্থীদের মধ্যে একটি স্থায়ী, কার্যকর ও শক্তিশালী নেটওয়ার্ক গড়ে তোলা।'],
                    ['Connect the generations', 'To ensure an environment of communication, harmony and cooperation between students of one generation and the next.', 'প্রজন্মের পর প্রজন্মের শিক্ষার্থীদের মধ্যে যোগাযোগ, সম্প্রীতি ও সহযোগিতার পরিবেশ নিশ্চিত করা।'],
                    ['Stand beside those in need', 'To stand beside talented and financially struggling students and arrange the support they need.', 'মেধাবী ও আর্থিকভাবে অসচ্ছল শিক্ষার্থীদের পাশে দাঁড়ানো এবং প্রয়োজনীয় সহযোগিতার ব্যবস্থা করা।'],
                    ['A structure for emergencies', 'To build a coordinated support structure to assist students of the department in medical or emergency need.', 'চিকিৎসা বা জরুরি প্রয়োজনে বিভাগের শিক্ষার্থীদের সহায়তার জন্য একটি সমন্বিত সহযোগিতা কাঠামো গড়ে তোলা।'],
                    ['Share what we know', 'To create opportunities for the exchange of knowledge, experience and advice between senior and junior students.', 'সিনিয়র ও জুনিয়র শিক্ষার্থীদের মধ্যে জ্ঞান, অভিজ্ঞতা ও পরামর্শ বিনিময়ের সুযোগ সৃষ্টি করা।'],
                    ['One large family', 'To keep every student of the Department of Mathematics connected as one large family.', 'গণিত বিভাগের সকল শিক্ষার্থীকে একটি বৃহৎ পরিবার হিসেবে সংযুক্ত রাখা।'],
                ] as $i => [$heading, $body, $bn])
                    <article class="flex gap-5 border-t border-ink-900/10 pt-7" data-reveal>
                        <span class="heading-display flex-none text-3xl text-brass-500">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="heading-display text-lg text-ink-950">{{ $heading }}</h3>
                            <p class="prose-rc mt-2 text-[0.92rem]">{{ $body }}</p>
                            <p lang="bn" class="mt-2 text-[0.85rem] leading-relaxed text-ink-400">{{ $bn }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Objectives ──────────────────────────────────────────────────── --}}
    <section id="objectives" class="scroll-mt-28 bg-parchment-dim py-20 md:py-28">
        <div class="container-rc">
            <x-section-heading eyebrow="উদ্দেশ্য · Objectives" title="How we intend to get there"/>

            <ul class="mt-14 grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-reveal data-reveal-stagger="0.07">
                @foreach ([
                    ['link', 'Re-establish the contact between past and present students that had been lost.', 'প্রাক্তন ও বর্তমান শিক্ষার্থীদের মধ্যে হারিয়ে যাওয়া যোগাযোগ পুনঃপ্রতিষ্ঠা করা।'],
                    ['shield', 'Establish RCMAA as an effective and long-term platform.', 'RCMAA-কে একটি কার্যকর ও দীর্ঘমেয়াদি প্ল্যাটফর্ম হিসেবে প্রতিষ্ঠিত করা।'],
                    ['users', 'Strengthen further the relationship between teachers, former students and current students.', 'শিক্ষক, প্রাক্তন শিক্ষার্থী ও বর্তমান শিক্ষার্থীদের মধ্যে সম্পর্ক আরও সুদৃঢ় করা।'],
                    ['book', 'Build, preserve and regularly update a session-wise database.', 'সেশনভিত্তিক তথ্যভান্ডার গড়ে তোলা, সংরক্ষণ করা এবং নিয়মিত হালনাগাদ রাখা।'],
                    ['globe', 'Keep the alumni network active through social, educational and organisational activities.', 'বিভিন্ন সামাজিক, শিক্ষামূলক ও সাংগঠনিক কার্যক্রমের মাধ্যমে Alumni Network সক্রিয় রাখা।'],
                    ['calendar', 'Lay the foundation for regular annual gatherings, seminars and discussion meetings.', 'প্রতি বছর নিয়মিতভাবে বিভিন্ন মিলনমেলা, সেমিনার, আলোচনা সভা ও অন্যান্য কার্যক্রম আয়োজনের ভিত্তি তৈরি করা।'],
                    ['sparkle', 'Bring students of every generation onto one common platform by successfully staging Math Nexus — RCMAA Reunion 2026.', 'Math Nexus - RCMAA Reunion 2026 সফলভাবে আয়োজনের মাধ্যমে গণিত বিভাগের সকল প্রজন্মের শিক্ষার্থীদের একটি অভিন্ন প্ল্যাটফর্মে একত্রিত করা।'],
                ] as [$icon, $body, $bn])
                    <li class="card p-6" data-reveal-item>
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-ink-900 text-brass-500">
                            <x-icon :name="$icon" class="h-5 w-5"/>
                        </span>
                        <p class="mt-5 text-[0.92rem] font-medium leading-relaxed text-ink-800">{{ $body }}</p>
                        <p lang="bn" class="mt-2 text-[0.82rem] leading-relaxed text-ink-400">{{ $bn }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ── Leadership ──────────────────────────────────────────────────── --}}
    <section class="bg-parchment py-20 md:py-28">
        <div class="container-rc">
            <x-section-heading
                eyebrow="আহ্বায়ক ও সদস্য সচিব · Leadership"
                title="Convenor and Member Secretary"/>

            <div class="mt-14 grid gap-6 md:grid-cols-2" data-reveal data-reveal-stagger="0.12">
                @foreach ([
                    ['md-rofikul-islam', 'Md. Rofikul Islam', 'মোঃ রফিকুল ইসলাম', 'Convenor', 'আহ্বায়ক', '1995-96', 'Associate Teacher, Mathematics', 'Government Women\'s College, Rajshahi'],
                    ['md-mahabub-khan-murad', 'Md. Mahabub Khan Murad', 'মোঃ মাহাবুব খান মুরাদ', 'Member Secretary', 'সদস্য সচিব', '1996-97', 'Assistant Teacher, Mathematics', 'Shaheed Abul Hasnat Mohammad Kamaruzzaman Government Degree College, Rajshahi'],
                ] as [$slug, $name, $nameBn, $role, $roleBn, $session, $profession, $workplace])
                    <article class="card card-hover flex flex-col gap-6 p-6 sm:flex-row sm:items-start" data-reveal-item>
                        <img src="{{ Storage::disk('public')->url("committee/{$slug}.png") }}"
                             alt="{{ $name }}" loading="lazy"
                             class="h-32 w-28 flex-none rounded-xl bg-ink-900 object-cover object-top">

                        <div class="min-w-0">
                            <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">{{ $role }}</p>
                            <p lang="bn" class="text-xs text-ink-400">{{ $roleBn }}</p>

                            <h3 class="heading-display mt-3 text-xl text-ink-950">{{ $name }}</h3>
                            <p lang="bn" class="text-sm text-ink-500">{{ $nameBn }}</p>

                            <dl class="mt-4 space-y-1.5 text-[0.85rem]">
                                <div class="flex gap-2">
                                    <dt class="flex-none text-ink-400">Session</dt>
                                    <dd class="font-medium text-ink-800">{{ $session }}</dd>
                                </div>
                                <div class="flex gap-2">
                                    <dt class="flex-none text-ink-400">Profession</dt>
                                    <dd class="font-medium text-ink-800">{{ $profession }}</dd>
                                </div>
                                <div class="flex gap-2">
                                    <dt class="flex-none text-ink-400">Workplace</dt>
                                    <dd class="text-ink-700">{{ $workplace }}</dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                @endforeach
            </div>

            <p class="mt-8 text-center text-sm text-ink-400" data-reveal>
                <a href="{{ route('committee') }}" class="text-brass-700 underline underline-offset-4 transition hover:text-ink-950">
                    See the full committee
                </a>
            </p>
        </div>
    </section>

    @include('partials.cta')
</x-layout>
