<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Tests\TestCase;

class UnduhUnitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Fake storage publik
        Storage::fake('public');

        // Definisikan route dummy saat testing aja
        Route::get('/unduh/laporan/{filename}', function ($filename) {
            $filePath = "laporan/{$filename}";

            if (!Storage::disk('public')->exists($filePath)) {
                abort(404);
            }

            $file = Storage::disk('public')->get($filePath);
            return Response::make($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);
        });
    }

    public function test_it_can_download_existing_file()
    {
        $filePath = 'laporan/test-file.pdf';
        $fileContent = 'Isi file dummy';

        Storage::disk('public')->put($filePath, $fileContent);

        $response = $this->get("/unduh/laporan/test-file.pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=test-file.pdf');
        $response->assertSee($fileContent);
    }

    public function test_it_returns_404_when_file_not_found()
    {
        $response = $this->get('/unduh/laporan/file-tidak-ada.pdf');

        $response->assertStatus(404);
    }
}
