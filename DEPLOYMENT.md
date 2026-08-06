# Deployment

Two audiences: whoever is **checking the build locally** before it ships, and
whoever is **putting it on the server**. Do them in that order.

---

## Part 1 — Check it on your own machine

Everything the site needs is in this repository apart from the two things that
cannot be committed: `.env` (secrets) and `vendor/` + `node_modules/`
(dependencies). The commands below rebuild all of it.

```bash
git clone https://github.com/ASTGD/rcmaa.git
cd rcmaa

composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

npm run build
php artisan serve
```

Open <http://127.0.0.1:8000>.

**Sign in to the admin** at `/login` with the values from `.env`:
`RCMAA_ADMIN_EMAIL` (default `admin@rcmaa.bd`) and `RCMAA_ADMIN_PASSWORD`
(default `change-me`). If you change `RCMAA_ADMIN_PASSWORD`, re-run
`php artisan db:seed` to apply it.

### What you should see

| Page | Expect |
| --- | --- |
| `/` | Video hero, countdown, "Join the Association" and "View Directory" |
| `/committee` | 48 members with portraits — 6 under Convening Committee, 42 under Reunion Sub-Committee |
| `/gallery` | 10 images |
| `/heritage` | 28 milestones, 1873–2026 |
| `/register` | 7-step form, category pricing, session dropdown |
| `/directory` | Empty until somebody registers — this is correct |
| `/faqs` | 14 questions |

**Advisory Committee and Batch Representatives are empty** — the association has
not supplied those lists yet. Both pages render a "No members" state rather than
breaking. Add them through `/admin` when the names arrive, or ask the client to
send them.

The directory and the home page's "Recently joined" panel are also **empty on a
fresh install**. Both are built from verified registrations, and there are none. To see
them populated, register through the form, then verify the payment in
`/admin/registrations`.

### Run the tests

```bash
php artisan test
./vendor/bin/pint --test
```

175 tests should pass. If any fail, stop and report before deploying.

---

## Part 2 — Put it on the server

### Blocking prerequisites

The application is finished, but **it will not function correctly until these are
set**. Do not skip them.

| Setting | Must become | Why |
| --- | --- | --- |
| `RCMAA_BKASH_NUMBER` | already set to `01400366369` | The association's bKash **Merchant** account. Confirm it is still correct before launch |
| `MAIL_MAILER` | `smtp` (with host/user/pass) | On `log`, no email leaves the server: no confirmations, and the alumni portal's magic link never arrives, so that whole area is unusable |
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | Otherwise stack traces with database credentials are shown publicly on any error |
| `APP_URL` | `https://rcmaa.bd` | Magic links and email URLs are built from this; on `http://localhost` they point nowhere |
| `MAIL_FROM_ADDRESS` | a real address on the domain | |
| `RCMAA_ADMIN_PASSWORD` | something strong | |

bKash is the **only** payment method — the association confirmed on 4 August 2026
that Nagad, Rocket and bank transfer are not used, and they have been removed.
Because it is a **Merchant** account, the form, FAQ and help pages all tell people
to use the bKash app's "Payment" option rather than "Send Money"; money sent the
wrong way does not reach a Merchant account correctly.

### Requirements

PHP 8.4 with `pdo_mysql`, `mbstring`, `gd` (or `imagick`), `fileinfo`, `zip`;
Composer; Node 20+ for the asset build; MySQL 8 / MariaDB 10.6+.

Uploads are capped at 4 MB (payment receipts), so PHP needs at least
`upload_max_filesize = 8M` and `post_max_size = 16M`.

### Steps

