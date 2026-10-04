# SDO San Juan City — Alternative Learning System (ALS)
### Web-Based Student Information System (SIS) & Android-Based Modular LMS

[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=flat&logo=vite&logoColor=white)](https://vitejs.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![DepEd RA 11510](https://img.shields.io/badge/DepEd-RA%2011510%20Compliant-0038A8?style=flat)](https://www.deped.gov.ph/)
[![Tests Passing](https://img.shields.io/badge/PHPUnit-17%20Passed%20(64%20Assertions)-success?style=flat)]()

---

## 📌 Project Overview

Under the mandate of the **Department of Education (DepEd)** and **Republic Act No. 11510 (The Alternative Learning System Act)**, this system serves the **Schools Division Office (SDO) of San Juan City** to empower Out-of-School Youth and Adults (OSYA) across all 21 barangays.

The platform unifies two essential operational components:
1. **Web-Based Student Information System (SIS):** Multi-role administrative and student portal handling digital intake (DepEd ALS Form 2), document verification, Community Learning Center (CLC) allocation, module management, and automated DepEd reporting (ALS Form 1 & Form 2).
2. **Android-Based Modular LMS (Learner-Only):** An offline-first mobile application designed for OSYA learners with limited internet connectivity, featuring local SQLite/Room caching, background telemetry synchronization, and an in-app PDF module reader.

---

## 🎨 UI/UX Design Architecture

The web application is built on a **Bento Grid UI** inspired by Constructivist educational philosophy:
- **Asymmetric Bento Compartmentalization:** High-density, rounded modular tiles (`rounded-3xl`), micro-badges, and clean borders.
- **DepEd Division Color Palette:** Deep DepEd Navy (`#0b193d`), Philippine Blue (`#0038A8`), Golden Yellow (`#FCD116`), and Crimson (`#CE1126`).
- **Dual-Logo Institutional Branding:** Dedicated containers showcasing both the **SDO San Juan City Logo** and the official **DepEd ALS Logo**.
- **Interactive Alpine.js Directory:** Real-time search and filter toolbar for locating the 12 Community Learning Centers (CLCs) across San Juan.

---

## 🚀 Key Features

### 1. Public Bento Portal
- **Home:** Interactive hero with division manifesto, live mobile app preview mockup, and real-time division statistics.
- **About ALS:** Constructivist learning methodology (Experience, Self-Pacing, Mentorship) and legal mandate under RA 11510.
- **A & E Program:** Academic tracks for Basic Literacy Program (BLP), Elementary (Grade 6), and Junior High School (Grade 10), plus the 6 Core Learning Strands (LS 1–6).
- **Enrolment Requirements:** Interactive document checklist (PSA Birth Certificate, Barangay Clearance, 2x2 Photos, Form 137 / SF10) with side-by-side online vs. walk-in comparison.
- **Learning Centers (CLCs):** Live searchable directory of all 12 San Juan CLCs with assigned mobile teachers and facility filters.
- **Contact Us:** Division office directory, emergency hotlines, and instant message inquiry form.

### 2. Digital Intake Wizard (ALS Form 2)
- Multi-step intake capturing learner demographics, civil status, educational background, and preferred CLC.
- Direct digital document uploading (PSA, Barangay Clearance, ID photos) into secure private storage.

### 3. Admin & Mobile Teacher Portal
- **Enrollment Queue:** Review pending applications, verify or reject intake documents with remarks, and transition statuses (`submitted`, `under_review`, `enrolled`, `rejected`).
- **CLC Allocator:** Real-time learner capacity and assignment tracking per barangay center.
- **Module Repository:** Manage official DepEd Self-Learning Modules (SLMs) categorized by Learning Strands with toggle publishing.
- **Automated DepEd Exports:**
  - **ALS Form 1:** Individual Learning Agreement and Masterlist.
  - **ALS Form 2:** Official Learner Enrolment and Profile Form.

### 4. Learner Portal
- Real-time intake status tracker with document compliance checklist.
- Built-in PDF module viewer modal for self-paced study.
- Module progress telemetry tracking.

---

## 🛠️ Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x | PHP 8.2.12 (CLI Visual C++ 2019 x64) |
| **API Authentication**| Laravel Sanctum | Bearer token authentication for web & mobile |
| **Frontend UI** | Blade + Vite v7.3.6 | Tailwind CSS 4, Alpine.js, Plus Jakarta Sans |
| **Database** | MySQL | Port 3306 (`als_sdosanjuan_db`) via XAMPP |
| **PDF Generation** | Barryvdh DomPDF | DepEd Form 1 & Form 2 printable formats |
| **Version Control** | Git 2.54.0 | Tracked on branch `main` |
| **Mobile App (Phase 4)** | Native Android | Kotlin, Room Database, Retrofit 2, WorkManager |

---

## 💻 Local Development Setup

### Prerequisites
- **PHP 8.2 or higher**
- **Composer 2.x**
- **Node.js 18+ and npm**
- **MySQL / MariaDB** (via XAMPP or standalone)
- **Git**

---

### 📥 How to Download & Install Git

If Git is not yet installed on your system, follow the steps for your Operating System:

#### 🪟 Windows (Recommended)
1. **Direct Download:**
   - Go to the official Git download page: **[https://git-scm.com/download/win](https://git-scm.com/download/win)**
   - Click **"Click here to download"** (64-bit Git for Windows Setup).
   - Run the downloaded `.exe` file.
   - Keep the default recommended options selected during setup (ensure *"Git from the command line and also from 3rd-party software"* and *"Git Bash"* are checked).
   - Click **Next** until the installation completes, then click **Finish**.

2. **Or Install via Windows Terminal / PowerShell (Using winget):**
   ```powershell
   winget install --id Git.Git -e --source winget
   ```

#### 🍎 macOS
- If you use **Homebrew**:
  ```bash
  brew install git
  ```
- Or download the binary installer from: **[https://git-scm.com/download/mac](https://git-scm.com/download/mac)**

#### 🐧 Linux (Ubuntu / Debian)
```bash
sudo apt update
sudo apt install git -y
```

#### ✅ Verify Installation
After installing, open a new terminal (Command Prompt, PowerShell, or Git Bash) and run:
```bash
git --version
```
*Expected output: `git version 2.x.x`*

#### ⚙️ Initial Git Configuration (First-Time Setup)
Set your name and email address in Git:
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

---

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Renren051/als_sdosanjuan.git
   cd als_sdosanjuan
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Update your `.env` database parameters:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=als_sdosanjuan_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Build Frontend Assets (Vite):**
   ```bash
   # For active development with Hot Module Replacement (HMR):
   npm run dev

   # Or compile production bundle:
   npm run build
   ```

6. **Start the Development Server:**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```
   Visit the portal in your browser at: **`http://127.0.0.1:8000`**

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
| **Administrator** | `admin_als` | `Password123!` | Full Division Administrative Access |
| **Mobile Teacher** | `teacher_juan` | `Password123!` | CLC & Learner Verification Portal |
| **Learner** | `learner_mark` *(LRN: `136542240001`)* | `Password123!` | Learner Dashboard & SLM Modules |

---

## 📡 Core REST API Endpoints

The API is fully secured via Laravel Sanctum and ready for Android LMS integration:

### Authentication
- `POST /api/v1/auth/login` — Authenticate and receive Sanctum bearer token.
- `POST /api/v1/auth/register` — Digital learner registration (ALS Form 2).
- `GET /api/v1/auth/me` — Retrieve active authenticated user profile.
- `POST /api/v1/auth/logout` — Revoke active token.

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

The system includes a PHPUnit test suite covering API authentication, module repository, offline telemetry sync, enrollment validation, and web portal access:

```bash
php artisan test
```

**Results:**
```text
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\AlsApiTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\WebPortalTest

Tests:    17 passed (64 assertions)
Duration: 1.47s
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
