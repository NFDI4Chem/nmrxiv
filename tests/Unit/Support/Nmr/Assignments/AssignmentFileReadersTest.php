<?php

namespace Tests\Unit\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;
use App\Support\Nmr\Assignments\AssignmentSetResolver;
use App\Support\Nmr\Assignments\InvalidAssignmentFileException;
use App\Support\Nmr\Assignments\SdfRecord;
use Tests\TestCase;

class AssignmentFileReadersTest extends TestCase
{
    private function fixture(string $name): string
    {
        return file_get_contents(base_path('tests/Fixtures/Assignments/'.$name));
    }

    private function resolver(): AssignmentSetResolver
    {
        return $this->app->make(AssignmentSetResolver::class);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function row(array $rows, string $nucleus, float $shift): array
    {
        foreach ($rows as $row) {
            if ($row['nucleus'] === $nucleus && abs($row['shift'] - $shift) < 0.001) {
                return $row;
            }
        }
        $this->fail("No {$nucleus} row at {$shift} ppm.");
    }

    public function test_sdf_record_splits_molfile_and_tags(): void
    {
        $record = SdfRecord::parse($this->fixture('trimethoxybenzaldehyde-mnova.sdf'));

        $this->assertStringEndsWith("M  END\n", $record->molfile);
        $this->assertSame('Chloroform', $record->tag('SOLVENT'));
        $this->assertCount(10, $record->tagLines('CHEMICAL_SHIFTS.13C'));
        $this->assertSame('4', $record->atomLabels()[5]);
        $this->assertSame('H', $record->atomSymbols()[8]);
    }

    public function test_mnova_export_groups_equivalent_atoms_and_uses_author_numbering(): void
    {
        $set = $this->resolver()->fromFile($this->fixture('trimethoxybenzaldehyde-mnova.sdf'));

        $this->assertSame(AssignmentSource::MnovaSdf, $set->source);
        $this->assertSame('Chloroform', $set->solvent);
        $this->assertSame(['13C', '1H'], $set->nuclei());
        $this->assertCount(11, $set->assignments);

        $this->assertSame(
            ['nucleus' => '13C', 'atoms' => [1, 3], 'shift' => 106.6459, 'label' => 'C-6, C-2'],
            $this->row($set->assignments, '13C', 106.646)
        );
        $this->assertSame('C-4', $this->row($set->assignments, '13C', 143.518)['label']);
        $this->assertSame([8], $this->row($set->assignments, '1H', 9.862)['atoms']);
        $this->assertSame('H-7a', $this->row($set->assignments, '1H', 9.862)['label']);
        $this->assertSame([13, 15], $this->row($set->assignments, '1H', 3.926)['atoms']);
        $this->assertSame(191.0882, $set->assignments[0]['shift']);
    }

    public function test_nmredata_reads_labels_nuclei_and_implicit_hydrogen_references(): void
    {
        $set = $this->resolver()->fromFile($this->fixture('trimethoxybenzaldehyde.nmredata.sdf'));

        $this->assertSame(AssignmentSource::Nmredata, $set->source);
        $this->assertSame('CDCl3', $set->solvent);
        $this->assertCount(11, $set->assignments);

        $this->assertSame(
            ['nucleus' => '13C', 'atoms' => [13, 15], 'label' => 'OMe-3/5', 'shift' => 56.26],
            $this->row($set->assignments, '13C', 56.26)
        );
        $this->assertSame(
            ['nucleus' => '1H', 'atoms' => [13, 15], 'label' => 'H-OMe-3/5', 'shift' => 3.926, 'multiplicity' => 's', 'n_h' => 6],
            $this->row($set->assignments, '1H', 3.926)
        );
        $this->assertSame([8], $this->row($set->assignments, '1H', 9.862)['atoms']);
    }

    public function test_file_without_assignment_tags_is_rejected(): void
    {
        $molfileOnly = explode('> <', $this->fixture('trimethoxybenzaldehyde.nmredata.sdf'))[0];

        $this->expectException(InvalidAssignmentFileException::class);
        $this->expectExceptionMessage('No assignments found');

        $this->resolver()->fromFile($molfileOnly);
    }

    public function test_file_without_molfile_is_rejected(): void
    {
        $this->expectException(InvalidAssignmentFileException::class);

        $this->resolver()->fromFile("> <NMREDATA_ASSIGNMENT>\nC-1, 10.0, 1\n");
    }
}
