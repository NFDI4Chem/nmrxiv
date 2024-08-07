<?php

namespace App\Actions\Jetstream;

use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\DeletesTeams;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     *
     * @param  mixed  $team
     * @return void
     */
    public function delete($team)
    {
        if ($team->projects()->count() > 0) {
            throw ValidationException::withMessages([
                'hasProject' => __('This team cannot be deletd as it has project associated with it. Please do not forget to wait for the cool off period of 30 days for your project to be deleted permanently.'),
            ]);
        }
        $team->purge();

        return redirect()->route('dashboard')->with('success', 'Team deleted successfully');
    }
}
