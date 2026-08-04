# RCMAA — Rajshahi College Mathematics Alumni Association

A Laravel 13 rebuild of [rcmaa.bd](https://rcmaa.bd), replacing the WordPress/Elementor
site with a bespoke application built around the association's reunion registration
process.

## Stack

| Layer | Choice |
| --- | --- |
| Framework | Laravel 13 (PHP 8.4) |
| Views | Blade components |
| CSS | Tailwind v4 (`@theme` tokens, no config file) |
| Motion | GSAP + ScrollTrigger, Lenis smooth scroll |
| Interactivity | Alpine.js |
| Build | Vite 8, self-hosted fonts via `laravel-vite-plugin/fonts` |
| Database | SQLite in development, MySQL/MariaDB in production |
| Tests | PHPUnit |

## Getting started

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run dev
```

Then `php artisan serve`. The seeder creates an administrator using
`RCMAA_ADMIN_EMAIL` / `RCMAA_ADMIN_PASSWORD` from `.env` — **change the password
before deploying.**

## What lives where

```
app/
  Http/Controllers/          public site
  Http/Controllers/Admin/    dashboard, registrations, generic content CMS
  Http/Requests/             RegistrationRequest — the full registration ruleset
  Models/                    Registration + seven CMS-managed content models
config/rcmaa.php             fees, payment numbers, contact details, option sets
resources/
  css/app.css                design tokens and components
  js/lib/                    motion.js, reveals.js, hero.js, split-text.js
  js/components/             Alpine: registration-form.js, gallery.js
  views/components/          layout, page-hero, field, choice-group, person-card…
  views/pages/               one file per route
  views/partials/home/       the eight home-page sections
```

### Configuration

Nearly everything the committee will want to change lives in `config/rcmaa.php`,
and every value there reads from `.env`. Registration fees, the bKash/Nagad/Rocket
numbers, the hotline and the registration open/closed switch need no code change.

## Registration

The form mirrors the association's printed *Registration Details* document part
for part — personal, academic, professional, reunion, memories, photograph,
payment — as six screens with a progress rail. Bangla sub-labels are carried
across verbatim.

- **Client side.** Alpine gates step navigation and mirrors the draft into
  `localStorage`, so a refresh mid-form loses nothing.
- **Server side.** `RegistrationRequest` is the source of truth: Bangladeshi
  mobile format, guest count reconciled against the guest rows supplied,
  transaction IDs unique per payment method, and payment rejected when it falls
  short of the computed total.
- **Priced by category, not a flat fee.** Step 1 picks one of four categories
  from the association's specification — Teacher/Former Teacher (৳2,535),
  Alumnus (৳2,535), Recent Graduate (৳1,525), Current Student (৳1,015). Only the
  first two may bring guests, at ৳3,000 each. `App\Support\RegistrationPricing`
  is the single source of truth; the form, the validator, the admin and the
  confirmation email all price through it, and each registration stores the fees
  actually applied so a later price change cannot rewrite history.
- **Payment.** Manual verification. The registrant sends money to one of the
  configured numbers and submits the transaction ID; an admin marks the record
  verified from the dashboard. No gateway credentials are involved.
- **The public directory publishes mobile numbers.** At the association's
  instruction, verified registrations show name, session, passing year,
  profession, photograph **and mobile number** so alumni can reach each other.
  Email, both addresses and blood group stay private. This is stated in the
  Privacy Policy and again on the registration form itself, so consent is
  informed rather than assumed, and `AdminTest` asserts all three stay in step.
  A removal request is honoured by clearing `mobile` or unpublishing the row.
- **Reference numbers** look like `RC26-4F9K2B` — short enough to read over the
  phone. Registrants check their status with the reference plus the mobile number
  they registered with; the pairing stops the reference alone being enumerable.

## Admin

`/admin`, gated by the `admin` middleware (`users.is_admin`). Sign in at `/login`
with the account the seeder creates from `RCMAA_ADMIN_EMAIL` / `RCMAA_ADMIN_PASSWORD`.

### Dashboard

Totals for registrations, pending verifications, verified count, accompanying
guests, money collected and money awaiting verification; a 14-day registration
sparkline; a T-shirt size tally for the merchandise order; best-represented
graduating years; and the eight most recent registrations. Quick links create an
event, notice, gallery photo or committee member in one click.

### Registrations — the daily job

The committee's routine is *verify payments*:

1. **Registrations → Pending** — everything awaiting a decision.
2. Open a record. It shows the registrant's photo, full submission, the
   **category and its fee**, the guest fee, amount due, amount paid, and a
   **balance** that turns red when short and green when over.
3. Match the transaction ID against the bKash/Nagad/Rocket statement.
4. Mark **Verified**, **Pending** or **Rejected**, optionally with a note. The
   note is shown to the registrant on `/registration-status`, so "please resend
   your TrxID" reaches them without a phone call.

Verifying stamps who did it and when. Only verified registrations reach the
public directory, so nothing unchecked is ever published.

**Finding people:** filter by status, category, passing year, degree and T-shirt
size; search by name, email, mobile, reference or transaction ID. Four category
cards at the top show a live count and money total per category and double as
one-click filters.

**CSV export** respects whatever filters are applied — so "verified teachers" or
"everyone needing an XL shirt" is two clicks and a download. UTF-8 BOM so Excel
opens the Bangla columns correctly.

> The export is built from a single `heading => value` map rather than parallel
> header and value lists. Those two drifted apart once and shifted every column
> after the third, which put the wrong figures under "Amount Paid". `AdminExportTest`
> now asserts each column by value, so it cannot happen again silently.

### Content

A generic CMS covering **Committee, Faculty, Events, Notices, Gallery, FAQs and
Sponsors** — list, create, edit, delete, publish/unpublish, reorder, with image
upload and automatic slug generation. Everything published appears on the public
site immediately. Adding a new content type is one entry in
`ContentController::types()`, not a new controller and view stack.

### Correcting a registration

`Registrations → open a record → Edit details`. The confirmation page tells
registrants to ring the helpdesk if they mistyped something, so the helpdesk has
to be able to act on it. Every field is editable; changing the category
re-derives the amount due; transaction IDs stay unique across everyone else; and
each change is appended to the admin note with who made it and when.

The same screen carries **List in the public directory**. The Privacy Policy
promises a registrant can have their entry removed on request — unticking this
honours that without touching their verified seat. `Registration::listed()` is
what the directory reads.

### Committee accounts

`Accounts` in the sidebar. The reunion is run by a sub-committee, so the seeded
login cannot be the only way in. Add accounts, grant or withhold administrator
access, and delete them. Two guards: nobody can strip their own admin rights, and
the last remaining administrator cannot be demoted or deleted.

`Your account` (bottom of the sidebar) is where an admin changes their own name,
email and password — proving the current password first. **This is how you rotate
the seeded default.**

### Messages

Enquiries from the public contact form, with read/unread state and a reply link.

### Not built

Deliberately absent, in case they matter to you: bulk verify (payments are
verified one at a time), an in-app settings screen for fees and payment numbers
(`.env` only), email notification on verify/reject (the note appears on the
registrant's status page but nothing is sent), and editing a registration's
guests (delete and re-register, or ask the registrant).

### Legacy import

`php artisan registrations:import-legacy` brings across the old WordPress
MetForm entries. Safe to re-run.

## Content from the association's specification

The association supplied a per-page content specification in Google Drive
(9 page folders, 32 files). Everything textual in it is now on the site:

| Spec section | Where it lives |
| --- | --- |
| Nav bar links (9 items, with sub-items) | `partials/header.blade.php` |
| আমাদের ঐতিহ্য — 28 historical milestones, 1873–2026 | `config/heritage.php` → `/heritage` |
| ইতিহাস · আমাদের পথচলা · লক্ষ্য (6) · উদ্দেশ্য (7) | `/about`, `/our-goal#journey`, `#aims`, `#objectives` |
| Banner title, description, buttons, countdown | Home hero |
| About, Registration, Gallery, Sponsors titles & copy | Home sections |
| Registration categories and fees | `config/rcmaa.php` → `RegistrationPricing` |
| Directory required information | `/directory` |
| Contact channels and hours | `config/rcmaa.php` → `contact_channels` |
| 4 gallery photographs | `ContentSeeder` → `/gallery` |

The specification lists the nav in Bangla. The site runs an **English UI with
Bangla alongside** — agreed before the spec arrived — so primary nav items are
English while the Bangla sits on the dropdown sub-items and throughout the mobile
drawer. Change this if the association would rather the top level were Bangla.

`Notice` is not one of the nine specified nav items, so it now lives in the
footer rather than the primary navigation; the pages and routes are unchanged.

**Empty in the specification** at the time of writing, and so not built: FAQ Page,
Login Page, Department Building Image, Open for Registration Banner, Grand Reunion
Photocard, "Top 3/4 People with Name, Session", "Top 3/4 Gallery Images", and
Sponsors details.

## Committee data

`CommitteeSeeder` holds the association's official roster: a 6-member Reunion
Convening Committee and 42 members across 11 sub-committees — 48 people in all.

The source is `Committee.pdf`, the declaration dated 28/02/2026 signed by the
Convenor and Member Secretary. Bangla names, sessions and roles are verbatim from
it. Portraits come from the committee's own Drive folders, cropped to 4:5 and
matched by name; 30 of 48 have one, and the rest fall back to an initials seal.

Every phone number and session was transcribed from a high-resolution render of
the PDF, then **cross-checked against an independent `pdftotext` extraction** —
48/48 now agree. That check earned its keep: it caught one misread digit
(Md. Nice Reza, `01780` → `01740`). Bangla ৪ (four) renders like a Latin "8" in
this document's font while ৮ (eight) renders like a "b", and both appear in that
one number. Worth re-running the same cross-check on any future roster update.

**Every member's personal mobile number is in that document.** They are stored so
the committee can work from the admin, but they are never rendered publicly —
`CommitteePrivacyTest` asserts all 47 private numbers stay off ten public pages.
The one exception is deliberate: `+880 1643-740416`, the association's designated
public contact, is the personal mobile of Rafawat Ahmed (Information & Technology,
session 2019-20). The test exempts only the numbers named in `config/rcmaa.php`.

The Convenor's and Member Secretary's messages (আহ্বায়কের বাণী / সদস্য সচিবের বাণী)
are on the Committee page in Bangla with a collapsible English translation.

## Faculty

The 12 teaching staff on `/faculty` come from the college's own official site —
`rc.gov.bd/mathematics/` and `rc.gov.bd/teachers-list/`. **Not** from the old
rcmaa.bd Teachers page, which still carries the WordPress theme's stock
placeholders ("Prof. Dr. Alex Thunder", "Dr. Bill Skarsgard"); that was verified
against the live page, not assumed.

The department's own milestones, former heads (15 of them, back to 1912) and
institutional contact details live in `config/rcmaa.php` under `department` and
are rendered on the Faculty page. Note these are the DEPARTMENT's dates — B.Sc
from 1878, Honours 1881, Masters 1893 — distinct from `college_founded` (1873).

Two things to confirm with the department:

- The college roster lists two **vacant posts** (one Associate Professor, one
  Assistant Professor). They are not seeded.
- Several names carry a second parenthetical rank, e.g. "Nadira Nazneen (15968)
  (Associate Professor)" against a DESIGNATION of "Lecturer". The designation
  column is used; the parenthetical is left alone because whether it means
  substantive post or promoted rank is ambiguous.

**Their personal mobile numbers are published on the college site and were
deliberately not imported** — the association never supplied them, and
republishing a third party's contact details is not this project's call.

A useful cross-check fell out of this: Mst. Mafruha Mustari appears on both the
college faculty roster and the RCMAA committee declaration, and the mobile number
matches in both (`01725905474` / `01725-905474`) — independent confirmation that
the committee transcription is sound.

## Hero video

`public/media/hero.mp4` is a 10-second aerial of the Rajshahi College building,
cut from the association's own campus footage — 1920×1080, 30 fps, no audio,
3.0 MB. The trailing 0.8 s is cross-blended into the opening so the loop point is
invisible; `hero-poster.jpg` is the clip's own first frame, so the still and the
video agree exactly.

The `<source>` tags are emitted only for files that exist, so dropping in a
replacement `hero.mp4` (or adding `hero.webm`) needs no template change. A WebM
was encoded and discarded — VP9 came out larger than the H.264 at equivalent
quality on this footage, so it would have cost bandwidth rather than saving it.

The scrim over the video is weighted left on desktop, where the headline sits,
and even on mobile, where the text spans the full width.

## Motion

GSAP drives everything; Lenis and ScrollTrigger share a single RAF loop so
triggers never lag the smoothed scroll position. Reveals are declarative:

```html
<div data-reveal="split">…</div>          <!-- line-by-line mask reveal -->
<div data-reveal data-reveal-stagger="0.1"><span data-reveal-item>…</span></div>
<img data-parallax="-0.06">                <!-- inside [data-parallax-scope] -->
<span data-count="1873">0</span>           <!-- counts up on scroll -->
```

`prefers-reduced-motion` disables Lenis and every reveal, and the pre-animation
hidden state is guarded by a `.js-ready` class so the site is fully readable with
JavaScript off.

## What was carried over from rcmaa.bd

The old site was migrated in full. Worth knowing before looking for anything else:
**most of its inner pages were never written.** About, Committee, Faculty, How to
Apply, Notice, Event, Gallery and all five Bangla committee pages still carried the
WordPress theme's untouched "Universite" demo content — Lorem ipsum, an address in
Jakarta, and stock portraits named "Prof. Dr. Alex Thunder". Features, Help Center,
Privacy, Terms, FAQs and Directory were empty.

Genuine content existed in three places, and all of it is now in this app:

| Source | Carried into |
| --- | --- |
| Home page | Home-page copy, verbatim |
| **Our goal** (Bangla) — history, journey, 6 aims, 7 objectives, leadership profiles | `pages/our-goal.blade.php` (bilingual), `pages/about.blade.php`, FAQs |
| **Contact** — three channels with published hours | `config/rcmaa.php` → `contact_channels`, contact and help-center pages |
| Media library | Logo, 3 committee portraits, 2 event covers, 6 gallery photographs |

Two dates are easy to confuse and are kept apart in config: `college_founded`
(1873, Rajshahi College) and `founded` (2026, the association — its seal reads
EST. 2026). The association's journey began 16 December 2025 and it was formally
constituted on 3 January 2026.

## Outstanding items

1. **Close the old registration form at cut-over.** `rcmaa.bd/student-form/` is a
   live MetForm build of this same form and still accepts submissions. Once this
   site goes live it must be taken down or redirected, or people will keep
   registering into a system nobody is reading.

   Its two existing entries have been imported (`php artisan registrations:import-legacy`,
   re-runnable safely). Both are `pending` and carry an admin note listing what
   the legacy form never captured — **no name, session, address, transaction ID
   or amount**. Entry #12 was submitted by the `admircmaad` admin account and
   looks like a test. Confirm both with the registrants before verifying; neither
   can reach the public directory until someone does.

2. **Faculty portraits and verification.** The 12 teaching staff on `/faculty`
   come from the college's own site (see "Faculty" below), not from the
   association, and none has a photograph. Ask the department to confirm the
   roster and supply portraits, then add them from Admin → Faculty.
3. **13 portraits could not be matched to a person.** Google Drive truncated
   those filenames at the "Md." / "Mst." honorific (the trailing dot was read as
   a file extension), leaving `Md.png`, `Mst(1).png` and so on. Five were
   resolved by elimination — where a sub-committee had exactly one member left
   unphotographed, the match is certain. Six remain ambiguous:
   two in Information & Technology, two in Registration & Data, one in Food &
   Hospitality, plus one extra in Communication. **Fix:** rename those files in
   Drive to `Full Name_Role.png` and re-run the importer.
4. **Two roster discrepancies to confirm.** The Drive photo folders contain
   `Shakila Akter Riya` (Communication) and `Avishake Karmaker` (filed under
   Photography), neither of whom appears in the 28/02/2026 declaration — while
   the declaration lists `অভিষেক কুমার` under Souvenir. Either the committee has
   changed since February, or a photo is mis-filed.
5. **English name spellings.** Where the committee supplied an English spelling
   in a photo filename it is used verbatim. The remainder are transliterations
   from the Bangla and should be checked by a Bangla reader — the authoritative
   Bangla is stored alongside in `name_bn`.
6. **Payment accounts are placeholders.** `RCMAA_BKASH_NUMBER`, `RCMAA_NAGAD_NUMBER`,
   `RCMAA_ROCKET_NUMBER` and `RCMAA_BANK_ACCOUNT` ship as obvious placeholders
   rather than plausible numbers, because an account that merely *looks* right
   would quietly send registrants' money to the wrong person. The payment step
   renders any unset method in red and refuses to present it as payable, and
   `PaymentConfigTest` guards that. **Set the committee's real accounts in `.env`
   before opening registration.**
7. **Legal pages.** Privacy and Terms are drafted to describe what this application
   actually does with data. Have the committee review the refund clause against its
   own policy before launch.

## Production notes

- Switch `DB_CONNECTION` to `mysql` and run `php artisan migrate --force`.
- `php artisan storage:link` is required — uploads are served from the public disk.
- Uploaded images are addressed **root-relative** (`/storage/...`), so they survive
  a stale `APP_URL`, an http→https move or a www/apex switch. Laravel's default
  derives them from `APP_URL`, which silently breaks every photograph the moment
  those disagree; `config/filesystems.php` overrides it and `AssetUrlTest` guards
  it. Set `ASSET_URL` only to move uploads to a separate origin such as a CDN.
- Configure real `MAIL_*` credentials; registration confirmations are sent inline
  and a send failure is logged rather than losing the registration.
- `npm run build`, then cache config, routes and views.
- The registration route is rate-limited to 10 submissions per hour per IP and the
  contact form to 6 per minute.

## Tests

```bash
php artisan test
```

Covers the whole registration flow (validation, guests, fees, duplicate
transactions, photo upload, honeypot, closed-registration), every public route
with and without content, admin authorisation, payment verification, CSV export,
the CMS create/edit/upload cycle, and the guarantee that unverified registrations
and private contact details never reach the public directory.
