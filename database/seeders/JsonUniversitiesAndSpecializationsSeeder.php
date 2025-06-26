<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JsonUniversitiesAndSpecializationsSeeder extends Seeder
{
    public function run()
    {
        // Clear tables first
        DB::table('specialization_university')->truncate();
        DB::table('specializations')->truncate();
        DB::table('universities')->truncate();

        // Load JSON files
        $specializations = json_decode(file_get_contents(base_path('specializations.json')), true);
        $universities = json_decode(file_get_contents(base_path('universities.json')), true);
        $uniWithSpecs = json_decode(file_get_contents(base_path('universities-with-specializations.json')), true);

        // Insert universities
        foreach ($universities as $uni) {
            DB::table('universities')->insert([
                'id' => $uni['id'],
                'name' => $uni['name'],
            ]);
        }

        // Insert specializations
        foreach ($specializations as $spec) {
            DB::table('specializations')->insert([
                'id' => $spec['id'],
                'name' => $spec['name'],
            ]);
        }

        // Insert specialization_university
        foreach ($uniWithSpecs as $row) {
            $universityId = $row['university_id'];
            foreach ($row['specialization_ids'] as $specId) {
                DB::table('specialization_university')->insert([
                    'university_id' => $universityId,
                    'specialization_id' => $specId,
                ]);
            }
        }
    }
}
