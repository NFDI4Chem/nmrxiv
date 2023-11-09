<?php

namespace App\Console\Commands;

use App\Models\Draft;
use App\Models\FileSystemObject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:manage-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        return DB::transaction(function () {
            $drafts = Draft::where([
                ['is_deleted', false],
            ])->get();

            foreach ($drafts as $draft) {
                $path = $draft->path;
                $this->processFiles($path);
            }
        });
    }

    public function processFiles($path)
    {
        $listing = Storage::disk(env('FILESYSTEM_DRIVER'))->listContents($path, true);
        foreach ($listing as $item) {
            $path = $item->path();
            if ($item instanceof \League\Flysystem\FileAttributes) {
                $fsObject = FileSystemObject::where([
                    ['path', '/'.$path],
                ])->first();
                if ($fsObject) {
                    $metaData = $item->extraMetadata();
                    $fsInfo = json_decode($fsObject->info, true);
                    $fsInfo = array_merge($metaData, $fsInfo);
                    $fsObject->info = $fsInfo;
                    $fsObject->save();
                }
            } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
                // echo $item->study_id;
            }
        }
    }
}
