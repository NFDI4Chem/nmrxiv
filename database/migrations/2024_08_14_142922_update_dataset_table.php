<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->text('solvent')->nullable();
            // $table->text('shift_reference')->nullable();
            // $table->text('shift_calibration')->nullable();
            // $table->text('shift_calibration_peak_shift')->nullable();
            // $table->text('tube_diameter')->nullable();
            // $table->text('tube_type')->nullable();
            $table->text('nucleus')->nullable();
            $table->text('base_frequency')->nullable();
            $table->text('experiment')->nullable();
            $table->text('pulse_sequence')->nullable();
            // $table->integer('flip_angle')->nullable();
            $table->text('relaxation_time')->nullable();
            $table->text('number_of_points')->nullable();
            $table->text('temperature')->nullable();
            $table->text('number_of_scans')->nullable();

            // $table->text('pulse_power')->nullable();
            $table->text('acquisition_time')->nullable();
            // $table->boolean('shaped_pulse')->nullable();
            // $table->text('mixing_time')->nullable();
            // $table->text('constant_time')->nullable();
            // $table->text('manufacturer')->nullable();
            // $table->text('instrument')->nullable();
            $table->text('probe_name')->nullable();
            // $table->integer('zero_filling_points_number')->nullable();
            // $table->text('apodization_function')->nullable();
            // $table->text('apodization_parameters')->nullable();
            // $table->text('baseline_correction_algorithm')->nullable();
            // $table->text('baseline_correction_parameters')->nullable();
            // $table->text('phase_correction')->nullable();
            // $table->text('ph0')->nullable();
            // $table->text('ph1')->nullable();
            // $table->boolean('absolute_correction')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {

            $table->dropColumn('solvent');
            // $table->dropColumn('shift_reference');
            // $table->dropColumn('shift_calibration');
            // $table->dropColumn('shift_calibration_peak_shift');
            // $table->dropColumn('tube_diameter');
            $table->dropColumn('nucleus');
            $table->dropColumn('base_frequency');
            $table->dropColumn('experiment');
            $table->dropColumn('pulse_sequence');
            // $table->dropColumn('flip_angle');
            $table->dropColumn('relaxation_time');
            $table->dropColumn('number_of_points');
            $table->dropColumn('temperature');

            $table->dropColumn('number_of_scans');
            // $table->dropColumn('pulse_power');
            $table->dropColumn('acquisition_time');
            // $table->dropColumn('shaped_pulse');
            // $table->dropColumn('mixing_time');
            // $table->dropColumn('constant_time');
            // $table->dropColumn('manufacturer');
            // $table->dropColumn('instrument');
            $table->dropColumn('probe_name');
            // $table->dropColumn('zero_filling_points_number');
            // $table->dropColumn('apodization_function');
            // $table->dropColumn('apodization_parameters');
            // $table->dropColumn('baseline_correction_algorithm');
            // $table->dropColumn('baseline_correction_parameters');
            // $table->dropColumn('phase_correction');
            // $table->dropColumn('ph0');
            // $table->dropColumn('ph1');
            // $table->dropColumn('absolute_correction');
        });
    }
};
