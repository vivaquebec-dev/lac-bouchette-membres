<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_active_member_can_download_a_document_with_the_expected_filename_and_content_type(): void
    {
        Storage::fake('documents');
        Storage::disk('documents')->put('stored-document.PDF', 'PDF contents');

        $document = Document::query()->create([
            'title' => 'Rapport annuel',
            'category' => DocumentCategory::FinancialReport,
            'file_path' => 'stored-document.PDF',
            'original_filename' => 'Rapport annuel.PDF',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('documents.download', $document));

        $response
            ->assertDownload('Rapport annuel.pdf')
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
