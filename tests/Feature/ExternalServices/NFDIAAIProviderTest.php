<?php

namespace Tests\Feature\ExternalServices;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
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

    public function test_provider_get_auth_url(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            config('services.regapp.client_id', 'test-client'),
            config('services.regapp.client_secret', 'test-secret'),
            config('services.regapp.redirect', 'http://localhost/callback')
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('getAuthUrl');
        $method->setAccessible(true);

        $url = $method->invoke($provider, 'test-state');

        $this->assertStringStartsWith('https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/auth', $url);
        $this->assertStringContainsString('state=test-state', $url);
        $this->assertStringContainsString('client_id=', $url);
    }

    public function test_provider_get_token_url(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            config('services.regapp.client_id', 'test-client'),
            config('services.regapp.client_secret', 'test-secret'),
            config('services.regapp.redirect', 'http://localhost/callback')
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('getTokenUrl');
        $method->setAccessible(true);

        $url = $method->invoke($provider);

        $this->assertEquals('https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/token', $url);
    }

    public function test_provider_map_user_to_object_with_full_data(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [
            'sub' => 'user-123',
            'preferred_username' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'picture' => 'https://example.com/avatar.jpg',
        ];

        $user = $method->invoke($provider, $userData);

        $this->assertEquals('user-123', $user->getId());
        $this->assertEquals('testuser', $user->getNickname());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatar());
    }

    public function test_provider_map_user_to_object_with_fallback_fields(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [
            'id' => 'user-456',
            'username' => 'fallbackuser',
            'given_name' => 'Fallback',
            'family_name' => 'User',
            'avatar' => 'https://example.com/fallback.jpg',
        ];

        $user = $method->invoke($provider, $userData);

        $this->assertEquals('user-456', $user->getId());
        $this->assertEquals('fallbackuser', $user->getNickname());
        $this->assertEquals('Fallback User', $user->getName());
        $this->assertEquals('https://example.com/fallback.jpg', $user->getAvatar());
    }

    public function test_provider_map_user_to_object_with_minimal_data(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [
            'sub' => 'user-789',
        ];

        $user = $method->invoke($provider, $userData);

        $this->assertEquals('user-789', $user->getId());
        $this->assertNull($user->getNickname());
        $this->assertEquals('', $user->getName());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getAvatar());
    }

    public function test_provider_get_user_by_token(): void
    {
        $provider = Mockery::mock(\App\Services\Socialite\NFDIAAI\Provider::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mockBody = Mockery::mock(\Psr\Http\Message\StreamInterface::class);
        $mockBody->shouldReceive('__toString')
            ->once()
            ->andReturn('{"sub":"test-user-id","email":"test@example.com","name":"Test User"}');

        $mockResponse = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $mockResponse->shouldReceive('getBody')->andReturn($mockBody);

        $mockClient = Mockery::mock(\GuzzleHttp\Client::class);
        $mockClient->shouldReceive('get')
            ->with('https://regapp.nfdi-aai.de/oidc/realms/nfdi/protocol/openid-connect/userinfo', Mockery::on(function ($arg) {
                return isset($arg[\GuzzleHttp\RequestOptions::HEADERS]['Authorization']) &&
                       $arg[\GuzzleHttp\RequestOptions::HEADERS]['Authorization'] === 'Bearer test-token' &&
                       $arg[\GuzzleHttp\RequestOptions::HEADERS]['Accept'] === 'application/json';
            }))
            ->once()
            ->andReturn($mockResponse);

        $provider->shouldReceive('getHttpClient')->andReturn($mockClient);

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('getUserByToken');
        $method->setAccessible(true);

        $result = $method->invoke($provider, 'test-token');

        $this->assertIsArray($result);
        $this->assertEquals('test-user-id', $result['sub']);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals('Test User', $result['name']);
    }

    public function test_provider_map_user_handles_null_values(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [];

        $user = $method->invoke($provider, $userData);

        $this->assertNull($user->getId());
        $this->assertNull($user->getNickname());
        $this->assertEquals('', $user->getName());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getAvatar());
    }

    public function test_provider_map_user_trims_name_from_parts(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [
            'sub' => 'user-999',
            'given_name' => 'John',
            'family_name' => 'Doe',
        ];

        $user = $method->invoke($provider, $userData);

        $this->assertEquals('user-999', $user->getId());
        $this->assertEquals('John Doe', $user->getName());
    }

    public function test_provider_map_user_with_only_given_name(): void
    {
        $provider = new \App\Services\Socialite\NFDIAAI\Provider(
            request(),
            'test-client',
            'test-secret',
            'http://localhost/callback'
        );

        $reflection = new \ReflectionClass($provider);
        $method = $reflection->getMethod('mapUserToObject');
        $method->setAccessible(true);

        $userData = [
            'sub' => 'user-888',
            'given_name' => 'Jane',
        ];

        $user = $method->invoke($provider, $userData);

        $this->assertEquals('user-888', $user->getId());
        $this->assertEquals('Jane', trim($user->getName()));
    }
}
