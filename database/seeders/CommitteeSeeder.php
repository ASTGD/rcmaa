<?php

namespace Database\Seeders;

use App\Models\CommitteeMember;
use Illuminate\Database\Seeder;

/**
 * The association's official committee roster.
 *
 * Source: "Committee.pdf", the signed declaration dated 28/02/2026 issued by the
 * Convenor and Member Secretary. Bangla names, sessions and roles are verbatim
 * from that document and are authoritative.
 *
 * English spellings come from the committee's own photograph filenames wherever
 * they supplied one; the rest are transliterations and are marked `en_provisional`
 * in the README so a Bangla reader can correct them.
 *
 * PRIVACY: every member's personal mobile number is in that document. They are
 * stored here because the committee needs them in the admin, but they are NEVER
 * rendered on a public page — CommitteePrivacyTest enforces that.
 */
class CommitteeSeeder extends Seeder
{
    /**
     * [bangla name, english name, session, role (en), role (bn), phone, sub-committee]
     */
    private const CONVENING = [
        ['মোঃ রফিকুল ইসলাম', 'Md. Rofikul Islam', '1995-96', 'Convenor', 'আহ্বায়ক', '01718-720520'],
        ['মোঃ মাহবুব খান মুরাদ', 'Md. Mahbub Khan Murad', '1996-97', 'Member Secretary', 'সদস্য সচিব', '01717-797577'],
        ['মোছাঃ মাফরুহা মুস্তারী', 'Mst. Mafruha Mustari', '1996-97', 'Treasurer', 'কোষাধ্যক্ষ', '01725-905474'],
        ['মোঃ শাফিউল ইসলাম', 'Md. Shafiul Islam', '2011-12', 'Overall supervision of sub-committees', 'উপকমিটির সার্বিক তত্ত্বাবধায়নে', '01729-190424'],
        ['মোঃ আব্দুল বারী', 'Md. Abdul Bari', '2014-15', 'Overall supervision of sub-committees', 'উপকমিটির সার্বিক তত্ত্বাবধায়নে', '01521-477371'],
        ['মোঃ সামিউল ইসলাম', 'Md. Samiul Islam', '2017-18', 'Overall supervision of sub-committees', 'উপকমিটির সার্বিক তত্ত্বাবধায়নে', '01770-788627'],
    ];

