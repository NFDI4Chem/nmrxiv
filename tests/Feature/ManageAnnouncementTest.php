<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManageAnnouncementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test if an announcement can be created.
     *
     * @return void
     */
    public function test_announcement_can_be_created()
    {
        $user = $this->createUser('super-admin');

        $announcement = Announcement::factory()->create([
            'user_id' => $user->id,
        ]);

        $body = [
            'title' => $announcement->title.'_updated',
            'enabled' => $this->faker->randomElement([true, false]),
            'start_time' => $announcement->start_time,
            'end_time' => $announcement->end_time,
            'message' => $announcement->message,
            'user_id' => $announcement->user_id,
        ];

        //Create Announcement
        $response = $this->createAnnouncement($body);

        $response->assertStatus(200);

        //Check if entry got created in DB
        $this->assertDatabaseHas('announcements', [
            'title' => $body['title'],
        ]);
    }

    /**
     * Test if an announcement can be updated.
     *
     * @return void
     */
    public function test_announcement_can_be_updated()
    {
        $user = $this->createUser('super-admin');

        $announcement = Announcement::factory()->create([
            'user_id' => $user->id,
        ]);

        $body = [
            'id' => $announcement->id,
            'title' => $announcement->title.'_updated',
            'enabled' => $this->faker->randomElement([true, false]),
            'start_time' => $announcement->start_time,
            'end_time' => $announcement->end_time,
            'message' => $announcement->message.'_updated',
            'user_id' => $announcement->user_id,
        ];

        //Edit Announcement
        $response = $this->editAnnouncement($body, $announcement->id);
        $response->assertStatus(200);

        //Check if entry got edited in DB
        $this->assertDatabaseHas('announcements', [
            'title' => $body['title'],
        ]);
    }

    /**
     * Test if an announcement can be deleted.
     *
     * @return void
     */
    public function test_announcement_can_be_deleted()
    {
        $user = $this->createUser('super-admin');

        $announcement = Announcement::factory()->create([
            'user_id' => $user->id,
        ]);

        //Delete Announcement
        $response = $this->deleteAnnouncement($announcement->id);

        $response->assertStatus(200);

        //Check if entry got deleted from DB
        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }

    /**
     * Test if an announcement cannot be managed from role other than super-admin & developer
     *
     * @return void
     */
    public function test_announcement_can_be_managed_only_by_admins()
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $adminRoles = ['super-admin', 'developer'];

        $roles = Role::whereNotIn('name', $adminRoles)->get();

        foreach ($roles as $role) {
            $this->actingAs($user = User::factory()->withPersonalTeam()->create());
            if ($user) {
                $user->syncRoles([$role]);
            }

            $announcement = Announcement::factory()->create([
                'user_id' => $user->id,
            ]);

            $body = [
                'title' => $announcement->title.'_updated',
                'enabled' => $this->faker->randomElement([true, false]),
                'start_time' => $announcement->start_time,
                'end_time' => $announcement->end_time,
                'message' => $announcement->message,
                'user_id' => $announcement->user_id,
            ];

            //Create Announcement
            $response = $this->createAnnouncement($body);
            $response->assertStatus(403);

            //Edit Announcement
            $reponse = $this->editAnnouncement($body, $announcement->id);
            $response->assertStatus(403);

            //Delete Announcement
            $reponse = $this->deleteAnnouncement($announcement->id);
            $response->assertStatus(403);
        }

        //Check if the entry is not affected
        $this->assertDatabaseHas('announcements', [
            'title' => $announcement->title,
        ]);
    }

    /**
     * Create user with given role
     *
     * @param  string  $role
     * @return \App\Model\User $user
     */
    public function createUser($role)
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        if ($user) {
            $user->syncRoles([$role]);
        }

        return $user;
    }

    /**
     * Make Request to create announcement
     *
     * @param  \App\Models\Announcement  $body
     * @return \Illuminate\Http\Response
     */
    public function createAnnouncement($body)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('admin/announcements/create', $body);
    }

    /**
     * Make Request to edit announcement
     *
     * @param  \App\Models\Announcement  $body
     * @param  int  $announcementId
     * @return \Illuminate\Http\Response
     */
    public function editAnnouncement($body, $announcementId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('admin/announcements/edit/'.$announcementId, $body);
    }

    /**
     * Make Request to delete announcement
     *
     * @param  int  $announcementId
     * @return \Illuminate\Http\Response
     */
    public function deleteAnnouncement($announcementId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('admin/announcements/delete/'.$announcementId);
    }
}
