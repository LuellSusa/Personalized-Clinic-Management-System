# TitaClinic Pediatric Clinic Management System

## System Documentation

**Document version:** 1.0  
**Prepared:** August 2026  
**System type:** Web-based pediatric clinic management system

---

## Table of Contents

1. Introduction
2. Purpose of the System
3. System Overview
4. General and Specific Objectives
5. Scope of the System
6. System Users and Roles
7. Functional Modules
8. Major System Workflows
9. Appointment and Branch Business Rules
10. System Architecture
11. Technology Stack
12. Database Overview
13. User Interface and Frontend Structure
14. Security and Access Control
15. Validation and Data Integrity
16. Installation and Local Deployment
17. Testing and Verification
18. Current Limitations
19. Recommended Future Enhancements
20. Conclusion

---

## 1. Introduction

TitaClinic is a web-based pediatric clinic management system designed to improve the coordination of clinic appointments, patient information, user accounts, and daily administrative activities. The system provides a centralized platform where parents or guardians can register their children, select a clinic branch, request appointment dates, and review their booking history. Doctors can monitor assigned appointments using a calendar-based dashboard, while administrators can approve accounts and oversee clinic activity through system-wide key performance indicators.

The system was developed to replace fragmented or primarily manual clinic processes with a more organized and accessible digital workflow. It combines a Laravel backend, a React-based modern dashboard interface, and a MySQL database. This architecture allows the application to maintain secure server-side validation while providing responsive and interactive user interfaces.

TitaClinic focuses on pediatric care coordination. A parent account may manage one or more child patient profiles, and appointments are associated with both the parent and the selected child. Role-based access separates the functions available to administrators, doctors, and parents.

## 2. Purpose of the System

The primary purpose of TitaClinic is to provide a centralized and secure clinic portal that simplifies how parents, doctors, and administrators coordinate pediatric appointments and patient records.

The system is intended to:

- Reduce the amount of manual work involved in account registration and appointment scheduling.
- Allow parents to maintain organized child patient profiles.
- Give parents clear information regarding clinic branches, operating schedules, and available appointment dates.
- Allow doctors to view their scheduled patients using a calendar-based interface.
- Provide administrators with control over account approval, user roles, and account status.
- Maintain a reliable history of current, past, cancelled, completed, and missed appointments.
- Prevent unauthorized access to role-specific functions and records.
- Establish a technical foundation for future medical-record, immunization, and inventory features.

## 3. System Overview

TitaClinic is composed of three primary user areas:

### 3.1 Parent or Guardian Portal

The parent portal allows an approved user to:

- Complete or update a parent profile.
- Register one or more children as pediatric patients.
- Update child demographic and medical background information.
- Select a clinic branch and an available appointment date.
- Select an active doctor or request any available doctor.
- View upcoming and previous appointments.
- Edit an eligible upcoming appointment.
- Cancel an eligible appointment.
- Reschedule a missed or past appointment while retaining the original booking in the history.
- Review dashboard KPIs for registered children and appointment activity.

### 3.2 Doctor Portal

The doctor portal allows an approved doctor to:

- View appointment KPIs.
- Review today's bookings and upcoming appointments.
- View a monthly calendar containing assigned patient bookings.
- Identify dates with bookings through green calendar indicators.
- Open a selected date and review its patients.
- Review the patient, parent, branch, clinic hours, appointment type, and reason for the visit.
- Confirm, complete, cancel, or mark an appointment as a no-show according to permitted status transitions.

### 3.3 Administrator Portal

The administrator portal allows authorized staff to:

- View system-wide KPIs.
- Review the number of registered users, pending accounts, doctors, patients, and upcoming appointments.
- Review recently created appointments.
- View accounts waiting for approval.
- Assign a user role as parent, doctor, or administrator.
- Change an account status to pending, active, suspended, or inactive.
- Protect the system from losing its final active administrator.

## 4. General and Specific Objectives

### 4.1 General Objective

To design and develop a secure, accessible, and organized pediatric clinic management system that improves appointment coordination and information management among parents, doctors, and clinic administrators.

### 4.2 Specific Objectives

The system specifically aims to:

1. Provide an online registration and administrator-approval process for clinic users.
2. Implement role-based access for administrators, doctors, and parents.
3. Allow parents to create and manage pediatric patient profiles.
4. Provide branch-based appointment scheduling according to actual clinic operating days.
5. Maintain an appointment history that clearly distinguishes upcoming and past bookings.
6. Allow parents to reschedule missed appointments without deleting the original record.
7. Give doctors a live calendar view of assigned bookings.
8. Provide useful KPIs to parents, doctors, and administrators.
9. Store system information in a structured MySQL relational database.
10. Apply server-side validation, authorization checks, and secure password handling.

