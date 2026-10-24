# SDO San Juan City — Alternative Learning System (ALS)
### Unified Web Student Information System (SIS), Admin LMS & Mobile Backend API

[![Live Production](https://img.shields.io/badge/Render-Live%20Production-success?style=flat&logo=render)](https://als-sdosanjuan-for-live.onrender.com/)
[![Cloud Database](https://img.shields.io/badge/TiDB%20Cloud-AWS%20Singapore-1D2935?style=flat&logo=mysql&logoColor=white)](https://tidbcloud.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![Docker](https://img.shields.io/badge/Docker-PHP%208.2%20Apache-2496ED?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%20Compatible-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![DepEd RA 11510](https://img.shields.io/badge/DepEd-RA%2011510%20Compliant-0038A8?style=flat)](https://www.deped.gov.ph/)
[![Android APK](https://img.shields.io/badge/Android%20LMS-v2.1%20Ready-success?style=flat&logo=android&logoColor=white)](https://github.com/Renren051/als_sdosanjuan_modules_apk)
[![PHPUnit Tests](https://img.shields.io/badge/PHPUnit-107%20Passed%20(646%20Assertions)-success?style=flat)]()

---

## 📌 Executive Overview

Under the mandate of the **Department of Education (DepEd)** and **Republic Act No. 11510 (The Alternative Learning System Act)**, this ecosystem serves the **Schools Division Office (SDO) of San Juan City** to empower Out-of-School Youth and Adults (OSYA) across all 21 barangays.

The platform operates as a **single, unified dual-platform system** powered by a **shared central MySQL database**:
1. **Web-Based Student Information System (SIS) & Administrative Portal:** Multi-role governance portal handling digital intake (DepEd ALS Form 2), document verification, Community Learning Center (CLC) allocation, hero banner management, module authoring/publishing, and automated DepEd masterlist exports (ALS Form 1 & Form 2).
2. **Centralized Backend API:** High-performance Laravel Sanctum REST API serving data, authentication, binary PDF streaming, telemetry ingestion, and educational game score synchronization.
3. **Android-Based Modular LMS (Native Mobile App v2.1):** An offline-first mobile application designed for OSYA learners with limited or intermittent internet connectivity, focused strictly on **Self-Learning Modules (SLMs)** and **Interactive Gamified Learning (Educational Mini-Games)** with local SQLite/Room caching and background telemetry synchronization. *(Note: AI features are officially disclosed/decommissioned from the mobile app to ensure complete offline autonomy, zero data costs, and dedicated focus on curriculum content).* Pre-built standalone package available in [`als_sdosanjuan_modules_apk`](https://github.com/Renren051/als_sdosanjuan_modules_apk).

---

## 🚀 Live Cloud Production Deployment (Render + TiDB Cloud)

The production environment is deployed in the cloud with automated GitHub CI/CD:

| Component | Provider & URL | Details |
| :--- | :--- | :--- |
| **Live Web Portal & SIS** | [https://als-sdosanjuan-for-live.onrender.com](https://als-sdosanjuan-for-live.onrender.com) | Hosted on **Render.com** (Docker container running PHP 8.2 + Apache + dynamic port binding) |
| **Production REST API** | `https://als-sdosanjuan-for-live.onrender.com/api/v1` | Public HTTPS API consumed by Web and Android clients |
| **Cloud MySQL Database** | **TiDB Cloud Serverless** (`AWS Singapore ap-southeast-1`) | High-availability MySQL 8.0-compatible cluster (`als_sdosanjuan_db`) |
| **Live Git Repository** | [https://github.com/Renren051/als_sdosanjuan_for_live.git](https://github.com/Renren051/als_sdosanjuan_for_live.git) | Connected to Render for automatic build & deployment on commit |

---

### 🛡️ Clean Production Database

For live deployment, a **clean production schema** has been generated and populated into the cloud database with zero mock learners or test applications:

- **SQL Dump File:** [`database/clean_production_database.sql`](database/clean_production_database.sql)
- **Production Seeder Class:** [`database/seeders/CleanProductionDatabaseSeeder.php`](database/seeders/CleanProductionDatabaseSeeder.php)

#### Summary of Clean Seeded Data:
- **1 Super Administrator:** Division IT Officer (`superadmin.als@deped.gov.ph` / `Password123!`)
- **1 Division Administrator:** ALS Coordinator (`admin.als@deped.gov.ph` / `Password123!`)
- **2 Mobile Teachers:** Field Educators (`teacher_juan`, `teacher_alyssa` / `Password123!`)
- **12 Community Learning Centers (CLCs):** All 12 official learning stations in San Juan City
- **7 Learning Strands:** LS1 (Communication English/Filipino), LS2, LS3, LS4, LS5, LS6 + Core
- **427 Official Curriculum Modules:** Full repository of elementary, secondary, and BLP modules ready for offline reading
- **0 Fake Students & 0 Test Enrollments:** Intake queue is completely clean for real applicant registration

To re-seed clean production data at any time:
```bash
php artisan db:seed --class=CleanProductionDatabaseSeeder --force
```

---

## 📱 Connecting the Android Mobile App to Live Server

The native Android app ([`als_sdosanjuan_modules_apk`](https://github.com/Renren051/als_sdosanjuan_modules_apk)) communicates directly with the live cloud backend over secure HTTPS.

### 1. Update the API Base URL
In your Android Studio project (typically in `NetworkConstants.kt`, `ApiService.kt`, or `build.gradle`), set the live URL:
```kotlin
// Live Production Backend API Endpoint:
const val BASE_URL = "https://als-sdosanjuan-for-live.onrender.com/api/v1/"
```

### 2. Build Release APK for Distribution
1. In Android Studio, go to **Build** > **Generate Signed Bundle / APK**.
2. Select **APK** and click **Next**.
3. Select your release keystore credentials.
4. Choose **release** build variant.
5. Distribute the resulting `app-release.apk` directly to learners, mobile teachers, or upload to GitHub Releases / Google Drive.

---

## 🗄️ Single Shared Database Architecture

Both the **Web SIS / LMS** and the **Android Mobile App (v2.1)** operate on the **EXACT SAME MySQL database** (`als_sdosanjuan_db`). There is zero data segregation or secondary synchronization delays:

```mermaid
graph TB
    subgraph Centralized_Database [Centralized Cloud MySQL: TiDB Cloud Singapore]
        DB_Users[("users & user_profiles\nLearners, Teachers, Admins")]
        DB_Modules[("learning_strands & modules\n427 Official DepEd SLMs LS1-LS6")]
        DB_Telemetry[("module_progress & enrollments\nPages Read, Completion, Timestamps")]
        DB_Games[("module_game_attempts\nInteractive Quiz Scores & Badges")]
        DB_CLCs[("clcs & announcements\n12 Learning Centers, Banners")]
    end

    subgraph Laravel_Backend [Laravel 12 Backend & API: Render.com Cloud]
        SIS_Web["Web SIS / LMS\n(Blade + Alpine.js + Tailwind CSS)"]
        Sanctum_API["Laravel Sanctum REST API\n(/api/v1/*)"]
        AI_Web_Only["Google Gemini 2.5 Flash\n(Web-Only Info Assistant — Disclosed on App)"]
    end

    subgraph Client_Applications [Client Access Touchpoints]
        Web_Admin["Super Admin & Division Admin\n(Teacher Provisioning, Intake, Modules)"]
        Web_Teacher["Mobile Teachers\n(CLC Masterlist & Progress Verification)"]
        Web_Learner["Learners on Web Browser\n(Intake, Status Tracker, Fit-Page PDF Reader)"]
        Android_App["Learners on Android Mobile App v2.1\n(Modules & Educational Games Only — No AI)"]
    end

    Laravel_Backend <--> Centralized_Database
    Web_Admin --> SIS_Web
    Web_Teacher --> SIS_Web
    Web_Learner --> SIS_Web
    Android_App <-->|HTTPS REST API via Live URL| Sanctum_API
    Android_App -.->|Offline Mode: Cache Modules & Games in Room DB| Android_App
    Android_App -->|Background Telemetry & Game Sync\nWorkManager SyncWorker| Sanctum_API
    SIS_Web -.-> AI_Web_Only
```

### How the Shared Database Works in Practice:
- **Unified User Accounts & Authentication:** Every learner and teacher registered through the Web Intake Wizard (or created by administrators) is immediately stored in the `users` table. They can log into **both the Web Portal and the Android App** using their email address or username and password.
- **Centralized Module Control:** When an Administrator uploads, publishes, or updates an official DepEd module on the Web SIS (`/admin/modules`), the Android app immediately receives the updated module, title, file URL, and strand count without requiring an app store update.
- **Bi-Directional Reading Telemetry:** When a learner reads a module on Android (even without internet connection), their reading position (`last_page_read`) and completion status (`is_completed`) are logged into local SQLite. When the mobile device reconnects to Wi-Fi or mobile data, the Android `SyncWorker` sends this data to `/api/v1/learner/sync-progress`, instantly updating the web database so Teachers and Admins can see the progress on their web dashboards.
- **Gamified Learning & Score Synchronization:** When a learner completes or plays an interactive educational game/quiz on Android, game scores, mastery badges, and attempts are stored in local SQLite (`module_game_attempts`). When online, `SyncWorker` transmits these records to `/api/v1/learner/sync-game-scores`, giving Mobile Teachers verifiable proof of comprehension.
- **App AI Disclosure Notice:** The mobile application is purely focused on **Modules** and **Games**; the AI chatbot is intentionally disclosed/omitted to preserve device battery, avoid data requirements, and provide a distraction-free learning environment.

---

## 🛠️ Technology Stack

| Layer | Component | Details |
| :--- | :--- | :--- |
| **Cloud Hosting** | Render.com | Dockerized PHP 8.2 + Apache runtime with automated Git deploys |
| **Database** | TiDB Cloud / MySQL 8.0 | Serverless MySQL on AWS Singapore (`gateway01.ap-southeast-1.prod.aws.tidbcloud.com`) |
| **Backend & API** | Laravel 12.x | PHP 8.2+ (Visual C++ 2019 x64), Eloquent ORM |
| **Authentication** | Laravel Sanctum | State-based session cookies for Web; Bearer Tokens for Android |
| **Web Frontend** | Blade + Vite v7.x | Tailwind CSS, Alpine.js, Plus Jakarta Sans, Playfair Display |
| **PDF Generation** | DomPDF | Official DepEd Form 1 (Masterlist) & Form 2 (Enrolment Profile) |
| **Gamified Learning** | Interactive Module Games | Module-linked comprehension quizzes, offline scoring, and `module_game_attempts` sync |
| **AI Assistant (Web Only)** | Google Gemini 2.5 Flash | Web-only assistant with Google Search Grounding for San Juan advisories |
| **Mobile App (v2.1)** | Native Android | Kotlin 2.0, Jetpack Compose, Material 3, Room SQLite, Retrofit 2, WorkManager |

---

## 💻 Local Development Setup (Optional)

### 📋 Prerequisites
1. **PHP 8.2 or higher** (Included in XAMPP; extensions required: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`, `curl`).
2. **Composer 2.x** ([getcomposer.org](https://getcomposer.org/)).
3. **Node.js 18+ & npm** ([nodejs.org](https://nodejs.org/)).
4. **MySQL 8.0 / MariaDB** (Default XAMPP port 3306).
5. **Git** ([git-scm.com](https://git-scm.com/)).

### 📥 Step-by-Step Installation Guide

```powershell
# 1. Clone repository
git clone https://github.com/Renren051/als_sdosanjuan_for_live.git
cd als_sdosanjuan_for_live

# 2. Setup environment
cp .env.example .env

# 3. Install dependencies
composer install
npm install

# 4. Generate key & link storage
php artisan key:generate
php artisan storage:link

# 5. Compile assets
npm run build

# 6. Run migrations & start server
php artisan migrate --force
php artisan db:seed --class=CleanProductionDatabaseSeeder
php artisan serve
```

---

## 👥 Clean Default Accounts (Production)

| Role / Track | Primary Login Email | Username | Default Password | Program Track / Assignment | Access Capabilities |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Administrator** | `superadmin.als@deped.gov.ph` | `superadmin_als` | `Password123!` | Division IT / All CLCs | Full System Control, Mobile Teacher Provisioning (`/superadmin/teachers`), CLC Assignments, Account Resets |
| **Division Administrator** | `admin.als@deped.gov.ph` | `admin_als` | `Password123!` | SDO San Juan Division Office | Enrollment Intake Queue, Document Verification, Banner Manager, Module Uploading & Publishing, DepEd Form 1 & 2 Exports |
| **Mobile Teacher** | `juan.delacruz@deped.gov.ph` | `teacher_juan` | `Password123!` | West Crame CLC | Assigned CLC Roster, Learner Attendance, Document Verification |
| **Mobile Teacher** | `alyssa.reyes@deped.gov.ph` | `teacher_alyssa` | `Password123!` | Batis CLC | Assigned CLC Roster, Learner Attendance, Document Verification |

---

## 📡 Core REST API Reference (Shared Backend)

All endpoints below are consumed directly by the native Android application (v2.1):

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Authenticate user (Email or Username) & issue Sanctum Bearer token | No |
| `POST` | `/api/v1/auth/register` | Digital learner registration (ALS Form 2) | No |
| `GET` | `/api/v1/auth/me` | Fetch authenticated learner profile & track | Yes (Bearer Token) |
| `POST` | `/api/v1/auth/logout` | Revoke active bearer token | Yes (Bearer Token) |
| `GET` | `/api/v1/strands` | Retrieve 7 Learning Strands with dynamic module counts | Yes (Bearer Token) |
| `GET` | `/api/v1/modules` | Fetch published modules (filterable by strand and track level) | Yes (Bearer Token) |
| `GET` | `/api/v1/modules/{id}/download` | Binary PDF download stream for offline caching | Yes (Bearer Token) |
| `POST` | `/api/v1/learner/sync-progress` | Ingest offline reading telemetry (page numbers, completion) | Yes (Bearer Token) |
| `GET` | `/api/v1/learner/modules/{id}/game-status` | Retrieve completion and high score for module educational mini-game | Yes (Bearer Token) |
| `POST` | `/api/v1/learner/sync-game-scores` | Ingest offline educational game quiz attempts & scores | Yes (Bearer Token) |
| `GET` | `/api/v1/teacher/module-progress` | Retrieve assigned learner module & game completion matrix | Yes (Bearer Token) |
| `POST` | `/chat` | Web inquiry assistant *(disclosed / omitted from mobile app)* | No (CSRF-exempt) |

---

## 🧪 Automated Testing Suite

The backend includes a comprehensive PHPUnit test suite ensuring data integrity, authentication security, role authorization, and API contracts:

```powershell
php artisan test
```

**Results:**
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\AdminDashboardMetricsTest
PASS  Tests\Feature\AlsApiTest
PASS  Tests\Feature\ChatControllerTest
PASS  Tests\Feature\DepEdReportsValidationTest
PASS  Tests\Feature\EndToEndPilotJourneyTest
PASS  Tests\Feature\EnrollmentQueueAndTransitionsTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\HeroBannerTest
PASS  Tests\Feature\LearnerJourneyTest
PASS  Tests\Feature\RegistrationIntakeTest
PASS  Tests\Feature\SuperAdminTeacherManagementTest
PASS  Tests\Feature\WebPortalTest

Tests:    107 passed (646 assertions)
Duration: ~24s
```

---

## ⚖️ Legal & Privacy Compliance

- **Republic Act No. 11510:** An Act Institutionalizing the Alternative Learning System in Basic Education for Out-of-School Children in Special Cases and Adults.
- **Republic Act No. 10173 (Data Privacy Act of 2012):** All learner personal identification documents, contact numbers, and academic records are securely encrypted and protected against unauthorized access.

---

## 🏛️ Division Contact & Administration

- **Department of Education (DepEd)** — National Capital Region (NCR)
- **Schools Division Office of San Juan City** — Alternative Learning System Unit
- Pinaglabanan Street, Barangay Pedro Cruz, San Juan City, Metro Manila
- Email: `als.sanjuan@deped.gov.ph`

---

## 📑 Technical Documentation & Appendices

For complete architectural specifications, UML diagrams, procedural workflows, and deployment parameters, see [**README_APPENDIX.md**](README_APPENDIX.md):
- **Appendix A:** Procedure of the System (Intake, Teacher Onboarding, Modular Study & Gamified Learning)
- **Appendix B:** Context Diagram (DFD Level 0 — Modules & Educational Games Architecture)
- **Appendix C:** Activity Diagrams (Intake, Teacher Onboarding, Offline Telemetry Sync, Module Educational Games)
- **Appendix D:** Use Case Diagram & Specifications (UC01–UC14)
- **Appendix E:** Hardware Requirements (Server, Workstation, Mobile)
- **Appendix F:** Software Requirements (Full Stack Matrix & Game Engine Schema)
- **Appendix G:** Network Requirements & Offline-First Fault Tolerance
