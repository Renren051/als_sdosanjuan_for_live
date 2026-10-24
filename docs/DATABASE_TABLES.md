# ALS SDO San Juan Database Tables

This document is a consolidated copy of the table structures defined by the Laravel migrations in this project. It describes the schema only; it does not contain learner, account, or other database row data. Types use Laravel migration names (for example, `string` means a variable-length string and `foreignId` means an unsigned big integer).

## Application tables

### `users`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `lrn` | String(12), nullable, unique |
| `username` | String, unique |
| `email` | String, nullable, unique |
| `email_verified_at` | Timestamp, nullable |
| `password` | String |
| `role` | Enum: `super_admin`, `admin`, `mobile_teacher`, `learner`; default `learner` |
| `status` | Enum: `active`, `inactive`; default `active` |
| `last_login_at` | Timestamp, nullable |
| `remember_token` | Remember token, nullable |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: (`role`, `status`), `lrn`, and `username`.

### `user_profiles`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `user_id` | Foreign key to `users.id`, unique, cascade on delete |
| `first_name` | String |
| `middle_name` | String, nullable |
| `last_name` | String |
| `name_extension` | String(10), nullable |
| `birthdate` | Date |
| `gender` | Enum: `male`, `female`, `other`; default `male` |
| `civil_status` | Enum: `single`, `married`, `widowed`, `separated`, `solo_parent`, `other`; default `single` |
| `religion` | String, nullable |
| `contact_number` | String(25) |
| `facebook_name` | String, nullable |
| `street_address` | String |
| `barangay` | String |
| `city` | String; default `San Juan City` |
| `province` | String; default `Metro Manila` |
| `highest_education_attained` | String |
| `dropout_reason` | String, nullable |
| `has_attended_als` | Boolean; default `false` |
| `previous_als_program` | String, nullable |
| `previous_als_year` | String(20), nullable |
| `previous_als_completed` | Boolean, nullable |
| `previous_als_incomplete_reason` | String, nullable |
| `mother_tongue` | String; default `Tagalog / Filipino` |
| `is_ip` | Boolean; default `false` |
| `ip_community_name` | String, nullable |
| `is_pwd` | Boolean; default `false` |
| `pwd_disability_type` | String, nullable |
| `is_4ps_beneficiary` | Boolean; default `false` |
| `father_name` | String, nullable |
| `mother_name` | String, nullable |
| `guardian_name` | String, nullable |
| `guardian_relationship` | String, nullable |
| `emergency_contact_name` | String, nullable |
| `emergency_contact_number` | String(25), nullable |
| `profile_photo_path` | String, nullable |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: `barangay` and (`last_name`, `first_name`).

### `community_learning_centers`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `clc_name` | String |
| `clc_code` | String(30), unique |
| `clc_type` | String; default `Community Center` |
| `barangay` | String |
| `physical_location_notes` | Text, nullable |
| `assigned_teacher_id` | Foreign key to `users.id`, nullable, set null on delete |
| `status` | Enum: `active`, `inactive`; default `active` |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: `barangay`, `assigned_teacher_id`, and `status`.

### `enrollments`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `user_id` | Foreign key to `users.id`, cascade on delete |
| `calendar_year` | String(10); default `2026` |
| `program_track` | Enum: `BLP`, `A&E_Elementary`, `A&E_Junior_High` |
| `clc_id` | Foreign key to `community_learning_centers.id`, cascade on delete |
| `assigned_teacher_id` | Foreign key to `users.id`, nullable, set null on delete |
| `enrollment_status` | Enum: `pending`, `under_review`, `enrolled`, `needs_correction`, `rejected`, `withdrawn`, `completed`; default `pending` |
| `delivery_mode` | Enum: `modular_offline`, `modular_print`, `face_to_face`, `blended`; default `modular_offline` |
| `schedule_preference` | Enum: `weekend`, `weekday_am`, `weekday_pm`, `flexible`; default `flexible` |
| `remarks` | Text, nullable |
| `enrolled_at` | Timestamp, nullable |
| `completed_at` | Timestamp, nullable |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: (`user_id`, `calendar_year`), (`clc_id`, `enrollment_status`), `enrollment_status`, `calendar_year`, and `program_track`.

### `learner_documents`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `user_id` | Foreign key to `users.id`, cascade on delete |
| `enrollment_id` | Foreign key to `enrollments.id`, nullable, cascade on delete |
| `document_type` | Enum: `psa_birth_cert`, `barangay_clearance`, `form_137_sf10`, `photo_id`, `portfolio_document` |
| `file_path` | String |
| `file_name` | String |
| `file_size` | Unsigned big integer (bytes) |
| `mime_type` | String(100) |
| `verification_status` | Enum: `pending`, `verified`, `rejected`; default `pending` |
| `verification_notes` | Text, nullable |
| `reviewed_by` | Foreign key to `users.id`, nullable, set null on delete |
| `reviewed_at` | Timestamp, nullable |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: (`user_id`, `document_type`), `verification_status`, and `enrollment_id`.

### `learning_strands`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `code` | String(20), unique |
| `name` | String |
| `description` | Text, nullable |
| `color_code` | String(30); default `#2563eb` |
| `icon_name` | String(50); default `book-open` |
| `order_seq` | Unsigned integer; default `1` |
| `created_at`, `updated_at` | Timestamps |

Additional indexes: `code` and `order_seq`.

