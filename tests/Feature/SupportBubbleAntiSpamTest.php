<?php

namespace Tests\Feature;

use Tests\TestCase;

class SupportBubbleAntiSpamTest extends TestCase
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
}
