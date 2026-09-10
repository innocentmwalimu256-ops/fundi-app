<?php

namespace Tests\Feature;

use App\Models\CancellationRecord;
use App\Models\Complaint;
use App\Models\Quotation;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\TechnicianApplication;
use App\Models\TechnicianPortfolio;
use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\UserReport;
use App\Services\SmartMatchingService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FundiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_client_registration_assigns_client_role_and_redirects_to_dashboard(): void
    {
        $response = $this->post(route('register.submit'), [
            'full_name' => 'Michael Tester',
            'email' => 'michael@tester.test',
            'phone' => '0719001122',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('client.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'michael@tester.test',
            'role' => 'client',
            'status' => 'active',
        ]);
    }

    public function test_login_supports_both_email_and_phone(): void
    {
        $resEmail = $this->post(route('login.submit'), [
            'login' => 'client@fundi.test',
            'password' => 'password123',
        ]);
        $resEmail->assertRedirect(route('client.dashboard'));

        $this->post(route('logout'));

        $resPhone = $this->post(route('login.submit'), [
            'login' => '0712345678',
            'password' => 'password123',
        ]);
        $resPhone->assertRedirect(route('technician.dashboard'));
    }

    public function test_technician_subscription_activation_and_payment_record(): void
    {
        $tech = User::where('email', 'expiredtech@fundi.test')->first();
        $plan = SubscriptionPlan::where('slug', 'professional')->first();

        $this->actingAs($tech)->post(route('technician.subscription.pay', $plan->slug), [
            'payment_method' => 'mpesa',
            'phone_number' => '0755123456',
        ]);

        $tech->refresh();
        $this->assertTrue($tech->hasActiveSubscription());

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $tech->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('subscription_payments', [
            'user_id' => $tech->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'success',
        ]);
    }

    public function test_expired_technician_is_redirected_by_subscription_middleware(): void
    {
        $tech = User::where('email', 'expiredtech@fundi.test')->first();

        // Expired tech trying to view incoming requests
        $response = $this->actingAs($tech)->get(route('technician.requests.index'));
        $response->assertRedirect(route('technician.subscription.expired'));
    }

    public function test_contact_protection_and_whatsapp_link(): void
    {
        $tech = User::where('email', 'tech@fundi.test')->first();
        $client = User::where('email', 'client@fundi.test')->first();

        $this->assertStringContainsString('••••••••', $tech->masked_phone);
        $this->assertStringContainsString('https://wa.me/255', $tech->getWhatsappUrl('Hello'));
    }

    public function test_digital_receipt_view_accessible_for_completed_job(): void
    {
        $client = User::where('email', 'client@fundi.test')->first();
        $request = ServiceRequest::where('reference_no', 'REQ-2026-000103')->first();

        $response = $this->actingAs($client)->get(route('requests.receipt', $request->id));
        $response->assertStatus(200);
        $response->assertSee('Receipt #' . $request->reference_no);
        $response->assertSee('Labour / Technical Work');
    }

    public function test_smart_matching_calculates_high_score_for_matching_specialty(): void
    {
        $tech = User::where('email', 'tech@fundi.test')->first();
        $electrical = Service::where('name', 'Electrical')->first();

        $score = SmartMatchingService::calculateMatchScore($tech, $electrical->id, 'Kinondoni');
        $this->assertGreaterThanOrEqual(85, $score);
    }

    public function test_admin_can_approve_technician_application(): void
    {
        $admin = User::where('email', 'admin@fundi.test')->first();
        $application = TechnicianApplication::where('status', 'pending')->first();
        $applicant = $application->user;

        $this->assertEquals('client', $applicant->role);

        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application->id));
        $response->assertRedirect(route('admin.applications.index'));

        $applicant->refresh();
        $application->refresh();

        $this->assertEquals('technician', $applicant->role);
        $this->assertEquals('approved', $application->status);
        $this->assertEquals('approved', $applicant->technicianProfile->verification_status);
    }
}
