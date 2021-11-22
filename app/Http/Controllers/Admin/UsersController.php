<?php

namespace App\Http\Controllers\Admin;

use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class UsersController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request)
    {
        return Inertia::render('Console/Users/Index', [
            'filters' => $request->all('search', 'owner', 'role', 'trashed'),
            'users' => User::orderBy('name')
                ->filter($request->only('search', 'owner', 'role', 'trashed'))
                ->get()
                ->transform(function ($user) {  
                    return [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'profile_photo_url' => $user->profile_photo_url,
                        'verified_at' => $user->email_verified_at,
                        'role' => $user->getRoleNames(),
                    ];
                }),
            'roles' => Role::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name', 'description'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Console/Users/Create',[
            'roles' => Role::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            ]
        );
    }

    public function store(Request $request, CreateNewUser $creator)
    {
        $user = $creator->create($request->all());
        event(new Registered($user));
        return redirect::route('users.edit', [$user]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Console/Users/Edit', [
            'edituser' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_url
            ],
        ]);
    }

    public function update(User $user, Request $request, UpdatesUserProfileInformation $updater)
    {
        $updater->update($user, $request->all());

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'profile-information-updated');
    }

    public function updatePassword(User $user, Request $request)
    {
        Validator::make($request->all(), [
            'password' => $this->passwordRules(),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($request['password']),
        ])->save();

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'profile-information-updated');
    }

    public function updateRole(User $user, Request $request)
    {
        Validator::make($request->all(), [
            'role' => 'required',
        ]);
        
        if (Auth::user()->id == $user->id ) {
            return $request->wantsJson() ? new JsonResponse('', 405) : back()->withErrors([
                'error_message' => 'Updating currently logged in user role is not allowed.'
            ]);
        }
        
        $role = $request->get('role');
        
        if($user && $role){
            $user->syncRoles([$role]);
        }

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('status', 'profile-information-updated');
    }

    public function destroyPhoto(User $user, Request $request)
    {
        $user->deleteProfilePhoto();

        return back(303)->with('status', 'profile-photo-deleted');
    }
}
