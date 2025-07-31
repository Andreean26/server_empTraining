<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING PDF UPLOADS IN DATABASE ===\n\n";

$trainings = App\Models\Training::whereNotNull('pdf_material')->get(['id', 'title', 'pdf_material']);

echo "Trainings with PDF materials:\n";
foreach ($trainings as $training) {
    echo "ID: {$training->id}\n";
    echo "Title: {$training->title}\n";
    echo "PDF Path: {$training->pdf_material}\n";
    
    $fullPath = storage_path('app/public/' . $training->pdf_material);
    echo "Full Path: {$fullPath}\n";
    echo "File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    if (file_exists($fullPath)) {
        echo "File Size: " . filesize($fullPath) . " bytes\n";
    }
    echo "---\n";
}

echo "\nTotal trainings with PDF: " . $trainings->count() . "\n";
