# Dental Clinic Management SaaS — Database & System Architecture

## 1. High-Level Architecture Overview

System Multi-Tenant Single Database Architecture follow karega, jahan har tenant-owned table mein `clinic_id` foreign key hogi.

```
                         [ Public Patient Browser / Mobile ]
                                         │
                                         ▼
                             [ Public Booking Portal ]
                             /clinic/{slug}/book
                                         │
                                         ▼
[ Admin / Receptionist / Doctor ] ──► [ Laravel Backend API & Livewire ]
                                         │
                      ┌──────────────────┴──────────────────┐
                      ▼                                     ▼
             [ Auth & Tenant Scope ]               [ Business Logic ]
             - Multi-Tenant Middleware             - Conflict Detection
             - Role-Based Policy                   - Financial Integrity
                      │                                     │
                      └──────────────────┬──────────────────┘
                                         ▼
                             [ MySQL Database ]
                         - clinic_id scoped queries
                         - Foreign Key Constraints
                         - Decimal Financial Storage
```

---

## 2. Core Relational Schema

### 2.1 `clinics`
Platform par registered har clinic ka master record.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `name` (VARCHAR 255)
* `slug` (VARCHAR 100, UNIQUE, INDEX) — e.g. `shifa-dental`
* `phone` (VARCHAR 30)
* `email` (VARCHAR 255, NULLABLE)
* `address` (TEXT)
* `logo_path` (VARCHAR 255, NULLABLE)
* `currency` (VARCHAR 10, DEFAULT 'PKR')
* `timezone` (VARCHAR 50, DEFAULT 'Asia/Karachi')
* `status` (ENUM: 'active', 'inactive', 'suspended', DEFAULT 'active')
* `created_at`, `updated_at` (TIMESTAMP)

