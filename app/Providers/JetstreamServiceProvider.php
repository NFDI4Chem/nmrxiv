<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['project:read']);

        Jetstream::role('owner', 'Owner', [
            'project:create',
            'project:read',
            'project:update',
            'project:delete',
            'study:create',
            'study:read',
            'study:update',
            'study:delete',
            'dataset:read',
            'dataset:update',
            'dataset:delete',
        ])->description('Can read and/or update including delete the project, study and dataset.');

        Jetstream::role('collaborator', 'Collaborator', [
            'project:read',
            'project:update',
            'study:read',
            'study:update',
            'dataset:read',
            'dataset:update',
        ])->description('Can read and/or update project, study and dataset information.');

        Jetstream::role('reviewer', 'Reviewer', [
            'project:read',
            'study:read',
            'dataset:read',
        ])->description('Can only read project, study and dataset information.');
    }
}
