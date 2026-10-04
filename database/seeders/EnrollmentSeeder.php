<?php

namespace Database\Seeders;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds for enrollments, documents, and progress telemetry.
     */
    public function run(): void
    {
        $teacherJuan = User::where('username', 'teacher_juan')->first();
        $teacherAlyssa = User::where('username', 'teacher_alyssa')->first();

        $clcWestCrame = CommunityLearningCenter::where('clc_code', 'CLC-SJ-WCR-01')->first();
        $clcAdditionHills = CommunityLearningCenter::where('clc_code', 'CLC-SJ-ADH-01')->first();
        $clcBatis = CommunityLearningCenter::where('clc_code', 'CLC-SJ-BAT-01')->first();
        $clcSalapan = CommunityLearningCenter::where('clc_code', 'CLC-SJ-SAL-01')->first();

        $mark = User::where('username', 'learner_mark')->first();
        $jenny = User::where('username', 'learner_jenny')->first();
        $carlos = User::where('username', 'learner_carlos')->first();
        $bea = User::where('username', 'learner_bea')->first();

        // 1. Mark's Enrollment (Officially Enrolled)
        if ($mark && $clcWestCrame) {
            $enrMark = Enrollment::updateOrCreate(
                ['user_id' => $mark->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
                    'clc_id' => $clcWestCrame->id,
                    'assigned_teacher_id' => $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekend',
                    'remarks' => 'Credentials verified. LRN assigned and registered in ALS Masterlist.',
                    'enrolled_at' => now()->subDays(10),
                ]
            );

            // Add sample documents
            $this->seedSampleDocument($mark, $enrMark, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $teacherJuan);
            $this->seedSampleDocument($mark, $enrMark, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'verified', $teacherJuan);
            $this->seedSampleDocument($mark, $enrMark, LearnerDocument::TYPE_PHOTO_ID, 'verified', $teacherJuan);

            // Add sample module progress telemetry
            $modules = Module::take(3)->get();
            foreach ($modules as $index => $mod) {
                $status = $index === 0 ? ModuleProgress::STATUS_COMPLETED : ModuleProgress::STATUS_IN_PROGRESS;
                ModuleProgress::updateOrCreate(
                    [
                        'user_id' => $mark->id,
                        'module_id' => $mod->id,
                    ],
                    [
                        'downloaded_at' => now()->subDays(5),
                        'last_opened_at' => now()->subHours(3),
                        'completion_status' => $status,
                        'device_identifier' => 'Samsung-SM-A135F-Android13',
                        'last_page_read' => $status === ModuleProgress::STATUS_COMPLETED ? 4 : 2,
                        'total_pages' => 4,
                        'synced_at' => now()->subHours(2),
                    ]
                );
            }
        }

        // 2. Jenny's Enrollment (Pending Review)
        if ($jenny && $clcAdditionHills) {
            $enrJenny = Enrollment::updateOrCreate(
                ['user_id' => $jenny->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_AE_ELEMENTARY,
                    'clc_id' => $clcAdditionHills->id,
                    'assigned_teacher_id' => $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_PENDING,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekday_am',
                    'remarks' => 'Submitted online registration. Awaiting teacher document review.',
                ]
            );

            $this->seedSampleDocument($jenny, $enrJenny, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'pending', null);
            $this->seedSampleDocument($jenny, $enrJenny, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'pending', null);
        }

        // 3. Carlos' Enrollment (Needs Correction)
        if ($carlos && $clcBatis) {
            $enrCarlos = Enrollment::updateOrCreate(
                ['user_id' => $carlos->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_BLP,
                    'clc_id' => $clcBatis->id,
                    'assigned_teacher_id' => $teacherAlyssa?->id,
                    'enrollment_status' => Enrollment::STATUS_NEEDS_CORRECTION,
                    'delivery_mode' => 'blended',
                    'schedule_preference' => 'flexible',
                    'remarks' => 'Barangay clearance scan is blurry. Please upload a clear photo or photocopy with visible seal.',
                ]
            );

            $this->seedSampleDocument($carlos, $enrCarlos, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'rejected', $teacherAlyssa, 'Photo too dark to verify dry seal');
        }

        // 4. Bea's Enrollment (Under Review)
        if ($bea && $clcSalapan) {
            $enrBea = Enrollment::updateOrCreate(
                ['user_id' => $bea->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
                    'clc_id' => $clcSalapan->id,
                    'assigned_teacher_id' => $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_UNDER_REVIEW,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekend',
                    'remarks' => 'Assigned for initial screening and interview schedule.',
                ]
            );

            $this->seedSampleDocument($bea, $enrBea, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $teacherJuan);
            $this->seedSampleDocument($bea, $enrBea, LearnerDocument::TYPE_FORM_137_SF10, 'pending', null);
        }
    }

    private function seedSampleDocument(
        User $user,
        Enrollment $enrollment,
        string $docType,
        string $status,
        ?User $reviewer = null,
        ?string $notes = null
    ): void {
        $docDir = storage_path('app/public/documents/' . $user->id);
        if (!File::isDirectory($docDir)) {
            File::makeDirectory($docDir, 0755, true);
        }

        $fileName = "{$docType}_{$user->id}.pdf";
        $filePath = "documents/{$user->id}/{$fileName}";
        $fullPath = storage_path('app/public/' . $filePath);

        if (!File::exists($fullPath)) {
            // Write a dummy file placeholder
            File::put($fullPath, "%PDF-1.4 sample DepEd credential document for {$user->username}");
        }

        LearnerDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'enrollment_id' => $enrollment->id,
                'document_type' => $docType,
            ],
            [
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => File::exists($fullPath) ? File::size($fullPath) : 2048,
                'mime_type' => 'application/pdf',
                'verification_status' => $status,
                'verification_notes' => $notes,
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => $reviewer ? now()->subDay() : null,
            ]
        );
    }
}
