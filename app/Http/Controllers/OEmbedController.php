<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Http\Resources\StudyResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Response;

class OEmbedController extends Controller
{
    public function spectra(Request $request)
    {
        $url = $request->get('url');
        $format = $request->get('format');
        $height = $request->get('height');
        $width = $request->get('width');
        $parsedURL = parse_url($url);
        $URLPath = $parsedURL['path'];
        $identifier = preg_split('#/#', $URLPath)[1];
        if ($identifier) {
            $resolvedModel = resolveIdentifier($identifier);
            $namespace = $resolvedModel['namespace'];
            $model = $resolvedModel['model'];
            // dd($model);
            if ($model) {
                $data = [
                    'success' => true,
                    'type' => 'rich',
                    'version' => '1.0',
                    'provider_name' => config('app.name'),
                    'provider_url' => config('app.url'),
                    'title' => $model->name,
                    'author_name' => $model->owner->name,
                    'author_url' => config('app.url').'/author/'.$model->owner->username,
                    'height' => $height ? $height : '300',
                    'width' => $width ? $width : '320',
                    'thumbnail_width' => '300',
                    'thumbnail_height' => '125',
                    'thumbnail_url' => $model->study_preview_urls[0],
                    'html' => '<iframe id="nmrxiv_embed" class="nmrxiv_embed_iframe" style="width: 100%; overflow: hidden;" src="'.config('app.url').'/embed/'.$model->identifier.'" width="300" height="300" frameborder="0" scrolling="no"></iframe>',
                ];

                return Response::json($data);
            }
        }
    }

    public function embed(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];
        if ($model) {
            if ($namespace == 'Study') {
                $study = $model;

                return Inertia::render('Public/Embed/Sample', [
                    'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license']),
                ]);
            } elseif ($namespace == 'Dataset') {
                $dataset = $model;
                $study = $dataset->study;

                return Inertia::render('Public/Embed/Dataset', [
                    'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license']),
                    'dataset' => (new DatasetResource($dataset)),
                ]);
            }
        }
    }
}
