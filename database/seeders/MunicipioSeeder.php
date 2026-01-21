<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/municipios_desde2007_20170101.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('No se pudo abrir el CSV de municipios');
        }

        $now = Carbon::now();

        /**
         * Cacheamos las islas para no consultar la DB en cada fila
         * [
         *   'ES705' => ['id' => 3, 'name' => 'Gran Canaria']
         * ]
         */
        $islas = DB::table('isla')
            ->select('id', 'name', 'gdc_isla')
            ->get()
            ->keyBy('gdc_isla');

        $municipios = [];

        // Saltar cabecera
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {

            $gdcMunicipio = trim($row[0]); // geocode
            $nombreMunicipio = trim($row[2]); // etiqueta
            $gdcIsla = trim($row[6]); // gcd_isla

            if ($gdcMunicipio === '' || $nombreMunicipio === '' || $gdcIsla === '') {
                continue;
            }

            // Solo municipios canarios
            if (!isset($islas[$gdcIsla])) {
                continue;
            }

            $isla = $islas[$gdcIsla];

            $municipios[$gdcMunicipio] = [
                'name' => $nombreMunicipio,
                'gdc_municipio' => $gdcMunicipio,
                'gdc_isla' => $gdcIsla,
                'isla_id' => $isla->id,
                'isla_name' => $isla->name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        DB::table('municipio')->insert(array_values($municipios));
    }
}