### `modules`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `strand_id` | Foreign key to `learning_strands.id`, cascade on delete |
| `level` | Enum: `BLP`, `Elementary`, `Junior_High` |
| `sub_level` | String(50), nullable |
| `language` | String(20); default `English` |
| `module_number` | String(50) |
| `title` | String |
| `description` | Text, nullable |
| `pdf_file_path` | String |
| `pdf_file_size` | Unsigned big integer; default `0` |
| `version` | String(20); default `1.0` |
| `is_published` | Boolean; default `true` |
| `uploaded_by` | Foreign key to `users.id`, cascade on delete |
| `created_at`, `updated_at` | Timestamps |
| `deleted_at` | Timestamp, nullable (soft delete) |

Additional indexes: (`strand_id`, `level`), (`level`, `is_published`), `is_published`, (`level`, `sub_level`, `language`), `sub_level`, and `language`.

### `module_downloads_and_progress`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `user_id` | Foreign key to `users.id`, cascade on delete |
| `module_id` | Foreign key to `modules.id`, cascade on delete |
| `downloaded_at` | Timestamp, nullable |
| `last_opened_at` | Timestamp, nullable |
| `completion_status` | Enum: `downloaded`, `in_progress`, `completed`; default `downloaded` |
| `device_identifier` | String, nullable |
| `last_page_read` | Unsigned integer; default `1` |
| `total_pages` | Unsigned integer, nullable |
| `synced_at` | Timestamp, nullable |
| `created_at`, `updated_at` | Timestamps |

Unique constraint: (`user_id`, `module_id`). Additional indexes: `completion_status` and `synced_at`.

### `module_game_attempts`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `attempt_uuid` | UUID |
| `user_id` | Foreign key to `users.id`, cascade on delete |
| `module_id` | Foreign key to `modules.id`, cascade on delete |
| `game_type` | String(50); default `memory_match` |
| `score` | Unsigned small integer |
| `max_score` | Unsigned small integer |
| `moves` | Unsigned small integer |
| `elapsed_seconds` | Unsigned integer |
| `device_identifier` | String(150), nullable |
| `attempted_at` | Timestamp |
| `created_at`, `updated_at` | Timestamps |

Unique constraint: (`user_id`, `attempt_uuid`). Additional indexes: (`user_id`, `attempted_at`) and (`module_id`, `attempted_at`).

### `hero_banners`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `title` | String, nullable |
| `subtitle` | String, nullable |
| `image_path` | String |
| `position_x` | Integer; default `50` |
| `position_y` | Integer; default `50` |
| `zoom` | Unsigned small integer; default `100` |
| `button_text` | String, nullable; default `Take A Tour` |
| `button_url` | String, nullable; default `/register` |
| `order_seq` | Integer; default `0` |
| `is_active` | Boolean; default `true` |
| `created_at`, `updated_at` | Timestamps |

## Laravel support tables

### `password_reset_tokens`

| Column | Type / constraints |
|---|---|
| `email` | String, primary key |
| `token` | String |
| `created_at` | Timestamp, nullable |

### `sessions`

| Column | Type / constraints |
|---|---|
| `id` | String, primary key |
| `user_id` | Foreign ID, nullable, indexed |
| `ip_address` | String(45), nullable |
| `user_agent` | Text, nullable |
| `payload` | Long text |
| `last_activity` | Integer, indexed |

### `cache`

| Column | Type / constraints |
|---|---|
| `key` | String, primary key |
| `value` | Medium text |
| `expiration` | Integer, indexed |

### `cache_locks`

| Column | Type / constraints |
|---|---|
| `key` | String, primary key |
| `owner` | String |
| `expiration` | Integer, indexed |

### `jobs`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `queue` | String, indexed |
| `payload` | Long text |
| `attempts` | Unsigned tiny integer |
| `reserved_at` | Unsigned integer, nullable |
| `available_at` | Unsigned integer |
| `created_at` | Unsigned integer |

### `job_batches`

| Column | Type / constraints |
|---|---|
| `id` | String, primary key |
| `name` | String |
| `total_jobs` | Integer |
| `pending_jobs` | Integer |
| `failed_jobs` | Integer |
| `failed_job_ids` | Long text |
| `options` | Medium text, nullable |
| `cancelled_at` | Integer, nullable |
| `created_at` | Integer |
| `finished_at` | Integer, nullable |

### `failed_jobs`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `uuid` | String, unique |
| `connection` | Text |
| `queue` | Text |
| `payload` | Long text |
| `exception` | Long text |
| `failed_at` | Timestamp; default current timestamp |

### `personal_access_tokens`

| Column | Type / constraints |
|---|---|
| `id` | Big integer, primary key, auto-increment |
| `tokenable_type` | String (polymorphic relation) |
| `tokenable_id` | Unsigned big integer (polymorphic relation) |
| `name` | Text |
| `token` | String(64), unique |
| `abilities` | Text, nullable |
| `last_used_at` | Timestamp, nullable |
| `expires_at` | Timestamp, nullable, indexed |
| `created_at`, `updated_at` | Timestamps |

## Main relationships

- `users` has one `user_profiles` row and can have many enrollments, documents, module progress rows, and game attempts.
- `enrollments` reference a learner (`users`), a community learning center, and optionally an assigned teacher (`users`).
- `learner_documents` reference a learner, optionally an enrollment, and optionally the user who reviewed the document.
- `modules` reference a learning strand and the user who uploaded the module.
- `module_downloads_and_progress` connects a user to a module and stores that learner's progress for that module.
- `module_game_attempts` record individual user attempts associated with a module.
- `personal_access_tokens` use a polymorphic `tokenable_type` / `tokenable_id` relation.