```bash
git clone https://github.com/ASTGD/rcmaa.git
cd rcmaa

composer install --no-dev --optimize-autoloader
npm ci && npm run build          # build assets, then Node is no longer needed

cp .env.example .env
php artisan key:generate
# ...now edit .env per the table above, and set DB_* for MySQL

php artisan migrate --force --seed
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Point the web root at `public/`, never at the project root.

Ownership and permissions:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Database

`php artisan migrate --seed` builds everything: schema, 48 committee members, 28
heritage milestones, events, gallery, notices, FAQs and the admin user. That is
the canonical path — you do not need the dump.

`database/dumps/rcmaa-seed.sql` is the same content as a portable SQL file, kept
as a reference and a safety net. It contains **no registrations and no user
accounts**. Only use it if you specifically want to inspect or restore content
without running the seeders.

### Images

Committee portraits, gallery and event images are committed under
`storage/app/public/`, and `php artisan storage:link` exposes them at `/storage`.
If images 404 after deploying, the symlink is missing or the web root is wrong.

`storage/app/public/registrations/` holds registrant photos and payment receipts.
It is **deliberately excluded from git** — personal data. It is created on first
upload; just make sure the directory is writable.

**Replacing a photo in place is safe.** Media URLs carry the file's modification
time (`?v=…`, see `Publishable::diskUrl`), so keeping the filename — same person,
better crop — still busts every visitor's cache. Before that existed, two
re-cropped portraits deployed correctly and the site went on showing the old
ones. If a replacement does not appear, check the `?v=` actually changed.

**The cards crop to 4:5, so framing decides how big a face lands.** A wide
environmental shot centre-crops to a tiny head next to a tight headshot, however
many pixels it has. Aim for roughly 800×1000 head-and-shoulders; anything under
about 800px wide is upscaled into the card and looks soft. Several portraits on
`/committee` are still well under that and want better originals.

---

## After deploying — check these before announcing

1. `/register` — the payment step shows the **real bKash number**, with no red
   "Not configured" warning.
2. Complete one real registration. The confirmation email must **arrive in an
   inbox**, not the log.
3. `/my` — request a link with that email; the magic link must arrive and open.
4. `/admin/registrations` — the registration is listed; verify it; the receipt
   thumbnail opens.
5. `/registration-status` — look it up by reference + mobile; it reads "Verified".
6. `/directory` — the person now appears under their batch heading.
7. Delete that test registration from the admin before announcing.

---

## Things worth knowing

**Registration is priced by category** and only teachers and alumni may bring
guests. All of it lives in `config/rcmaa.php`; `app/Support/RegistrationPricing.php`
is the single source of truth for money. Fees are snapshotted onto each
registration, so changing a price later never rewrites what somebody was charged.

**Sessions are a fixed dropdown** (`2025-26` back to `1950-51`), because the
directory groups people by batch and free text produced `2008-09`, `2008-2009`
and `২০০৮-০৯` for one cohort. The range is two constants in
`config/rcmaa.php` (`session_newest`, `session_oldest`).

**The alumni portal has no passwords.** Registrants get a signed link valid for
one hour, which lets them correct their details, print an entry pass, attach a
payment receipt and opt out of the public directory. It depends entirely on
working email.

**Phone numbers are published** in the alumni directory at the association's
instruction. Registrants can withdraw from their portal, and committee members'
private numbers are never published.

---

## The live deployment (panel2.firevps.net)

Deployed 4 August 2026 to **https://rcmalumni.astgd.com**, a CyberPanel site on
`panel2.firevps.net` (51.79.149.130), a shared host running 57 sites. Everything
below is confined to this one site.

| | |
| --- | --- |
| App root | `/home/rcmalumni.astgd.com/rcmaa` |
| Web root | `/home/rcmalumni.astgd.com/public_html` → symlink to `rcmaa/public` |
| PHP | `/usr/local/lsws/lsphp84/bin/php` (8.4.20) |
| Database | `rcmal8475_rcmaa` (MariaDB 10.3) |
| Server | LiteSpeed Enterprise 6.3.5 — `.htaccess` is honoured |
| Repo access | read-only deploy key at `/home/rcmalumni.astgd.com/.ssh/rcmaa_deploy` |

### Redeploying

```bash
cd /home/rcmalumni.astgd.com/rcmaa      # the app root, not public_html
sudo -u rcmal8475 env HOME=/home/rcmalumni.astgd.com \
  GIT_SSH_COMMAND='ssh -i /home/rcmalumni.astgd.com/.ssh/rcmaa_deploy -o IdentitiesOnly=yes' \
  git pull --ff-only

