<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JsonUniversitiesAndSpecializationsSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables in the correct order
        DB::table('specialization_university')->truncate();
        DB::table('universities')->truncate();
        DB::table('specializations')->truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Load JSON files
        $specializations = json_decode(file_get_contents(base_path('specializations.json')), true);
        $universities = json_decode(file_get_contents(base_path('universities.json')), true);
        $uniWithSpecs = json_decode(file_get_contents(base_path('universities-with-specializations.json')), true);

        echo "Loaded JSON files successfully.\n";

        // Insert specializations first
        foreach ($specializations as $spec) {
            DB::table('specializations')->insert([
                'id' => $spec['id'],
                'name' => $spec['name'],
            ]);
        }
        echo "Inserted specializations successfully.\n";

        // Insert universities next
        foreach ($universities as $uni) {
            DB::table('universities')->insert([
                'id' => $uni['id'],
                'name' => $uni['name'],
            ]);
        }
        echo "Inserted universities successfully.\n";

        // Insert specialization_university last (using new structure)
        foreach ($uniWithSpecs as $row) {
            $universityId = $row['id'];
            if (!isset($row['specializations']) || !is_array($row['specializations'])) continue;
            foreach ($row['specializations'] as $spec) {
                DB::table('specialization_university')->insert([
                    'university_id' => $universityId,
                    'specialization_id' => $spec['id'],
                ]);
            }
        }
        echo "Inserted specialization_university successfully.\n";
    }
}
