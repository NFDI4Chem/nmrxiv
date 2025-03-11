<?php

namespace Database\Seeders;

use App\Models\Ticker;
use Illuminate\Database\Seeder;

class TickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['project', 'study', 'dataset', 'sample', 'assay', 'molecule'];

        foreach ($types as $type) {
            Ticker::create([
                'index' => 0,
                'type' => $type,
            ]);
        }
    }
}
