<?php

namespace App\Console\Commands;

use App\Models\Dataset;
use App\Services\MIChI\MIChIService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SeedMIChI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:seed-michi {model} {operator?} {update-date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the table of the selected entries with the corresponding MIChI values that are automatically extracted. Entries can be selected updated before or after a given date.';

    /**
     * The MIChI service instance.
     *
     * @var App\Services\MIChI\MIChIService
     */
    protected $michiService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(MIChIService $michiService)
    {
        parent::__construct();

        // Assign the MIChI service instance
        $this->michiService = $michiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $model takes values from
        // {'project', 'study', 'daatset'}
        // $operator takes values from
        // {'>' : after a certain update date,
        //  '<' : before a certain update date,
        //  '=' : at a certain update date}
        $model = $this->argument('model');
        $operator = $this->argument('operator');
        $date = $this->argument('update-date');
        $date = $date ? Carbon::parse($date) : null;

        if ($model == 'dataset') {
            // Fetch the datasets based on the date
            $datasets = $date ? Dataset::where('updated_at', $operator, $date)->get() : Dataset::all();
            foreach ($datasets as $dataset) {
                echo $dataset->identifier."\r\n";
                $dataset->seedMIChI($this->michiService);
            }
        }
    }
}
