<?php

namespace Tests\Feature;

use Spatie\SupportBubble\Events\SupportBubbleSubmittedEvent;
use Tests\TestCase;

class SupportBubbleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure we're in testing environment
        app()->detectEnvironment(function () {
            return 'testing';
        });
    }

    /**
     * Make a JSON request to the support bubble endpoint
     */
    protected function postSupportBubble(array $data)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/support-bubble', $data);
    }

    /**
     * Test that legitimate support messages are accepted
     */
    public function test_legitimate_support_message_is_accepted(): void
    {
        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Need help with data upload',
            'message' => 'Hello, I am having trouble uploading my NMR data. Can you please help me understand the process?',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->post('/support-bubble', $validData);

        // Should return success view (status 200)
        $response->assertStatus(200);
    }

    /**
     * Test gibberish email detection with actual spam pattern
     */
    public function test_exact_spam_email_is_rejected(): void
    {
        $spamData = [
            'email' => 'αχΥηΤrvnIbuQzoGkkbYqjEr@gmail.com',
            'subject' => 'Test subject',
            'message' => 'Test message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test random gibberish email pattern
     */
    public function test_random_gibberish_email_is_rejected(): void
    {
        $spamData = [
            'email' => 'ogekocedi00@gmail.com',
            'subject' => 'Test subject',
            'message' => 'Test message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test gibberish content in message
     */
    public function test_gibberish_message_is_rejected(): void
    {
        $spamData = [
            'email' => 'user@example.com',
            'subject' => 'Test subject',
            'message' => 'αχΥηΤrvnIbuQzoGkkbYqjEr kjshdkjsh dkjshd kjshd',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test gibberish content in subject
     */
    public function test_gibberish_subject_is_rejected(): void
    {
        $spamData = [
            'email' => 'user@example.com',
            'subject' => 'αχΥηΤrvnIbuQzoGkkbYqjEr',
            'message' => 'This is a normal message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    }

    /**
     * Test disposable email domains
     */
    public function test_disposable_email_is_rejected(): void
    {
        $spamData = [
            'email' => 'test@10minutemail.com',
            'subject' => 'Test subject',
            'message' => 'Test message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test emails with excessive special characters
     */
    public function test_email_with_excessive_special_characters_is_rejected(): void
    {
        $spamData = [
            'email' => 'user!!!@@@$$$%%%domain.com',
            'subject' => 'Test subject',
            'message' => 'Test message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($spamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test missing required fields
     */
    public function test_missing_required_fields_are_rejected(): void
    {
        $incompleteData = [
            'email' => 'user@example.com',
            // Missing subject and message
        ];

        $response = $this->postSupportBubble($incompleteData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject', 'message']);
    }

    /**
     * Test invalid email format
     */
    public function test_invalid_email_format_is_rejected(): void
    {
        $invalidData = [
            'email' => 'not-an-email',
            'subject' => 'Test subject',
            'message' => 'Test message',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test messages that are too short
     */
    public function test_message_too_short_is_rejected(): void
    {
        $shortData = [
            'email' => 'user@example.com',
            'subject' => 'Test subject',
            'message' => 'Hi',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($shortData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test that exception handling works correctly when event fails
     */
    public function test_exception_handling_when_event_fails(): void
    {
        // Mock the SupportBubbleSubmittedEvent to throw an exception when constructed
        $this->mock(SupportBubbleSubmittedEvent::class, function ($mock) {
            $mock->shouldReceive('__construct')
                ->andThrow(new \Exception('Event construction failed'));
        });

        // Listen for the event and throw an exception
        \Event::listen(SupportBubbleSubmittedEvent::class, function () {
            throw new \Exception('Event listener failed');
        });

        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Test subject',
            'message' => 'This is a test message that should trigger an exception.',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->postSupportBubble($validData);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
            ]);
    }

    /**
     * Test that errors are logged when exception occurs
     */
    public function test_exception_is_logged_with_details(): void
    {
        // Set up Log spy to capture log messages
        \Log::spy();

        // Listen for the event and throw an exception
        \Event::listen(SupportBubbleSubmittedEvent::class, function () {
            throw new \Exception('Event listener failed');
        });

        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Test subject',
            'message' => 'This is a test message.',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $this->postSupportBubble($validData);

        // Assert that error was logged
        \Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function ($message, $context) use ($validData) {
                return $message === 'Support bubble submission failed'
                    && isset($context['error'])
                    && isset($context['ip'])
                    && isset($context['data'])
                    && $context['data']['email'] === $validData['email']
                    && $context['data']['subject'] === $validData['subject'];
            });
    }

    /**
     * Test successful submission fires event with correct parameters
     */
    public function test_successful_submission_fires_event_with_correct_parameters(): void
    {
        \Event::fake();

        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Need help',
            'message' => 'This is a legitimate support request.',
            'url' => 'https://nmrxiv.org/dashboard',
            'name' => 'John Doe',
        ];

        $response = $this->post('/support-bubble', $validData);

        $response->assertStatus(200);

        \Event::assertDispatched(SupportBubbleSubmittedEvent::class, function ($event) use ($validData) {
            return $event->subject === $validData['subject']
                && $event->message === $validData['message']
                && $event->email === $validData['email']
                && $event->name === $validData['name']
                && $event->url === $validData['url'];
        });
    }

    /**
     * Test successful submission without optional name field
     */
    public function test_successful_submission_without_name_field(): void
    {
        \Event::fake();

        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Help needed',
            'message' => 'I need assistance with uploading files.',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->post('/support-bubble', $validData);

        $response->assertStatus(200);

        \Event::assertDispatched(SupportBubbleSubmittedEvent::class);
    }

    /**
     * Test that IP address and user agent are captured in event
     */
    public function test_ip_and_user_agent_captured_in_event(): void
    {
        \Event::fake();

        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Test subject',
            'message' => 'Test message with sufficient length.',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->withHeaders([
            'User-Agent' => 'TestBrowser/1.0',
        ])->post('/support-bubble', $validData);

        $response->assertStatus(200);

        \Event::assertDispatched(SupportBubbleSubmittedEvent::class, function ($event) {
            return ! empty($event->ip) && ! empty($event->userAgent);
        });
    }

    /**
     * Test that success view is returned on successful submission
     */
    public function test_success_view_is_returned(): void
    {
        $validData = [
            'email' => 'user@example.com',
            'subject' => 'Question about features',
            'message' => 'I would like to know more about the advanced features available.',
            'url' => 'https://nmrxiv.org/dashboard',
        ];

        $response = $this->post('/support-bubble', $validData);

        $response->assertStatus(200)
            ->assertViewIs('support-bubble::success');
    }
}
