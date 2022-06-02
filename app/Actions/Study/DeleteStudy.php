<?php

namespace App\Actions\Study;

use App\Models\Team;
use App\Models\Study;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeleteStudy
{
    /**
     * Delete the given study.
     *
     * @param  mixed  $study
     * @return void
     */
    public function delete($study)
    {
        $study->delete();
    }
}
