# ALS SDO San Juan City Entity Relationship Diagram

This diagram summarizes the application data model defined by the Laravel migrations. It shows primary keys, foreign keys, and the main identifying or business fields; timestamps and other non-key detail fields are omitted to keep the ERD readable. See [DATABASE_TABLES.md](DATABASE_TABLES.md) for the full column inventory.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string lrn UK
        string username UK
        string email UK
        string role
        string status
    }

    USER_PROFILES {
        bigint id PK
        bigint user_id FK,UK
        string first_name
        string last_name
        date birthdate
        string barangay
        string highest_education_attained
    }

    COMMUNITY_LEARNING_CENTERS {
        bigint id PK
        string clc_code UK
        string clc_name
        string barangay
        bigint assigned_teacher_id FK
        string status
    }

    ENROLLMENTS {
        bigint id PK
        bigint user_id FK
        bigint clc_id FK
        bigint assigned_teacher_id FK
        string calendar_year
        string program_track
        string enrollment_status
    }

    LEARNER_DOCUMENTS {
        bigint id PK
        bigint user_id FK
        bigint enrollment_id FK
        bigint reviewed_by FK
        string document_type
        string verification_status
        string file_path
    }

    LEARNING_STRANDS {
        bigint id PK
        string code UK
        string name
        int order_seq
    }

    MODULES {
        bigint id PK
        bigint strand_id FK
        bigint uploaded_by FK
        string level
        string sub_level
        string language
        string module_number
        string title
        boolean is_published
    }

    MODULE_DOWNLOADS_AND_PROGRESS {
        bigint id PK
        bigint user_id FK
        bigint module_id FK
        string completion_status
        int last_page_read
    }

    MODULE_GAME_ATTEMPTS {
        bigint id PK
        bigint user_id FK
        bigint module_id FK
        uuid attempt_uuid
        string game_type
        int score
        timestamp attempted_at
    }

    HERO_BANNERS {
        bigint id PK
        string title
        string image_path
        int order_seq
        boolean is_active
    }

    USERS ||--o| USER_PROFILES : has
    USERS o|--o{ COMMUNITY_LEARNING_CENTERS : assigned_to
    USERS ||--o{ ENROLLMENTS : learner
    COMMUNITY_LEARNING_CENTERS ||--o{ ENROLLMENTS : hosts
    USERS o|--o{ ENROLLMENTS : assigned_teacher
    USERS ||--o{ LEARNER_DOCUMENTS : owns
    ENROLLMENTS o|--o{ LEARNER_DOCUMENTS : includes
    USERS o|--o{ LEARNER_DOCUMENTS : reviews
    LEARNING_STRANDS ||--o{ MODULES : contains
    USERS ||--o{ MODULES : uploads
    USERS ||--o{ MODULE_DOWNLOADS_AND_PROGRESS : has_progress
    MODULES ||--o{ MODULE_DOWNLOADS_AND_PROGRESS : tracks
    USERS ||--o{ MODULE_GAME_ATTEMPTS : makes
    MODULES ||--o{ MODULE_GAME_ATTEMPTS : has_attempts
```

## Relationship notes

- `USER_PROFILES.user_id` is unique, so a user has at most one profile. Each profile belongs to one user.
- Enrollment records identify a learner, a community learning center, and optionally an assigned teacher. `users` appears in more than one role because the same table stores learners and staff.
- A learner document belongs to a user and may optionally be attached to an enrollment. Its reviewer is also an optional user reference.
- `MODULE_DOWNLOADS_AND_PROGRESS` is the learner-to-module association and allows one progress row per user and module.
- `MODULE_DOWNLOADS_AND_PROGRESS` has a composite unique key on `(user_id, module_id)`; neither foreign key is unique by itself.
- `MODULE_GAME_ATTEMPTS` stores multiple attempts per learner and module. The attempt UUID is unique per user.
- `HERO_BANNERS` has no foreign-key relationships in the migrations.
- Laravel support tables are not shown. `personal_access_tokens` uses a polymorphic `tokenable_type` / `tokenable_id` pair, and the framework tables have no ERD relationship to the application data model.
