<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Str::random();
        $email = 'superadmin@email.com';

        $admin = User::create([
            'first_name' => 'Super admin',
            'last_name' => 'User',
            'email' => $email,
            'password' => bcrypt($password),
            'email_verified_at' => Carbon::now(),
        ]);

        $admin->ownedTeams()->save(Team::forceCreate([
            'user_id' => $admin->id,
            'name' => explode(' ', $admin->first_name, 2)[0]."'s Team",
            'personal_team' => boolval(1),
        ]));

        $admin->assignRole('super-admin');

        $this->command->alert('nmrXiv: Users table seed successfully');
        $this->command->line('You may log in to admin console using <info>'.$email.'</info> and password: <info>'.$password.'</info>');
        $this->command->line('');
    }
}
