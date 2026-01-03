<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin user or create a default one for seeding
        $adminUser = User::where('role', 'ADMIN')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        if (!$adminUser) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        $sampleSuppliers = [
            [
                'supplier_name_original' => 'ABC Computer Solutions Inc.',
                'supplier_category' => 'IT Equipment',
                'supplier_address' => '123 Tech Street, Quezon City, Metro Manila',
                'supplier_contact' => '+63 2 1234 5678',
                'supplier_email' => 'contact@abccomputers.com',
                'business_registration_number' => 'SEC-2020-001234',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Office Supplies Depot',
                'supplier_category' => 'Office Supplies',
                'supplier_address' => '456 Business Avenue, Makati City, Metro Manila',
                'supplier_contact' => '+63 2 2345 6789',
                'supplier_email' => 'sales@officesupplies.com',
                'business_registration_number' => 'SEC-2019-005678',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Construction Materials Co.',
                'supplier_category' => 'Construction',
                'supplier_address' => '789 Builder Road, Pasig City, Metro Manila',
                'supplier_contact' => '+63 2 3456 7890',
                'supplier_email' => 'info@constructionmaterials.ph',
                'business_registration_number' => 'SEC-2018-009012',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Network Equipment Pro',
                'supplier_category' => 'IT Equipment',
                'supplier_address' => '321 Network Boulevard, Mandaluyong City, Metro Manila',
                'supplier_contact' => '+63 2 4567 8901',
                'supplier_email' => 'support@networkequip.com',
                'business_registration_number' => 'SEC-2021-003456',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Furniture & Fixtures Ltd.',
                'supplier_category' => 'Furniture',
                'supplier_address' => '654 Furniture Lane, Taguig City, Metro Manila',
                'supplier_contact' => '+63 2 5678 9012',
                'supplier_email' => 'sales@furniturefixtures.ph',
                'business_registration_number' => 'SEC-2020-007890',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Printing Services Manila',
                'supplier_category' => 'Printing Services',
                'supplier_address' => '987 Print Street, Manila City, Metro Manila',
                'supplier_contact' => '+63 2 6789 0123',
                'supplier_email' => 'print@printingservices.ph',
                'business_registration_number' => 'SEC-2019-001234',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Security Systems Inc.',
                'supplier_category' => 'Security Equipment',
                'supplier_address' => '147 Security Plaza, San Juan City, Metro Manila',
                'supplier_contact' => '+63 2 7890 1234',
                'supplier_email' => 'info@securitysystems.ph',
                'business_registration_number' => 'SEC-2021-005678',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Electrical Supplies Corp.',
                'supplier_category' => 'Electrical',
                'supplier_address' => '258 Electric Avenue, Marikina City, Metro Manila',
                'supplier_contact' => '+63 2 8901 2345',
                'supplier_email' => 'sales@electricalsupplies.ph',
                'business_registration_number' => 'SEC-2020-009012',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Medical Equipment Solutions',
                'supplier_category' => 'Medical Equipment',
                'supplier_address' => '369 Medical Center, Pasay City, Metro Manila',
                'supplier_contact' => '+63 2 9012 3456',
                'supplier_email' => 'contact@medicalequip.ph',
                'business_registration_number' => 'SEC-2022-001234',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Vehicle & Transportation Co.',
                'supplier_category' => 'Transportation',
                'supplier_address' => '741 Transport Road, Caloocan City, Metro Manila',
                'supplier_contact' => '+63 2 0123 4567',
                'supplier_email' => 'info@vehicletransport.ph',
                'business_registration_number' => 'SEC-2019-003456',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Cleaning Supplies Distributor',
                'supplier_category' => 'Cleaning Supplies',
                'supplier_address' => '852 Clean Street, Valenzuela City, Metro Manila',
                'supplier_contact' => '+63 2 1234 5678',
                'supplier_email' => 'sales@cleaningsupplies.ph',
                'business_registration_number' => 'SEC-2021-007890',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Catering Services Manila',
                'supplier_category' => 'Catering Services',
                'supplier_address' => '963 Food Court, Las Piñas City, Metro Manila',
                'supplier_contact' => '+63 2 2345 6789',
                'supplier_email' => 'events@cateringservices.ph',
                'business_registration_number' => 'SEC-2020-001234',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Audio Visual Equipment Pro',
                'supplier_category' => 'AV Equipment',
                'supplier_address' => '159 AV Boulevard, Parañaque City, Metro Manila',
                'supplier_contact' => '+63 2 3456 7890',
                'supplier_email' => 'sales@avequipment.ph',
                'business_registration_number' => 'SEC-2022-005678',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'HVAC Systems & Services',
                'supplier_category' => 'HVAC',
                'supplier_address' => '357 Climate Street, Muntinlupa City, Metro Manila',
                'supplier_contact' => '+63 2 4567 8901',
                'supplier_email' => 'info@hvacsystems.ph',
                'business_registration_number' => 'SEC-2021-009012',
                'status' => 'ACTIVE',
            ],
            [
                'supplier_name_original' => 'Stationery & Paper Products',
                'supplier_category' => 'Office Supplies',
                'supplier_address' => '741 Paper Road, Navotas City, Metro Manila',
                'supplier_contact' => '+63 2 5678 9012',
                'supplier_email' => 'sales@stationerypaper.ph',
                'business_registration_number' => 'SEC-2020-003456',
                'status' => 'ACTIVE',
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($sampleSuppliers as $supplierData) {
                $normalizedName = Supplier::normalizeName($supplierData['supplier_name_original']);
                
                // Check if supplier already exists
                $existing = Supplier::where('supplier_name', $normalizedName)->first();
                
                if (!$existing) {
                    Supplier::create([
                        'supplier_name' => $normalizedName,
                        'supplier_name_original' => $supplierData['supplier_name_original'],
                        'supplier_address' => $supplierData['supplier_address'],
                        'supplier_contact' => $supplierData['supplier_contact'],
                        'supplier_email' => $supplierData['supplier_email'],
                        'business_registration_number' => $supplierData['business_registration_number'],
                        'supplier_category' => $supplierData['supplier_category'],
                        'status' => $supplierData['status'],
                        'created_by' => $adminUser->id,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Sample suppliers created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create sample suppliers: ' . $e->getMessage());
        }
    }
}
