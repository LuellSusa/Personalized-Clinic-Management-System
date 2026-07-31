<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\ParentProfile;
use App\Models\User;
use App\Support\BranchSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_mounts_the_react_application(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-page="landing"', false);
    }

    public function test_parent_dashboard_mounts_react_with_live_kpi_props(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $this->createChild($profile, 'PAT-DASHBOARD-001');

        $this->actingAs($parent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-page="dashboard"', false)
            ->assertSee('"children":1', false);
    }

    public function test_admin_dashboard_mounts_the_admin_react_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-page="admin-dashboard"', false);
    }

    public function test_doctor_dashboard_mounts_react_and_exposes_calendar_data(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);

        $this->actingAs($doctor)
            ->get(route('doctor.dashboard'))
            ->assertOk()
            ->assertSee('data-page="doctor-dashboard"', false);

        $this->actingAs($doctor)
            ->getJson(route('doctor.calendar', ['month' => today()->format('Y-m')]))
            ->assertOk()
            ->assertJsonPath('month', today()->format('Y-m'));
    }

    public function test_doctor_cannot_access_admin_user_management(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);

        $this->actingAs($doctor)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_dashboard_redirects_staff_to_their_own_area(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($doctor)
            ->get(route('dashboard'))
            ->assertRedirect(route('doctor.dashboard'));
    }

    public function test_parent_without_profile_is_sent_to_profile_form(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $this->actingAs($parent)
            ->get(route('children.index'))
            ->assertRedirect(route('parent-profile.create'));
    }

    public function test_parent_cannot_book_an_appointment_for_another_parents_child(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $otherParent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $otherProfile = ParentProfile::create(['user_id' => $otherParent->id]);
        $otherChild = Child::create([
            'parent_profile_id' => $otherProfile->id,
            'patient_number' => 'PAT-OTHER-001',
            'first_name' => 'Other',
            'last_name' => 'Child',
            'birth_date' => '2020-01-01',
            'sex' => 'female',
            'status' => 'active',
        ]);

        $this->actingAs($parent)
            ->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'child_id' => $otherChild->id,
                'appointment_type' => 'consultation',
                'branch' => 'chong_hua_medical_mall',
                'appointment_date' => $this->nextAvailableDate('chong_hua_medical_mall'),
            ])
            ->assertRedirect(route('appointments.create'))
            ->assertSessionHasErrors('child_id');

        $this->assertDatabaseCount('appointments', 0);
        $this->assertDatabaseHas('parent_profiles', ['id' => $profile->id]);
    }

    public function test_only_active_accounts_can_log_in(): void
    {
        $pending = User::factory()->create([
            'email' => 'pending@example.test',
            'status' => 'pending',
        ]);
        $active = User::factory()->create([
            'email' => 'active@example.test',
            'status' => 'active',
        ]);

        $this->post(route('login'), [
            'email' => $pending->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->post(route('login'), [
            'email' => $active->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($active);
    }

    public function test_admin_can_approve_an_account_and_assign_a_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $pendingUser = User::factory()->create(['role' => 'parent', 'status' => 'pending']);

        $this->actingAs($admin)
            ->put(route('admin.users.access', $pendingUser), [
                'role' => 'doctor',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'role' => 'doctor',
            'status' => 'active',
        ]);
    }

    public function test_final_active_administrator_cannot_remove_their_own_admin_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->from(route('admin.users'))
            ->put(route('admin.users.access', $admin), [
                'role' => 'parent',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.users'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_doctor_can_only_update_their_own_appointment_using_valid_transitions(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $otherDoctor = User::factory()->create(['role' => 'doctor']);
        $parent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $child = $this->createChild($profile, 'PAT-DOCTOR-001');
        $appointment = $this->createAppointment($profile, $child, $doctor);

        $this->actingAs($otherDoctor)
            ->patch(route('doctor.appointments.status', $appointment), ['status' => 'confirmed'])
            ->assertForbidden();

        $this->actingAs($doctor)
            ->patch(route('doctor.appointments.status', $appointment), ['status' => 'completed'])
            ->assertSessionHasErrors('status');

        $this->actingAs($doctor)
            ->patch(route('doctor.appointments.status', $appointment), ['status' => 'confirmed'])
            ->assertRedirect(route('doctor.dashboard').'#doctor-bookings');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_multiple_day_bookings_for_the_same_doctor_are_allowed_without_time_slots(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'status' => 'active']);
        $parent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $child = $this->createChild($profile, 'PAT-CONFLICT-001');
        $this->createAppointment($profile, $child, $doctor);

        $this->actingAs($parent)
            ->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'child_id' => $child->id,
                'doctor_user_id' => $doctor->id,
                'appointment_type' => 'consultation',
                'branch' => 'chong_hua_medical_mall',
                'appointment_date' => $this->nextAvailableDate('chong_hua_medical_mall'),
            ])
            ->assertRedirect(route('appointments.index'));

        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseHas('appointments', ['start_time' => null, 'end_time' => null]);
    }

    public function test_branch_rejects_a_date_outside_its_available_weekdays(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $child = $this->createChild($profile, 'PAT-BRANCH-001');
        $sunday = today()->next(0)->toDateString();

        $this->actingAs($parent)
            ->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'child_id' => $child->id,
                'appointment_type' => 'consultation',
                'branch' => 'chong_hua_medical_mall',
                'appointment_date' => $sunday,
            ])
            ->assertRedirect(route('appointments.create'))
            ->assertSessionHasErrors('appointment_date');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_past_booking_can_be_rescheduled_while_preserving_history(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $parent = User::factory()->create(['role' => 'parent']);
        $profile = ParentProfile::create(['user_id' => $parent->id]);
        $child = $this->createChild($profile, 'PAT-RESCHEDULE-001');
        $appointment = $this->createAppointment($profile, $child, $doctor);
        $appointment->update(['appointment_date' => today()->subDay(), 'status' => 'confirmed']);

        $this->actingAs($parent)
            ->patch(route('appointments.reschedule.store', $appointment), [
                'child_id' => $child->id,
                'doctor_user_id' => $doctor->id,
                'appointment_type' => 'consultation',
                'branch' => 'chong_hua_mandaue',
                'appointment_date' => $this->nextAvailableDate('chong_hua_mandaue'),
            ])
            ->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'no_show']);
        $this->assertDatabaseCount('appointments', 2);
        $this->assertDatabaseHas('appointments', [
            'branch' => 'chong_hua_mandaue',
            'status' => 'scheduled',
            'start_time' => null,
            'end_time' => null,
        ]);
    }

    public function test_registration_phone_number_must_contain_exactly_eleven_digits(): void
    {
        $payload = [
            'first_name' => 'Test',
            'last_name' => 'Parent',
            'email' => 'phone-test@example.test',
            'phone_number' => '0912345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->post(route('register'), $payload)->assertSessionHasErrors('phone_number');

        $payload['phone_number'] = '09123456789';
        $this->post(route('register'), $payload)->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => $payload['email'], 'phone_number' => '09123456789']);
    }

    private function createChild(ParentProfile $profile, string $patientNumber): Child
    {
        return Child::create([
            'parent_profile_id' => $profile->id,
            'patient_number' => $patientNumber,
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'birth_date' => '2020-01-01',
            'sex' => 'female',
            'status' => 'active',
        ]);
    }

    private function createAppointment(ParentProfile $profile, Child $child, User $doctor): Appointment
    {
        return Appointment::create([
            'child_id' => $child->id,
            'parent_profile_id' => $profile->id,
            'doctor_user_id' => $doctor->id,
            'created_by_user_id' => $profile->user_id,
            'appointment_type' => 'consultation',
            'branch' => 'chong_hua_medical_mall',
            'appointment_date' => $this->nextAvailableDate('chong_hua_medical_mall'),
            'start_time' => null,
            'end_time' => null,
            'status' => 'scheduled',
        ]);
    }

    private function nextAvailableDate(string $branch): string
    {
        $date = today()->addDay();

        while (! BranchSchedule::isAvailable($branch, $date)) {
            $date->addDay();
        }

        return $date->toDateString();
    }
}
