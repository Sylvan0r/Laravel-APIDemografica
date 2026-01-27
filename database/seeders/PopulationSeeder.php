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

        /**
         * Cache islas válidas
         * ['ES707' => true]
         */
        $islas = DB::table('isla')
            ->pluck('gdc_isla')
            ->flip();

        /**
         * Cache municipios por nombre normalizado
         * ['santa cruz de la palma' => '35001']
         */
        $municipios = DB::table('municipio')
            ->select('gdc_municipio', 'name')
            ->get()
            ->mapWithKeys(function ($m) {
                return [
                    $this->normalize($m->name) => $m->gdc_municipio
                ];
            });

        $rows = [];

        // Saltar cabecera
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {

            /**
             * CSV columns relevantes:
             * 0  TERRITORIO (nombre)
             * 1  TERRITORIO_CODE (gdc_isla / ES70)
             * 3  TIME_PERIOD_CODE (year)
             * 4  SEXO
             * 6  EDAD
             * 12 MEDIDAS#es
             * 14 OBS_VALUE
             */

            $territorioNombre = trim($row[0]);
            $gdcIsla = trim($row[1]);
            $year = (int) $row[3];
            $gender = trim($row[4]);
            $age = trim($row[6]);
            $measure = trim($row[12]);
            $value = trim($row[14]);

            if ($value === '') {
                continue;
            }

            // Solo islas canarias
            if (!isset($islas[$gdcIsla])) {
                continue;
            }

            // Normalizar género
            if ($gender === 'Total') {
                $gender = 'T';
            }

            // Resolver municipio por nombre
            $gdcMunicipio = null;
            $normalizedTerritorio = $this->normalize($territorioNombre);

            if (isset($municipios[$normalizedTerritorio])) {
                $gdcMunicipio = $municipios[$normalizedTerritorio];
            }

            // Clave única lógica
            $key = implode('|', [
                $gdcIsla,
                $gdcMunicipio ?? 'ISLA',
                $year,
                $age,
                $gender,
            ]);

            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'gdc_isla' => $gdcIsla,
                    'gdc_municipio' => $gdcMunicipio,
                    'year' => $year,
                    'gender' => $gender,
                    'age' => $age,
                    'population' => null,
                    'proportion' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($measure === 'Población') {
                $rows[$key]['population'] = (int) $value;
            }

            if ($measure === 'Población. Proporción') {
                $rows[$key]['proportion'] = (float) $value;
            }
        }

        fclose($handle);

        // Insertar en bloques
        foreach (array_chunk(array_values($rows), 500) as $chunk) {
            DB::table('population')->insertOrIgnore($chunk);
        }
    }

    /**
     * Normaliza strings para comparación robusta
     */
    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        return preg_replace('/\s+/', ' ', $value);
    }
}
