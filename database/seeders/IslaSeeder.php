<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class IslaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path(
            'data/dataset-ISTAC_E30243A_000001_1.5_20260116173657.csv'
        );

        if (!File::exists($path)) {
            $this->command->error('El archivo CSV no existe.');
            return;
        }

        $file = fopen($path, 'r');

        $header = fgetcsv($file, 0, ',');

        $territorioIndex = array_search('TERRITORIO#es', $header);

        if ($territorioIndex === false) {
            $this->command->error('La columna TERRITORIO#es no existe.');
            return;
        }

        $islas = [];

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $territorio = trim($row[$territorioIndex] ?? '');

            if (in_array($territorio, ['Canarias', 'Total', ''], true)) {
                continue;
            }

            $islas[] = $territorio;
        }

        fclose($file);

        $islas = array_unique($islas);

        DB::table('isla')->upsert(
            collect($islas)->map(fn ($isla) => [
                'name' => $isla,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray(),
            ['name']
        );

        $this->command->info('Islas de Canarias insertadas correctamente.');
    }
}
