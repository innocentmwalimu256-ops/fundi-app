<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\CancellationRecord;
use App\Models\Complaint;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\TechnicianApplication;
use App\Models\TechnicianAvailability;
use App\Models\TechnicianPortfolio;
use App\Models\TechnicianProfile;
use App\Models\TechnicianServiceArea;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Core Services
        $servicesData = [
            ['name' => 'Electrical', 'icon' => 'zap', 'description' => 'House wiring, circuit breakers, lighting, generators, fault diagnosis, and electrical repairs.'],
            ['name' => 'Plumbing', 'icon' => 'droplet', 'description' => 'Pipe fitting, leak repairs, bathroom fixtures, drainage, pumps, and water heater installation.'],
            ['name' => 'AC Repair', 'icon' => 'wind', 'description' => 'Air conditioner servicing, gas refilling, compressor repairs, and refrigeration maintenance.'],
            ['name' => 'Phone Repair', 'icon' => 'smartphone', 'description' => 'Screen replacements, battery servicing, charging port repairs, and motherboard diagnostics.'],
            ['name' => 'Computer Repair', 'icon' => 'monitor', 'description' => 'Laptop servicing, OS reinstallation, hardware upgrades, malware removal, and network setup.'],
            ['name' => 'Carpentry', 'icon' => 'hammer', 'description' => 'Furniture making, door repairs, roof trussing, partition installation, and woodwork finishes.'],
            ['name' => 'Welding', 'icon' => 'flame', 'description' => 'Metal gates, window grilles, structural welding, steel fabrication, and metal repairs.'],
            ['name' => 'Painting', 'icon' => 'paint-roller', 'description' => 'Interior and exterior house painting, wall skimming, waterproofing, and decorative finishes.'],
            ['name' => 'Masonry', 'icon' => 'brick-wall', 'description' => 'Bricklaying, plastering, floor screeding, tile installation, and structural renovation.'],
            ['name' => 'Appliance Repair', 'icon' => 'tv', 'description' => 'Washing machines, microwaves, refrigerators, TV repairs, and home appliance maintenance.'],
        ];

        $services = [];
        foreach ($servicesData as $idx => $s) {
            $services[$s['name']] = Service::create([
                'name' => $s['name'],
                'slug' => Str::slug($s['name']),
                'description' => $s['description'],
                'icon' => $s['icon'],
                'status' => 'active',
                'display_order' => $idx + 1,
            ]);
        }

        // 2. Create Subscription Plans with Distinct Features & Limits
        $planStarter = SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 10000,
            'currency' => 'TZS',
            'duration_days' => 30,
            'description' => 'Essential marketplace access for newly onboarded artisans and technicians.',
            'features' => ['Receive client service requests', 'Direct WhatsApp & chat communication', 'Showcase up to 5 portfolio works', '1 Service coverage area', 'Basic analytics dashboard'],
            'request_limit' => 20,
            'portfolio_limit' => 5,
            'service_area_limit' => 1,
            'contact_access' => true,
            'priority_listing' => false,
            'is_featured' => false,
            'priority_support' => false,
            'analytics_level' => 'basic',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $planPro = SubscriptionPlan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'price' => 20000,
            'currency' => 'TZS',
            'duration_days' => 30,
            'description' => 'Full-powered toolkit with priority matching and expanded coverage areas.',
            'features' => ['Receive up to 60 client requests', 'Direct WhatsApp & chat communication', 'Showcase up to 15 portfolio works', 'Up to 3 Service coverage areas', 'Priority matching in search results', 'Advanced performance analytics', 'Priority technician support'],
            'request_limit' => 60,
            'portfolio_limit' => 15,
            'service_area_limit' => 3,
            'contact_access' => true,
            'priority_listing' => true,
            'is_featured' => false,
            'priority_support' => true,
            'analytics_level' => 'advanced',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $planPremium = SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 35000,
            'currency' => 'TZS',
            'duration_days' => 30,
            'description' => 'Maximum visibility with Featured Technician badge and unlimited projects.',
            'features' => ['Unlimited client service requests', 'Direct WhatsApp & chat communication', 'Unlimited portfolio project gallery', 'Up to 5 Service coverage areas', '⭐ Featured Technician profile badge', 'Top ranked priority matching', 'Advanced analytics & revenue tracking', 'Dedicated priority support desk'],
            'request_limit' => 0,
            'portfolio_limit' => 0,
            'service_area_limit' => 5,
            'contact_access' => true,
            'priority_listing' => true,
            'is_featured' => true,
            'priority_support' => true,
            'analytics_level' => 'advanced',
            'is_active' => true,
            'display_order' => 3,
        ]);

        // 3. Create Users

        // A. Administrator
        $admin = User::create([
            'full_name' => 'FUNDI Administrator',
            'email' => 'admin@fundi.test',
            'phone' => '0675315279',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // B. Active Verified Technician (John Mwakyusa - Electrical Specialist with Premium Plan)
        $tech1 = User::create([
            'full_name' => 'John Mwakyusa',
            'email' => 'tech@fundi.test',
            'phone' => '0712345678',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'status' => 'active',
        ]);

        $tech1Profile = TechnicianProfile::create([
            'user_id' => $tech1->id,
            'professional_title' => 'Certified Electrical Technician & Solar Specialist',
            'bio' => "Professional electrical installation, domestic wiring, fault diagnosis, and solar maintenance with over 5 years of industry experience across Dar es Salaam. Focused on safe, certified, and clean finishes.",
            'years_experience' => 5,
            'location' => 'Dar es Salaam, Kinondoni',
            'service_area' => 'Kinondoni, Mikocheni, Masaki & Oysterbay',
            'availability_status' => 'available',
            'verification_status' => 'approved',
            'average_rating' => 4.85,
            'total_reviews' => 48,
            'completed_jobs_count' => 89,
            'completion_rate' => 98,
            'response_rate' => 96,
            'avg_response_time' => '12 min',
            'skills' => ['House Wiring', 'Fault Diagnosis', 'Circuit Breakers', 'Solar Inverter Setup', 'Appliance Repair', 'Generator Service'],
        ]);
        $tech1->services()->sync([$services['Electrical']->id, $services['Appliance Repair']->id]);

        foreach (['Kinondoni', 'Ubungo', 'Ilala'] as $area) {
            TechnicianServiceArea::create(['technician_id' => $tech1->id, 'area_name' => $area]);
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            TechnicianAvailability::create([
                'technician_id' => $tech1->id,
                'day_of_week' => $day,
                'start_time' => '08:00:00',
                'end_time' => $day === 'Saturday' ? '15:00:00' : '18:00:00',
                'is_available' => $day !== 'Sunday',
            ]);
        }

        TechnicianPortfolio::create([
            'technician_id' => $tech1->id,
            'service_id' => $services['Electrical']->id,
            'title' => 'Residential House Wiring & Fuse Box Replacement',
            'description' => 'Complete 3-phase rewiring of a 4-bedroom villa with Schneider distribution breakers and earthing rods in Mikocheni.',
            'project_date' => 'July 2026',
        ]);

        // Active Premium Subscription for Tech 1 (Featured)
        $sub1 = Subscription::create([
            'user_id' => $tech1->id,
            'plan_id' => $planPremium->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addDays(29),
            'auto_renew' => false,
        ]);

        SubscriptionPayment::create([
            'subscription_id' => $sub1->id,
            'user_id' => $tech1->id,
            'plan_id' => $planPremium->id,
            'amount' => $planPremium->price,
            'currency' => 'TZS',
            'payment_reference' => 'SUB-20260901-TECH01',
            'payment_method' => 'mpesa',
            'status' => 'success',
            'paid_at' => now()->subDay(),
        ]);

        // C. Expired Verified Technician (Hassan Ali - Expired Subscription for Demo Scenario B)
        $tech2 = User::create([
            'full_name' => 'Hassan Ali',
            'email' => 'expiredtech@fundi.test',
            'phone' => '0755123456',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'status' => 'active',
        ]);

        TechnicianProfile::create([
            'user_id' => $tech2->id,
            'professional_title' => 'Master Plumber & Drainage Specialist',
            'bio' => "Expert in commercial and residential plumbing systems, high-pressure pipe installations, bathroom fixtures, and emergency leak repairs.",
            'years_experience' => 7,
            'location' => 'Dar es Salaam, Ilala',
            'service_area' => 'Ilala, Kariakoo, Upanga & Temeke',
            'availability_status' => 'available',
            'verification_status' => 'approved',
            'average_rating' => 4.90,
            'total_reviews' => 64,
            'completed_jobs_count' => 112,
            'completion_rate' => 99,
            'response_rate' => 94,
            'avg_response_time' => '10 min',
            'skills' => ['PPR & PVC Piping', 'Booster Pumps', 'Leak Detection', 'Drainage Unblocking'],
        ]);
        $tech2->services()->sync([$services['Plumbing']->id]);

        foreach (['Ilala', 'Temeke'] as $area) {
            TechnicianServiceArea::create(['technician_id' => $tech2->id, 'area_name' => $area]);
        }

        // Expired Subscription for Tech 2
        $sub2 = Subscription::create([
            'user_id' => $tech2->id,
            'plan_id' => $planStarter->id,
            'status' => 'expired',
            'started_at' => now()->subDays(35),
            'expires_at' => now()->subDays(5),
            'auto_renew' => false,
        ]);

        SubscriptionPayment::create([
            'subscription_id' => $sub2->id,
            'user_id' => $tech2->id,
            'plan_id' => $planStarter->id,
            'amount' => $planStarter->price,
            'currency' => 'TZS',
            'payment_reference' => 'SUB-20260725-TECH02',
            'payment_method' => 'tigopesa',
            'status' => 'success',
            'paid_at' => now()->subDays(35),
        ]);

        // D. Free Trial Technician (Amina Juma - AC Specialist)
        $tech3 = User::create([
            'full_name' => 'Amina Juma',
            'email' => 'ac@fundi.test',
            'phone' => '0766987654',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'status' => 'active',
        ]);

        TechnicianProfile::create([
            'user_id' => $tech3->id,
            'professional_title' => 'HVAC & AC Cooling Specialist',
            'bio' => "VETA-certified refrigeration and AC technician specializing in split unit servicing, refrigerant gas charging, and compressor replacements.",
            'years_experience' => 4,
            'location' => 'Dar es Salaam, Ubungo',
            'service_area' => 'Ubungo, Mbezi, Sinza & Kijitonyama',
            'availability_status' => 'available',
            'verification_status' => 'approved',
            'average_rating' => 4.75,
            'total_reviews' => 32,
            'completed_jobs_count' => 56,
            'completion_rate' => 97,
            'response_rate' => 95,
            'avg_response_time' => '15 min',
            'skills' => ['Split AC Servicing', 'Gas Refill R410/R22', 'Compressor Overhaul'],
        ]);
        $tech3->services()->sync([$services['AC Repair']->id]);

        Subscription::create([
            'user_id' => $tech3->id,
            'plan_id' => $planPro->id,
            'status' => 'free_trial',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addDays(6),
            'auto_renew' => false,
        ]);

        // E. Client 1 (John Client)
        $client1 = User::create([
            'full_name' => 'John Client',
            'email' => 'client@fundi.test',
            'phone' => '0788112233',
            'password' => Hash::make('password123'),
            'role' => 'client',
            'status' => 'active',
        ]);

        // F. Client 2 (Sarah Makonda)
        $client2 = User::create([
            'full_name' => 'Sarah Makonda',
            'email' => 'client2@fundi.test',
            'phone' => '0788445566',
            'password' => Hash::make('password123'),
            'role' => 'client',
            'status' => 'active',
        ]);

        // F2. Client: Leryn (leryn12@gmail.com & leryn@fundi.test)
        $clientLeryn = User::create([
            'full_name' => 'Leryn',
            'email' => 'leryn12@gmail.com',
            'phone' => '0700000001',
            'password' => Hash::make('innocent'),
            'role' => 'client',
            'status' => 'active',
        ]);

        // F3. Technician: Innocent Steven (Guzman)
        $techGuzman = User::create([
            'full_name' => 'Innocent Steven (Guzman)',
            'email' => 'innocentsteven206@gmail.com',
            'phone' => '0700000002',
            'password' => Hash::make('innocent'),
            'role' => 'technician',
            'status' => 'active',
        ]);

        TechnicianProfile::create([
            'user_id' => $techGuzman->id,
            'professional_title' => 'Master Plumber & Electrical Specialist',
            'bio' => 'Professional technician specializing in domestic wiring, plumbing fixtures, pipe fitting, and maintenance.',
            'years_experience' => 5,
            'location' => 'Dar es Salaam, Kinondoni',
            'service_area' => 'Dar es Salaam Citywide',
            'availability_status' => 'available',
            'verification_status' => 'approved',
            'average_rating' => 4.95,
            'total_reviews' => 28,
            'completed_jobs_count' => 54,
            'completion_rate' => 99,
            'response_rate' => 98,
            'avg_response_time' => '10 min',
            'skills' => ['House Wiring', 'Pipe Fitting', 'Drainage', 'Water Heating'],
        ]);
        $techGuzman->services()->sync([$services['Plumbing']->id, $services['Electrical']->id]);

        Subscription::create([
            'user_id' => $techGuzman->id,
            'plan_id' => $planPremium->id,
            'status' => 'active',
            'started_at' => now()->subDays(2),
            'expires_at' => now()->addDays(28),
            'auto_renew' => true,
        ]);



        // G. Applicant (Baraka Peter - Pending Verification)
        $applicant = User::create([
            'full_name' => 'Baraka Peter',
            'email' => 'pendingtech@fundi.test',
            'phone' => '0711998877',
            'password' => Hash::make('password123'),
            'role' => 'client',
            'status' => 'active',
        ]);

        TechnicianApplication::create([
            'user_id' => $applicant->id,
            'professional_title' => 'Furniture & Architectural Carpentry Specialist',
            'service_id' => $services['Carpentry']->id,
            'years_experience' => 4,
            'location' => 'Dar es Salaam, Kinondoni',
            'service_area' => 'Dar es Salaam Citywide',
            'bio' => "4 years of modern furniture designing, custom kitchen cabinets, wooden partitions, and roof carpentry.",
            'skills' => ['Kitchen Cabinets', 'Wood Finishing', 'Partitioning', 'Roof Trusses'],
            'status' => 'pending',
        ]);

        TechnicianProfile::create([
            'user_id' => $applicant->id,
            'professional_title' => 'Furniture & Architectural Carpentry Specialist',
            'bio' => "4 years of modern furniture designing, custom kitchen cabinets, wooden partitions, and roof carpentry.",
            'years_experience' => 4,
            'location' => 'Dar es Salaam, Kinondoni',
            'service_area' => 'Dar es Salaam Citywide',
            'verification_status' => 'pending',
            'skills' => ['Kitchen Cabinets', 'Wood Finishing', 'Partitioning', 'Roof Trusses'],
        ]);

        // 4. Favorites Setup
        Favorite::create(['client_id' => $client1->id, 'technician_id' => $tech1->id]);

        // 5. Create Requests

        // Req 1: Quotation Pending Acceptance (Client 1 <-> Tech 1)
        $req1 = ServiceRequest::create([
            'reference_no' => 'REQ-2026-000101',
            'client_id' => $client1->id,
            'technician_id' => $tech1->id,
            'service_id' => $services['Electrical']->id,
            'description' => 'Main house circuit breaker trips repeatedly when living room AC runs. Need socket inspection and circuit balancing.',
            'location' => 'Mikocheni B, Kinondoni',
            'preferred_date' => now()->addDay(),
            'preferred_time' => '14:00:00',
            'urgency' => 'high',
            'status' => 'quotation_pending',
            'contact_unlocked' => true,
        ]);

        Quotation::create([
            'request_id' => $req1->id,
            'labour_cost' => 50000,
            'materials_cost' => 25000,
            'transport_cost' => 5000,
            'discount' => 0,
            'total_cost' => 80000,
            'notes' => 'Includes replacement of 32A Schneider breaker and full wiring load test. Payment settled directly upon completion.',
            'estimated_duration' => '3 Hours',
            'valid_until' => now()->addDays(7),
            'status' => 'pending',
        ]);

        Message::create([
            'request_id' => $req1->id,
            'sender_id' => $tech1->id,
            'message_text' => 'Hello John, I have prepared an itemized quotation of TZS 80,000. Please review it so we can schedule.',
            'sent_at' => now()->subMinutes(30),
        ]);

        // Req 2: Completed Job (Client 1 <-> Tech 1)
        $req2 = ServiceRequest::create([
            'reference_no' => 'REQ-2026-000103',
            'client_id' => $client1->id,
            'technician_id' => $tech1->id,
            'service_id' => $services['Electrical']->id,
            'description' => 'Installation of 8 LED security floodlights with photocell automatic daylight sensors around perimeter fence.',
            'location' => 'Oysterbay, Kinondoni',
            'preferred_date' => now()->subDays(2),
            'preferred_time' => '09:00:00',
            'urgency' => 'normal',
            'status' => 'client_confirmed',
            'contact_unlocked' => true,
        ]);

        Quotation::create([
            'request_id' => $req2->id,
            'labour_cost' => 80000,
            'materials_cost' => 120000,
            'transport_cost' => 10000,
            'discount' => 10000,
            'total_cost' => 200000,
            'notes' => 'Conduit piping, 8 floodlights, dusk-to-dawn sensors.',
            'status' => 'accepted',
        ]);

        ServiceJob::create([
            'request_id' => $req2->id,
            'scheduled_date' => now()->subDays(2),
            'status' => 'completed',
            'started_at' => now()->subDays(2)->setTime(9, 0),
            'completed_at' => now()->subDays(2)->setTime(16, 30),
            'confirmed_at' => now()->subDay(),
        ]);

        Review::create([
            'request_id' => $req2->id,
            'client_id' => $client1->id,
            'technician_id' => $tech1->id,
            'rating' => 5,
            'quality' => 5,
            'professionalism' => 5,
            'punctuality' => 5,
            'communication' => 5,
            'comment' => 'John arrived on time and executed clean wiring. Highly recommend!',
            'status' => 'published',
        ]);

        // Audit Logs
        AuditLog::log('system_seed', 'Platform initialized with FUNDI final business model.', 'System', 1);
    }
}