# No npm step: public/build is committed. Build assets on your own machine
# (`npm run build`) and commit them with the change.
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php /usr/bin/composer install --no-dev --optimize-autoloader
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php artisan migrate --force
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php artisan optimize:clear
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php artisan config:cache
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php artisan route:cache
sudo -u rcmal8475 /usr/local/lsws/lsphp84/bin/php artisan view:cache
```

### Five things that will bite you

**The deploy key is not wired into ssh.** There is no `~/.ssh/config` for the
site user, so a bare `sudo -u rcmal8475 git pull` offers no key and fails with
`Permission denied (publickey)` — hence the `GIT_SSH_COMMAND` above. Running it
as root instead fails differently, on git's `dubious ownership` guard. Writing
the ssh config once would let the short command work; it has not been done.


**Work in `rcmaa/`, not `public_html/`.** `public_html` is a symlink to
`rcmaa/public`. Running npm or git through the symlink half-works — npm walks up
and finds the real `package.json`, but a relative `git checkout -- <file>` does
not resolve and the deploy script stops midway, leaving the caches stale.

**Permissions.** LiteSpeed runs as `nobody`. The document root must stay group
`nogroup` and traversable — `chown -R rcmal8475:rcmal8475` on it makes every page
404, because the web server can no longer descend into the document root. It is
currently `711 rcmal8475:nogroup`. The app files inside are owned by the site
user; only PHP (via suexec) needs to read them.

**Do not build assets on the server.** `public/build` is committed for this
reason. Three things stack up against building there: the host has no outbound
network, so `vite build` dies in the `laravel:fonts` plugin fetching webfonts
from Bunny (`ETIMEDOUT`); Node 22 exists only in root's nvm, the site user has
Node 10; and `npm ci` refuses to run at all, because `package-lock.json` was
generated on macOS and omits the Linux-only optional packages (`@emnapi/*`).

The first build after launch only worked because the font plugin's download
cache — `node_modules/.cache/laravel-vite-plugin/fonts` — was still warm.
Deleting `node_modules` afterwards, as the old instructions here said to,
destroyed it, and the next build failed. Build on your own machine and commit
`public/build` with the change.

If you ever do need to build on the host, restore that cache directory first,
or give the box outbound HTTPS.

**The SSL certificate.** CyberPanel issued a Let's Encrypt **staging**
certificate when the site was created, which no browser trusts. It was replaced
with a real one via acme.sh:

```bash
/root/.acme.sh/acme.sh --issue -d rcmalumni.astgd.com -d www.rcmalumni.astgd.com \
  -w /usr/local/lsws/Example/html --server letsencrypt --ecc --force
/root/.acme.sh/acme.sh --install-cert -d rcmalumni.astgd.com --ecc \
  --key-file /etc/letsencrypt/live/rcmalumni.astgd.com/privkey.pem \
  --fullchain-file /etc/letsencrypt/live/rcmalumni.astgd.com/fullchain.pem \
  --reloadcmd "/usr/local/lsws/bin/lswsctrl restart"
```

acme.sh renews it automatically. If the site is ever recreated through the
CyberPanel UI, check the issuer again — a staging cert says
`CN = (STAGING) ...`.

### The vhost

`/usr/local/lsws/conf/vhosts/rcmalumni.astgd.com/vhost.conf` was edited to point
`DocumentRoot` at `public_html/public` and to use the php84 handler instead of
php83. A backup of the original is at `/root/vhost.rcmalumni.bak.*`. CyberPanel
can rewrite this file if the site is modified through the panel — check the
document root if the site suddenly 404s.
