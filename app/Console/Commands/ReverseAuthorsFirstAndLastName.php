<?php

namespace App\Console\Commands;

use App\Models\Author;
use Illuminate\Console\Command;

class ReverseAuthorsFirstAndLastName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:reverse-author-names {ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reverse column data of given name and family name in author table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ids = $this->argument('ids') ? explode(',', $this->argument('ids')) : null;
        if ($ids) {
            $authors = Author::whereIn('id', $ids)->get();
        } else {
            $authors = Author::all();
        }

        foreach ($authors as $author) {
            if ($author->given_name && $author->family_name) {
                $given_name_tmp = $author->given_name;
                $family_name_tmp = $author->family_name;
                $author->given_name = $family_name_tmp;
                $author->family_name = $given_name_tmp;
            }
            $author->save();
        }
    }
}
