<?php

namespace Database\Seeders;

use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use Illuminate\Database\Seeder;

class ServiceRequestCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Plumbing' => [
                ['name' => 'Leak Repair', 'sla' => [360, 240, 120, 60]],
                ['name' => 'Drain Cleaning', 'sla' => [480, 360, 180, 90]],
            ],
            'AC' => [
                ['name' => 'Cooling Issue', 'sla' => [420, 300, 120, 45]],
                ['name' => 'AC Maintenance', 'sla' => [720, 480, 240, 90]],
            ],
            'Electrical' => [
                ['name' => 'Power Trip', 'sla' => [240, 180, 90, 30]],
                ['name' => 'Light Fixture', 'sla' => [480, 360, 180, 60]],
            ],
            'Housekeeping' => [
                ['name' => 'Common Area Cleaning', 'sla' => [720, 480, 240, 120]],
                ['name' => 'Waste Removal', 'sla' => [360, 240, 120, 60]],
            ],
            'Internet' => [
                ['name' => 'Connection Unstable', 'sla' => [360, 240, 120, 60]],
                ['name' => 'Router Replacement', 'sla' => [480, 360, 180, 90]],
            ],
            'General' => [
                ['name' => 'General Assistance', 'sla' => [720, 480, 240, 120]],
                ['name' => 'Inspection Request', 'sla' => [720, 480, 240, 120]],
            ],
        ];

        foreach (array_values($catalog) as $categoryIndex => $subcategories) {
            $categoryName = array_keys($catalog)[$categoryIndex];

            $category = ServiceRequestCategory::query()->updateOrCreate(
                ['name' => $categoryName],
                [
                    'is_active' => true,
                    'sort_order' => ($categoryIndex + 1) * 10,
                ]
            );

            foreach ($subcategories as $subcategoryIndex => $subcategory) {
                ServiceRequestSubcategory::query()->updateOrCreate(
                    [
                        'service_request_category_id' => $category->id,
                        'name' => $subcategory['name'],
                    ],
                    [
                        'is_active' => true,
                        'sort_order' => ($subcategoryIndex + 1) * 10,
                        'low_sla_minutes' => $subcategory['sla'][0],
                        'medium_sla_minutes' => $subcategory['sla'][1],
                        'high_sla_minutes' => $subcategory['sla'][2],
                        'emergency_sla_minutes' => $subcategory['sla'][3],
                    ]
                );
            }
        }
    }
}
