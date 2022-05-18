<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FileSystemObject;
use App\Models\Study;
use Illuminate\Http\Request;

class FileSystemController extends Controller
{
    public function children(Request $request, Study $study, $fileId)
    {
        return [
            'files' => FileSystemObject::with('children')->where([
                ['id', $fileId]
            ])->orderBy('type')->get()
        ];
    }
}
