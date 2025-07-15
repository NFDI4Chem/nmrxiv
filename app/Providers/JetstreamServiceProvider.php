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

/**
 * Jetstream Service Provider
 *
 * This service provider configures Laravel Jetstream for team management
 * functionality in the NMRXIV application. It defines team actions, roles,
 * and permissions for collaborative research projects, enabling users to
 * work together on NMR data analysis and sharing.
 *
 * @package App\Providers
 * @author NMRXIV Development Team
 * @since 1.0.0
 */
class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is intentionally empty as Jetstream services are
     * configured in the boot method after the application has
     * been fully initialized.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Configures Jetstream team management features by registering custom
     * action classes for team operations and setting up the permission
     * system for collaborative research projects.
     *
     * @return void
     */
    public function boot(): void
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
     * Defines the permission system for collaborative research projects,
     * establishing three distinct roles (owner, collaborator, reviewer) with
     * specific permissions for project, study, and dataset operations.
     * This enables fine-grained access control for NMR data sharing.
     *
     * @return void
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['project:read']);

        Jetstream::role('owner', 'Owner', [
            'project:create',
            'project:read',
            'project:update',
            'study:create',
            'study:read',
            'study:update',
            'dataset:read',
            'dataset:update',
        ])->description('Can read and/or update project, sample and dataset(spectra) information. Additionally owner can also manage users/members of the project/sample/team. Please be aware that only the creator is authorized to delete/deprecate the project/sample/team.');

        Jetstream::role('collaborator', 'Collaborator', [
            'project:create',
            'project:read',
            'project:update',
            'study:create',
            'study:read',
            'study:update',
            'dataset:read',
            'dataset:update',
        ])->description('Can read and/or update project, sample and dataset(spectra) information.');

        Jetstream::role('reviewer', 'Reviewer', [
            'project:read',
            'study:read',
            'dataset:read',
        ])->description('Can only read project, sample and dataset(spectra) information.');
    }
}
