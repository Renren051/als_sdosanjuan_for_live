# SDO San Juan City — Alternative Learning System (ALS)
### Web-Based Student Information System (SIS) & Android-Based Modular LMS

[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=flat&logo=vite&logoColor=white)](https://vitejs.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![DepEd RA 11510](https://img.shields.io/badge/DepEd-RA%2011510%20Compliant-0038A8?style=flat)](https://www.deped.gov.ph/)
[![Tests Passing](https://img.shields.io/badge/PHPUnit-92%20Passed%20(578%20Assertions)-success?style=flat)]()

---

## 📌 Project Overview

Under the mandate of the **Department of Education (DepEd)** and **Republic Act No. 11510 (The Alternative Learning System Act)**, this system serves the **Schools Division Office (SDO) of San Juan City** to empower Out-of-School Youth and Adults (OSYA) across all 21 barangays.

The platform unifies two essential operational components:
1. **Web-Based Student Information System (SIS):** Multi-role administrative and student portal handling digital intake (DepEd ALS Form 2), document verification, Community Learning Center (CLC) allocation, hero banner management, module management, and automated DepEd reporting (ALS Form 1 & Form 2).
2. **Android-Based Modular LMS (Learner-Only):** An offline-first mobile application designed for OSYA learners with limited internet connectivity, featuring local SQLite/Room caching, background telemetry synchronization, and an in-app PDF module reader.

---

## 🎨 UI/UX Design Architecture

The web application combines an institutional **Kingster University-Style Photographic Hero** with a modern **Bento Grid UI** inspired by Constructivist educational philosophy:
- **Kingster University-Style Photographic Hero Carousel:** Full-width photographic showcase with an institutional top emerald accent bar (`bg-emerald-600`), dark navy frosted overlay card (`bg-[#07132c]/90`), Playfair Display serif typography, and gold accent badges.
- **Ultra-Smooth Slow Crossfade & Ken Burns Transitions:** Powered by Alpine.js with 1,000ms easing (`transition-all duration-1000 ease-in-out`), subtle scale effects (`scale-105`), auto-cycling timer (6.5s), slide indicator dots, previous/next chevron navigation, and pause-on-hover.
- **Zero-FOUC Single-Cell Grid Stacking:** Slides are stacked in a single CSS Grid cell (`grid grid-cols-1 grid-rows-1`) with server-rendered initial opacities, preventing layout height flashes, jumps, or stacking glitches during initial page load and refresh.
- **Asymmetric Bento Compartmentalization:** High-density, rounded modular tiles (`rounded-3xl`), micro-badges, and clean borders.
- **DepEd Division Color Palette:** Deep DepEd Navy (`#0b193d`), Philippine Blue (`#0038A8`), Golden Yellow (`#FCD116`), Emerald Green (`#059669`), and Crimson (`#CE1126`).
- **Dual-Logo Institutional Branding:** Dedicated containers showcasing both the **SDO San Juan City Logo** and the official **DepEd ALS Logo**.
- **Interactive Alpine.js Directory:** Real-time search and filter toolbar for locating the 12 Community Learning Centers (CLCs) across San Juan.
- **Modern Sticky Frosted-Glass Navbar:** Dynamic `backdrop-blur-md` navigation header with border blur transitions on window scroll.
- **Floating AI Assistant Widget (ALSJay):** Anchored circular launcher at bottom-right with gentle slow-motion jumping animation, speech bubble callout, and bottom-anchored pop-up chat window.

---

## 🚀 Key Features

### 1. Public Bento Portal
- **Hero Banner Showcase:** Dynamic, slow-fade photo carousel displaying division milestones, Kariton Klasrum outreach, and ALIVE programs with customizable CTAs ("Take A Tour", "Mag-enroll Na").
- **About ALS:** Constructivist learning methodology (Experience, Self-Pacing, Mentorship) and legal mandate under RA 11510.
- **A & E Program:** Academic tracks for Basic Literacy Program (BLP), Elementary (Grade 6), and Junior High School (Grade 10), plus the 6 Core Learning Strands (LS 1–6).
- **Enrolment Requirements & DepEd Qualifications:** Official DepEd A&E requirement checklist (2 pcs 1x1 photo with name tag `SURNAME, FIRST NAME & MIDDLE NAME`, PSA Birth Certificate, Barangay Certificate of Residency, 1 Valid ID), age qualification matrices (Elementary: ages 12+, Secondary/JHS: ages 16+), retakers & overage learner policies, and sectoral eligibility breakdown (OSYAs, workers, IPs, inmates) referencing `DepEd.gov.ph`.
- **Learning Centers (CLCs):** Live searchable directory of all 12 San Juan CLCs with assigned mobile teachers and facility filters.
- **Contact Us:** Division office directory, emergency hotlines, and instant message inquiry form.

### 2. Digital Intake Wizard (ALS Form 2 — Complete Learner Record)
- **Part I — Learner Demographics & Address:**
  - PSA legal name verification: First name, middle name, last name, name extension.
  - Birthdate, sex/gender, civil status, active mobile contact, Facebook profile, street address, and 21 San Juan City barangays.
- **Part II — Educational Background & Social Inclusion:**
  - Highest prior grade completed (No Formal Schooling through Junior/Senior High).
  - Last school attended, last school year attended, and primary reason for leaving school (economic, family, health, distance, etc.).
  - Mother tongue and 4Ps (Pantawid Pamilyang Pilipino Program) beneficiary tracking.
  - Indigenous Peoples (IP) identification with conditional IP community/ethnicity name requirement.
  - Person with Disability (PWD) identification with conditional disability type/specification.
  - Complete parents and legal guardian profile: Father's full name, Mother's maiden name, Legal guardian name, relationship, and emergency contact details.
- **Part III — Program Track & Center Allocation:**
  - Academic tracks: Basic Literacy Program (BLP), A&E Elementary, A&E Junior High School.
  - Preferred Community Learning Center (CLC) in San Juan City with auto-assignment of Barangay Mobile Teacher.
  - Learning delivery modes (Modular Offline App, Modular Print, Blended, Face-to-Face) and schedule preferences.
- **Part IV — Account Credentials & Intake Safety:**
  - Unique username & email validation with clear duplicate guidance and direct sign-in links.
  - Client-side validation per step via `reportValidity()` preventing skipped sections.
  - Dynamic initial step restoration (`$initialStep`) jumping straight to erroneous inputs upon server validation with full `old(...)` preservation.
  - Double-click submission protection (`isSubmitting` flag with loading spinner).
  - Traceable enrollment reference generation (`ALS-YYYY-0000X`) displayed directly on the Learner Dashboard in the pending review banner.

### 3. Admin & Mobile Teacher Portal
- **Super Admin Teacher Management (`/superadmin/teachers`):**
  - Exclusive Super Administrator role (`super_admin`) authorized to provision, edit, activate/deactivate, and assign Mobile Teachers across 21 San Juan barangay Community Learning Centers (CLCs).
  - Quick credential reset with temporary password dispatch for field teachers.
  - Deletion guardrails preventing removal of teachers with active learner enrollments.
- **Hero Banner Manager (`/admin/banners`):**
  - Upload custom high-resolution banner photography directly from the browser.
  - Configure banner title, subtitle, badge tag, CTA button label, and destination URL.
  - Toggle slide visibility (Active/Inactive) and manage presentation sort order without touching template code.
- **Enrollment Queue:** Review pending applications, verify or reject intake documents with remarks, and transition statuses (`submitted`, `under_review`, `enrolled`, `rejected`).
- **CLC Allocator:** Real-time learner capacity and assignment tracking per barangay center.
- **Module Repository (`/admin/modules` & `/learner/modules`):**
  - Manage official DepEd Self-Learning Modules (SLMs) categorized by Learning Strands (LS1 to LS6) with toggle publishing.
  - Multi-tier program hierarchy supporting **A&E Lower & Advanced Elementary** with sub-levels (**Lower Elementary** Grades 1–3 and **Advanced Elementary** Grades 4–6) and dual-language tracks (**English** & **Filipino**).
  - 156 authentic DepEd ALS Elementary PDF modules integrated and served with in-browser reading, auto-resume points, and binary streaming for the Android app.
- **Automated DepEd Exports:**
  - **ALS Form 1:** Individual Learning Agreement and Masterlist.
  - **ALS Form 2:** Official Learner Enrolment and Profile Form.

### 4. Learner Portal
- Real-time intake status tracker with document compliance checklist.
- Built-in PDF module viewer modal for self-paced study.
- Module progress telemetry tracking.

### 5. ALSJay — Official AI Virtual Assistant
- **AI Persona & Institutional Role:** Official AI assistant for DepEd SDO San Juan City Alternative Learning System named **ALSJay**.
- **1-to-1 Language Mirroring:**
  - Queries in Tagalog/Filipino yield **100% pure Tagalog/Filipino** replies with polite honorifics (`po` and `opo`).
  - Queries in English yield **100% clean, professional English** replies.
- **Strict School-Only Guardrails:**
  - Dedicated exclusively to DepEd ALS educational programs (BLP, A&E Elementary and Junior High School), enrollment requirements, Community Learning Centers, and official division announcements.
  - Strictly refuses requests regarding **coding & software programming**, **student homework & assignment solving**, harassment, or non-school topics with polite redirection to ALS.
- **Live Weather & Class Suspension Advisories:** Powered by Google Gemini with live Google Search Grounding for real-time DOST-PAGASA forecasts and official San Juan City class suspensions (Mayor Francis Zamora / San Juan City PIO).
- **Anchored Floating Widget (`<x-chat-widget />`):**
  - Gentle slow-motion jumping launcher button anchored at the bottom-right (`bottom-5 right-5`).
  - Speech callout bubble (*"Ano ang maitutulong ko sa’yo ngayon?"*) with `ALSJay` badge.
  - Clicking the bubble keeps it anchored in place while the chat dialog pops up directly above it; bubble smoothly transforms into an `✕` close button.
- **Resilient Multi-Tier Architecture:** High-availability `/chat` endpoint with CSRF validation exemption and an offline smart local knowledge-base fallback.

---

## 🛠️ Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x | PHP 8.2.12 (CLI Visual C++ 2019 x64) |
| **AI Virtual Assistant** | Google Gemini 2.5 Flash | Real-time Search Grounding, Language Mirroring, School Guardrails |
| **API Authentication**| Laravel Sanctum | Bearer token authentication for web & mobile |
| **Frontend UI** | Blade + Vite v7.3.6 | Tailwind CSS 4, Alpine.js, Plus Jakarta Sans |
| **Database** | MySQL | Port 3306 (`als_sdosanjuan_db`) via XAMPP |
| **PDF Generation** | Barryvdh DomPDF | DepEd Form 1 & Form 2 printable formats |
| **Version Control** | Git 2.54.0 | Tracked on branch `main` |
| **Mobile App (Phase 4)** | Native Android | Kotlin, Room Database, Retrofit 2, WorkManager |

---

## 💻 Local Development Setup

### 📋 Prerequisites Across All Platforms
- **PHP 8.2 or higher** (Required extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`)
- **Composer 2.x**
- **Node.js 18+ and npm**
- **MySQL 8.0+ / MariaDB** (Port 3306)
- **Git**

---

### 📥 Platform-Specific Installation Guides

Select your operating system:
* [🪟 Windows Setup Guide](#-windows-installation-guide)
* [🍎 macOS Setup Guide](#-macos-installation-guide)
* [🐧 Linux (Ubuntu / Debian) Setup Guide](#-linux-ubuntu--debian-installation-guide)

---

#### 🪟 Windows Installation Guide

##### 1. Prerequisites Setup
- **Git:** Download the 64-bit installer from [git-scm.com/download/win](https://git-scm.com/download/win) or run `winget install --id Git.Git -e --source winget`.
- **PHP & MySQL (XAMPP):** Download and install [XAMPP for Windows](https://www.apachefriends.org/) (PHP 8.2+).
  - Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
  - Ensure PHP is in your Windows System PATH (`C:\xampp\php`).
- **Composer:** Download and run the installer from [getcomposer.org/download](https://getcomposer.org/download/).
- **Node.js & npm:** Download and run the LTS installer from [nodejs.org](https://nodejs.org/).

##### 2. Clone the Repository
Open PowerShell or Git Bash:
```powershell
git clone --branch preview-deployment --single-branch https://github.com/Renren051/als_sdosanjuan.git als_sdosanjuan
cd als_sdosanjuan
```

##### 3. Automated 1-Click Setup (Recommended)
Double-click or run:
```powershell
.\setup.bat
```
This automated script will:
1. Verify PHP, Composer, and Node.js in your system PATH.
2. Install PHP dependencies (`composer install`) and Node packages (`npm install`).
3. Create `.env` from `.env.example` and generate the `APP_KEY`.
4. Link public storage (`php artisan storage:link`) for hero banners and uploaded documents.
5. Prompt to run database migrations and seed default demo data (`php artisan migrate --seed --force`).
6. Compile production frontend assets (`npm run build`).

##### 4. Daily Development Launch
Double-click or run:
```powershell
.\run.bat
```
This instantly launches:
- **Laravel API Server:** `http://127.0.0.1:8000`
- **Vite Dev Server:** `http://localhost:5173`

*(Alternatively, run manually in PowerShell: `php artisan serve --host=127.0.0.1 --port=8000` and `npm run dev`.)*

---

#### 🍎 macOS Installation Guide

##### 1. Prerequisites via Homebrew
If you do not have [Homebrew](https://brew.sh/) installed:
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Install Git, PHP 8.2, Composer, Node.js, and MySQL:
```bash
brew install git php@8.2 composer node mysql
```

Start the local MySQL service:
```bash
brew services start mysql
```

##### 2. Clone the Repository
```bash
git clone --branch preview-deployment --single-branch https://github.com/Renren051/als_sdosanjuan.git als_sdosanjuan
cd als_sdosanjuan
```

##### 3. Install Dependencies
```bash
composer install --no-interaction --prefer-dist
npm install
```

##### 4. Configure Environment & Storage
```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Ensure your `.env` database parameters match your local MySQL configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=als_sdosanjuan_db
DB_USERNAME=root
DB_PASSWORD=
```

##### 5. Create Database, Migrate & Seed
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS als_sdosanjuan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
npm run build
```

##### 6. Start Development Servers
Open two terminal tabs in the project directory:

**Tab 1 (Laravel server):**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Tab 2 (Vite HMR):**
```bash
npm run dev
```

Visit the portal at: **`http://127.0.0.1:8000`**

---

#### 🐧 Linux (Ubuntu / Debian) Installation Guide

##### 1. Prerequisites via APT
Update package lists and install required tools, PHP 8.2 with extensions, Composer, Node.js, and MySQL:
```bash
sudo apt update && sudo apt install -y software-properties-common curl git unzip
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y php8.2 php8.2-cli php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
    mysql-server composer
```

Install Node.js (LTS v20+ via NodeSource):
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Start and enable MySQL service:
```bash
sudo systemctl enable --now mysql
```

##### 2. Clone the Repository
```bash
git clone --branch preview-deployment --single-branch https://github.com/Renren051/als_sdosanjuan.git als_sdosanjuan
cd als_sdosanjuan
```

##### 3. Configure Directory Permissions
Grant write permissions to the storage and bootstrap cache directories:
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

##### 4. Install Dependencies
```bash
composer install --no-interaction --prefer-dist
npm install
```

##### 5. Configure Environment & Storage
```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Update your `.env` with database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=als_sdosanjuan_db
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

##### 6. Create Database, Migrate & Seed
```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS als_sdosanjuan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
npm run build
```

##### 7. Start Development Servers
Open two terminal sessions:

**Session 1 (Laravel server):**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Session 2 (Vite HMR):**
```bash
npm run dev
```

Visit the portal at: **`http://localhost:8000`** (or your server's IP address).

---

### Run `main` and `preview-deployment`

The same checkout can run only one branch at a time. To switch branches in the existing project folder, stop the server with `Ctrl+C`, then run:

```powershell
git switch main
php artisan serve --host=127.0.0.1 --port=8000
```

Open **`http://127.0.0.1:8000`**. To run the preview instead, stop the server and switch branches:

```powershell
git switch preview-deployment
php artisan serve --host=127.0.0.1 --port=8000
```

On a fresh clone, fetch and create the local preview branch once with:

```powershell
git fetch origin
git switch --track origin/preview-deployment
```

#### Run both branches at the same time

Use a separate Git worktree for `main`, then run each branch on a different port. In the original project folder, keep the preview branch on port `8000`:

```powershell
git switch preview-deployment
php artisan serve --host=127.0.0.1 --port=8000
```

In a second PowerShell window, create and prepare the `main` worktree. Run these commands from the original project folder:

```powershell
git worktree add ..\als_sdosanjuan-main main
Copy-Item .env ..\als_sdosanjuan-main\.env
cd ..\als_sdosanjuan-main
composer install
php artisan serve --host=127.0.0.1 --port=8001
```

Open **`http://127.0.0.1:8000`** for `preview-deployment` and **`http://127.0.0.1:8001`** for `main`. The worktree setup is needed only once. Both worktrees use the database configured in the copied `.env`; keep that in mind when changing or migrating local data. If both branches point to the same commit, their pages will look the same until one branch has different changes.

---

## 👥 Default Demo Accounts

The database seeder automatically creates the following accounts for evaluation:

| Role | Username / Identifier | Password | Access / Scope |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `superadmin_als` | `Password123!` | Division IT / System Admin: Mobile Teacher Provisioning (`/superadmin/teachers`), CLC Assignments & Credential Resets |
| **Administrator** | `admin_als` | `Password123!` | Full Division Administrative Access (Enrollment Queue, CLC Allocator, Banner Manager) |
| **Mobile Teacher** | `teacher_juan` | `Password123!` | Assigned CLC & Learner Verification Portal |
| **Learner** | `learner_mark` *(LRN: `136542240001`)* | `Password123!` | Learner Dashboard & SLM Modules |

---

## 📡 Core REST API Endpoints

The API is fully secured via Laravel Sanctum and ready for Android LMS integration:

### Authentication
- `POST /api/v1/auth/login` — Authenticate and receive Sanctum bearer token.
- `POST /api/v1/auth/register` — Digital learner registration (ALS Form 2).
- `GET /api/v1/auth/me` — Retrieve active authenticated user profile.
- `POST /api/v1/auth/logout` — Revoke active token.

### AI Virtual Assistant (ALSJay)
- `POST /chat` — Send query to ALSJay (Gemini API with Google Search grounding and smart local fallback).

### Curriculum & Modules
- `GET /api/v1/strands` — List the 6 ALS Learning Strands with module counts.
- `GET /api/v1/modules` — List published self-learning modules (filterable by strand).
- `GET /api/v1/modules/{id}/download` — Stream or download official module PDF.

### Learner Telemetry & Sync
- `POST /api/v1/learner/documents/upload` — Upload PSA / Clearance / Photo credentials.
- `GET /api/v1/learner/enrollment-status` — Check real-time enrollment progress.
- `POST /api/v1/learner/sync-progress` — Sync offline modular completion data from Android LMS.

### Administrative Reporting
- `GET /api/v1/admin/reports/form-1` — Export ALS Form 1 masterlist JSON / PDF data.
- `GET /api/v1/admin/reports/form-2/{id}` — Export ALS Form 2 individual intake data.

---

## 🧪 Automated Testing

The system includes a comprehensive PHPUnit / Pest test suite covering API authentication, hero banner rendering and administrative management, learner journey and module progress, registration intake validation (ALS Form 2), AI chatbot guardrails, offline telemetry sync, and web portal access:

```bash
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
PASS  Tests\Feature\WebPortalTest

Tests:    92 passed (578 assertions)
Duration: ~13s
```

---

## ⚖️ Legal & Privacy Compliance

- **Republic Act No. 11510:** An Act Institutionalizing the Alternative Learning System in Basic Education for Out-of-School Children in Special Cases and Adults.
- **Republic Act No. 10173 (Data Privacy Act of 2012):** All learner personal identification documents, contact numbers, and academic records are securely encrypted and protected against unauthorized access.

---

## 🏛️ Acknowledgments & Division Information

- **Department of Education (DepEd)** — Republic of the Philippines
- **National Capital Region (NCR)**
- **Schools Division Office of San Juan City** — Alternative Learning System Unit
- Pinaglabanan Street, Barangay Pedro Cruz, San Juan City, Metro Manila
- Email: `als.sanjuan@deped.gov.ph`
