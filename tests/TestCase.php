<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use JsonException;

abstract class TestCase extends BaseTestCase
{
    protected function inertiaPageFromResponse(TestResponse $response): array
    {
        $content = $response->getContent();

        if (! preg_match('/id="app" data-page="([^"]+)"/', $content, $matches)) {
            $this->fail('Missing Inertia page payload in response.');
        }

        try {
            $page = json_decode(html_entity_decode($matches[1], ENT_QUOTES), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->fail('Unable to decode Inertia page payload: '.$exception->getMessage());
        }

        if (! is_array($page) || ! isset($page['component'], $page['props'])) {
            $this->fail('Inertia page payload is missing required keys.');
        }

        return $page;
    }

    protected function assertInertiaPageComponent(TestResponse $response, string $component): array
    {
        $response->assertOk();

        $page = $this->inertiaPageFromResponse($response);

        $this->assertSame($component, $page['component']);

        return $page;
    }
}
