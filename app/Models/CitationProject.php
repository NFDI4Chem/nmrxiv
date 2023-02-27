<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitationProject extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'citation_project';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'citation_id',
        'project_id',
        'user',
    ];
}
