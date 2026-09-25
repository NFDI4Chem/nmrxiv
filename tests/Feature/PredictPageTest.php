<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PredictPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_predict_page_can_be_rendered(): void
    {
        config(['external-links.nmrkit_url' => 'https://nmrkit.example.test']);

        $this->get(route('predict'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Predict')
                ->where('nmrPredictUrl', 'https://nmrkit.example.test/latest/predict/')
            );
    }

    public function test_predict_url_strips_trailing_slash_from_nmrkit_base(): void
    {
        config(['external-links.nmrkit_url' => 'https://nmrkit.example.test/']);

        $this->get(route('predict'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Predict')
                ->where('nmrPredictUrl', 'https://nmrkit.example.test/latest/predict/')
            );
    }
}
