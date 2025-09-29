<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\County;

class CountiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filepath = database_path('seeders/csv/county.csv');

        $file = fopen($filepath, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = explode(';', trim($line));
                //echo "Inserting county: " . $line[0] . " - " . $line[1] . PHP_EOL;
                County::create([
                    'id' => (int) $line[0],
                    'name' => (string) $line[1]
                ]);
            }
        } else {
            echo "Error opening the file.";
        }
        fclose($file);
    }
}
