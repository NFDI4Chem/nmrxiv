<?php

namespace App\Actions\Study;

use App\Models\Study;

class PublishStudy
{
    /**
     * Publish the given study.
     *
     * @param  mixed  $study
     * @return void
     */
    public function publish($study)
    {
        $study->is_public = true;
        $study->save();
        $datasets = $study->datasets;
        foreach ($datasets as $dataset) {
            $dataset->is_public = true;
            $dataset->save();
        }
    }
}
