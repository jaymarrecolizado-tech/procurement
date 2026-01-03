<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierQuotationHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierQuotationHistorySeeder extends Seeder
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

        $suppliers = Supplier::all();
        
        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        // Sample quotation history data
        $quotationHistory = [
            // IT Equipment Suppliers
            [
                'supplier_name' => 'ABC Computer Solutions Inc.',
                'items' => [
                    ['item_name' => 'Dell Latitude 5520 Laptop', 'item_code' => 'LAP-DELL-LAT5520', 'description' => '15.6" FHD, Intel Core i7, 16GB RAM, 512GB SSD', 'quantity' => 10, 'unit' => 'pcs', 'unit_price' => 45000.00, 'date' => '2024-11-15', 'rfq' => 'RFQ-2024-0123'],
                    ['item_name' => 'HP LaserJet Pro M404dn', 'item_code' => 'PRT-HP-M404DN', 'description' => 'Monochrome Laser Printer, Network Ready', 'quantity' => 5, 'unit' => 'pcs', 'unit_price' => 15000.00, 'date' => '2024-10-20', 'rfq' => 'RFQ-2024-0105'],
                    ['item_name' => 'Logitech MX Master 3 Mouse', 'item_code' => 'MSE-LOG-MX3', 'description' => 'Wireless Mouse, Ergonomic Design', 'quantity' => 20, 'unit' => 'pcs', 'unit_price' => 3500.00, 'date' => '2024-12-01', 'rfq' => 'RFQ-2024-0156'],
                ],
            ],
            [
                'supplier_name' => 'Network Equipment Pro',
                'items' => [
                    ['item_name' => 'Cisco Catalyst 2960 Switch', 'item_code' => 'SW-CISCO-2960', 'description' => '24-Port Gigabit Ethernet Switch', 'quantity' => 3, 'unit' => 'pcs', 'unit_price' => 25000.00, 'date' => '2024-11-10', 'rfq' => 'RFQ-2024-0112'],
                    ['item_name' => 'TP-Link AC1750 Router', 'item_code' => 'RT-TPLINK-AC1750', 'description' => 'Dual Band WiFi Router, 1750Mbps', 'quantity' => 8, 'unit' => 'pcs', 'unit_price' => 4500.00, 'date' => '2024-10-25', 'rfq' => 'RFQ-2024-0098'],
                    ['item_name' => 'Cat6 Ethernet Cable', 'item_code' => 'CBL-CAT6-1M', 'description' => 'Cat6 UTP Cable, 1 meter length', 'quantity' => 100, 'unit' => 'pcs', 'unit_price' => 150.00, 'date' => '2024-12-05', 'rfq' => 'RFQ-2024-0167'],
                ],
            ],
            // Office Supplies
            [
                'supplier_name' => 'Office Supplies Depot',
                'items' => [
                    ['item_name' => 'A4 Bond Paper (Ream)', 'item_code' => 'PAP-A4-REAM', 'description' => 'High Quality A4 Bond Paper, 500 sheets', 'quantity' => 50, 'unit' => 'reams', 'unit_price' => 220.00, 'date' => '2024-11-20', 'rfq' => 'RFQ-2024-0134'],
                    ['item_name' => 'Ballpoint Pen (Blue)', 'item_code' => 'PEN-BP-BLUE', 'description' => 'Standard Blue Ballpoint Pen, Pack of 12', 'quantity' => 30, 'unit' => 'packs', 'unit_price' => 120.00, 'date' => '2024-10-15', 'rfq' => 'RFQ-2024-0087'],
                    ['item_name' => 'Stapler Heavy Duty', 'item_code' => 'STP-HD-001', 'description' => 'Heavy Duty Stapler with 1000 staples', 'quantity' => 10, 'unit' => 'pcs', 'unit_price' => 450.00, 'date' => '2024-11-05', 'rfq' => 'RFQ-2024-0101'],
                ],
            ],
            [
                'supplier_name' => 'Stationery & Paper Products',
                'items' => [
                    ['item_name' => 'Legal Size Bond Paper', 'item_code' => 'PAP-LEGAL-REAM', 'description' => 'Legal Size Bond Paper, 500 sheets per ream', 'quantity' => 25, 'unit' => 'reams', 'unit_price' => 240.00, 'date' => '2024-11-18', 'rfq' => 'RFQ-2024-0129'],
                    ['item_name' => 'Folder with Fastener', 'item_code' => 'FLD-FAST-001', 'description' => 'A4 Folder with 2 Fasteners, Colored', 'quantity' => 100, 'unit' => 'pcs', 'unit_price' => 35.00, 'date' => '2024-10-30', 'rfq' => 'RFQ-2024-0095'],
                    ['item_name' => 'Correction Tape', 'item_code' => 'COR-TAPE-001', 'description' => 'White Correction Tape, 6mm width', 'quantity' => 50, 'unit' => 'pcs', 'unit_price' => 85.00, 'date' => '2024-12-03', 'rfq' => 'RFQ-2024-0172'],
                ],
            ],
            // Construction
            [
                'supplier_name' => 'Construction Materials Co.',
                'items' => [
                    ['item_name' => 'Portland Cement (40kg)', 'item_code' => 'CEM-PORT-40KG', 'description' => 'Type 1 Portland Cement, 40kg bag', 'quantity' => 100, 'unit' => 'bags', 'unit_price' => 280.00, 'date' => '2024-11-12', 'rfq' => 'RFQ-2024-0118'],
                    ['item_name' => 'Steel Bar #4 (6m)', 'item_code' => 'STL-BAR-4-6M', 'description' => 'Deformed Steel Bar #4, 6 meters length', 'quantity' => 200, 'unit' => 'pcs', 'unit_price' => 450.00, 'date' => '2024-10-22', 'rfq' => 'RFQ-2024-0092'],
                    ['item_name' => 'Gravel (1 cubic meter)', 'item_code' => 'GRV-1M3', 'description' => 'Crushed Gravel, 1 cubic meter', 'quantity' => 50, 'unit' => 'cu.m', 'unit_price' => 1200.00, 'date' => '2024-11-28', 'rfq' => 'RFQ-2024-0145'],
                ],
            ],
            // Furniture
            [
                'supplier_name' => 'Furniture & Fixtures Ltd.',
                'items' => [
                    ['item_name' => 'Office Desk (120cm)', 'item_code' => 'DSK-OFF-120CM', 'description' => 'Wooden Office Desk, 120cm x 60cm', 'quantity' => 15, 'unit' => 'pcs', 'unit_price' => 8500.00, 'date' => '2024-11-08', 'rfq' => 'RFQ-2024-0108'],
                    ['item_name' => 'Ergonomic Office Chair', 'item_code' => 'CHR-ERG-001', 'description' => 'Adjustable Ergonomic Office Chair with Lumbar Support', 'quantity' => 20, 'unit' => 'pcs', 'unit_price' => 6500.00, 'date' => '2024-10-18', 'rfq' => 'RFQ-2024-0085'],
                    ['item_name' => 'Filing Cabinet (4 Drawer)', 'item_code' => 'CAB-FILE-4DR', 'description' => 'Steel Filing Cabinet, 4 Drawers, Lockable', 'quantity' => 8, 'unit' => 'pcs', 'unit_price' => 12000.00, 'date' => '2024-12-10', 'rfq' => 'RFQ-2024-0189'],
                ],
            ],
            // Security Equipment
            [
                'supplier_name' => 'Security Systems Inc.',
                'items' => [
                    ['item_name' => 'CCTV Camera (4MP)', 'item_code' => 'CAM-CCTV-4MP', 'description' => '4MP IP CCTV Camera, Night Vision', 'quantity' => 12, 'unit' => 'pcs', 'unit_price' => 8500.00, 'date' => '2024-11-22', 'rfq' => 'RFQ-2024-0141'],
                    ['item_name' => 'DVR System (16 Channel)', 'item_code' => 'DVR-16CH', 'description' => '16 Channel Digital Video Recorder', 'quantity' => 2, 'unit' => 'pcs', 'unit_price' => 25000.00, 'date' => '2024-10-28', 'rfq' => 'RFQ-2024-0100'],
                    ['item_name' => 'Access Control Card Reader', 'item_code' => 'ACR-CARD-001', 'description' => 'RFID Card Reader for Access Control', 'quantity' => 6, 'unit' => 'pcs', 'unit_price' => 12000.00, 'date' => '2024-12-08', 'rfq' => 'RFQ-2024-0180'],
                ],
            ],
            // Electrical
            [
                'supplier_name' => 'Electrical Supplies Corp.',
                'items' => [
                    ['item_name' => 'LED Panel Light (36W)', 'item_code' => 'LED-PANEL-36W', 'description' => '36W LED Panel Light, 600x600mm', 'quantity' => 40, 'unit' => 'pcs', 'unit_price' => 1800.00, 'date' => '2024-11-14', 'rfq' => 'RFQ-2024-0120'],
                    ['item_name' => 'Circuit Breaker (20A)', 'item_code' => 'CB-20A', 'description' => '20 Ampere Circuit Breaker, Single Pole', 'quantity' => 30, 'unit' => 'pcs', 'unit_price' => 850.00, 'date' => '2024-10-24', 'rfq' => 'RFQ-2024-0096'],
                    ['item_name' => 'Electrical Wire (2.5mm)', 'item_code' => 'WIR-2.5MM', 'description' => 'THHN Wire, 2.5mm, 100 meters', 'quantity' => 20, 'unit' => 'rolls', 'unit_price' => 3200.00, 'date' => '2024-12-12', 'rfq' => 'RFQ-2024-0195'],
                ],
            ],
            // Medical Equipment
            [
                'supplier_name' => 'Medical Equipment Solutions',
                'items' => [
                    ['item_name' => 'Digital Thermometer', 'item_code' => 'THM-DIG-001', 'description' => 'Infrared Digital Thermometer, Non-contact', 'quantity' => 25, 'unit' => 'pcs', 'unit_price' => 2500.00, 'date' => '2024-11-16', 'rfq' => 'RFQ-2024-0125'],
                    ['item_name' => 'Blood Pressure Monitor', 'item_code' => 'BPM-AUTO-001', 'description' => 'Automatic Digital Blood Pressure Monitor', 'quantity' => 10, 'unit' => 'pcs', 'unit_price' => 4500.00, 'date' => '2024-10-26', 'rfq' => 'RFQ-2024-0099'],
                    ['item_name' => 'First Aid Kit (Standard)', 'item_code' => 'FAK-STD-001', 'description' => 'Standard First Aid Kit, 100 pieces', 'quantity' => 15, 'unit' => 'kits', 'unit_price' => 1800.00, 'date' => '2024-12-06', 'rfq' => 'RFQ-2024-0175'],
                ],
            ],
            // AV Equipment
            [
                'supplier_name' => 'Audio Visual Equipment Pro',
                'items' => [
                    ['item_name' => 'Projector (Full HD)', 'item_code' => 'PRJ-FHD-001', 'description' => 'Full HD Projector, 3500 Lumens', 'quantity' => 5, 'unit' => 'pcs', 'unit_price' => 35000.00, 'date' => '2024-11-19', 'rfq' => 'RFQ-2024-0132'],
                    ['item_name' => 'Wireless Microphone System', 'item_code' => 'MIC-WLS-001', 'description' => 'UHF Wireless Microphone System, 2 Handheld', 'quantity' => 3, 'unit' => 'sets', 'unit_price' => 15000.00, 'date' => '2024-10-29', 'rfq' => 'RFQ-2024-0102'],
                    ['item_name' => 'Sound System Speaker (Active)', 'item_code' => 'SPK-ACT-001', 'description' => 'Active PA Speaker, 15" Woofer, 1000W', 'quantity' => 4, 'unit' => 'pcs', 'unit_price' => 28000.00, 'date' => '2024-12-14', 'rfq' => 'RFQ-2024-0201'],
                ],
            ],
            // Cleaning Supplies
            [
                'supplier_name' => 'Cleaning Supplies Distributor',
                'items' => [
                    ['item_name' => 'Floor Cleaner (5L)', 'item_code' => 'CLN-FLR-5L', 'description' => 'Concentrated Floor Cleaner, 5 Liters', 'quantity' => 20, 'unit' => 'bottles', 'unit_price' => 450.00, 'date' => '2024-11-24', 'rfq' => 'RFQ-2024-0148'],
                    ['item_name' => 'Disinfectant Spray (1L)', 'item_code' => 'DIS-SPR-1L', 'description' => 'Alcohol-based Disinfectant Spray, 1 Liter', 'quantity' => 50, 'unit' => 'bottles', 'unit_price' => 280.00, 'date' => '2024-10-31', 'rfq' => 'RFQ-2024-0106'],
                    ['item_name' => 'Trash Bag (Large)', 'item_code' => 'BAG-TRSH-LRG', 'description' => 'Large Trash Bag, 50 pieces per roll', 'quantity' => 30, 'unit' => 'rolls', 'unit_price' => 350.00, 'date' => '2024-12-16', 'rfq' => 'RFQ-2024-0208'],
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            $createdCount = 0;
            
            foreach ($quotationHistory as $supplierData) {
                // Find supplier by name
                $supplier = $suppliers->firstWhere('supplier_name_original', $supplierData['supplier_name']);
                
                if (!$supplier) {
                    continue; // Skip if supplier not found
                }

                // Create quotation history for each item
                foreach ($supplierData['items'] as $item) {
                    $itemCode = SupplierQuotationHistory::generateItemCode($item['item_name']);
                    $totalPrice = $item['quantity'] * $item['unit_price'];
                    
                    SupplierQuotationHistory::create([
                        'supplier_id' => $supplier->id,
                        'item_name' => $item['item_name'],
                        'item_code' => $itemCode,
                        'item_description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_of_measure' => $item['unit'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice,
                        'quotation_date' => $item['date'],
                        'rfq_number' => $item['rfq'],
                        'delivery_days' => rand(7, 30), // Random delivery days between 7-30
                        'payment_terms' => 'Net 30',
                        'validity_period' => Carbon::parse($item['date'])->addDays(60), // 60 days validity
                        'is_selected' => rand(0, 1) == 1, // Random selection
                        'entered_by' => $adminUser->id,
                    ]);
                    
                    $createdCount++;
                }
            }

            DB::commit();
            $this->command->info("Successfully created {$createdCount} quotation history entries!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create quotation history: ' . $e->getMessage());
        }
    }
}
