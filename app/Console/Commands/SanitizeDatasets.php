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

        foreach ($datasets as $dataset) {
            if ($dataset->has_nmrium) {
                $nmrium = $dataset->nmrium;
                $nmriumInfo = json_decode($nmrium['nmrium_info'], true);
                if ($nmriumInfo && ! empty($nmriumInfo)) {
                    $spectra = $nmriumInfo['data']['spectra'];
                    $experiment = [];
                    $solvent = [];
                    $temperature = [];
                    $nucleus = [];
                    $probe = [];
                    foreach ($spectra as $spectrum) {
                        if (array_key_exists('experiment', $spectrum['info'])) {
                            array_push($experiment, $spectrum['info']['experiment']);
                        }
                        if (array_key_exists('solvent', $spectrum['info'])) {
                            array_push($solvent, $spectrum['info']['solvent']);
                        }
                        if (array_key_exists('temperature', $spectrum['info'])) {
                            array_push($temperature, $spectrum['info']['temperature']);
                        }
                        if (array_key_exists('nucleus', $spectrum['info'])) {
                            array_push($nucleus, implode('-', is_string($spectrum['info']['nucleus']) ? [$spectrum['info']['nucleus']] : $spectrum['info']['nucleus']));
                        }
                        if (array_key_exists('probeName', $spectrum['info'])) {
                            array_push($probe, $spectrum['info']['probeName']);
                        }
                    }
                    $dataset->experiment = $this->stringify($experiment, true);
                    $dataset->solvent = $this->stringify($solvent, false);
                    $dataset->temperature = $this->stringify($temperature, false);
                    $dataset->probe = $this->stringify($probe, false);
                    $dataset->nucleus = $this->stringify($nucleus, false);
                } else {
                    echo 'Dataset: '.$dataset->id.' - No spectra';
                    echo "\r\n";
                }
            }
            $dataset->save();
        }
    }

    public function stringify($array, $uppercase)
    {
        $array = array_filter(array_unique($array));
        if ($uppercase) {
            return strtoupper(implode(',', $array));
        }

        return implode(',', $array);
    }
}
