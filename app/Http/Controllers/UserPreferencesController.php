<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPreferencesRequest;
use Illuminate\Http\RedirectResponse;

class UserPreferencesController extends Controller
{
    public function update(UpdateUserPreferencesRequest $request): RedirectResponse
    {
        $user = $request->user();
        $preferences = $user->preferences ?? [];

        $tab = $request->validated('default_spectrum_tab');
        if ($tab === null) {
            unset($preferences['default_spectrum_tab']);
        } else {
            $preferences['default_spectrum_tab'] = $tab;
        }

        $user->forceFill([
            'preferences' => $preferences === [] ? null : $preferences,
        ])->save();

        return back();
    }
}
