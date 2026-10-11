<?php

namespace App\Console\Commands;

use App\Models\CommunityLearningCenter;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SeedProgramLearnersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:seed-program-learners';

    /**
     * The console command description.
     */
    protected $description = 'Seed or update authentic dedicated learner accounts for each ALS program track (BLP, Elementary, Junior High)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultPassword = Hash::make('Password123!');

        $teacherJuan = User::where('username', 'teacher_juan')->first();
        $teacherAlyssa = User::where('username', 'teacher_alyssa')->first();

        $clcBatis = CommunityLearningCenter::where('clc_name', 'like', '%Batis%')->first()
            ?? CommunityLearningCenter::first();
        $clcAdditionHills = CommunityLearningCenter::where('clc_name', 'like', '%Addition Hills%')->first()
            ?? CommunityLearningCenter::first();
        $clcPedroCruz = CommunityLearningCenter::where('clc_name', 'like', '%Pedro Cruz%')->first()
            ?? CommunityLearningCenter::first();

        $programLearners = [
            // 1. Basic Literacy Program (BLP)
            [
                'username' => 'learner_blp',
                'email' => 'blp.learner@als-sanjuan.local',
                'lrn' => '136542240010',
                'track' => Enrollment::TRACK_BLP,
                'clc_id' => $clcBatis->id,
                'teacher_id' => $teacherAlyssa?->id ?? $teacherJuan?->id,
                'profile' => [
                    'first_name' => 'Bayani',
                    'middle_name' => 'Liwanag',
                    'last_name' => 'Panaligan',
                    'name_extension' => null,
                    'birthdate' => '1995-04-18',
                    'gender' => 'male',
                    'civil_status' => 'single',
                    'contact_number' => '09171112233',
                    'facebook_name' => 'Bayani Panaligan BLP',
                    'street_address' => '25 F. Blumentritt St.',
                    'barangay' => 'Batis',
                    'city' => 'San Juan City',
                    'province' => 'Metro Manila',
                    'mother_tongue' => 'Tagalog / Filipino',
                    'highest_education_attained' => 'No Formal Schooling',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => true,
                    'emergency_contact_name' => 'Nanette Panaligan',
                    'emergency_contact_number' => '09171112233',
                ],
                'module_level' => Module::LEVEL_BLP,
            ],

            // 2. A&E Lower and Advanced Elementary
            [
                'username' => 'learner_elem',
                'email' => 'elem.learner@als-sanjuan.local',
                'lrn' => '136542240020',
                'track' => Enrollment::TRACK_AE_ELEMENTARY,
                'clc_id' => $clcAdditionHills->id,
                'teacher_id' => $teacherJuan?->id ?? $teacherAlyssa?->id,
                'profile' => [
                    'first_name' => 'Elena',
                    'middle_name' => 'Magno',
                    'last_name' => 'Ramos',
                    'name_extension' => null,
                    'birthdate' => '2004-08-22',
                    'gender' => 'female',
                    'civil_status' => 'single',
                    'contact_number' => '09182223344',
                    'facebook_name' => 'Elena Ramos ALS',
                    'street_address' => '78 A. Lake St.',
                    'barangay' => 'Addition Hills',
                    'city' => 'San Juan City',
                    'province' => 'Metro Manila',
                    'mother_tongue' => 'Tagalog / Filipino',
                    'highest_education_attained' => 'Grade 4 (Elementary Undergraduate)',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => false,
                    'guardian_name' => 'Dolores Ramos',
                    'guardian_relationship' => 'Mother',
                    'emergency_contact_name' => 'Dolores Ramos',
                    'emergency_contact_number' => '09182223344',
                ],
                'module_level' => Module::LEVEL_ELEMENTARY,
            ],

            // 3. A&E Secondary / Junior High School
            [
                'username' => 'learner_jhs',
                'email' => 'jhs.learner@als-sanjuan.local',
                'lrn' => '136542240030',
                'track' => Enrollment::TRACK_AE_JUNIOR_HIGH,
                'clc_id' => $clcPedroCruz->id,
                'teacher_id' => $teacherAlyssa?->id ?? $teacherJuan?->id,
                'profile' => [
                    'first_name' => 'Jun',
                    'middle_name' => 'Hernandez',
                    'last_name' => 'Santos',
                    'name_extension' => null,
                    'birthdate' => '2003-10-15',
                    'gender' => 'male',
                    'civil_status' => 'single',
                    'contact_number' => '09193334455',
                    'facebook_name' => 'Jun Santos ALS',
                    'street_address' => '102 N. Domingo St.',
                    'barangay' => 'Pedro Cruz',
                    'city' => 'San Juan City',
                    'province' => 'Metro Manila',
                    'mother_tongue' => 'Tagalog / Filipino',
                    'highest_education_attained' => 'Grade 8 (Junior High Undergraduate)',
                    'is_ip' => false,
                    'is_pwd' => false,
                    'is_4ps_beneficiary' => false,
                    'emergency_contact_name' => 'Mario Santos',
                    'emergency_contact_number' => '09193334455',
                ],
                'module_level' => Module::LEVEL_JUNIOR_HIGH,
            ],
        ];

        foreach ($programLearners as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'lrn' => $data['lrn'],
                    'email' => $data['email'],
                    'password' => $defaultPassword,
                    'role' => User::ROLE_LEARNER,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );

            $enrollment = Enrollment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'calendar_year' => '2026',
                ],
                [
                    'program_track' => $data['track'],
                    'clc_id' => $data['clc_id'],
                    'assigned_teacher_id' => $data['teacher_id'],
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                    'delivery_mode' => 'modular_offline',
                    'schedule_preference' => 'weekend',
                    'remarks' => 'Officially enrolled in ' . $data['track'] . '. Credentials verified by Mobile Teacher.',
                    'enrolled_at' => now()->subDays(7),
                ]
            );

            // Seed verified documents
            $this->seedSampleDocument($user, $enrollment, LearnerDocument::TYPE_PSA_BIRTH_CERT, 'verified', $data['teacher_id']);
            $this->seedSampleDocument($user, $enrollment, LearnerDocument::TYPE_BARANGAY_CLEARANCE, 'verified', $data['teacher_id']);
            $this->seedSampleDocument($user, $enrollment, LearnerDocument::TYPE_PHOTO_ID, 'verified', $data['teacher_id']);

            // Seed sample progress on a module matching their exact level
            $firstModule = Module::published()->where('level', $data['module_level'])->first();
            if ($firstModule) {
                ModuleProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'module_id' => $firstModule->id,
                    ],
                    [
                        'downloaded_at' => now()->subDays(3),
                        'last_opened_at' => now()->subHours(2),
                        'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
                        'device_identifier' => 'LearnerPortal-Web',
                        'last_page_read' => 2,
                        'total_pages' => 4,
                        'synced_at' => now()->subHour(),
                    ]
                );
            }

            $this->info("Created/Updated dedicated learner account: {$data['username']} ({$data['track']})");
        }

        $this->info("Program learners successfully seeded.");
        return Command::SUCCESS;
    }

    private function seedSampleDocument(
        User $user,
        Enrollment $enrollment,
        string $docType,
        string $status,
        ?int $reviewerId = null
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
                'verification_notes' => 'Verified by Mobile Teacher',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now()->subDays(5),
            ]
        );
    }
}
