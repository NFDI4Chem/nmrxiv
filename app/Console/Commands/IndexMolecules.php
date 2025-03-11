<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IndexMolecules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:index-molecules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex molecules';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        return DB::transaction(function () {
            DB::statement('DROP TABLE IF EXISTS mols, fps');
            DB::statement('create extension if not exists rdkit');
            DB::statement('select * into mols from (select id,mol_from_smiles(canonical_smiles::cstring) m  from molecules) tmp where m is not null');
            DB::statement('create index molidx on mols using gist(m)');
            DB::statement('alter table mols add primary key (id)');
            DB::statement('select id, torsionbv_fp(m) as torsionbv,morganbv_fp(m) as mfp2, featmorganbv_fp(m) as ffp2 into fps from mols');
            DB::statement('create index fps_ttbv_idx on fps using gist(torsionbv)');
            DB::statement('create index fps_mfp2_idx on fps using gist(mfp2)');
            DB::statement('create index fps_ffp2_idx on fps using gist(ffp2)');
            DB::statement('alter table fps add primary key (id)');
        });
    }
}
