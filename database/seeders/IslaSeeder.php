<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IslaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/dataset-ISTAC_E30243A_000001_1.5_20260116173657.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('No se pudo abrir el CSV');
        }

        $now = Carbon::now();
        $islas = [];

        // Saltar cabecera
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $nombre = trim($row[0]); // TERRITORIO#es
            $codigo = trim($row[1]); // TERRITORIO_CODE

            if ($nombre === '' || $codigo === '') {
                continue;
            }

            //  FILTRO SOLO CANARIAS
            if (!str_starts_with($codigo, 'ES70')) {
                continue;
            }

            // DEDUPLICACIÓN POR ISLA
            if (!isset($islas[$codigo])) {
                $islas[$codigo] = [
                    'gdc_isla' => $codigo,
                    'name' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        fclose($handle);

        DB::table('isla')->insert(array_values($islas));
    }
}