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
     * Seeds only the authentic official demo learners (BLP, Elementary, JHS).
     */
    public function run(): void
    {
        $teacherJuan = User::where('username', 'teacher_juan')->first();
        $teacherAlyssa = User::where('username', 'teacher_alyssa')->first();

        $clcWestCrame = CommunityLearningCenter::where('clc_code', 'CLC-SJ-WCR-01')->first();
        $clcAdditionHills = CommunityLearningCenter::where('clc_code', 'CLC-SJ-ADH-01')->first();
        $clcBatis = CommunityLearningCenter::where('clc_code', 'CLC-SJ-BAT-01')->first();
        $clcPedroCruz = CommunityLearningCenter::where('clc_code', 'CLC-SJ-CDJ-01')->first()
            ?? CommunityLearningCenter::where('clc_name', 'like', '%Pedro Cruz%')->first();

        // 1. Basic Literacy Program (BLP) - Bayani L. Panaligan
        $blpLearner = User::where('username', 'learner_blp')->first();
        if ($blpLearner && $clcBatis) {
            $enrBlp = Enrollment::updateOrCreate(
                ['user_id' => $blpLearner->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_BLP,
                    'clc_id' => $clcBatis->id,
                    'assigned_teacher_id' => $teacherAlyssa?->id ?? $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekend',
                    'remarks' => 'Officially enrolled in Basic Literacy Program (BLP). Verified credentials.',
                    'enrolled_at' => now()->subDays(10),
                ]
            );
            $this->seedSampleDocument($blpLearner, $enrBlp, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $teacherAlyssa);
            $this->seedSampleDocument($blpLearner, $enrBlp, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'verified', $teacherAlyssa);
            $this->seedSampleDocument($blpLearner, $enrBlp, LearnerDocument::TYPE_PHOTO_ID, 'verified', $teacherAlyssa);

            $blpModule = Module::published()->where('level', Module::LEVEL_BLP)->first();
            if ($blpModule) {
                ModuleProgress::updateOrCreate(
                    [
                        'user_id' => $blpLearner->id,
                        'module_id' => $blpModule->id,
                    ],
                    [
                        'downloaded_at' => now()->subDays(5),
                        'last_opened_at' => now()->subHours(2),
                        'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
                        'device_identifier' => 'LearnerPortal-Web',
                        'last_page_read' => 2,
                        'total_pages' => 4,
                        'synced_at' => now()->subHours(1),
                    ]
                );
            }
        }

        // 2. A&E Elementary (Grades 1–6) - Elena M. Ramos
        $elemLearner = User::where('username', 'learner_elem')->first();
        if ($elemLearner && $clcAdditionHills) {
            $enrElem = Enrollment::updateOrCreate(
                ['user_id' => $elemLearner->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_AE_ELEMENTARY,
                    'clc_id' => $clcAdditionHills->id,
                    'assigned_teacher_id' => $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekday_pm',
                    'remarks' => 'Officially enrolled in A&E Elementary (Grades 1–6). Verified credentials.',
                    'enrolled_at' => now()->subDays(10),
                ]
            );
            $this->seedSampleDocument($elemLearner, $enrElem, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $teacherJuan);
            $this->seedSampleDocument($elemLearner, $enrElem, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'verified', $teacherJuan);
            $this->seedSampleDocument($elemLearner, $enrElem, LearnerDocument::TYPE_PHOTO_ID, 'verified', $teacherJuan);

            $elemModule = Module::published()->where('level', Module::LEVEL_ELEMENTARY)->first();
            if ($elemModule) {
                ModuleProgress::updateOrCreate(
                    [
                        'user_id' => $elemLearner->id,
                        'module_id' => $elemModule->id,
                    ],
                    [
                        'downloaded_at' => now()->subDays(5),
                        'last_opened_at' => now()->subHours(2),
                        'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
                        'device_identifier' => 'LearnerPortal-Web',
                        'last_page_read' => 2,
                        'total_pages' => 4,
                        'synced_at' => now()->subHours(1),
                    ]
                );
            }
        }

        // 3. A&E Junior High School - Jun H. Santos
        $jhsLearner = User::where('username', 'learner_jhs')->first();
        if ($jhsLearner) {
            $clcJhs = $clcPedroCruz ?? $clcWestCrame;
            $enrJhs = Enrollment::updateOrCreate(
                ['user_id' => $jhsLearner->id, 'calendar_year' => '2026'],
                [
                    'program_track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
                    'clc_id' => $clcJhs?->id ?? 1,
                    'assigned_teacher_id' => $teacherJuan?->id,
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekend',
                    'remarks' => 'Officially enrolled in A&E Junior High School. Verified credentials.',
                    'enrolled_at' => now()->subDays(10),
                ]
            );
            $this->seedSampleDocument($jhsLearner, $enrJhs, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $teacherJuan);
            $this->seedSampleDocument($jhsLearner, $enrJhs, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'verified', $teacherJuan);
            $this->seedSampleDocument($jhsLearner, $enrJhs, LearnerDocument::TYPE_PHOTO_ID, 'verified', $teacherJuan);

            // Add sample module progress telemetry
            $modules = Module::take(3)->get();
            foreach ($modules as $index => $mod) {
                $status = $index === 0 ? ModuleProgress::STATUS_COMPLETED : ModuleProgress::STATUS_IN_PROGRESS;
                ModuleProgress::updateOrCreate(
                    [
                        'user_id' => $jhsLearner->id,
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
                'verification_notes' => $notes ?? 'Verified by Mobile Teacher',
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => $reviewer ? now()->subDay() : null,
            ]
        );
    }
}
