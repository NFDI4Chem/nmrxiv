<?php

namespace Tests\Feature\Auth;

use App\Models\LinkedSocialAccount;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;
use Tests\TestCase;

class SocialControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test redirect to GitHub provider
     */
    public function test_redirect_to_github_provider(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/login/github');

        $response->assertRedirect();
    }

    /**
     * Test redirect to ORCID provider with scopes
     */
    public function test_redirect_to_orcid_provider_with_scopes(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('scopes')
            ->with(['/authenticate', 'openid', '/read-limited'])
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://orcid.org/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('orcid')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/login/orcid');

        $response->assertRedirect();
    }

    /**
     * Test redirect to Google provider
     */
    public function test_redirect_to_google_provider(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get('/auth/login/google');

        $response->assertRedirect();
    }

    /**
     * Test callback for existing user with linked social account
     */
    public function test_callback_with_existing_linked_social_account(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $linkedAccount = LinkedSocialAccount::factory()->create([
            'user_id' => $user->id,
            'provider_id' => '123456',
            'provider_name' => 'github',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('123456');
        $socialiteUser->shouldReceive('getEmail')->andReturn($user->email);
        $socialiteUser->shouldReceive('getName')->andReturn($user->name);

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test callback creates new user when email exists but no linked account
     */
    public function test_callback_links_existing_user_by_email(): void
    {
        Event::fake();

        $existingUser = User::factory()->withPersonalTeam()->create([
            'email' => 'john@example.com',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('789012');
        $socialiteUser->shouldReceive('getEmail')->andReturn('john@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($existingUser);

        $this->assertDatabaseHas('linked_social_accounts', [
            'user_id' => $existingUser->id,
            'provider_id' => '789012',
            'provider_name' => 'github',
        ]);

        Event::assertDispatched(Registered::class, function ($event) use ($existingUser) {
            return $event->user->id === $existingUser->id;
        });
    }

    /**
     * Test callback creates new user when no existing user or linked account
     */
    public function test_callback_creates_new_user_when_none_exists(): void
    {
        Event::fake();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('345678');
        $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('New User');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $initialUserCount = User::count();

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertEquals($initialUserCount + 1, User::count());

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('New User', $newUser->name);
        $this->assertEquals('New', $newUser->first_name);
        $this->assertEquals('User', $newUser->last_name);
        $this->assertEquals('newuser', $newUser->username);

        $this->assertDatabaseHas('linked_social_accounts', [
            'user_id' => $newUser->id,
            'provider_id' => '345678',
            'provider_name' => 'github',
        ]);

        // Verify personal team was created
        $this->assertDatabaseHas('teams', [
            'user_id' => $newUser->id,
            'name' => "New's Team",
            'personal_team' => true,
        ]);

        Event::assertDispatched(Registered::class);
    }

    /**
     * Test callback with single name (no space)
     */
    public function test_callback_handles_single_name_correctly(): void
    {
        Event::fake();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('999888');
        $socialiteUser->shouldReceive('getEmail')->andReturn('madonna@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Madonna');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');

        $newUser = User::where('email', 'madonna@example.com')->first();
        $this->assertEquals('Madonna', $newUser->first_name);
        $this->assertEquals('', $newUser->last_name);
    }

    /**
     * Test callback redirects to login when no name provided
     */
    public function test_callback_redirects_when_no_name_provided(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('111222');
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('message', 'We require your name. Please provide your name in your github account and try again.');
        $this->assertGuest();
    }

    /**
     * Test callback redirects to login when no email provided (ORCID specific)
     */
    public function test_callback_redirects_when_no_email_provided(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('333444');
        $socialiteUser->shouldReceive('getEmail')->andReturn(null);
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');

        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('orcid')->once()->andReturn($provider);

        $response = $this->get('/auth/login/orcid/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('message', 'We require your email id to communicate. Please enable email sharing on your ORCID account and try again.');
        $this->assertGuest();
    }

    /**
     * Test callback handles InvalidStateException with stateless fallback
     */
    public function test_callback_handles_invalid_state_exception(): void
    {
        Event::fake();

        $user = User::factory()->withPersonalTeam()->create();
        $linkedAccount = LinkedSocialAccount::factory()->create([
            'user_id' => $user->id,
            'provider_id' => '555666',
            'provider_name' => 'github',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('555666');
        $socialiteUser->shouldReceive('getEmail')->andReturn($user->email);
        $socialiteUser->shouldReceive('getName')->andReturn($user->name);

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new InvalidStateException);
        $provider->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->twice()
            ->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test callback creates user with name containing multiple spaces
     */
    public function test_callback_handles_name_with_multiple_spaces(): void
    {
        Event::fake();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('777888');
        $socialiteUser->shouldReceive('getEmail')->andReturn('multi@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Paul Smith');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $response = $this->get('/auth/login/github/callback');

        $response->assertRedirect('/dashboard');

        $newUser = User::where('email', 'multi@example.com')->first();
        $this->assertEquals('John', $newUser->first_name);
        $this->assertEquals('Paul Smith', $newUser->last_name);
    }

    /**
     * Test multiple providers (GitHub, Google, ORCID)
     */
    public function test_supports_multiple_oauth_providers(): void
    {
        $providers = ['github', 'google', 'orcid'];

        foreach ($providers as $providerName) {
            $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');

            if ($providerName === 'orcid') {
                $provider->shouldReceive('scopes')
                    ->with(['/authenticate', 'openid', '/read-limited'])
                    ->once()
                    ->andReturnSelf();
            }

            $provider->shouldReceive('redirect')
                ->once()
                ->andReturn(redirect("https://{$providerName}.com/oauth"));

            Socialite::shouldReceive('driver')
                ->with($providerName)
                ->once()
                ->andReturn($provider);

            $response = $this->get("/auth/login/{$providerName}");
            $response->assertRedirect();
        }
    }

    /**
     * Test that personal team is created with correct name format
     */
    public function test_personal_team_creation_format(): void
    {
        Event::fake();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('123abc');
        $socialiteUser->shouldReceive('getEmail')->andReturn('alice@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Alice Johnson');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $this->get('/auth/login/github/callback');

        $newUser = User::where('email', 'alice@example.com')->first();
        $team = $newUser->ownedTeams()->where('personal_team', true)->first();

        $this->assertNotNull($team);
        $this->assertEquals("Alice's Team", $team->name);
    }

    /**
     * Test username is derived from email correctly
     */
    public function test_username_derived_from_email(): void
    {
        Event::fake();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('456def');
        $socialiteUser->shouldReceive('getEmail')->andReturn('bob.smith@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Bob Smith');

        $provider = Mockery::mock('Laravel\Socialite\Two\GithubProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->once()->andReturn($provider);

        $this->get('/auth/login/github/callback');

        $newUser = User::where('email', 'bob.smith@example.com')->first();
        $this->assertEquals('bob.smith', $newUser->username);
    }
}
