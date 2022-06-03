<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasTeams;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use HasProjects;
    use HasStudies;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name' , 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * User model and LinkedSocialAccount relationship - one to many
     *
     * @var array
     */
    public function linkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }

    /**
     * Get the announcements created by the user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->first_name.'+'.$this->last_name).'&color=7F9CF5&background=EBF4FF';
    }

    public function scopeOrderByName($query)
    {
        return $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeWhereRole($query, $role)
    {
        return $query->whereHas("roles", function($q) use($role) { $q->where("name", $role); });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        });
    }
}