## 5. Scope of the System

### 5.1 Included Scope

The current implementation includes:

- Public landing page.
- Parent account registration.
- Account login, remember-me functionality, and logout.
- Administrator-controlled account activation.
- Role and account-status management.
- Parent profile management.
- Child patient creation, viewing, editing, and soft deletion.
- Branch-based appointment creation and editing.
- Appointment cancellation and rescheduling.
- Parent booking history.
- Parent dashboard with live KPIs.
- Administrator dashboard with live KPIs.
- Doctor dashboard with live KPIs and a booking calendar.
- Doctor appointment status management.
- MySQL database migrations and relational constraints.
- Responsive interfaces for desktop and mobile screen sizes.

### 5.2 Database Foundation Included but Not Yet Fully Exposed in the Interface

The database already contains structural support for:

- Doctor schedule blocks.
- Patient visits and clinical notes.
- Immunization definitions.
- Patient immunization records.
- Inventory items.
- Inventory batches.
- Inventory transactions.

These areas currently represent database groundwork. Complete models, controllers, interfaces, and workflows for these modules remain future development work.

### 5.3 Excluded from the Current Scope

The current version does not yet include:

- Online payment processing.
- SMS appointment notifications.
- Full email verification.
- Password-reset and password-change interfaces.
- Electronic prescription generation.
- Laboratory or diagnostic result management.
- Complete electronic medical records through the user interface.
- Automated appointment capacity limits or queue-number assignment.
- Public deployment and production hosting configuration.

## 6. System Users and Roles

### 6.1 Administrator

The administrator is responsible for controlling access to the system. New registrations remain pending until an administrator approves them. Administrators can assign roles and modify account status. Administrative functions cannot be accessed by doctors or parents.

### 6.2 Doctor

A doctor can view and manage only appointments assigned to their user account. Doctors do not have permission to modify user access or administrative settings. Appointment status actions follow controlled transitions to protect the accuracy of booking history.

### 6.3 Parent or Guardian

A parent or guardian can manage only their own profile, children, and appointments. Ownership checks prevent one parent from editing another parent's child or appointment. A parent must complete a parent profile before accessing children and appointment functions.

## 7. Functional Modules

### 7.1 Authentication Module

The authentication module provides:

- Account registration.
- Secure password hashing.
- Login using email and password.
- Remember-me support.
- Session regeneration after login.
- Logout and session invalidation.
- Blocking of pending, suspended, and inactive accounts.

### 7.2 Account Approval and Role Management Module

Newly registered accounts receive:

- Default role: `parent`
- Default status: `pending`

The administrator reviews the account and changes its status to `active`. The administrator may also assign the account as a doctor or administrator. At least one active administrator must remain in the system.

Account approval is currently performed inside the administrator portal. It is not email verification. No verification email is required before the administrator approves an account.

### 7.3 Parent Profile Module

The parent profile stores:

- Address.
- Eleven-digit emergency-contact number.
- Occupation.

The profile can be created or updated. Users without a parent profile are redirected to the profile form before they can manage patients or appointments.

### 7.4 Child Patient Module

The child patient module stores:

- Patient number.
- First, middle, and last name.
- Birth date.
- Sex.
- Blood type.
- Allergies.
- Medical conditions.
- Current medications.
- Active or inactive status.

Child records use soft deletion so that removing a record does not immediately erase it from the database.

### 7.5 Appointment Module

An appointment contains:

- Child patient.
- Parent profile.
- Assigned doctor, when selected.
- User who created the booking.
- Appointment type.
- Clinic branch.
- Appointment date.
- Reason for the appointment.
- Notes.
- Appointment status.

Parents do not select an exact start or end time. They select an available clinic date, and the interface displays the operating hours for the selected branch.

### 7.6 Booking History and Rescheduling Module

Appointment cards automatically appear grey when their scheduled date is earlier than the current date. The recorded appointment status remains visible for audit purposes.

If a past appointment remained scheduled, confirmed, or was marked as a no-show, the parent may reschedule it. Rescheduling:

1. Changes the original record to `no_show`.
2. Retains the original appointment in booking history.
3. Creates a new appointment with the selected branch and date.
4. Assigns the new appointment a `scheduled` status.

### 7.7 Dashboard Module

