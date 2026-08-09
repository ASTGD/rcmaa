<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The readable text of a PDF response.
     *
     * The registration slip, the payment slip and the entry pass are all
     * downloads, so asserting only on the content-type would prove the file
     * exists and nothing about what is printed on it. dompdf writes its text as
     * `[(...)] TJ` inside FlateDecode streams; this pulls it back out so a test
     * can check the reference number and the name really did reach the page.
     */
    protected function pdfText(string $pdf): string
    {
        $text = '';

        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $streams);

        foreach ($streams[1] ?? [] as $stream) {
            $decoded = @gzuncompress($stream);

            if ($decoded === false) {
                $decoded = @gzinflate($stream);
            }

            if ($decoded === false) {
                $decoded = $stream;
            }

            // The ] before the operator is why a naive \)\s*TJ finds nothing.
            if (preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)[\s\]]*T[Jj]/', $decoded, $chunks)) {
                foreach ($chunks[1] as $chunk) {
                    $chunk = stripcslashes($chunk);

                    // A document carrying an embedded Unicode font writes its
                    // text two bytes per character; the ASCII core fonts write
                    // one. Both turn up in the same file.
                    if (str_contains($chunk, "\0")) {
                        $chunk = mb_convert_encoding($chunk, 'UTF-8', 'UTF-16BE');
                    }

                    $text .= $chunk.' ';
                }
            }
        }

        return $text;
    }
}
