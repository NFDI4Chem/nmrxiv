<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $author = Author::create([
            'orcid_id' => '',
            'given_name' => 'Super',
            'family_name' => 'admin',
            'email_id' => 'superadmin@email.com',
            'affiliation' => ''

        ]);
    }
}
