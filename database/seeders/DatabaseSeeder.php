<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PurchaseRequest;
use App\Models\PrItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
            'department' => 'IT Division',
        ]);

        $procurementOfficer = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'procurement@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'PROCUREMENT_OFFICER',
            'department' => 'Procurement Division',
        ]);

        $endUser = User::create([
            'name' => 'Maria Santos',
            'email' => 'enduser@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'END_USER',
            'department' => 'Finance Division',
        ]);

        $canvasser = User::create([
            'name' => 'Pedro Rivera',
            'email' => 'canvasser@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'CANVASSER',
            'department' => 'Procurement Division',
        ]);

        $bacChair = User::create([
            'name' => 'Dr. Ana Lim',
            'email' => 'bac.chair@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'BAC_CHAIR',
            'department' => 'BAC Secretariat',
        ]);

        $bacSecretariat = User::create([
            'name' => 'Carlos Mendoza',
            'email' => 'bac.secretariat@dict.gov.ph',
            'password' => Hash::make('password'),
            'role' => 'BAC_SECRETARIAT',
            'department' => 'BAC Secretariat',
        ]);

        // Create some sample purchase requests
        $pr1 = PurchaseRequest::create([
            'pr_number' => 'PR-2024-0001',
            'project_title' => 'Office Supplies Procurement',
            'project_description' => 'Procurement of office supplies for Q1 2024',
            'end_user_id' => $endUser->id,
            'end_user_department' => 'Finance Division',
            'fund_source' => 'Regular Budget',
            'estimated_budget' => 50000.00,
            'urgency_level' => 'MEDIUM',
            'urgency_timeline' => 'Within 30 days',
            'approval_date' => '2024-01-15',
            'status' => 'PR_UNDER_REVIEW',
            'has_signatures' => true,
            'has_specs' => true,
            'has_quantity' => true,
            'has_market_survey' => true,
        ]);

        $pr2 = PurchaseRequest::create([
            'pr_number' => 'PR-2024-0002',
            'project_title' => 'IT Equipment Upgrade',
            'project_description' => 'Upgrade of server infrastructure and workstations',
            'end_user_id' => $endUser->id,
            'end_user_department' => 'IT Division',
            'fund_source' => 'Capital Outlay',
            'estimated_budget' => 500000.00,
            'urgency_level' => 'HIGH',
            'urgency_timeline' => 'Within 60 days',
            'approval_date' => '2024-01-10',
            'status' => 'RFQ_READY',
            'has_signatures' => true,
            'has_specs' => true,
            'has_quantity' => true,
            'has_market_survey' => true,
        ]);

        $pr3 = PurchaseRequest::create([
            'pr_number' => 'PR-2024-0003',
            'project_title' => 'Vehicle Maintenance Services',
            'project_description' => 'Annual maintenance services for DICT vehicles',
            'end_user_id' => $endUser->id,
            'end_user_department' => 'Administrative Division',
            'fund_source' => 'Maintenance and Operating Expenses',
            'estimated_budget' => 200000.00,
            'urgency_level' => 'URGENT',
            'urgency_timeline' => 'Within 15 days',
            'approval_date' => '2024-01-20',
            'status' => 'BAC_DOCS_READY',
            'has_signatures' => true,
            'has_specs' => true,
            'has_quantity' => true,
            'has_market_survey' => true,
        ]);

        // Create PR items for the first purchase request
        PrItem::create([
            'purchase_request_id' => $pr1->id,
            'item_code' => 'OS-001',
            'item_name' => 'A4 Size Bond Paper',
            'item_description' => 'High quality A4 bond paper, 500 sheets per ream',
            'quantity' => 50,
            'unit_of_measure' => 'reams',
            'estimated_price' => 200.00,
            'total_estimated' => 10000.00,
        ]);

        PrItem::create([
            'purchase_request_id' => $pr1->id,
            'item_code' => 'OS-002',
            'item_name' => 'Ballpoint Pens',
            'item_description' => 'Blue ink ballpoint pens, pack of 12',
            'quantity' => 20,
            'unit_of_measure' => 'packs',
            'estimated_price' => 150.00,
            'total_estimated' => 3000.00,
        ]);

        PrItem::create([
            'purchase_request_id' => $pr1->id,
            'item_code' => 'OS-003',
            'item_name' => 'Stapler',
            'item_description' => 'Heavy duty stapler with staples',
            'quantity' => 5,
            'unit_of_measure' => 'pieces',
            'estimated_price' => 500.00,
            'total_estimated' => 2500.00,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Test users created:');
        $this->command->info('Admin: admin@dict.gov.ph / password');
        $this->command->info('Procurement Officer: procurement@dict.gov.ph / password');
        $this->command->info('End User: enduser@dict.gov.ph / password');
        $this->command->info('Canvasser: canvasser@dict.gov.ph / password');
        $this->command->info('BAC Chair: bac.chair@dict.gov.ph / password');
        $this->command->info('BAC Secretariat: bac.secretariat@dict.gov.ph / password');
    }
}