    /** Sub-committee => [bangla, english, session, role, phone] */
    private const SUB = [
        'তথ্য ও প্রযুক্তি কমিটি|Information & Technology Committee' => [
            ['রাফাওয়াত আহমেদ', 'Rafawat Ahmed', '2019-20', 'Member', '01643-740416'],
            ['মোঃ সৌরভ ইসলাম', 'Md. Sourav Islam', '2021-22', 'Member', '01521-774557'],
            ['মোঃ মনির হোসেন', 'Md. Monir Hossen', '2019-20', 'Support', '01787-830899'],
            ['শ্যানন চৌধুরী', 'Sanon Chowdhury', '2023-24', 'Support', '01773-559497'],
            ['মোঃ নাইস রেজা', 'Md. Nice Reza', '2024-25', 'Support', '01740-801328'],
        ],
        'সাংস্কৃতিক কমিটি|Cultural Committee' => [
            ['মোঃ তৌফিকুল ইসলাম', 'Md. Toufiqul Islam', '2019-20', 'Member', '01778-014983'],
            ['মাহজাবীন আরা তন্দ্রা', 'Mahjabin Ara Tandra', '2019-20', 'Member', '01778-039038'],
            ['অপুর্ব কুমার', 'Apurbo Kumar', '2023-24', 'Support', '01745-462695'],
        ],
        'স্পনসরশিপ কমিটি|Sponsorship Committee' => [
            ['মোসাঃ তাসলিমা খাতুন', 'Mst. Taslima Khatun', '2019-20', 'Member', '01763-007381'],
            ['ইমতিয়াজ হোসেন', 'Imtiaz Hossain', '2021-22', 'Member', '01617-516519'],
        ],
        'নিবন্ধন ও তথ্যসংরক্ষণ কমিটি|Registration & Data Preservation Committee' => [
            ['মোঃ রাসেল রানা', 'Md. Rasel Rana', 'Masters 2022-23', 'Member', '01303-679188'],
            ['মোঃ জুবায়ের হোসেন', 'Md. Jubair Hossain', '2021-22', 'Member', '01849-694676'],
            ['মারুফা সুলতানা', 'Marufa Sultana', '2024-25', 'Support', '01720-214960'],
            ['মোঃ রাশিদুল ইসলাম', 'Md. Rashidul Islam', '2024-25', 'Support', '01797-781407'],
        ],
        'প্রচার ও প্রকাশনা কমিটি|Publicity & Publications Committee' => [
            ['মোঃ রুহুল আমিন শাওন', 'Md. Ruhul Amin Shawon', '2021-22', 'Member', '01858-414233'],
            ['মাহমুদুল হাসান শিমুল', 'Mahmudul Hasan Shimul', '2022-23', 'Member', '01770-666646'],
            ['সদরুল আনাম', 'Md. Sadrul Anam', '2022-23', 'Member', '01303-301125'],
        ],
        'স্মরণিকা ও প্রকাশনা কমিটি|Souvenir & Publications Committee' => [
            ['সাবরিহা নিশাত চেলসি', 'Sabriha Nishat Chelsi', '2019-20', 'Member', '01722-030649'],
            ['সাবিকুন্নাহার স্বর্ণা', 'Sabikun Swarna', '2019-20', 'Member', '01781-774518'],
            ['বিজয় আহমেদ', 'Bijoy Ahmed', '2019-20', 'Member', '01795-974006'],
            ['অভিষেক কুমার', 'Avishake Kumar', '2023-24', 'Support', '01707-909729'],
            ['মিসকাতুল মেহেজাবিন', 'Miskatul Mehajabin', '2022-23', 'Support', '01718-183892'],
            ['মোছাঃ নুসরাত জাহান ঋতু', 'Mst. Nusrat Jahan Ritu', '2023-24', 'Support', '01763-489188'],
        ],
        'দপ্তর কমিটি|Administrative Committee' => [
            ['নাদিম মোস্তফা জুবায়ের', 'Nadim Mostofa Zubair', '2019-20', 'Member', '01717-087778'],
            ['সাবরিনা ইসলাম দিনা', 'Sabrina Islam Dina', '2020-21', 'Member', '01328-969766'],
            ['আলী আহসান মোঃ মুজাহিদ', 'Ali Ahsan Md. Mujahid', '2022-23', 'Member', '01609-135890'],
        ],
        'ফটোগ্রাফি ও ভিডিওগ্রাফি কমিটি|Photography & Videography Committee' => [
            ['মোঃ জুহাইর হাসান মুন্নাদ', 'Md. Juhaer Hasan Munnad', '2021-22', 'Member', '01949-611806'],
            ['মোঃ আসিফ ইকবাল', 'Md. Asif Iqbal', '2021-22', 'Member', '01305-824148'],
        ],
        'যোগাযোগ বিষয়ক কমিটি|Communication Committee' => [
            ['মরিয়ম আক্তার মিম', 'Moriom Akter Mim', '2020-21', 'Member', '01755-783991'],
            ['মোবাশশির রাইয়ান', 'Mobashshir Raiyan', '2021-22', 'Member', '01796-893489'],
            ['কাশফিয়া তাবাসসুম', 'Kasfiea Tabassum', '2022-23', 'Support', '01621-684746'],
            ['তনিমা রহমান', 'Tonima Rahman', '2022-23', 'Support', '01777-918028'],
            ['মুগ্ধশ্রী দাস তিথি', 'Mughdhasree Das Tithi', '2023-24', 'Support', '01704-492354'],
        ],
        'খাদ্য ও আপ্যায়ন বিষয়ক কমিটি|Food & Hospitality Committee' => [
            ['শরিফুল ইসলাম সাগর', 'Md. Shariful Islam Sagor', '2018-19', 'Member', '01568-835489'],
            ['আবরার শাহারিয়ার', 'Abrar Shahariar', '2020-21', 'Member', '01792-926048'],
            ['মাসুদ মিরাজ', 'Masud Miraj', '2021-22', 'Member', '01700-995472'],
            ['মোঃ মোস্তাফিজুর রহমান', 'Md. Mostafizur Rahman', '2021-22', 'Support', '01786-045191'],
            ['মুজাহিদ ইসলাম কাব্য', 'Muhammad Islam Kabbo', '2022-23', 'Support', '01572-919814'],
            ['মোঃ তৌফিক হাসান', 'Md. Toufiq Hasan', '2024-25', 'Support', '01323-745311'],
        ],
        'ব্যাচ প্রতিনিধি সমন্বয় বিষয়ক কমিটি|Batch Representative Coordination Committee' => [
            ['জয়শ্রী রানী সরকার', 'Joysree Rani Sarker', '2018-19', 'Member', '01720-561921'],
            ['জ্যোতি ভৌমিক', 'Joti Voumik', '2017-18', 'Member', '01775-774039'],
            ['মোসাঃ আশিফা ইয়াসমিন', 'Mst. Ashifa Yasmin', '2024-25', 'Support', '01933-953511'],
        ],
    ];

