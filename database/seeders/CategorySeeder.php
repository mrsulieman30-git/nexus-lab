<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Laboratory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get the Lab ID (Defaults to the latest created lab)
        $lab = Laboratory::latest('id')->first();

        if (!$lab) {
            // Fallback if no lab exists
            $lab = Laboratory::create([
                'name' => 'Default Lab',
                'email' => 'admin@default.com',
                'status' => 'active'
            ]);
        }

        $labId = $lab->id;

        // 2. Define the Comprehensive List
        $categories = [
            // --- DEPARTMENT: HEMATOLOGY ---
            [
                'name' => 'Hematology Reagents',
                'description' => 'Diluents, Lyse, Cleaners, and Rinse solutions for CBC analyzers.',
                'color_hex' => '#ef4444' // Red-500
            ],
            [
                'name' => 'Coagulation Supplies',
                'description' => 'PT/INR, APTT reagents, and cuvettes.',
                'color_hex' => '#b91c1c' // Red-700
            ],

            // --- DEPARTMENT: BIOCHEMISTRY ---
            [
                'name' => 'Clinical Chemistry Reagents',
                'description' => 'Routine substrates, enzymes, and electrolytes (e.g., Glucose, Urea, Creatinine).',
                'color_hex' => '#22c55e' // Green-500
            ],
            [
                'name' => 'Calibrators & Controls',
                'description' => 'Standard solutions and QC materials for machine calibration.',
                'color_hex' => '#15803d' // Green-700
            ],

            // --- DEPARTMENT: MICROBIOLOGY ---
            [
                'name' => 'Culture Media',
                'description' => 'Agar plates (Blood, MacConkey, CLED) and broth powders.',
                'color_hex' => '#a855f7' // Purple-500
            ],
            [
                'name' => 'Microbiology Stains',
                'description' => 'Gram stain, Giemsa, Ziehl-Neelsen, and other dyes.',
                'color_hex' => '#7e22ce' // Purple-700
            ],
            [
                'name' => 'Antibiotic Discs',
                'description' => 'Susceptibility testing discs.',
                'color_hex' => '#d8b4fe' // Purple-300
            ],

            // --- DEPARTMENT: IMMUNOLOGY & SEROLOGY ---
            [
                'name' => 'Rapid Test Kits',
                'description' => 'Lateral flow cassettes (Malaria, HIV, H. Pylori, HCG, Hep B/C).',
                'color_hex' => '#f59e0b' // Amber-500
            ],
            [
                'name' => 'ELISA / CLIA Reagents',
                'description' => 'Hormone assays, tumor markers, and infectious disease kits.',
                'color_hex' => '#d97706' // Amber-600
            ],

            // --- CONSUMABLES: PHLEBOTOMY ---
            [
                'name' => 'Phlebotomy Supplies',
                'description' => 'Vacutainers (EDTA, Gel, Plain), needles, syringes, tourniquets, and cotton.',
                'color_hex' => '#3b82f6' // Blue-500
            ],

            // --- CONSUMABLES: GENERAL ---
            [
                'name' => 'Plasticware (Disposables)',
                'description' => 'Pipette tips (Blue/Yellow/White), Eppendorf tubes, Urine cups, Petri dishes.',
                'color_hex' => '#64748b' // Slate-500
            ],
            [
                'name' => 'Glassware',
                'description' => 'Microscope slides, cover slips, beakers, measuring cylinders.',
                'color_hex' => '#94a3b8' // Slate-400
            ],

            // --- HISTOPATHOLOGY ---
            [
                'name' => 'Histopathology Supplies',
                'description' => 'Formalin, Xylene, Ethanol, Paraffin wax, and Microtome blades.',
                'color_hex' => '#be185d' // Pink-700
            ],

            // --- SAFETY & PPE ---
            [
                'name' => 'PPE & Safety',
                'description' => 'Examination gloves, face masks, lab coats, biohazard bags, and sharps containers.',
                'color_hex' => '#f97316' // Orange-500
            ],

            // --- EQUIPMENT ---
            [
                'name' => 'Spare Parts & Maintenance',
                'description' => 'Bulbs, electrodes, tubing, and service kits for analyzers.',
                'color_hex' => '#475569' // Slate-600
            ],
            [
                'name' => 'Stationery & Office',
                'description' => 'Thermal paper rolls, A4 paper, printer toner, markers.',
                'color_hex' => '#000000' // Black
            ],
        ];

        // 3. Loop and Create
        foreach ($categories as $cat) {
            Category::firstOrCreate(
                [
                    'laboratory_id' => $labId,
                    'name' => $cat['name']
                ],
                [
                    'description' => $cat['description'],
                    'color_hex' => $cat['color_hex']
                ]
            );
        }
    }
}
