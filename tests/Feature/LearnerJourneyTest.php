<?php

namespace Tests\Feature;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LearnerJourneyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test 1: Learner can access dashboard and views strictly their own profile & enrollment.
     */
    public function test_learner_can_access_dashboard_and_views_own_enrollment(): void
    {
        $mark = User::where('username', 'learner_mark')->first();
        $this->assertNotNull($mark);

        $response = $this->actingAs($mark)->get(route('learner.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Welcome back, ' . $mark->profile->first_name)
            ->assertSee($mark->lrn)
            ->assertSee('Officially Enrolled')
            ->assertSee('Self-Learning Modules');
    }

    /**
     * Test 2: Learner can access dedicated enrollment tracking page.
     */
    public function test_learner_can_view_own_enrollment_journey_page(): void
    {
        $mark = User::where('username', 'learner_mark')->first();

        $response = $this->actingAs($mark)->get(route('learner.enrollment'));

        $response->assertStatus(200)
            ->assertSee('My Enrollment Status & Review')
            ->assertSee('Officially Enrolled')
            ->assertSee('Credential Document Checklist')
            ->assertSee('PSA Birth Certificate')
            ->assertSee('Barangay Clearance');
    }

    /**
     * Test 3: Learner can view dedicated profile page with their own ALS Form 2 record.
     */
    public function test_learner_can_view_own_profile_page(): void
    {
        $mark = User::where('username', 'learner_mark')->first();

        $response = $this->actingAs($mark)->get(route('learner.profile'));

        $response->assertStatus(200)
            ->assertSee('My Learner Profile')
            ->assertSee($mark->profile->first_name)
            ->assertSee($mark->profile->last_name)
            ->assertSee($mark->profile->street_address)
            ->assertSee($mark->profile->contact_number);
    }

    /**
     * Test 4: Learner isolation - learner cannot access staff/admin pages or another learner's documents.
     */
    public function test_learner_cannot_access_staff_admin_pages(): void
    {
        $mark = User::where('username', 'learner_mark')->first();

        // Admin dashboard
        $response = $this->actingAs($mark)->get('/admin/dashboard');
        $response->assertStatus(403);

        // Admin enrollments queue
        $response = $this->actingAs($mark)->get('/admin/enrollments');
        $response->assertStatus(403);

        // Admin CLC manager
        $response = $this->actingAs($mark)->get('/admin/clcs');
        $response->assertStatus(403);

        // Admin modules manager
        $response = $this->actingAs($mark)->get('/admin/modules');
        $response->assertStatus(403);
    }

    /**
     * Test 5: Learner cannot view another learner's private documents.
     */
    public function test_learner_cannot_view_another_learners_document(): void
    {
        $mark = User::where('username', 'learner_mark')->first();
        $carlos = User::where('username', 'learner_carlos')->first();

        // Create a document for Carlos
        $carlosDoc = LearnerDocument::updateOrCreate(
            [
                'user_id' => $carlos->id,
                'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
            ],
            [
                'file_path' => 'documents/' . $carlos->id . '/test_carlos.pdf',
                'file_name' => 'carlos_psa.pdf',
                'file_size' => 1024,
                'mime_type' => 'application/pdf',
                'verification_status' => LearnerDocument::STATUS_PENDING,
            ]
        );

        // Mark tries to access Carlos's document file
        $response = $this->actingAs($mark)->get(route('learner.documents.file', $carlosDoc->id));
        $response->assertStatus(403);
    }

    /**
     * Test 6: Learner can securely view their own uploaded document.
     */
    public function test_learner_can_view_their_own_document(): void
    {
        $mark = User::where('username', 'learner_mark')->first();

        // Store a fake file in storage
        $relativePath = 'documents/' . $mark->id . '/test_mark.pdf';
        Storage::disk('public')->put($relativePath, '%PDF-1.4 Fake PDF Content');

        $doc = LearnerDocument::updateOrCreate(
            [
                'user_id' => $mark->id,
                'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
            ],
            [
                'file_path' => $relativePath,
                'file_name' => 'mark_psa.pdf',
                'file_size' => 24,
                'mime_type' => 'application/pdf',
                'verification_status' => LearnerDocument::STATUS_VERIFIED,
            ]
        );

        $response = $this->actingAs($mark)->get(route('learner.documents.file', $doc->id));
        $response->assertStatus(200);
    }

    /**
     * Test 7: Enrollment status and document requirements agree with staff review workflow.
     */
    public function test_enrollment_status_and_review_remarks_reflected_for_needs_correction(): void
    {
        $carlos = User::where('username', 'learner_carlos')->first();
        $this->assertNotNull($carlos);

        $teacher = User::where('role', User::ROLE_MOBILE_TEACHER)->first();
        LearnerDocument::updateOrCreate(
            [
                'user_id' => $carlos->id,
                'document_type' => LearnerDocument::TYPE_BARANGAY_CLEARANCE,
            ],
            [
                'verification_status' => LearnerDocument::STATUS_REJECTED,
                'verification_notes' => 'Photo too dark to verify dry seal',
                'reviewed_by' => $teacher?->id,
                'reviewed_at' => now(),
            ]
        );

        $response = $this->actingAs($carlos)->get(route('learner.enrollment'));

        $response->assertStatus(200)
            ->assertSee('Needs Correction')
            ->assertSee('Document Corrections Needed')
            ->assertSee('Barangay clearance scan is blurry')
            ->assertSee('Photo too dark to verify dry seal');
    }

    /**
     * Test 8: Learner can upload a document and it resets status to pending for staff review.
     */
    public function test_learner_can_upload_document_and_resets_verification_status(): void
    {
        $carlos = User::where('username', 'learner_carlos')->first();

        // Upload a new barangay clearance to fix the rejected one
        $file = UploadedFile::fake()->create('barangay_clearance_new.pdf', 500, 'application/pdf');

        $response = $this->actingAs($carlos)->post(route('learner.documents.upload'), [
            'document_type' => LearnerDocument::TYPE_BARANGAY_CLEARANCE,
            'document_file' => $file,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        // Verify document is now pending and notes are reset
        $doc = LearnerDocument::where('user_id', $carlos->id)
            ->where('document_type', LearnerDocument::TYPE_BARANGAY_CLEARANCE)
            ->first();

        $this->assertNotNull($doc);
        $this->assertEquals(LearnerDocument::STATUS_PENDING, $doc->verification_status);
        $this->assertNull($doc->verification_notes);
        $this->assertNull($doc->reviewed_by);

        // Reset back to seeded rejected state for other tests and clean database
        $doc->update([
            'verification_status' => 'rejected',
            'verification_notes' => 'Photo too dark to verify dry seal',
        ]);
    }

    /**
     * Test 9: Document upload validation rejects invalid types and bad file extensions.
     */
    public function test_document_upload_validation(): void
    {
        $mark = User::where('username', 'learner_mark')->first();

        // Invalid document type
        $response = $this->actingAs($mark)->post(route('learner.documents.upload'), [
            'document_type' => 'invalid_doc_type',
            'document_file' => UploadedFile::fake()->create('test.pdf', 100),
        ]);
        $response->assertSessionHasErrors('document_type');

        // Invalid file format (e.g. .exe)
        $response = $this->actingAs($mark)->post(route('learner.documents.upload'), [
            'document_type' => LearnerDocument::TYPE_PSA_BIRTH_CERT,
            'document_file' => UploadedFile::fake()->create('malicious.exe', 100),
        ]);
        $response->assertSessionHasErrors('document_file');
    }

    /**
     * Test 10: Published modules can be found and filtered by strand, level, and keyword search.
     */
    public function test_published_modules_can_be_found_and_filtered(): void
    {
        $mark = User::where('username', 'learner_mark')->first();
        $strand = LearningStrand::where('code', 'LS1_ENG')->first();

        // Filter by strand
        $response = $this->actingAs($mark)->get(route('learner.modules', ['strand_id' => $strand->id]));
        $response->assertStatus(200)
            ->assertSee('Self-Learning Module Library');

        // Search by keyword
        $response = $this->actingAs($mark)->get(route('learner.modules', ['q' => 'Communication']));
        $response->assertStatus(200)
            ->assertSee('Communication');
    }

    /**
     * Test 11: Unpublished modules cannot be seen or opened by learners.
     */
    public function test_unpublished_modules_cannot_be_accessed_by_learners(): void
    {
        $mark = User::where('username', 'learner_mark')->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $strand = LearningStrand::first();

        // Create an unpublished draft module
        $draftModule = Module::create([
            'strand_id' => $strand->id,
            'level' => Module::LEVEL_JUNIOR_HIGH,
            'module_number' => 'Draft 99',
            'title' => 'Secret Unpublished Curriculum Module',
            'description' => 'Draft only for teachers',
            'pdf_file_path' => 'modules/draft.pdf',
            'pdf_file_size' => 1024,
            'version' => '0.1',
            'is_published' => false,
            'uploaded_by' => $admin->id,
        ]);

        // Should not appear in catalog
        $response = $this->actingAs($mark)->get(route('learner.modules'));
        $response->assertDontSee('Secret Unpublished Curriculum Module');

        // Attempting to read directly returns 404
        $response = $this->actingAs($mark)->get(route('learner.modules.read', $draftModule->id));
        $response->assertStatus(404);

        $draftModule->delete();
    }

    /**
     * Test 12: Published module can be opened in browser and transitions to in_progress.
     */
    public function test_published_module_can_be_opened_and_sets_in_progress(): void
    {
        $jenny = User::where('username', 'learner_jenny')->first();
        $module = Module::published()->first();

        // Clear any previous progress so we test fresh opening
        ModuleProgress::where('user_id', $jenny->id)->where('module_id', $module->id)->delete();

        $response = $this->actingAs($jenny)->get(route('learner.modules.read', $module->id));

        $response->assertStatus(200)
            ->assertSee($module->title)
            ->assertSee('moduleReader');

        // Verify progress record was created and is in_progress
        $progress = ModuleProgress::where('user_id', $jenny->id)
            ->where('module_id', $module->id)
            ->first();

        $this->assertNotNull($progress);
        $this->assertEquals(ModuleProgress::STATUS_IN_PROGRESS, $progress->completion_status);
        $this->assertNotNull($progress->last_opened_at);
    }

    /**
     * Test 13: Learner can update reading progress and resume reading at last page read.
     */
    public function test_learner_can_update_progress_and_resume(): void
    {
        $jenny = User::where('username', 'learner_jenny')->first();
        $module = Module::published()->orderBy('strand_id')->orderBy('level')->first();

        // Update progress to Page 2 of 4
        $response = $this->actingAs($jenny)->postJson(route('learner.modules.progress', $module->id), [
            'last_page_read' => 2,
            'total_pages' => 4,
            'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'progress' => [
                    'last_page_read' => 2,
                    'total_pages' => 4,
                    'percent' => 50,
                    'is_completed' => false,
                ],
            ]);

        // Opening the reader now resumes at Page 2
        $readerResponse = $this->actingAs($jenny)->get(route('learner.modules.read', [
            'id' => $module->id,
            'page' => 2,
        ]));

        $readerResponse->assertStatus(200)
            ->assertSee('initialPage: 2', false);

        // Modules catalog shows Resume button for this module
        $catalogResponse = $this->actingAs($jenny)->get(route('learner.modules'));
        $catalogResponse->assertStatus(200)
            ->assertSee('Resume (P. 2)');
    }

    /**
     * Test 14: Learner can mark module as completed and progress reaches 100%.
     */
    public function test_learner_can_complete_module(): void
    {
        $jenny = User::where('username', 'learner_jenny')->first();
        $module = Module::published()->first();

        // Post completion status
        $response = $this->actingAs($jenny)->postJson(route('learner.modules.progress', $module->id), [
            'last_page_read' => 4,
            'total_pages' => 4,
            'completion_status' => ModuleProgress::STATUS_COMPLETED,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'progress' => [
                    'completion_status' => ModuleProgress::STATUS_COMPLETED,
                    'percent' => 100,
                    'is_completed' => true,
                ],
            ]);

        $progress = ModuleProgress::where('user_id', $jenny->id)
            ->where('module_id', $module->id)
            ->first();

        $this->assertEquals(ModuleProgress::STATUS_COMPLETED, $progress->completion_status);
        $this->assertEquals(100, $progress->progress_percent);
    }

    /**
     * Test 15: Progress indicators have clear, consistent behavior across models and statuses.
     */
    public function test_progress_indicators_consistency(): void
    {
        $mark = User::where('username', 'learner_mark')->first();
        $module = Module::published()->first();

        // Clear any previous progress so we test fresh indicators
        ModuleProgress::where('user_id', $mark->id)->where('module_id', $module->id)->delete();

        // 1. Downloaded only -> 0%
        $p1 = ModuleProgress::create([
            'user_id' => $mark->id,
            'module_id' => $module->id,
            'completion_status' => ModuleProgress::STATUS_DOWNLOADED,
            'last_page_read' => 1,
            'total_pages' => 4,
        ]);
        $this->assertEquals(0, $p1->progress_percent);

        // 2. In progress -> proportional
        $p1->update([
            'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
            'last_page_read' => 2,
            'total_pages' => 4,
        ]);
        $this->assertEquals(50, $p1->fresh()->progress_percent);

        // 3. Completed -> 100%
        $p1->update([
            'completion_status' => ModuleProgress::STATUS_COMPLETED,
        ]);
        $this->assertEquals(100, $p1->fresh()->progress_percent);

        $p1->delete();
    }
}
