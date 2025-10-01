<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class NFDIAAIProviderTest extends TestCase
{
    public function test_redirect_builds_authorization_url(): void
    {
        if (! config('services.regapp.client_id')) {
            $this->markTestSkipped('NFDIAAI env vars not set');
        }

        // Prevent DB lookup in HandleInertiaRequests::share
        Schema::shouldReceive('hasTable')->with('announcements')->andReturn(false);

        $response = $this->get('/auth/login/regapp');
        $response->assertRedirect();
        $target = $response->headers->get('Location');

        $this->assertNotNull($target);
        $this->assertTrue(Str::startsWith($target, 'https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/auth'));
        $this->assertStringContainsString('client_id='.urlencode(config('services.regapp.client_id')), $target);
        $this->assertStringContainsString('response_type=code', $target);
        $this->assertStringContainsString('scope=', $target);
        $this->assertStringContainsString('redirect_uri=', $target);
    }
}