#### Parent Dashboard KPIs

- Registered children.
- Upcoming visits.
- Confirmed appointments.
- Completed visits.

#### Doctor Dashboard KPIs

- Today's bookings.
- Upcoming bookings.
- Confirmed bookings.
- Completed visits.

#### Administrator Dashboard KPIs

- Total users.
- Pending approvals.
- Active doctors.
- Active patients.
- Upcoming appointments.

## 8. Major System Workflows

### 8.1 Registration and Account Approval

1. The user opens the registration page.
2. The user supplies personal details, an email address, an optional eleven-digit phone number, and a password.
3. Laravel validates the submitted information.
4. The system creates a parent account with a pending status.
5. The user is redirected to the login page with an approval notice.
6. The administrator opens the User Access page.
7. The administrator assigns the appropriate role and changes the account status to active.
8. The user can then log in.

### 8.2 Parent and Child Setup

1. The parent logs in.
2. The parent completes their parent profile.
3. The parent opens the Children page.
4. The parent creates a child patient record.
5. The patient becomes available for appointment selection.

### 8.3 Appointment Booking

1. The parent opens the Book Appointment page.
2. The parent selects a child.
3. The parent selects an active doctor or any available doctor.
4. The parent selects an appointment type.
5. The parent chooses a clinic branch.
6. The branch calendar enables only valid dates for that location.
7. The parent selects an available date.
8. The parent optionally supplies a reason and notes.
9. Laravel validates child ownership, doctor status, branch, and date availability.
10. The appointment is created with a scheduled status.

### 8.4 Doctor Appointment Processing

1. The doctor logs in and opens the dashboard.
2. The monthly calendar highlights dates containing bookings in green.
3. Calendar information refreshes every 30 seconds.
4. The doctor selects a highlighted date.
5. The system opens the appointments assigned to the doctor for that date.
6. The doctor reviews patient and branch information.
7. The doctor confirms, completes, cancels, or marks the appointment as a no-show according to the current status.

### 8.5 Appointment Rescheduling

1. The system detects that an appointment date is earlier than the current date.
2. The booking card appears grey in the parent's booking history.
3. An eligible missed booking displays a Reschedule action.
4. The parent selects a new branch and available date.
5. The original record remains in history as a no-show.
6. A new scheduled appointment is created.

## 9. Appointment and Branch Business Rules

### 9.1 Clinic Branch Schedules

| Clinic Branch | Available Days | Operating Hours |
| --- | --- | --- |
| Chong Hua Medical Mall | Monday to Saturday | 7:00 AM–12:00 PM |
| Chong Hua Mandaue | Wednesday, Friday, and Saturday | 2:00 PM–7:00 PM |
| Mindiro Clinic | Monday, Tuesday, and Thursday | 2:00 PM–7:00 PM |

Branch schedules are defined centrally in `config/clinic.php`.

### 9.2 Date Selection Rules

- A parent cannot select a date before the current date.
- Only weekdays configured for the selected branch are enabled.
- Server-side validation rejects a date that does not belong to the selected branch schedule.
- The system does not require a start or end time from the parent.
- More than one patient may book the same doctor and date because bookings represent attendance during the branch's operating window rather than an exact time slot.

### 9.3 Appointment Status Rules

Permitted doctor transitions are:

| Current Status | Allowed Next Status |
| --- | --- |
| Scheduled | Confirmed or Cancelled |
| Confirmed | Completed, No-show, or Cancelled |
| Completed | No further transition |
| Cancelled | No further transition |
| No-show | No further doctor transition |

### 9.4 Past Appointment Rules

- A booking becomes visually historical when its date is earlier than today.
- Today's appointments are not treated as past appointments.
- Past scheduled, confirmed, and no-show records may be rescheduled by the parent.
- Completed and cancelled records remain historical but cannot be rescheduled through the current workflow.

## 10. System Architecture

TitaClinic uses a layered web application architecture:

```text
Web Browser
    ↓
Blade Mount View / React Interface
    ↓
Laravel Routes and Middleware
    ↓
Laravel Controllers and Validation
    ↓
Eloquent Models
    ↓
MySQL Database
```

### 10.1 Presentation Layer

React renders the landing page and the parent, doctor, and administrator dashboards. Blade currently renders the remaining forms and management pages. Each React page has a dedicated JSX component and CSS file.

### 10.2 Application Layer

Laravel controllers process requests, perform validation, enforce ownership rules, query the database, and prepare data for Blade or React.

### 10.3 Authorization Layer

