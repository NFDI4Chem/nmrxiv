<?php

namespace App\Console\Commands;

use App\Models\Dataset;
use Illuminate\Console\Command;

class SanitizeDatasets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:datasets {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper scripts to maintain the database datasets.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $datasets = Dataset::orderBy('id', 'ASC')->get();

        foreach($datasets as $dataset){
            $dataset->type = implode( ',', array_unique(array_map('trim', explode(",", $dataset->type))));
            $dataset->save();
        }
    }
}
