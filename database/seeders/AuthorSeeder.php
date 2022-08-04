<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

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
            'affiliation' => '',

        ]);
    }
}