### 2.2 `users`
Clinic staff, dentists, clinic owners aur platform admins.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, NULLABLE for Platform Owner, INDEX)
* `name` (VARCHAR 255)
* `email` (VARCHAR 255, UNIQUE)
* `password` (VARCHAR 255)
* `phone` (VARCHAR 30, NULLABLE)
* `role` (ENUM: 'platform_owner', 'clinic_admin', 'dentist', 'receptionist')
* `is_active` (BOOLEAN, DEFAULT TRUE)
* `remember_token` (VARCHAR 100, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.3 `doctor_profiles`
Dentist-specific configuration aur details.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `user_id` (BIGINT, FK -> users.id, UNIQUE)
* `specialization` (VARCHAR 255) — e.g. Orthodontist, Endodontist
* `consultation_fee` (DECIMAL 10,2, DEFAULT 0.00)
* `slot_duration_minutes` (INT, DEFAULT 30)
* `bio` (TEXT, NULLABLE)
* `working_days` (JSON) — e.g. `["monday", "tuesday", "wednesday", "thursday", "friday", "saturday"]`
* `start_time` (TIME, DEFAULT '10:00:00')
* `end_time` (TIME, DEFAULT '20:00:00')
* `break_start` (TIME, NULLABLE)
* `break_end` (TIME, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.4 `patients`
Clinic ke registered patients.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `name` (VARCHAR 255)
* `phone` (VARCHAR 30, INDEX)
* `email` (VARCHAR 255, NULLABLE)
* `date_of_birth` (DATE, NULLABLE)
* `gender` (ENUM: 'male', 'female', 'other', NULLABLE)
* `blood_group` (VARCHAR 10, NULLABLE)
* `address` (TEXT, NULLABLE)
* `medical_history` (TEXT, NULLABLE) — Allergies, systemic conditions
* `created_at`, `updated_at` (TIMESTAMP)

### 2.5 `services`
Treatments catalog with pricing.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `name` (VARCHAR 255) — e.g. Scaling & Polishing, Root Canal, Composite Filling
* `description` (TEXT, NULLABLE)
* `standard_fee` (DECIMAL 10,2)
* `estimated_duration_minutes` (INT, DEFAULT 30)
* `is_active` (BOOLEAN, DEFAULT TRUE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.6 `appointments`
Patient booking aur schedule calendar.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `patient_id` (BIGINT, FK -> patients.id, INDEX)
* `doctor_id` (BIGINT, FK -> users.id, INDEX)
* `service_id` (BIGINT, FK -> services.id, NULLABLE)
* `appointment_date` (DATE, INDEX)
* `starts_at` (TIME, INDEX)
* `ends_at` (TIME)
* `status` (ENUM: 'pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show', DEFAULT 'pending')
* `patient_notes` (TEXT, NULLABLE)
* `cancellation_reason` (TEXT, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.7 `treatment_records`
Clinical execution history.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `patient_id` (BIGINT, FK -> patients.id, INDEX)
* `doctor_id` (BIGINT, FK -> users.id, INDEX)
* `appointment_id` (BIGINT, FK -> appointments.id, NULLABLE)
* `service_id` (BIGINT, FK -> services.id, NULLABLE)
* `tooth_numbers` (VARCHAR 100, NULLABLE) — e.g. '16, 26'
* `diagnosis` (TEXT)
* `treatment_rendered` (TEXT)
* `clinical_notes` (TEXT, NULLABLE) — Restricted to authorized roles
* `prescription` (TEXT, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.8 `invoices` & `invoice_items`
Billing records.
* **`invoices`**:
  * `id` (BIGINT, PK, AUTO_INCREMENT)
  * `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
  * `patient_id` (BIGINT, FK -> patients.id, INDEX)
  * `appointment_id` (BIGINT, FK -> appointments.id, NULLABLE)
  * `invoice_number` (VARCHAR 50, INDEX) — e.g. `INV-2026-0001`
  * `total_amount` (DECIMAL 10,2)
  * `discount_amount` (DECIMAL 10,2, DEFAULT 0.00)
  * `paid_amount` (DECIMAL 10,2, DEFAULT 0.00)
  * `balance_amount` (DECIMAL 10,2, DEFAULT 0.00)
  * `status` (ENUM: 'unpaid', 'partially_paid', 'paid', 'cancelled', DEFAULT 'unpaid')
  * `due_date` (DATE, NULLABLE)
  * `created_at`, `updated_at` (TIMESTAMP)

* **`invoice_items`**:
  * `id` (BIGINT, PK, AUTO_INCREMENT)
  * `invoice_id` (BIGINT, FK -> invoices.id, ON DELETE CASCADE)
  * `service_id` (BIGINT, FK -> services.id, NULLABLE)
  * `description` (VARCHAR 255)
  * `unit_price` (DECIMAL 10,2)
  * `quantity` (INT, DEFAULT 1)
  * `total_price` (DECIMAL 10,2)

### 2.9 `payments`
Recorded financial transactions.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `invoice_id` (BIGINT, FK -> invoices.id, INDEX)
* `patient_id` (BIGINT, FK -> patients.id, INDEX)
* `received_by_user_id` (BIGINT, FK -> users.id)
* `amount` (DECIMAL 10,2)
* `payment_method` (ENUM: 'cash', 'card', 'bank_transfer', 'easypaisa_jazzcash')
* `transaction_reference` (VARCHAR 100, NULLABLE)
* `payment_date` (DATETIME)
* `notes` (TEXT, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)

### 2.10 `follow_ups`
Internal queue for patient recalls and multi-visit tracking.
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `clinic_id` (BIGINT, FK -> clinics.id, INDEX)
* `patient_id` (BIGINT, FK -> patients.id, INDEX)
* `treatment_record_id` (BIGINT, FK -> treatment_records.id, NULLABLE)
* `due_date` (DATE, INDEX)
* `reason` (VARCHAR 255) — e.g. "Crown fitting after 14 days", "6-Month Dental Cleaning"
* `status` (ENUM: 'pending', 'contacted', 'booked', 'cancelled', DEFAULT 'pending')
* `last_contacted_at` (DATETIME, NULLABLE)
* `staff_notes` (TEXT, NULLABLE)
* `created_at`, `updated_at` (TIMESTAMP)
