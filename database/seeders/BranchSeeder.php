<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create branches - Damascus areas
        $branches = [
            [
                'name' => 'فرع المزة',
                'code' => 'MZE',
                'city' => 'دمشق',
                'address' => 'المزة - شارع الفيلات الغربية',
                'phone' => '+963111234567',
                'email' => 'mazzeh@khezana.com',
                'latitude' => 33.5024,
                'longitude' => 36.2465,
                'is_active' => true,
                'working_hours' => [
                    'السبت' => '09:00 - 21:00',
                    'الأحد' => '09:00 - 21:00',
                    'الاثنين' => '09:00 - 21:00',
                    'الثلاثاء' => '09:00 - 21:00',
                    'الأربعاء' => '09:00 - 21:00',
                    'الخميس' => '09:00 - 22:00',
                    'الجمعة' => 'مغلق',
                ],
            ],
            [
                'name' => 'فرع الشعلان',
                'code' => 'SHL',
                'city' => 'دمشق',
                'address' => 'الشعلان - بجانب مطعم أبو كمال',
                'phone' => '+963112234567',
                'email' => 'shaalan@khezana.com',
                'latitude' => 33.5139,
                'longitude' => 36.2835,
                'is_active' => true,
                'working_hours' => [
                    'السبت' => '10:00 - 22:00',
                    'الأحد' => '10:00 - 22:00',
                    'الاثنين' => '10:00 - 22:00',
                    'الثلاثاء' => '10:00 - 22:00',
                    'الأربعاء' => '10:00 - 22:00',
                    'الخميس' => '10:00 - 23:00',
                    'الجمعة' => 'مغلق',
                ],
            ],
            [
                'name' => 'فرع المالكي',
                'code' => 'MLK',
                'city' => 'دمشق',
                'address' => 'المالكي - شارع أبو رمانة',
                'phone' => '+963113234567',
                'email' => 'malki@khezana.com',
                'latitude' => 33.5185,
                'longitude' => 36.2785,
                'is_active' => true,
                'working_hours' => [
                    'السبت' => '09:00 - 20:00',
                    'الأحد' => '09:00 - 20:00',
                    'الاثنين' => '09:00 - 20:00',
                    'الثلاثاء' => '09:00 - 20:00',
                    'الأربعاء' => '09:00 - 20:00',
                    'الخميس' => '09:00 - 21:00',
                    'الجمعة' => 'مغلق',
                ],
            ],
            [
                'name' => 'فرع باب توما',
                'code' => 'BTM',
                'city' => 'دمشق',
                'address' => 'باب توما - الشارع المستقيم',
                'phone' => '+963114234567',
                'email' => 'babtoma@khezana.com',
                'latitude' => 33.5116,
                'longitude' => 36.3147,
                'is_active' => true,
                'working_hours' => [
                    'السبت' => '10:00 - 21:00',
                    'الأحد' => '10:00 - 21:00',
                    'الاثنين' => '10:00 - 21:00',
                    'الثلاثاء' => '10:00 - 21:00',
                    'الأربعاء' => '10:00 - 21:00',
                    'الخميس' => '10:00 - 22:00',
                    'الجمعة' => 'مغلق',
                ],
            ],
            [
                'name' => 'فرع جرمانا',
                'code' => 'JRM',
                'city' => 'ريف دمشق',
                'address' => 'جرمانا - الشارع الرئيسي',
                'phone' => '+963115234567',
                'email' => 'jaramana@khezana.com',
                'latitude' => 33.4835,
                'longitude' => 36.3547,
                'is_active' => true,
                'working_hours' => [
                    'السبت' => '09:00 - 21:00',
                    'الأحد' => '09:00 - 21:00',
                    'الاثنين' => '09:00 - 21:00',
                    'الثلاثاء' => '09:00 - 21:00',
                    'الأربعاء' => '09:00 - 21:00',
                    'الخميس' => '09:00 - 22:00',
                    'الجمعة' => 'مغلق',
                ],
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::firstOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );
        }

        $this->command->info('Created ' . count($branches) . ' branches in Damascus.');
    }
}
