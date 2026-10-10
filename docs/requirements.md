# Dental Clinic Management SaaS — Requirements & Specification Document

## 1. Product Overview & Vision
Dental Clinic SaaS ek multi-tenant cloud-based clinic management system hai jo dental clinics ke daily operations, appointments, patient clinical records, treatments, billing aur follow-ups ko streamline karta hai.

* **Business Model:** Multi-Tenant SaaS (Ek codebase, multiple clinics with isolated data).
* **Primary Platform:** Responsive Web Application (Desktop for Clinic Admin/Doctor/Receptionist, Mobile-friendly for Patients).
* **Core Value Proposition:** Missed appointments kam karna, patient retention/recall barhana, manual diary aur phone booking ko automated calendar se replace karna, aur clear payment/treatment tracking provide karna.

---

## 2. Scope Definition

### 2.1 In-Scope (MVP - Phase 1 Release)
| Module / Feature | Description | Status |
| :--- | :--- | :--- |
| **Clinic Registration & Onboarding** | Clinic creation, branding (name, slug, logo, contact, timings). | In MVP |
| **Authentication & RBAC** | Secure login, session management, role-based authorization. | In MVP |
| **Public Clinic Website** | Dynamic public portal (`/clinic/{slug}`) for clinic info & booking. | In MVP |
| **Appointment Booking Engine** | Conflict-free slot booking, doctor schedules, calendar view. | In MVP |
| **Patient Management** | Patient profiles, search by phone/name, medical history tags. | In MVP |
| **Doctor Timings & Schedule** | Shift management, working days, break slots, slot durations. | In MVP |
| **Treatment Records** | Visit-wise procedures, clinical notes, multi-step progress tracking. | In MVP |
| **Invoices & Payments** | Itemized billing, partial payments, outstanding balance tracking. | In MVP |
| **Internal Follow-up Queue** | Daily task dashboard for recall, crown fittings, checkups. | In MVP |
| **Multi-Tenant Isolation** | Database scoping on every query via `clinic_id`. | In MVP |

### 2.2 Out-of-Scope (Future Phases / Post-MVP)
* Automated WhatsApp Cloud API Notifications (Scheduled for Phase 2).
* Native Mobile App (iOS / Android) — PWA/Responsive Web first.
* AI-driven Automated Diagnosis or Radiograph (X-Ray) Interpretation.
* Integration with third-party Dental Hardware / Imaging Sensors.
* Multi-branch inventory tracking (Raw dental consumables).

---

## 3. User Roles & Permission Matrix

| Role | Target User | Key Responsibilities & Access Boundaries |
| :--- | :--- | :--- |
| **Platform Owner** | SaaS Super Admin | Manages clinics, subscription plans, platform telemetry. Routine medical records access strictly restricted. |
| **Clinic Admin** | Clinic Owner / Head Doctor | Full access to clinic settings, staff management, financial reports, all clinic patients & appointments. |
| **Receptionist** | Front Desk Staff | Manages appointments, registers patients, records payments, handles follow-up calls. Cannot view confidential clinical notes. |
| **Dentist** | Associate / Consulting Doctor | Views assigned appointments, manages treatment records, dental notes, prescriptions for assigned patients. Read-only for billing summaries. |
| **Patient** | End Customer | Public booking form, selects service, doctor, date/time, confirms appointment. No dashboard login required in MVP. |

### Role-Based Access Control (RBAC) Matrix

| Feature / Action | Platform Owner | Clinic Admin | Receptionist | Dentist | Patient (Public) |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Register Clinic | Yes | Yes (Self) | No | No | No |
| Manage Staff & Roles | Platform-wide | Clinic only | No | No | No |
| Configure Services & Fees | No | Yes | View only | View only | View only |
| Public Booking Submission | No | No | No | No | Yes |
| Calendar & Booking Management | No | Full | Full | Assigned Only | No |
| Patient Contact Information | No | Full | Full | Full | No |
| Confidential Clinical Notes | No | Authorized | No | Authorized | No |
| Create Invoice & Record Payment| No | Full | Full | Read-only | No |
| View Financial Reports | SaaS Revenue | Clinic Revenue | No | No | No |

---

## 4. Module Specifications & Workflows

### 4.1 Clinic Onboarding & Public Portal
1. **Clinic Slug:** Har clinic ka unique URL slug hoga (e.g. `yourplatform.com/clinic/city-dental`).
2. **Branding Elements:** Name, logo, address, phone number, WhatsApp contact, operating hours, emergency notices.
3. **Services Catalog:** List of dental treatments with estimated duration and starting prices.

### 4.2 Appointment Booking Engine
1. **Conflict Resolution:** System strictly rejects overlapping bookings for the same doctor.
2. **Booking Lifecycle:**
   `Pending` → `Confirmed` → `Checked In` → `In Progress` → `Completed` | `Cancelled` | `No Show`
3. **Concurrency Protection:** Database-level transactions ensure two patients submitting the same slot simultaneously cannot create duplicate appointments.

### 4.3 Patient Clinical Management
1. **Directory:** Quick lookup by patient name, phone number, or CNIC/patient ID.
2. **Clinical Profile:**
   - Demographics & Emergency Contact.
   - Medical Alerts (e.g., Allergies, High BP, Diabetes, Bleeding disorders).
   - Past Treatment History & Visit Timeline.
   - Active Treatment Plans (e.g., Root Canal: Step 2 of 4).

