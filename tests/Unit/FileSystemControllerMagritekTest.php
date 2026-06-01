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
     * @param  list<string|array{name: string, type?: string, children?: list<string>}>  $children
     */
    private function folderWithChildNames(array $children): object
    {
        return (object) [
            'children' => Collection::make(array_map(function ($child) {
                if (is_string($child)) {
                    return (object) ['name' => $child, 'type' => 'file'];
                }

                $item = (object) [
                    'name' => $child['name'],
                    'type' => $child['type'] ?? 'file',
                ];

                if (isset($child['children'])) {
                    $item->children = Collection::make(
                        array_map(
                            fn (string $name) => (object) ['name' => $name, 'type' => 'file'],
                            $child['children']
                        )
                    );
                }

                return $item;
            }, $children)),
        ];
    }

    public function test_is_magritek_returns_true_for_classic_spinsolve_folder(): void
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

    public function test_is_magritek_returns_false_when_core_files_missing(): void
    {
        $folder = $this->folderWithChildNames(['acqu.par', 'data.1d']);

        $this->assertFalse($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_true_for_modern_export_with_enhanced_subfolder(): void
    {
        $folder = $this->folderWithChildNames([
            'acqu.par',
            'data.1d',
            'display.par',
            'nmr_fid.dx',
            'MTiglate.sdf',
            [
                'name' => 'Enhanced',
                'type' => 'directory',
                'children' => ['acqu.par', 'data.1d'],
            ],
        ]);

        $this->assertTrue($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_false_when_enhanced_subfolder_is_empty(): void
    {
        $folder = $this->folderWithChildNames([
            'acqu.par',
            'data.1d',
            'display.par',
            'nmr_fid.dx',
            ['name' => 'Enhanced', 'type' => 'directory', 'children' => []],
        ]);

        $this->assertFalse($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_false_when_enhanced_subfolder_lacks_core_files(): void
    {
        $folder = $this->folderWithChildNames([
            'acqu.par',
            'data.1d',
            [
                'name' => 'Enhanced',
                'type' => 'directory',
                'children' => ['acqu.par'],
            ],
        ]);

        $this->assertFalse($this->controller->isMagritek($folder));
    }

    public function test_is_magritek_returns_false_when_enhanced_subfolder_is_not_loaded(): void
    {
        $folder = $this->folderWithChildNames([
            'acqu.par',
            'data.1d',
            ['name' => 'Enhanced', 'type' => 'directory'],
        ]);

        $this->assertFalse($this->controller->isMagritek($folder));
    }
}
