<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Notice;
use App\Models\Sponsor;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * One controller for every simple content type.
 *
 * Each type declares its model, validation rules, upload field and list
 * columns in TYPES; the shared CRUD below and a single set of Blade views do
 * the rest. Adding a new content type is one array entry, not a new stack.
 */
class ContentController extends Controller
{
    public function index(string $type): View
    {
        $config = $this->config($type);
        $query = $config['model']::query();

        $records = isset($config['order'])
            ? $config['order']($query)->paginate(30)
            : $query->orderBy('sort_order')->orderByDesc('id')->paginate(30);

        return view('admin.content.index', [
            'title' => $config['plural'],
            'type' => $type,
            'config' => $config,
            'records' => $records,
        ]);
    }

    public function create(string $type): View
    {
        $config = $this->config($type);

        return view('admin.content.form', [
            'title' => 'New '.$config['singular'],
            'type' => $type,
            'config' => $config,
            'record' => new $config['model'],
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $config = $this->config($type);
        $data = $this->validated($request, $config);

        $config['model']::create($data);

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $config['singular'].' created.');
    }

    public function edit(string $type, int $id): View
    {
        $config = $this->config($type);

        return view('admin.content.form', [
            'title' => 'Edit '.$config['singular'],
            'type' => $type,
            'config' => $config,
            'record' => $config['model']::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $config = $this->config($type);
        $record = $config['model']::findOrFail($id);
        $data = $this->validated($request, $config, $record);

        // Keep the existing file when no replacement was uploaded.
        if (isset($config['upload']) && ! $request->hasFile('upload')) {
            unset($data[$config['upload']]);
        }

        $record->update($data);

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $config['singular'].' updated.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $config = $this->config($type);
        $record = $config['model']::findOrFail($id);

        if (isset($config['upload']) && $record->{$config['upload']}) {
            Storage::disk('public')->delete($record->{$config['upload']});
        }

        $record->delete();

        return back()->with('status', $config['singular'].' deleted.');
    }

    // ---------------------------------------------------------------------

    private function validated(Request $request, array $config, $record = null): array
    {
        $rules = $config['rules']($record);
        $data = $request->validate($rules);

        if (isset($config['slug']) && ! empty($data[$config['slug']['from']])) {
            $data[$config['slug']['field']] = $this->uniqueSlug(
                $config['model'],
                $config['slug']['field'],
                $data[$config['slug']['from']],
                $record?->id
            );
        }

        if (isset($config['upload']) && $request->hasFile('upload')) {
            if ($record?->{$config['upload']}) {
                Storage::disk('public')->delete($record->{$config['upload']});
            }
            $data[$config['upload']] = $request->file('upload')->store($config['upload_dir'], 'public');
        }

        foreach ($config['booleans'] ?? [] as $field) {
            $data[$field] = $request->boolean($field);
        }

        return $data;
    }

    private function uniqueSlug(string $model, string $field, string $source, ?int $ignoreId): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while ($model::where($field, $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function config(string $type): array
    {
        abort_unless(isset($this->types()[$type]), 404);

        return $this->types()[$type];
    }

    public static function menu(): array
    {
        return collect((new self)->types())
            ->map(fn ($c, $key) => ['key' => $key, 'label' => $c['plural'], 'icon' => $c['icon']])
            ->values()
            ->all();
    }

    private function types(): array
    {
        return [
            'committee' => [
                'model' => CommitteeMember::class,
                'singular' => 'Committee member',
                'plural' => 'Committee',
                'icon' => 'users',
                'upload' => 'photo_path',
                'upload_dir' => 'committee',
                'booleans' => ['is_featured', 'is_published'],
                'columns' => ['name' => 'Name', 'designation' => 'Designation', 'committee_label' => 'Committee'],
                'fields' => [
                    'committee' => ['type' => 'select', 'label' => 'Committee', 'options' => 'committees'],
                    'name' => ['type' => 'text', 'label' => 'Name (English)'],
                    'name_bn' => ['type' => 'text', 'label' => 'Name (Bangla)', 'bangla' => true],
                    'designation' => ['type' => 'text', 'label' => 'Designation'],
                    'designation_bn' => ['type' => 'text', 'label' => 'Designation (Bangla)', 'bangla' => true],
                    'batch' => ['type' => 'text', 'label' => 'Session / Batch'],
                    'profession' => ['type' => 'text', 'label' => 'Profession'],
                    'phone' => ['type' => 'text', 'label' => 'Phone'],
                    'email' => ['type' => 'email', 'label' => 'Email'],
                    'bio' => ['type' => 'textarea', 'label' => 'Short bio'],
                    'upload' => ['type' => 'file', 'label' => 'Portrait', 'target' => 'photo_path'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_featured' => ['type' => 'toggle', 'label' => 'Show on home page'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'committee' => ['required', Rule::in(array_keys(CommitteeMember::COMMITTEES))],
                    'name' => ['required', 'string', 'max:120'],
                    'name_bn' => ['nullable', 'string', 'max:120'],
                    'designation' => ['required', 'string', 'max:120'],
                    'designation_bn' => ['nullable', 'string', 'max:120'],
                    'batch' => ['nullable', 'string', 'max:60'],
                    'profession' => ['nullable', 'string', 'max:120'],
                    'phone' => ['nullable', 'string', 'max:32'],
                    'email' => ['nullable', 'email', 'max:190'],
                    'bio' => ['nullable', 'string', 'max:2000'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'upload' => ['nullable', 'image', 'max:2048'],
                ],
            ],

            'teachers' => [
                'model' => Teacher::class,
                'singular' => 'Faculty member',
                'plural' => 'Faculty',
                'icon' => 'book',
                'upload' => 'photo_path',
                'upload_dir' => 'teachers',
                'booleans' => ['is_head', 'is_published'],
                'columns' => ['name' => 'Name', 'designation' => 'Designation', 'specialisation' => 'Specialisation'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name'],
                    'designation' => ['type' => 'text', 'label' => 'Designation'],
                    'qualification' => ['type' => 'text', 'label' => 'Qualification'],
                    'specialisation' => ['type' => 'text', 'label' => 'Specialisation'],
                    'email' => ['type' => 'email', 'label' => 'Email'],
                    'phone' => ['type' => 'text', 'label' => 'Phone'],
                    'bio' => ['type' => 'textarea', 'label' => 'Short bio'],
                    'upload' => ['type' => 'file', 'label' => 'Portrait', 'target' => 'photo_path'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_head' => ['type' => 'toggle', 'label' => 'Head of department'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'name' => ['required', 'string', 'max:120'],
                    'designation' => ['required', 'string', 'max:120'],
                    'qualification' => ['nullable', 'string', 'max:180'],
                    'specialisation' => ['nullable', 'string', 'max:180'],
                    'email' => ['nullable', 'email', 'max:190'],
                    'phone' => ['nullable', 'string', 'max:32'],
                    'bio' => ['nullable', 'string', 'max:2000'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'upload' => ['nullable', 'image', 'max:2048'],
                ],
            ],

            'events' => [
                'model' => Event::class,
                'singular' => 'Event',
                'plural' => 'Events',
                'icon' => 'calendar',
                'upload' => 'cover_path',
                'upload_dir' => 'events',
                'booleans' => ['registration_open', 'is_featured', 'is_published'],
                'slug' => ['field' => 'slug', 'from' => 'title'],
                'order' => fn ($q) => $q->orderByDesc('starts_on'),
                'columns' => ['title' => 'Title', 'starts_on' => 'Date', 'venue' => 'Venue'],
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title'],
                    'starts_on' => ['type' => 'date', 'label' => 'Start date'],
                    'ends_on' => ['type' => 'date', 'label' => 'End date'],
                    'registration_deadline' => ['type' => 'date', 'label' => 'Registration deadline', 'hint' => 'Countdown timer will count down to this date.'],
                    'start_time' => ['type' => 'text', 'label' => 'Time', 'hint' => 'e.g. 9:00 AM — 6:00 PM'],
                    'venue' => ['type' => 'text', 'label' => 'Venue'],
                    'excerpt' => ['type' => 'textarea', 'label' => 'Summary'],
                    'body' => ['type' => 'richtext', 'label' => 'Full description'],
                    'upload' => ['type' => 'file', 'label' => 'Cover image', 'target' => 'cover_path'],
                    'registration_open' => ['type' => 'toggle', 'label' => 'Registration open'],
                    'is_featured' => ['type' => 'toggle', 'label' => 'Featured'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'title' => ['required', 'string', 'max:180'],
                    'starts_on' => ['required', 'date'],
                    'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
                    'registration_deadline' => ['nullable', 'date', 'before_or_equal:starts_on'],
                    'start_time' => ['nullable', 'string', 'max:60'],
                    'venue' => ['nullable', 'string', 'max:180'],
                    'excerpt' => ['nullable', 'string', 'max:500'],
                    'body' => ['nullable', 'string', 'max:20000'],
                    'upload' => ['nullable', 'image', 'max:4096'],
                ],
            ],

            'notices' => [
                'model' => Notice::class,
                'singular' => 'Notice',
                'plural' => 'Notices',
                'icon' => 'bell',
                'upload' => 'attachment_path',
                'upload_dir' => 'notices',
                'booleans' => ['is_pinned', 'is_published'],
                'slug' => ['field' => 'slug', 'from' => 'title'],
                'order' => fn ($q) => $q->orderByDesc('published_on'),
                'columns' => ['title' => 'Title', 'published_on' => 'Published'],
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title'],
                    'published_on' => ['type' => 'date', 'label' => 'Publish date'],
                    'excerpt' => ['type' => 'textarea', 'label' => 'Summary'],
                    'body' => ['type' => 'richtext', 'label' => 'Notice body'],
                    'upload' => ['type' => 'file', 'label' => 'Attachment (PDF/image)', 'target' => 'attachment_path'],
                    'is_pinned' => ['type' => 'toggle', 'label' => 'Pin to top'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'title' => ['required', 'string', 'max:180'],
                    'published_on' => ['required', 'date'],
                    'excerpt' => ['nullable', 'string', 'max:500'],
                    'body' => ['nullable', 'string', 'max:20000'],
                    'upload' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:8192'],
                ],
            ],

            'gallery' => [
                'model' => GalleryItem::class,
                'singular' => 'Photo',
                'plural' => 'Gallery',
                'icon' => 'camera',
                'upload' => 'image_path',
                'upload_dir' => 'gallery',
                'booleans' => ['is_featured', 'is_published'],
                'columns' => ['title' => 'Title', 'category_label' => 'Category'],
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title'],
                    'category' => ['type' => 'select', 'label' => 'Category', 'options' => 'gallery_categories'],
                    'caption' => ['type' => 'textarea', 'label' => 'Caption'],
                    'upload' => ['type' => 'file', 'label' => 'Image', 'target' => 'image_path'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_featured' => ['type' => 'toggle', 'label' => 'Show on home page'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'title' => ['required', 'string', 'max:180'],
                    'category' => ['required', Rule::in(array_keys(GalleryItem::CATEGORIES))],
                    'caption' => ['nullable', 'string', 'max:500'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'upload' => [$record?->exists ? 'nullable' : 'required', 'image', 'max:4096'],
                ],
            ],

            'faqs' => [
                'model' => Faq::class,
                'singular' => 'FAQ',
                'plural' => 'FAQs',
                'icon' => 'alert',
                'booleans' => ['is_published'],
                'columns' => ['question' => 'Question', 'category_label' => 'Category'],
                'fields' => [
                    'question' => ['type' => 'text', 'label' => 'Question'],
                    'category' => ['type' => 'select', 'label' => 'Category', 'options' => 'faq_categories'],
                    'answer' => ['type' => 'textarea', 'label' => 'Answer'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'question' => ['required', 'string', 'max:250'],
                    'category' => ['required', Rule::in(array_keys(Faq::CATEGORIES))],
                    'answer' => ['required', 'string', 'max:4000'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                ],
            ],

            'sponsors' => [
                'model' => Sponsor::class,
                'singular' => 'Sponsor',
                'plural' => 'Sponsors',
                'icon' => 'shield',
                'upload' => 'logo_path',
                'upload_dir' => 'sponsors',
                'booleans' => ['is_published'],
                'columns' => ['name' => 'Name', 'tier_label' => 'Tier'],
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Name'],
                    'tier' => ['type' => 'select', 'label' => 'Tier', 'options' => 'sponsor_tiers'],
                    'website' => ['type' => 'text', 'label' => 'Website'],
                    'upload' => ['type' => 'file', 'label' => 'Logo', 'target' => 'logo_path'],
                    'sort_order' => ['type' => 'number', 'label' => 'Sort order'],
                    'is_published' => ['type' => 'toggle', 'label' => 'Published'],
                ],
                'rules' => fn ($record) => [
                    'name' => ['required', 'string', 'max:180'],
                    'tier' => ['required', Rule::in(array_keys(Sponsor::TIERS))],
                    'website' => ['nullable', 'url', 'max:250'],
                    'sort_order' => ['nullable', 'integer', 'min:0'],
                    'upload' => ['nullable', 'image', 'max:2048'],
                ],
            ],
        ];
    }
}
