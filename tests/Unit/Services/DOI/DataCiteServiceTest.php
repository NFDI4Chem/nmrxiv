<?php

namespace Tests\Unit\Services\DOI;

use App\Services\DOI\DataCite;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\RequestInterface;
use Tests\TestCase;

class DataCiteServiceTest extends TestCase
{
    public function test_create_custom_doi_posts_with_exact_doi_string(): void
    {
        $history = [];
        $service = $this->makeService([
            new Response(201, [], json_encode(['data' => ['id' => '10.99999/nmrxiv.deadbeef']])),
        ], $history);

        $result = $service->createCustomDOI('10.99999/nmrxiv.deadbeef', [
            'titles' => [['title' => 'Provisional placeholder']],
        ]);

        $this->assertSame('10.99999/nmrxiv.deadbeef', $result['data']['id']);

        $request = $history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/dois', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('10.99999/nmrxiv.deadbeef', $body['data']['attributes']['doi']);
        $this->assertSame('10.99999', $body['data']['attributes']['prefix']);
        $this->assertSame('nmrxiv.deadbeef', $body['data']['attributes']['suffix']);
        $this->assertSame('Provisional placeholder', $body['data']['attributes']['titles'][0]['title']);
    }

    public function test_get_related_identifiers_returns_array(): void
    {
        $payload = ['data' => ['attributes' => ['relatedIdentifiers' => [
            ['relatedIdentifier' => '10.1/foo', 'relationType' => 'IsCitedBy', 'relatedIdentifierType' => 'DOI'],
        ]]]];

        $service = $this->makeService([new Response(200, [], json_encode($payload))]);

        $result = $service->getRelatedIdentifiers('10.99999/nmrxiv.P1');

        $this->assertCount(1, $result);
        $this->assertSame('10.1/foo', $result[0]['relatedIdentifier']);
    }

    public function test_get_related_identifiers_returns_empty_when_missing(): void
    {
        $service = $this->makeService([
            new Response(200, [], json_encode(['data' => ['attributes' => []]])),
        ]);

        $this->assertSame([], $service->getRelatedIdentifiers('10.99999/nmrxiv.P1'));
    }

    public function test_put_related_identifiers_writes_audit_snapshot_then_puts(): void
    {
        Storage::fake('local');

        $history = [];
        $service = $this->makeService([
            new Response(200, [], json_encode(['data' => ['attributes' => ['relatedIdentifiers' => []]]])),
            new Response(200, [], json_encode(['data' => ['id' => '10.99999/nmrxiv.P1']])),
        ], $history);

        $service->putRelatedIdentifiers('10.99999/nmrxiv.P1', [
            ['relatedIdentifier' => '10.99999/nmrxiv.deadbeef', 'relationType' => 'IsIdenticalTo', 'relatedIdentifierType' => 'DOI'],
        ], 'https://www.nmrxiv.org/project/P1');

        $this->assertCount(2, $history);
        $this->assertSame('GET', $history[0]['request']->getMethod());
        $this->assertSame('PUT', $history[1]['request']->getMethod());

        $files = Storage::disk('local')->allFiles('datacite-audit/10.99999_nmrxiv.P1');
        $this->assertNotEmpty($files, 'expected an audit snapshot file under storage/logs/datacite-audit/');

        $putBody = json_decode((string) $history[1]['request']->getBody(), true);
        $this->assertSame('IsIdenticalTo', $putBody['data']['attributes']['relatedIdentifiers'][0]['relationType']);
        $this->assertSame('https://www.nmrxiv.org/project/P1', $putBody['data']['attributes']['url']);
    }

    public function test_put_related_identifiers_swallows_audit_failures(): void
    {
        Storage::fake('local');

        $history = [];
        $service = $this->makeService([
            new ConnectException(
                'audit GET failed',
                new Request('GET', '/dois/foo')
            ),
            new Response(200, [], json_encode(['data' => ['id' => '10.99999/nmrxiv.P1']])),
        ], $history);

        $result = $service->putRelatedIdentifiers('10.99999/nmrxiv.P1', []);

        $this->assertSame('10.99999/nmrxiv.P1', $result['data']['id']);
    }

    /**
     * @param  list<Response|\Throwable>  $responses
     * @param  array<int, array{request: RequestInterface}>|null  $history
     */
    private function makeService(array $responses, ?array &$history = null): DataCite
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        if ($history !== null) {
            $stack->push(Middleware::history($history));
        }

        $client = new Client(['handler' => $stack, 'base_uri' => 'http://datacite.test']);

        $service = new DataCite;
        $service->setHttpClient($client);

        return $service;
    }
}
