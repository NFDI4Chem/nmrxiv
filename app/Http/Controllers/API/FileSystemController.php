<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FileSystemObject;
use App\Models\Study;
use Illuminate\Http\Request;

class FileSystemController extends Controller
{
    public function children(Request $request, Study $study, FileSystemObject $file)
    {
        return [
            'files' => FileSystemObject::with('children')->where([
                ['slug', $file->slug],
                ['level', $file->level],
                ['project_id', $study->project->id],
                ['study_id', $study->id]
            ])->orderBy('type')->get()
        ];
    }
}
