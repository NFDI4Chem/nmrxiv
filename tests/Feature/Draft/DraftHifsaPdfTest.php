<?php

namespace Tests\Feature\Draft;

use App\Actions\Draft\ProcessDraft;
use App\Jobs\ArchiveStudy;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use ReflectionMethod;
use Tests\TestCase;

class DraftHifsaPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;
        $this->draft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
        $this->project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'draft_id' => $this->draft->id,
        ]);
    }

    private function makeStudyWithHifsaSibling(): array
    {
        $sampleRoot = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'name' => 'Compound',
        ]);

        $study = Study::factory()->create([
            'name' => 'Compound',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'is_public' => false,
        ]);

        $studyFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $sampleRoot->id,
            'level' => 1,
            'name' => 'raw',
            'model_type' => 'study',
            'study_id' => $study->id,
        ]);
        $study->update(['fs_id' => $studyFolder->id]);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);

        $hifsaFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $sampleRoot->id,
            'level' => 1,
            'name' => 'hifsa',
            'instrument_type' => 'hifsa',
        ]);

        $pdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'report.pdf',
            'path' => '/drafts/compound/hifsa/report.pdf',
        ]);

        return [$study, $pdf, $hifsaFolder];
    }

    /**
     * Build a real zip archive containing a Cosmic Truth analysis CSV (and
     * optional REF CSVs) and return its binary contents.
     *
     * @param  array<string, string>  $files
     */
    private function makeExportZipContents(array $files = []): string
    {
        if ($files === []) {
            $files = [
                'analysis.csv' => $this->sampleAnalysisCsv(),
                'EXTRA/compound_REF.csv' => $this->sampleRefCsv(),
            ];
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'hifsa_export_');
        $this->assertNotFalse($tempPath);

        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($tempPath, \ZipArchive::OVERWRITE) === true);

        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

        $zip->close();

        $contents = file_get_contents($tempPath);
        @unlink($tempPath);

        $this->assertNotFalse($contents);

        return $contents;
    }

    private function sampleAnalysisCsv(): string
    {
        return <<<'CSV'
sep=,
"ANALYSIS INFO",,,,,,,,,
"URL:","https://ctb.nmrsolutions.fi//analysis-v/AnA_test",,,,,,,,
"CTKey:","AnA_test",,,,,,,,
"Name:","sample_analysis",,,,,,,,
"Remarks:","sample.fid - compound.sdf in DMSO-d6",,,,,,,,
"Created:","tester","2026-01-20T17:18:47.240142Z",,,,,,,
"Modifed:","reviewer","2026-01-21T09:00:00.000000Z",,,,,,,
,,,,,,,,
"SCORES",,,,,,,,,
"Match",0.8477409797043777,,,,,,,,
"RMS",0.8787728651855229,,,,,,,,
"Shift similarity",0.42920553376510706,,,,,,,,
"Coupling similarity",0.838980279054738,,,,,,,,
"Intensity",0.9993252820365425,,,,,,,,
,,,,,,,,
"SPINSYSTEMS",,,,,,,,,
"CTKey","Name","LRMS","LRMS Min","LRMS Max","SSType","InChI key","Formula","MW","Ref. MW","Purity-%","Sample Vol.","Sample Weight.","Population","Pop. Min","Pop. Max",,,
"Ss1","DMSO-d5",0.01,-Infinity,0.01,"Solvent","IAZDPXIOMUYVGZ-UHFFFAOYSA-N","C2D5HOS",83,-1,0,0,0,0.8,1e-9,1000000,,,
"Ss2","H2O",0.02,-Infinity,0.02,"Solvent","XLYOFNOQVPJJNP-UHFFFAOYSA-N","H2O",18,-1,0,0,0,0.1,1e-9,1000000,,,
"Ss3","compound.sdf",0.1,0.1,0.2,"Solute","LGPKJUJXISCYQZ-HTCHUFIESA-N","C29H26O7",486,-1,0,0,0,0.01,1e-9,1000000,,,
,,,,,,,,
"CHEMICAL SHIFTS (ppm)",,,,,,,,,
"SS CTKey","Spin system","SG CTKey","Name","Element","Nucleus","Spincount","Nucleicount","Shift","Response","Line shape","LRMS",,,,,,,
"Ss1","DMSO-d5","Sg1","DMSO-H",1,512,1,1,2.5,1,"H",0.01,,,,,,,
"Ss3","compound.sdf","Sg2",""C10,C11"",6,3072,2,2,160.1,1,""C10,C11"",-1,,,,,,,
"Ss3","compound.sdf","Sg3","H14",1,512,1,1,3.75,1,"H14",0.04,,,,,,,
,,,,,,,,
"COUPLING CONSTANTS (Hz)",,,,,,,,,
"SS CTKey","Spin system","CG CTKey","Name","Shift","Shift","Coupling",,,,,,,,,,,,
"Ss3","compound.sdf","Cg1","C14-H14","C14","H14",141.2,,,,,,,,,,,,
"Ss3","compound.sdf","Cg2","C24-H24",""C24,C25"",""H24,H25"",163.9,,,,,,,,,,,,
,,,,,,,,
"LINESHAPES",,,,,,,,,
"SS CTKey","Spin system","LS CTKey","Name","Line width (Hz)","Gaussian",,,,,,,,,,,,,
"Ss1","DMSO-d5","Sg1","DMSO-H",1.5,0.5,,,,,,,,,,,,,
"Ss3","compound.sdf","Sg2",""C10,C11"",3,0,,,,,,,,,,,,,
,,,,,,,,
"QMGI",,,,,,,,,
"S1S CTKey","Spectrum 1D","SS CTKey","Spin system","SG CTKey","Name","TotalSpins","RMS","Weight","Range min","Range max","SG Cal Fract","SG Obs sum","SG Cal sum","Obs sum","Cal sum","Over","Under","Orphan"
"S1test","sample.fid","Ss3","compound.sdf","Sg3","H14",0.04,0.97,3.7,3.8,0.98,100,103,288,323,0.9,3.4,0.3,0.1
CSV;
    }

    private function sampleRefCsv(): string
    {
        return <<<'CSV'
sep=,
"CTKey:","Ss3",,,,,,,,
"Name:","compound.sdf",,,,,,,,
"Solvent:","DMSO-d6",,,,,,,,
"Temperature:",298.15,,,,,,,,
"SSType:","Solute",,,,,,,,
"AType:","Reference",,,,,,,,
CSV;
    }

    private function invokeFinalizeProcessing(): mixed
    {
        Bus::fake([ArchiveStudy::class]);

        $processDraft = app(ProcessDraft::class);
        $method = new ReflectionMethod(ProcessDraft::class, 'finalizeProcessing');
        $method->setAccessible(true);

        return $method->invoke($processDraft, $this->draft, $this->project->fresh());
    }

    public function test_info_exposes_hifsa_pdf_url_for_a_study_with_a_hifsa_sibling(): void
    {
        [$study, $pdf] = $this->makeStudyWithHifsaSibling();

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/info");

        $response->assertStatus(200);

        $studies = $response->json('studies');
        $this->assertCount(1, $studies);
        $this->assertStringContainsString(
            "/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}",
            $studies[0]['hifsa_pdf_url']
        );
    }

    public function test_info_hifsa_pdf_url_is_null_without_a_hifsa_folder(): void
    {
        $study = Study::factory()->create([
            'name' => 'Plain',
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
            'draft_id' => $this->draft->id,
            'is_public' => false,
        ]);
        $studyFolder = FileSystemObject::factory()->directory()->rootLevel()->create([
            'draft_id' => $this->draft->id,
            'name' => 'Plain',
            'model_type' => 'study',
            'study_id' => $study->id,
        ]);
        $study->update(['fs_id' => $studyFolder->id]);
        Dataset::factory()->create([
            'study_id' => $study->id,
            'draft_id' => $this->draft->id,
            'project_id' => $this->project->id,
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/info");

        $response->assertStatus(200);
        $this->assertNull($response->json('studies.0.hifsa_pdf_url'));
    }

    public function test_process_response_exposes_hifsa_pdf_url_for_a_study_with_a_hifsa_sibling(): void
    {
        [$study, $pdf] = $this->makeStudyWithHifsaSibling();

        $response = $this->invokeFinalizeProcessing();
        $studies = $response->getData(true)['studies'];

        $this->assertCount(1, $studies);
        $this->assertStringContainsString(
            "/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}",
            $studies[0]['hifsa_pdf_url']
        );
        $this->assertSame($study->id, $studies[0]['id']);
    }

    public function test_finalize_processing_stores_hifsa_csv_from_export_zip(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, $this->makeExportZipContents());

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $response = $this->invokeFinalizeProcessing();

        $this->assertSame(200, $response->getStatusCode());

        $study->refresh();
        $this->assertSame('https://ctb.nmrsolutions.fi//analysis-v/AnA_test', $study->hifsa_data['url']);
        $this->assertSame(
            'sample.fid - compound.sdf in DMSO-d6',
            $study->hifsa_data['remarks'],
        );
        $this->assertSame('DMSO-d6', $study->hifsa_data['solvent']);
        $this->assertSame('298.15', $study->hifsa_data['temperature']);
        $this->assertSame([
            'by' => 'tester',
            'at' => '2026-01-20T17:18:47.240142Z',
        ], $study->hifsa_data['created']);
        $this->assertSame([
            'by' => 'reviewer',
            'at' => '2026-01-21T09:00:00.000000Z',
        ], $study->hifsa_data['modified']);
        $this->assertEqualsWithDelta(0.8477409797043777, $study->hifsa_data['scores']['match'], 1e-9);
        $this->assertEqualsWithDelta(0.8787728651855229, $study->hifsa_data['scores']['rms'], 1e-9);
        $this->assertEqualsWithDelta(0.42920553376510706, $study->hifsa_data['scores']['shift_similarity'], 1e-9);
        $this->assertEqualsWithDelta(0.838980279054738, $study->hifsa_data['scores']['coupling_similarity'], 1e-9);
        $this->assertEqualsWithDelta(0.9993252820365425, $study->hifsa_data['scores']['intensity'], 1e-9);

        $this->assertCount(3, $study->hifsa_data['spinsystems']);
        $this->assertSame('DMSO-d5', $study->hifsa_data['spinsystems'][0]['name']);
        $this->assertSame('Solvent', $study->hifsa_data['spinsystems'][0]['ss_type']);
        $this->assertSame('compound.sdf', $study->hifsa_data['spinsystems'][2]['name']);
        $this->assertSame('Solute', $study->hifsa_data['spinsystems'][2]['ss_type']);
        $this->assertEqualsWithDelta(486.0, $study->hifsa_data['spinsystems'][2]['mw'], 1e-9);
        $this->assertNull($study->hifsa_data['spinsystems'][0]['lrms_min']);

        $this->assertCount(3, $study->hifsa_data['chemical_shifts']);
        $this->assertSame('C10,C11', $study->hifsa_data['chemical_shifts'][1]['name']);
        $this->assertSame('C10,C11', $study->hifsa_data['chemical_shifts'][1]['line_shape']);
        $this->assertEqualsWithDelta(160.1, $study->hifsa_data['chemical_shifts'][1]['shift'], 1e-9);
        $this->assertNull($study->hifsa_data['chemical_shifts'][1]['lrms']);

        $this->assertCount(2, $study->hifsa_data['couplings']);
        $this->assertSame('C14-H14', $study->hifsa_data['couplings'][0]['name']);
        $this->assertSame('C14', $study->hifsa_data['couplings'][0]['shift_from']);
        $this->assertSame('H14', $study->hifsa_data['couplings'][0]['shift_to']);
        $this->assertSame('C24,C25', $study->hifsa_data['couplings'][1]['shift_from']);
        $this->assertSame('H24,H25', $study->hifsa_data['couplings'][1]['shift_to']);
        $this->assertEqualsWithDelta(163.9, $study->hifsa_data['couplings'][1]['coupling'], 1e-9);

        $this->assertCount(2, $study->hifsa_data['lineshapes']);
        $this->assertSame('C10,C11', $study->hifsa_data['lineshapes'][1]['name']);
        $this->assertEqualsWithDelta(3.0, $study->hifsa_data['lineshapes'][1]['line_width'], 1e-9);

        $this->assertCount(1, $study->hifsa_data['qmgi']);
        $this->assertSame('H14', $study->hifsa_data['qmgi'][0]['name']);
        $this->assertSame('compound.sdf', $study->hifsa_data['qmgi'][0]['spin_system']);
        $this->assertEqualsWithDelta(0.97, $study->hifsa_data['qmgi'][0]['rms'], 1e-9);
        $this->assertEqualsWithDelta(0.3, $study->hifsa_data['qmgi'][0]['under'], 1e-9);
        $this->assertEqualsWithDelta(0.1, $study->hifsa_data['qmgi'][0]['orphan'], 1e-9);
    }

    public function test_finalize_processing_leaves_hifsa_data_null_without_export_zip(): void
    {
        [$study] = $this->makeStudyWithHifsaSibling();

        $response = $this->invokeFinalizeProcessing();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNull($study->fresh()->hifsa_data);
    }

    public function test_finalize_processing_ignores_corrupt_export_zip(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, 'not-a-zip');

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $response = $this->invokeFinalizeProcessing();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNull($study->fresh()->hifsa_data);
    }

    public function test_finalize_processing_skips_reparse_when_hifsa_data_already_set(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();
        $study->update([
            'hifsa_data' => [
                'url' => 'https://example.test/existing',
                'remarks' => 'existing',
                'solvent' => 'CDCl3',
                'temperature' => '300',
                'scores' => [
                    'match' => 1.0,
                    'rms' => 1.0,
                    'shift_similarity' => 1.0,
                    'coupling_similarity' => 1.0,
                    'intensity' => 1.0,
                ],
                'spinsystems' => [],
                'chemical_shifts' => [],
                'couplings' => [],
                'lineshapes' => [],
                'qmgi' => [],
            ],
        ]);

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, $this->makeExportZipContents());

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $this->invokeFinalizeProcessing();

        $this->assertSame('https://example.test/existing', $study->fresh()->hifsa_data['url']);
        $this->assertEquals(1.0, $study->fresh()->hifsa_data['scores']['match']);
    }

    public function test_finalize_processing_upgrades_score_only_hifsa_data(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();
        $study->update([
            'hifsa_data' => [
                'url' => 'https://example.test/old',
                'remarks' => 'score-only',
                'solvent' => 'CDCl3',
                'temperature' => '300',
                'scores' => [
                    'match' => 1.0,
                    'rms' => 1.0,
                    'shift_similarity' => 1.0,
                    'coupling_similarity' => 1.0,
                    'intensity' => 1.0,
                ],
            ],
        ]);

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, $this->makeExportZipContents());

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $this->invokeFinalizeProcessing();

        $study->refresh();
        $this->assertSame('https://ctb.nmrsolutions.fi//analysis-v/AnA_test', $study->hifsa_data['url']);
        $this->assertArrayHasKey('spinsystems', $study->hifsa_data);
        $this->assertCount(3, $study->hifsa_data['spinsystems']);
        $this->assertArrayHasKey('chemical_shifts', $study->hifsa_data);
        $this->assertArrayHasKey('couplings', $study->hifsa_data);
        $this->assertArrayHasKey('lineshapes', $study->hifsa_data);
        $this->assertArrayHasKey('qmgi', $study->hifsa_data);
    }

    public function test_info_upgrades_score_only_hifsa_data(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();
        $study->update([
            'hifsa_data' => [
                'url' => 'https://example.test/old',
                'scores' => [
                    'match' => 1.0,
                    'rms' => 1.0,
                    'shift_similarity' => 1.0,
                    'coupling_similarity' => 1.0,
                    'intensity' => 1.0,
                ],
            ],
        ]);

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, $this->makeExportZipContents());

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/dashboard/drafts/{$this->draft->id}/info");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('studies.0.hifsa_data.spinsystems'));
        $this->assertSame(
            'https://ctb.nmrsolutions.fi//analysis-v/AnA_test',
            $response->json('studies.0.hifsa_data.url'),
        );
        $this->assertCount(3, $study->fresh()->hifsa_data['spinsystems']);
    }

    public function test_finalize_processing_reparses_legacy_row_shaped_hifsa_data(): void
    {
        Storage::fake(config('filesystems.default'));

        [$study, , $hifsaFolder] = $this->makeStudyWithHifsaSibling();
        $study->update([
            'hifsa_data' => [
                ['sep=' => 'ATOMS', '' => ''],
            ],
        ]);

        $zipPath = 'drafts/compound/hifsa/analysis_export.zip';
        Storage::disk(config('filesystems.default'))
            ->put($zipPath, $this->makeExportZipContents());

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $hifsaFolder->id,
            'name' => 'analysis_export.zip',
            'path' => '/'.$zipPath,
        ]);

        $this->invokeFinalizeProcessing();

        $study->refresh();
        $this->assertArrayHasKey('scores', $study->hifsa_data);
        $this->assertEqualsWithDelta(0.8477409797043777, $study->hifsa_data['scores']['match'], 1e-9);
    }

    public function test_hifsa_file_streams_pdf_inline(): void
    {
        Storage::fake(config('filesystems.default'));

        [, $pdf] = $this->makeStudyWithHifsaSibling();

        Storage::disk(config('filesystems.default'))
            ->put('drafts/compound/hifsa/report.pdf', '%PDF-1.4 fake');

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_hifsa_file_rejects_non_pdf(): void
    {
        $nonPdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'analysis.blob',
            'path' => '/drafts/compound/hifsa/analysis.blob',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$nonPdf->id}");

        $response->assertStatus(404);
    }

    public function test_hifsa_file_rejects_fso_from_another_draft(): void
    {
        $otherDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $pdf = FileSystemObject::factory()->file()->create([
            'draft_id' => $otherDraft->id,
            'name' => 'report.pdf',
            'path' => '/drafts/other/report.pdf',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/dashboard/drafts/{$this->draft->id}/hifsa/{$pdf->id}");

        $response->assertStatus(403);
    }
}
