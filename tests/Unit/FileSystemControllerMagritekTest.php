<?php

namespace Tests\Unit;

use App\Http\Controllers\FileSystemController;
use App\Services\FileSystemObjectService;
use App\Services\StorageSignedUrlService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FileSystemControllerMagritekTest extends TestCase
{
    private FileSystemController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new FileSystemController(
            $this->createMock(FileSystemObjectService::class),
            $this->createMock(StorageSignedUrlService::class),
        );
    }

    /**
     * @param  list<string>  $names
     */
    private function folderWithChildNames(array $names): object
    {
        return (object) [
            'children' => Collection::make(
                array_map(fn (string $name) => (object) ['name' => $name], $names)
            ),
        ];
    }

    public function test_is_magritek_returns_true_for_spinsolve_folder(): void
    {
        $folder = $this->folderWithChildNames([
            'acqu.par',
            'data.1d',
            'processing.script',
            'spectrum.1d',
        ]);

        $this->assertTrue($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_false_for_bruker_folder(): void
    {
        $folder = $this->folderWithChildNames(['acqus', 'acqu', 'pdata']);

        $this->assertFalse($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_false_when_required_files_missing(): void
    {
        $folder = $this->folderWithChildNames(['acqu.par', 'data.1d']);

        $this->assertFalse($this->controller->isMagritek($folder));
    }
}
