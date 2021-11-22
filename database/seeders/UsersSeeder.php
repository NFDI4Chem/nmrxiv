<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Seeder;
use App\Actions\Fortify\CreateNewUser;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'first_name'     => "Super admin",
            'last_name'     => "User",
            'email'    => 'superadmin@email.com',
            'password' => bcrypt('secret'),
            'email_verified_at' => Carbon::now()
        ]);

        $admin->ownedTeams()->save(Team::forceCreate([
            'user_id' => $admin->id,
            'name' => explode(' ', $admin->first_name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));

        $admin->assignRole('super-admin');
    }
}
