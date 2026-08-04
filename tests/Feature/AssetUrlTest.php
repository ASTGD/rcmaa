<?php

namespace Tests\Feature;

use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uploaded images must be addressed relative to the site root. Deriving them
     * from APP_URL silently breaks every photograph whenever the site is served
     * from a different host, scheme or subdomain than APP_URL happens to say.
     */
    #[Test]
    public function public_disk_urls_are_root_relative(): void
    {
        config(['app.url' => 'https://some-stale-value.example']);

        $this->assertSame('/storage/gallery/photo.jpg', Storage::disk('public')->url('gallery/photo.jpg'));
    }

    #[Test]
    public function uploaded_images_resolve_against_the_requesting_host(): void
    {
        config(['app.url' => 'https://some-stale-value.example']);

        $this->seed(ContentSeeder::class);

        $this->get(route('committee'))
            ->assertOk()
            ->assertSee('/storage/committee/', false)
            ->assertDontSee('some-stale-value.example');
    }
}