    /** Profession and bio for the officers, sourced from the association's own "Our goal" page. */
    private const OFFICER_DETAIL = [
        'Md. Rofikul Islam' => [
            'profession' => 'Associate Teacher, Mathematics',
            'bio' => "Currently teaching at Government Women's College, Rajshahi.",
        ],
        'Md. Mahbub Khan Murad' => [
            'profession' => 'Assistant Teacher, Mathematics',
            'bio' => 'Currently teaching at Shaheed Abul Hasnat Mohammad Kamaruzzaman Government Degree College, Rajshahi.',
        ],
        'Mst. Mafruha Mustari' => [
            'profession' => 'Teacher, Department of Mathematics, Rajshahi College',
            'bio' => 'A former student of the department and now one of its teachers; she established the contact that brought the founding committee together in January 2026.',
        ],
    ];

    /** storage/app/public/committee/<slug>.jpg, when the committee supplied a portrait. */
    private function photo(string $englishName): ?string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $englishName), '-'));

        foreach (['.jpg', '.png'] as $ext) {
            if (file_exists(storage_path("app/public/committee/{$slug}{$ext}"))) {
                return "committee/{$slug}{$ext}";
            }
        }

        return null;
    }

    public function run(): void
    {
        $order = 0;

        foreach (self::CONVENING as $m) {
            [$bn, $en, $session, $role, $roleBn, $phone] = $m;

            CommitteeMember::updateOrCreate(
                ['committee' => 'reunion_convening', 'name' => $en],
                [
                    'name_bn' => $bn,
                    'photo_path' => $this->photo($en),
                    ...(self::OFFICER_DETAIL[$en] ?? []),
                    'designation' => $role,
                    'designation_bn' => $roleBn,
                    'batch' => $session,
                    'phone' => $phone,
                    'sort_order' => $order++,
                    // The three officers are the ones the association publishes.
                    'is_featured' => in_array($role, ['Convenor', 'Member Secretary', 'Treasurer'], true),
                    'is_published' => true,
                ]
            );
        }

        $order = 0;

        foreach (self::SUB as $label => $members) {
            [$bn, $en] = explode('|', $label);

            foreach ($members as $m) {
                [$nameBn, $nameEn, $session, $role, $phone] = $m;

                CommitteeMember::updateOrCreate(
                    ['committee' => 'reunion_sub', 'name' => $nameEn],
                    [
                        'name_bn' => $nameBn,
                        'photo_path' => $this->photo($nameEn),
                        // The sub-committee is the meaningful title; role is the rank within it.
                        'designation' => $en.($role === 'Support' ? ' — Support' : ''),
                        'designation_bn' => $bn.($role === 'Support' ? ' (সহযোগিতায়)' : ''),
                        'batch' => $session,
                        'phone' => $phone,
                        'sort_order' => $order++,
                        'is_featured' => false,
                        'is_published' => true,
                    ]
                );
            }
        }
    }
}
