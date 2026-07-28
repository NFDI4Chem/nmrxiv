<?php

namespace App\OpenApi;

/**
 * Reusable OpenAPI schemas for the metadata statistics endpoint.
 *
 * @OA\Schema(
 *     schema="MetadataStatsBucket",
 *     type="object",
 *     description="Single distribution bucket with its spectra count.",
 *
 *     @OA\Property(property="value", type="string", example="1H"),
 *     @OA\Property(property="count", type="integer", example=7420)
 * )
 *
 * @OA\Schema(
 *     schema="MetadataStatsDimensionExperimentGroup",
 *     type="object",
 *     description="Spectra per dimension (1D / 2D), broken down by nucleus (1D) or experiment (2D).",
 *
 *     @OA\Property(property="dimension", type="string", example="1D"),
 *     @OA\Property(property="count", type="integer", example=9260),
 *     @OA\Property(property="breakdown", type="string", enum={"nucleus", "experiment"}, example="nucleus"),
 *     @OA\Property(
 *         property="segments",
 *         type="array",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="value", type="string", example="1H"),
 *             @OA\Property(property="count", type="integer", example=5720),
 *             @OA\Property(property="kind", type="string", enum={"nucleus", "experiment"}, example="nucleus")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="MetadataStatsNucleusFrequencyGroup",
 *     type="object",
 *     description="Spectra per nucleus, broken down by observed measuring frequency (MHz).",
 *
 *     @OA\Property(property="nucleus", type="string", example="1H"),
 *     @OA\Property(property="count", type="integer", example=7420),
 *     @OA\Property(
 *         property="frequencies",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/MetadataStatsBucket")
 *     )
 * )
 */
class MetadataStatsSchemas {}
