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
    public function handle(): int
    {
        $datasets = Dataset::orderBy('id', 'ASC')->get();

        foreach ($datasets as $dataset) {
            if ($dataset->has_nmrium) {
                $nmrium = $dataset->nmrium;
                $nmriumInfo = json_decode($nmrium['nmrium_info'], true);
                if ($nmriumInfo && ! empty($nmriumInfo)) {
                    $spectra = $nmriumInfo['data']['spectra'];
                    foreach ($spectra as $spectrum) {
                        $experiment = $spectrum['info']['experiment'];
                        if (is_null($experiment)) {
                            $nucleus = $spectrum['info']['nucleus'];
                            if (is_array($nucleus)) {
                                $nucleus = implode('-', $nucleus);
                            }
                            $dataset->type = implode(',', array_unique(array_map('trim', explode(',', $nucleus.', '.$dataset->type))));
                        } else {
                            $dataset->type = $spectrum['info']['experiment'];
                        }
                    }
                }
            }
            $dataset->save();
        }
    }
}
