<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get the first super admin account
        $admin = User::role('super-admin')->first();
        $password = Str::random();

        // If doesnt exist create one and assign the role super admin
        if (is_null($admin)) {
            $admin = User::create([
                'first_name' => 'Super admin',
                'last_name' => 'User',
                'email' => 'superadmin@email.com',
                'password' => bcrypt($password),
                'email_verified_at' => Carbon::now(),
            ]);

            $admin->ownedTeams()->save(Team::forceCreate([
                'user_id' => $admin->id,
                'name' => explode(' ', $admin->first_name, 2)[0]."'s Team",
                'personal_team' => boolval(1),
            ]));

            $admin->assignRole('super-admin');
        }

        // Creating seeder for the announcement table
        $announcement = Announcement::create([
            'title' => 'Scheduled-Maintenace',
            'status' => 'active',
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addDays(30),
            'message' => 'You may experience sudden downtime on 12/10/2021 for few hours between 12-2 pm CET, due to some mandatory scheduled maintenance of the site. Apologies for any inconvenience caused. ',
            'user_id' => $admin->id,
        ]);
    }
}
