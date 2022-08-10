<?php

namespace App\Actions\Study;

use App\Models\Study;

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
