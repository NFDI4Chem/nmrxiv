<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $announcement = Announcement::create([
            'title'   => "Scheduled-Maintenace",
            'message'     => "Dear User, You may experience sudden downtime on 12/10/2021 for few hours between 12-2 pm CET, due to some mandatory scheduled maintenance of the site. Apologies for any inconvenience caused. ",
            'status'     => "Inactive",
            'start_time'    => '12/10/2021 12:00:00',
            'end_time'    => '12/10/2021 14:00:00',
            'user_id' => 1
        ]);
    }
}
