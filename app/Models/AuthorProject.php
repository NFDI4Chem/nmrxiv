<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorProject extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'author_project';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'project_id',
        'contributor_type',
        'sort_order',
    ];
}
