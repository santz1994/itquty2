<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AssetsImportErrorsDownloadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function download_errors_requires_import_summary_in_session()
    {
        $user = \App\User::factory()->create();
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }

        $this->actingAs($user)
             ->get(route('assets.import-errors-download'))
             ->assertRedirect(route('assets.import-form'));
    }

    /** @test */
    public function download_errors_returns_csv_when_summary_present()
    {
        $user = \App\User::factory()->create();
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }

        $summary = [
            'created' => 0,
            'errors' => [
                ['row' => 2, 'errors' => ['Model not found: Foo'], 'data' => ['asset tag' => 'ASSET1']],
                ['row' => 3, 'error' => 'Database error', 'data' => ['asset tag' => 'ASSET2']],
            ]
        ];

        $response = $this->actingAs($user)
                         ->withSession(['import_summary' => $summary])
                         ->get(route('assets.import-errors-download'));

    $response->assertStatus(200);
    // Content-Type may include charset, assert it contains text/csv
    $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    $response->assertHeader('Content-Disposition');

    $content = $response->streamedContent();
        $this->assertStringContainsString('row,messages,data', trim($content));
        $this->assertStringContainsString('Model not found: Foo', $content);
        $this->assertStringContainsString('Database error', $content);
    }
}
