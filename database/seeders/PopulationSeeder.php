<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PopulationSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/dataset-ISTAC_E30243A_000001_1.5_20260116173657.csv');
        if (!file_exists($path)) {
            throw new \RuntimeException("CSV no encontrado: $path");
        }

        $handle = fopen($path, 'r');
        $now = Carbon::now();

        // Cacheamos islas
        $islas = DB::table('isla')->select('id', 'gdc_isla', 'name')->get()->keyBy('gdc_isla');

        // Cacheamos municipios
        $municipios = DB::table('municipio')->select('id', 'gdc_municipio')->get()->keyBy('gdc_municipio');

        $data = [];

        // Saltar cabecera
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {

            $gdcIsla = trim($row[1]);
            $gdcMunicipio = trim($row[10]); // columna de gdc_municipio si aplica

            if (!isset($islas[$gdcIsla])) continue;
            $isla = $islas[$gdcIsla];

            $year = (int)$row[3];
            $age = trim($row[6]);
            $genderCsv = trim($row[4]);
            $gender = $genderCsv === 'Total' ? 'T' : $genderCsv;

            $medida = trim($row[12]); // "Población" o "Población. Proporción"
            $value = trim($row[14]);

            if ($value === '') continue;

            // Asignar municipio_id según gdc_municipio
            $municipioId = null;
            if ($gdcMunicipio !== '' && isset($municipios[$gdcMunicipio])) {
                $municipioId = $municipios[$gdcMunicipio]->id;
            }

            // Clave única por municipio o isla
            $key = ($municipioId ?? $isla->id) . '_' . $year . '_' . $age . '_' . $gender;

            if (!isset($data[$key])) {
                $data[$key] = [
                    'municipio_id' => $municipioId,
                    'isla_id' => $isla->id,
                    'year' => $year,
                    'gender' => $gender,
                    'age' => $age,
                    'population' => null,
                    'proportion' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($medida === 'Población') {
                $data[$key]['population'] = (int)$value;
            } elseif ($medida === 'Población. Proporción') {
                $data[$key]['proportion'] = (float)$value;
            }
        }

        fclose($handle);

        // Insertar en bloques de 500
        $chunks = array_chunk(array_values($data), 500);
        foreach ($chunks as $chunk) {
            DB::table('population')->insertOrIgnore($chunk);
        }
    }
}