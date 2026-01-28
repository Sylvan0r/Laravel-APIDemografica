<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PopulationSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '1024M');
        $path = database_path('data/dataset-ISTAC_E30243A_000001_1.5_20260116173657.csv');
        
        $now = Carbon::now();

        // 1. CARGA DE MAPAS (Aseguramos que las claves sean strings limpios)
        $municipiosMap = DB::table('municipio')->pluck('gdc_isla', 'gdc_municipio')->toArray();
        $islasExistentes = DB::table('isla')->pluck('gdc_isla')->flip()->toArray();

        $rows = [];
        $handle = fopen($path, 'r');
        
        // Saltar cabecera
        fgets($handle); 

        $debugDone = false;

        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            if (count($row) < 15) continue;

            // --- LIMPIEZA EXTREMA ---
            // Quitamos BOM, comillas y espacios en blanco
            $codigoCSV = trim(str_replace(['"', "\xEF\xBB\xBF"], '', $row[1])); 
            
            // Si el código es puramente numérico, nos aseguramos de que no tenga espacios
            if (is_numeric($codigoCSV)) {
                $codigoCSV = (string)intval($codigoCSV);
            }

            $year      = (int) $row[3];
            $genderRaw = trim($row[4]);
            $age       = trim($row[6]);
            $measure   = trim($row[13]); 
            $value     = trim($row[14]);

            if ($value === "" || $value === null) continue;

            $gdcMunicipio = null;
            $gdcIsla      = null;

            // 2. LÓGICA DE ASIGNACIÓN CON DEBUG
            if (array_key_exists($codigoCSV, $municipiosMap)) {
                $gdcMunicipio = $codigoCSV;
                $gdcIsla      = $municipiosMap[$codigoCSV];
            } elseif (isset($islasExistentes[$codigoCSV]) || $codigoCSV === 'ES70') {
                $gdcIsla      = $codigoCSV;
                $gdcMunicipio = null;
            } else {
                // Si no coincide, intentamos buscarlo en el mapa ignorando ceros a la izquierda
                $clean = ltrim($codigoCSV, '0');
                if (array_key_exists($clean, $municipiosMap)) {
                    $gdcMunicipio = $clean;
                    $gdcIsla = $municipiosMap[$clean];
                } else {
                    continue; 
                }
            }

            // Normalizar género
            $gender = match ($genderRaw) {
                'Hombres' => 'M',
                'Mujeres' => 'F',
                default   => 'T'
            };

            $key = ($gdcIsla ?? 'X') . '-' . ($gdcMunicipio ?? 'Y') . "-{$year}-{$age}-{$gender}";

            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'gdc_isla'      => $gdcIsla,
                    'gdc_municipio' => $gdcMunicipio,
                    'year'          => $year,
                    'gender'        => $gender,
                    'age'           => $age,
                    'population'    => null,
                    'proportion'    => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            if ($measure === 'POBLACION') {
                $rows[$key]['population'] = (int) $value;
            } elseif ($measure === 'POBLACION_PROPORCION') {
                $rows[$key]['proportion'] = (float) $value;
            }
        }
        fclose($handle);

        // 3. INSERCIÓN
        $registrosConMunicipio = collect($rows)->whereNotNull('gdc_municipio')->count();
        $this->command->info("Registros con municipio encontrados: $registrosConMunicipio");

        foreach (array_chunk(array_values($rows), 1000) as $chunk) {
            DB::table('population')->insert($chunk);
        }
    }
}