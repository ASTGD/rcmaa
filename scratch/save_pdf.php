<?php

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Registration;
use Mpdf\Mpdf;

$member = Registration::where('reference', 'RC26-HBWHJW')->first();
if (!$member) {
    echo "Member not found\n";
    exit(1);
}

$html = view('pdf.registration-slip', [
    'r' => $member,
    'logo' => public_path('media/logo.png'),
    'photo' => $member->photo_path ? Illuminate\Support\Facades\Storage::disk('public')->path($member->photo_path) : null,
])->render();

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 14,
    'margin_right' => 14,
    'margin_top' => 15,
    'margin_bottom' => 13,
    'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [
        storage_path('fonts')
    ]),
    'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
        'notosansbengali' => [
            'R' => 'SolaimanLipi.ttf',
            'B' => 'SolaimanLipi.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ]
    ],
    'default_font' => 'notosansbengali'
]);

$mpdf->WriteHTML($html);
$pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
file_put_contents(__DIR__ . '/test_slip.pdf', $pdfContent);

echo "PDF generated successfully at scratch/test_slip.pdf\n";
