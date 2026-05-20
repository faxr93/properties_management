<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyRental;
use App\Models\PropertyValuation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // Sample properties in Cyberjaya, Selangor, Malaysia.
        // `center` is [lat, lng]. We build a rectangular polygon around each center.
        $samples = [
            [
                'reference_no' => 'CYB-0001',
                'name'         => 'Shaftsbury Square',
                'type'         => 'mixed_use',
                'status'       => 'occupied',
                'address'      => 'Persiaran Multimedia, Cyber 6',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 14_500,
                'building_area'=> 89_000,
                'year_built'   => 2014,
                'description'  => 'Integrated lifestyle, office and serviced-suites podium development in the heart of Cyberjaya.',
                'center'       => [2.91894, 101.65530],
                'size'         => 0.00080,
            ],
            [
                'reference_no' => 'CYB-0002',
                'name'         => 'Tamarind Square',
                'type'         => 'commercial',
                'status'       => 'occupied',
                'address'      => 'Persiaran Multimedia, Cyber 5',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 11_200,
                'building_area'=> 41_000,
                'year_built'   => 2018,
                'description'  => 'Open-air lifestyle precinct with retail, F&B, co-working and SOHO units.',
                'center'       => [2.92807, 101.65420],
                'size'         => 0.00060,
            ],
            [
                'reference_no' => 'CYB-0003',
                'name'         => 'Cyberview Garden Office Tower',
                'type'         => 'commercial',
                'status'       => 'available',
                'address'      => 'Persiaran Cyberpoint Selatan, Cyber 8',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 5_800,
                'building_area'=> 28_500,
                'year_built'   => 2016,
                'description'  => 'Grade-A office tower with MSC Malaysia status, near Cyberjaya MRT alignment.',
                'center'       => [2.92434, 101.65010],
                'size'         => 0.00045,
            ],
            [
                'reference_no' => 'CYB-0004',
                'name'         => 'Symphony Hills Cluster Home A12',
                'type'         => 'residential',
                'status'       => 'available',
                'address'      => 'Jalan Symphony 3, Symphony Hills',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 320,
                'building_area'=> 290,
                'bedrooms'     => 4,
                'bathrooms'    => 4,
                'year_built'   => 2017,
                'description'  => 'Three-storey link villa in the gated Symphony Hills enclave.',
                'center'       => [2.91020, 101.64150],
                'size'         => 0.00020,
            ],
            [
                'reference_no' => 'CYB-0005',
                'name'         => 'Cyberjaya Innovation Hub – Block A',
                'type'         => 'industrial',
                'status'       => 'under_review',
                'address'      => 'Persiaran APEC, Cyber 8',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 22_400,
                'building_area'=> 14_500,
                'year_built'   => 2012,
                'description'  => 'R&D-grade flexi-industrial campus catering to MSC status companies.',
                'center'       => [2.93105, 101.64545],
                'size'         => 0.00100,
            ],
            [
                'reference_no' => 'CYB-0006',
                'name'         => 'Cyberjaya AgroTech Estate',
                'type'         => 'agricultural',
                'status'       => 'available',
                'address'      => 'Jalan Putra Permai, Cyber 12',
                'city'         => 'Cyberjaya',
                'state'        => 'Selangor',
                'postal_code'  => '63000',
                'land_area'    => 68_000,
                'year_built'   => null,
                'description'  => 'Buffer agricultural plot near the Cyberjaya-Putrajaya boundary, suited to urban farming.',
                'center'       => [2.94550, 101.67500],
                'size'         => 0.00220,
            ],
        ];

        $tenantPool = [
            'IRIS Smart Tech Sdn Bhd',
            'Cyberview Innovations Sdn Bhd',
            'Sapura Digital Ventures Sdn Bhd',
            'Tech Mahindra Malaysia Sdn Bhd',
            'Maybank Innovation Hub Sdn Bhd',
            'Touch \'n Go Digital Sdn Bhd',
            'Nexa Ventures (M) Sdn Bhd',
            'DST Innovation Sdn Bhd',
        ];

        foreach ($samples as $sample) {
            [$lat, $lng] = $sample['center'];
            $size = $sample['size'];

            // GeoJSON Polygon ring (closed) — coordinates are [lng, lat] per GeoJSON spec.
            $ring = [
                [$lng - $size, $lat - $size],
                [$lng + $size, $lat - $size],
                [$lng + $size, $lat + $size],
                [$lng - $size, $lat + $size],
                [$lng - $size, $lat - $size],
            ];

            $property = Property::create(array_merge(
                collect($sample)->except(['center', 'size'])->toArray(),
                [
                    'country'  => 'Malaysia',
                    'location' => [
                        'type'        => 'Point',
                        'coordinates' => [$lng, $lat],
                    ],
                    'boundary' => [
                        'type'        => 'Polygon',
                        'coordinates' => [$ring],
                    ],
                ]
            ));

            // Indicative market values (MYR).
            $baseValue = match ($property->type) {
                'mixed_use'    => 480_000_000,
                'commercial'   => 240_000_000,
                'industrial'   =>  95_000_000,
                'residential'  =>   2_800_000,
                'agricultural' =>  18_000_000,
                'land'         =>   6_500_000,
                default        =>  10_000_000,
            };

            foreach (range(0, rand(1, 2)) as $i) {
                $valuationDate = Carbon::now()->subMonths($i * 6 + rand(0, 4));
                PropertyValuation::create([
                    'property_id'    => $property->id,
                    'valuation_date' => $valuationDate,
                    'market_value'   => $baseValue * (1 + ($i * -0.04)),
                    'land_value'     => $baseValue * 0.6 * (1 + ($i * -0.04)),
                    'building_value' => $baseValue * 0.4 * (1 + ($i * -0.04)),
                    'currency'       => 'MYR',
                    'method'         => ['market', 'income', 'comparative'][array_rand([0, 1, 2])],
                    'valuer_name'    => 'CBRE | WTW Malaysia',
                    'valuer_license' => 'V-' . rand(1000, 9999),
                    'notes'          => 'Indicative POC valuation for Cyberjaya portfolio.',
                ]);
            }

            if ($property->status === 'occupied') {
                $tenant = $tenantPool[array_rand($tenantPool)];
                PropertyRental::create([
                    'property_id'   => $property->id,
                    'tenant_name'   => $tenant,
                    'tenant_email'  => 'lease+' . $property->id . '@example.my',
                    'tenant_phone'  => '+60 3 ' . rand(8000, 8999) . ' ' . rand(1000, 9999),
                    'start_date'    => Carbon::now()->subMonths(rand(2, 18))->startOfMonth(),
                    'end_date'      => Carbon::now()->addMonths(rand(6, 24))->endOfMonth(),
                    'monthly_rent'  => round($baseValue * 0.0006, 2),
                    'deposit'       => round($baseValue * 0.0006 * 3, 2),
                    'currency'      => 'MYR',
                    'payment_cycle' => 'monthly',
                    'status'        => 'active',
                    'notes'         => 'Seeded sample lease for POC.',
                ]);
            }
        }
    }
}
