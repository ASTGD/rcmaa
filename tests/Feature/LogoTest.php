<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The logo takes its dimensions from a `size` prop. Naming that prop `class`
     * makes Blade bind the caller's HTML class attribute to it instead, dropping
     * the sizing utilities and rendering the seal at its natural 512px.
     */
    #[Test]
    public function the_logo_is_always_sized_even_when_the_caller_passes_a_class(): void
    {
        // The footer passes class="mb-5" and relies on the default size.
        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match_all('/<img[^>]*media\/logo\.png[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches[0], 'The logo image should be rendered.');

        foreach ($matches[0] as $tag) {
            $this->assertMatchesRegularExpression(
                '/class="[^"]*\bh-\d+\b[^"]*\bw-\d+\b/',
                $tag,
                "Logo rendered without sizing utilities: {$tag}"
            );
        }
    }
}