Laravel authentication middleware requires login for protected pages. Custom role middleware restricts administrative, doctor, and parent routes. Parent-profile middleware ensures profile completion before patient or appointment access.

### 10.4 Data Layer

Eloquent ORM manages relationships and database operations. MySQL stores application data, while Laravel migrations define and version the database structure.

## 11. Technology Stack

| Technology | Purpose |
| --- | --- |
| PHP 8.2 | Backend programming language |
| Laravel 12 | Backend framework, routing, validation, authentication, and ORM |
| React 19 | Interactive dashboards and landing-page interface |
| JSX | React component and interface structure |
| JavaScript | Frontend behavior and calendar interaction |
| CSS | Dedicated page styling and responsive layout |
| Blade | Laravel view mounting and remaining server-rendered forms |
| Vite 7 | React and CSS development/build pipeline |
| MySQL/MariaDB | Relational database |
| phpMyAdmin | Database administration interface |
| Bootstrap 5 | Styling support for remaining Blade forms |
| PHPUnit 11 | Automated backend and feature testing |

## 12. Database Overview

The application uses the MySQL database named `titaclinic`.

Major application tables include:

- `users`
- `parent_profiles`
- `doctor_profiles`
- `children`
- `appointments`
- `schedule_blocks`
- `patient_visits`
- `immunizations`
- `patient_immunizations`
- `inventory_items`
- `inventory_batches`
- `inventory_transactions`

Laravel support tables include:

- `migrations`
- `sessions`
- `password_reset_tokens`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

### 12.1 ERD

**Insert the completed Entity Relationship Diagram in this section.**

Suggested caption:

> Figure 1. Entity Relationship Diagram of the TitaClinic Pediatric Clinic Management System.

The ERD should show the relationships among users, parent profiles, doctor profiles, children, appointments, visits, immunizations, and inventory records.

## 13. User Interface and Frontend Structure

### 13.1 React Pages

| Screen | JSX Structure | Dedicated CSS |
| --- | --- | --- |
| Landing page | `resources/js/react/pages/LandingPage.jsx` | `resources/css/pages/landing.css` |
| Parent dashboard | `resources/js/react/pages/DashboardPage.jsx` | `resources/css/pages/dashboard.css` |
| Administrator dashboard | `resources/js/react/pages/AdminDashboardPage.jsx` | `resources/css/pages/admin-dashboard.css` |
| Doctor dashboard | `resources/js/react/pages/DoctorDashboardPage.jsx` | `resources/css/pages/doctor-dashboard.css` |

### 13.2 Appointment Page Styling

| Screen | View | Dedicated CSS |
| --- | --- | --- |
| Appointment booking | `resources/views/appointments/create.blade.php` | `public/css/pages/appointment-form.css` |
| Appointment editing and rescheduling | `resources/views/appointments/edit.blade.php` | `public/css/pages/appointment-form.css` |
| Booking history | `resources/views/appointments/index.blade.php` | `public/css/pages/appointments-index.css` |

### 13.3 UI Screenshots

**Insert the completed UI screenshots in this section.**

Recommended screenshot order:

1. Public landing page.
2. Registration page.
3. Login page.
4. Parent dashboard.
5. Child-patient management page.
6. Branch appointment calendar.
7. Parent booking history.
8. Doctor KPI dashboard and calendar.
9. Administrator KPI dashboard.
10. Administrator user-approval page.

Each image should include a figure number, caption, and short explanation of the screen's purpose.

## 14. Security and Access Control

The current system implements the following protections:

- Passwords are automatically hashed before storage.
- Successful login regenerates the session identifier.
- Logout invalidates the session and regenerates the CSRF token.
- Protected routes require authentication.
- Role middleware restricts access by user role.
- Pending, suspended, and inactive accounts cannot log in.
- CSRF protection is included in forms and React-submitted status forms.
- Parent ownership checks protect child and appointment records.
- Appointment child validation confirms that the selected child belongs to the authenticated parent.
- Doctor validation requires an active user with the doctor role.
- Doctors may modify only appointments assigned to them.
- The final active administrator cannot be disabled or demoted.
- Database foreign keys preserve relational consistency.
- Soft deletion is used for child and appointment-related records where appropriate.

## 15. Validation and Data Integrity

Examples of implemented validation rules include:

