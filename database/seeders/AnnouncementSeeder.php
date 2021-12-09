<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Team;
use Carbon\Carbon;


class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_id = null;
        //Create an entry for Super_Admin if not present, else fetch the id for the existing one.
        if(User::count() == 0){
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
                'personal_team' => boolval(1),
            ]));
    
            $admin->assignRole('super-admin');
            $admin_id = $admin->id;
        } 
        else {
            $admin = User::role('super-admin')->firstOrFail();
            $admin_id = $admin->id;
        }

        //Creating seeder for the announcement table
        $announcement = Announcement::create([
            'title'   => "Scheduled-Maintenace",
            'message'     => "Dear User, You may experience sudden downtime on 12/10/2021 for few hours between 12-2 pm CET, due to some mandatory scheduled maintenance of the site. Apologies for any inconvenience caused. ",
            'status'     => "inactive",
            'start_time'    => '12/10/2021 12:00:00',
            'end_time'    => '12/10/2021 14:00:00',
            'user_id' => $admin->id,
        ]);
    }
}