### 4.4 Invoicing & Financial Balance
1. **Line Item Billing:** Multiple procedures per invoice with quantities and standard fees.
2. **Balance Calculation:** 
   $$\text{Balance Due} = \text{Invoice Total} - \sum(\text{Verified Payments})$$
3. **No Overwrites:** Har payment record immutable entry hogi (Cash, Card, Online Transfer) with receipt number and audit timestamp.

### 4.5 Internal Follow-up & Recall System
1. **Trigger Points:** 
   - Post-treatment follow-up (e.g., 14 days after Root Canal for Crown).
   - Routine hygiene recall (e.g., 6 months after Scaling).
2. **Dashboard Queue:** Receptionist daily action list with patient name, phone, reason, and quick status toggle (`Contacted`, `Rescheduled`, `Declined`).

---

## 5. Security & Multi-Tenancy Architecture

1. **Tenant Isolation:**
   - Every domain query MUST include `where clinic_id = ?` or use Laravel Global Tenant Scopes.
   - Direct Object Reference (IDOR) testing: User A attempting `GET /patients/{id_from_clinic_b}` must result in `403 Forbidden` or `404 Not Found`.
2. **Data Privacy & Medical Ethics:**
   - Encrypted data transfer (HTTPS/TLS mandatory).
   - Password hashing via Argon2id / Bcrypt.
   - Sensitive clinical files (X-rays, PDFs) stored in private, unguessable storage paths served via signed authorization middleware.
3. **Audit Trail:**
   - Timestamped logging for appointment status changes, clinical note updates, and payment collections.

---

## 6. Acceptance Criteria (Definition of Done for MVP)

1. **Isolation Test:** Clinic Admin A cannot view, edit, or delete any records belonging to Clinic B under any circumstance. [VERIFIED - Passed in `FinalAcceptanceCriteriaTest`]
2. **Double Booking Test:** Two simultaneous booking requests for Doctor D at 10:00 AM on Date X must result in exactly 1 successful booking and 1 clear "Slot already booked" error. [VERIFIED - Passed in `FinalAcceptanceCriteriaTest`]
3. **Financial Integrity Test:** Partial payments accurately reflect remaining balance; zero balance marks invoice `Paid`. [VERIFIED - Passed in `FinalAcceptanceCriteriaTest`]
4. **Mobile Responsive Test:** Public booking page and receptionist calendar are completely usable on standard smartphone viewports (375px+). [VERIFIED - Flex/Grid modern UI with drawer/hamburger layout]
5. **Role Boundary Test:** Receptionist role attempting to access doctor clinical notes or clinic settings receives an authorization error. [VERIFIED - Middleware `role:clinic_admin` and strict authorization checks]

---

## 7. Delivery Matrix & Verification (Phases 1-15 Completed)

| Phase | Milestone Description | Status | Verification Reference |
| :--- | :--- | :--- | :--- |
| **Phase 0** | Comprehensive Requirements, Domain Models, & Architecture Blueprint | ✅ Complete | `docs/requirements.md` |
| **Phase 1** | Local Environment Setup, Laragon MySQL, Laravel 12 Bootstrap | ✅ Complete | DB `dentalcare` configured & verified |
| **Phase 2** | Multi-Tenant Database Schema, Migrations, Models, `BelongsToClinic` Trait | ✅ Complete | 10 Migrations, 11 Models, Seeder |
| **Phase 3** | Authentication & RBAC (Super Admin, Clinic Admin, Dentist, Receptionist) | ✅ Complete | `MultiTenantAuthTest` (9 tests) |
| **Phase 4** | Public Clinic Landing Page & Online Slot Booking Engine | ✅ Complete | `PublicBookingTest` (7 tests) |
| **Phase 5** | Appointment Calendar, Lifecycle Transitions & Conflict Prevention | ✅ Complete | `AppointmentManagementTest` (6 tests) |
| **Phase 6** | Patient Directory, Search, Dossier, Medical Alerts & Confidentiality | ✅ Complete | `PatientManagementTest` (7 tests) |
| **Phase 7** | Clinical Treatments, Interactive FDI Odontogram, Rx Slips, Recalls | ✅ Complete | `ClinicalTreatmentTest` (9 tests) |
| **Phase 8** | Dynamic Invoicing, Financial KPIs, Counter Payments, Printable Receipts | ✅ Complete | `BillingInvoicingTest` (10 tests) |
| **Phase 9** | Follow-Up & Recall Queue (`/follow-ups`), 1-Click WhatsApp Reminders | ✅ Complete | `FollowUpManagementTest` (6 tests) |
| **Phase 10** | Doctor Rostering, Working Shifts, Consultation Fees & Chair Timetable | ✅ Complete | `DoctorRosterTest` (7 tests) |
| **Phase 11** | Dental Services Catalog, Categories, Durations, Default Pricing | ✅ Complete | `ServiceCatalogTest` (7 tests) |
| **Phase 12** | Financial Analytics, Revenue Reports, Productivity Matrix & Print Audit | ✅ Complete | `FinancialReportsTest` (7 tests) |
| **Phase 13** | Clinic Staff Management (`/admin/staff`) & Workspace Settings (`/admin/settings`) | ✅ Complete | `ClinicStaffAndSettingsTest` (9 tests) |
| **Phase 14** | Platform Super Owner Portal (`/platform`), Multi-Clinic Controls & Telemetry | ✅ Complete | `PlatformAdminTest` (8 tests) |
| **Phase 15** | End-to-End Acceptance Tests, System Hardening & Production Polish | ✅ Complete | `FinalAcceptanceCriteriaTest` (5/5 tests, 89/89 total tests) |