- First name and last name are required during registration.
- Email addresses must be valid and unique.
- Passwords must be at least eight characters and confirmed.
- Phone and emergency-contact numbers must contain exactly eleven digits when supplied.
- Patient numbers must be unique.
- Child birth date and sex are required.
- Appointment type must be one of the supported values.
- Appointment branch must exist in the clinic configuration.
- Appointment date cannot be before today.
- Appointment date must match the selected branch's available weekdays.
- Appointment status must follow permitted values and transitions.
- User role and account status must use recognized values.

Validation is performed on the server even when the browser also provides input restrictions. This protects the application from manually modified requests.

## 16. Installation and Local Deployment

### 16.1 Requirements

- PHP 8.2 or newer.
- Composer.
- Node.js and npm.
- XAMPP MySQL/MariaDB or another compatible MySQL server.
- A web browser.

### 16.2 Database Configuration

The local `.env` configuration uses:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titaclinic
DB_USERNAME=root
DB_PASSWORD=
```

Production deployments must use a dedicated database account and a strong password rather than the local XAMPP root configuration.

### 16.3 Backend Setup

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 16.4 Frontend Setup

```powershell
npm.cmd install
npm.cmd run dev
```

For a production frontend build:

```powershell
npm.cmd run build
```

### 16.5 Development Administrator

The development seeder creates:

```text
Email: admin@titaclinic.test
Password: password
```

These credentials are intended only for local development and must be changed before production deployment.

## 17. Testing and Verification

The system uses PHPUnit feature and unit tests. The test suite currently covers important behaviors such as:

- React page mounting.
- Role-based route restrictions.
- Staff dashboard redirects.
- Parent-profile requirements.
- Parent ownership of appointments.
- Active-account login requirements.
- Administrator role and status management.
- Protection of the final active administrator.
- Doctor appointment ownership and status transitions.
- Multiple day bookings without time slots.
- Branch weekday validation.
- Past-appointment rescheduling and history preservation.
- Eleven-digit phone-number validation.
- Doctor calendar JSON data.

Current verification result:

```text
18 automated tests passed
67 assertions passed
React production build passed
PHP syntax validation passed
Blade template compilation passed
All MySQL migrations applied successfully
```

## 18. Current Limitations

The current implementation has the following known limitations:

- Account approval is performed by an administrator and is not email verification.
- Password reset and password change are not yet available through the interface.
- Calendar data refreshes every 30 seconds rather than using WebSockets for immediate push updates.
- Appointments represent a date and branch operating window rather than an exact queue number or time slot.
- Branch schedules are stored in configuration and do not yet have an administrator management screen.
- An appointment assigned to “Any available doctor” will not appear on a specific doctor's personal calendar until a doctor is assigned.
- Doctor profiles and specialties do not yet have a complete management interface.
- Clinical visits, immunizations, and inventory have database structures but incomplete user-facing workflows.
- Login, registration, child forms, appointment forms, and user management remain Blade-based rather than fully React-based.
- Automated email and SMS reminders are not yet implemented.

## 19. Recommended Future Enhancements

Recommended next development phases include:

1. Add a secure password-change and forgot-password workflow.
2. Implement optional verified-email notifications in addition to administrator approval.
3. Provide an administrator interface for clinic branches and operating schedules.
4. Add doctor assignment for appointments requesting any available doctor.
5. Introduce daily capacity limits or queue numbers for each branch.
6. Add schedule blocks for holidays, doctor leave, and unavailable dates.
7. Convert remaining Blade pages into React pages with dedicated CSS files.
8. Implement complete patient-visit and clinical-record workflows.
9. Implement immunization schedules and vaccination-history interfaces.
10. Implement inventory monitoring, expiration alerts, and transaction reporting.
11. Add email or SMS reminders for approved and upcoming appointments.
12. Add printable reports and dashboard analytics.
13. Add audit logs for role changes, status changes, and clinical updates.
14. Add automated backups and a formal production deployment configuration.
15. Expand automated testing for all future clinical and inventory modules.

## 20. Conclusion

TitaClinic demonstrates how a pediatric clinic can organize account approval, patient profiles, branch scheduling, appointment history, and doctor workflows within a single web application. Its role-based design ensures that each user sees only the functions relevant to their responsibilities. Parents receive a clear scheduling and history interface, doctors receive a live calendar and appointment workflow, and administrators receive centralized access and activity management.

The combination of Laravel, React, and MySQL provides a maintainable foundation for continued development. The current system already supports the primary account, patient, and appointment lifecycle while leaving room for future electronic medical records, immunization management, inventory monitoring, and notification services.

With its existing ERD, UI screenshots, automated tests, and documented workflows, the project is prepared for further evaluation, presentation, and expansion.
