<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use SplFileObject;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filepath = database_path('seeders/csv/city.csv');

        $file_for_line_count = new SplFileObject($filepath, "r");
        $file_for_line_count->seek(PHP_INT_MAX);   // jump to the last line
        $lineCount = $file_for_line_count->key() + 1; // keys are zero-based

        $bar = $this->command->getOutput()->createProgressBar($lineCount);

        $file = fopen($filepath, 'r');
        $bar->start();
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = explode(';', trim($line));
                //echo "Inserting county: " . $line[0] . " - " . $line[1] . PHP_EOL;
                City::create([
                    'zip_code' => (int) $line[0],
                    'name' => (string) $line[1],
                    'county_id' => (int) $line[2]
                ]);
                $bar->advance();
            }
        } else {
            echo "Error opening the file.";
        }
        $bar->finish();
        echo PHP_EOL;
        fclose($file);
    }
}
