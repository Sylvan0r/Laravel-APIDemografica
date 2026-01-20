<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Ruta al archivo CSV (asegúrate de colocarlo en /storage/app/csv/municipios.csv o similar)
        $csvFile = storage_path('database/data/municipios_desde2007_20170101.csv');
        
        if (!file_exists($csvFile)) {
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // Saltamos la cabecera

        // Mapeo manual o lógico de códigos de isla (ejemplo basado en tu CSV)
        // ES705 -> Isla 1, ES704 -> Isla 2, ES708 -> Isla 3
        // Ajusta estos IDs según cómo tengas rellena tu tabla 'isla'
        $mapaIslas = [
            'ES705' => 1, // Gran Canaria
            'ES704' => 2, // Fuerteventura
            'ES708' => 3, // Lanzarote
        ];

        while (($data = fgetcsv($handle)) !== FALSE) {
            DB::table('municipio')->insert([
                'name'       => $data[2], // Columna 'etiqueta'
                'isla_id'    => $mapaIslas[$data[6]] ?? 1, // Columna 'gcd_isla'
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($handle);
    }
}
