<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\ChatAttachment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Chat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->chat = Chat::create();
        $this->chat->users()->attach([$this->user->id, $this->otherUser->id]);
    }

    public function test_user_can_attach_file_to_message(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

        // First, visit the chat page to establish session
        $this->actingAs($this->user)->get("/chat/{$this->chat->id}");

        $response = $this->actingAs($this->user)
            ->post("/chat/{$this->chat->id}/messages", [
                'content' => 'Test message with attachment',
                'attachment' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $responseData = $response->json('message');

        $this->assertNotNull($responseData);
        $this->assertEquals('Test message with attachment', $responseData['content']);
        $this->assertCount(1, $responseData['attachments']);

        $attachment = $responseData['attachments'][0];
        $this->assertStringContainsString('test.txt', $attachment['file_name']);
        $this->assertEquals('text/plain', $attachment['file_mime_type']);
        $this->assertEquals(100, $attachment['file_size']);
    }

    public function test_user_can_send_message_with_only_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        // First, visit the chat page to establish session
        $this->actingAs($this->user)->get("/chat/{$this->chat->id}");

        $response = $this->actingAs($this->user)
            ->post("/chat/{$this->chat->id}/messages", [
                'content' => null,
                'attachment' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $responseData = $response->json('message');

        $this->assertNotNull($responseData);
        $this->assertNull($responseData['content']);
        $this->assertCount(1, $responseData['attachments']);
    }

    public function test_user_cannot_send_message_without_content_and_attachment(): void
    {
        // First, visit the chat page to establish session
        $this->actingAs($this->user)->get("/chat/{$this->chat->id}");

        $response = $this->actingAs($this->user)
            ->post("/chat/{$this->chat->id}/messages", [
                'content' => null,
                'attachment' => null,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Message content or an attachment is required.',
            ]);
    }

    public function test_user_can_download_chat_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('download_test.txt', 100, 'text/plain');
        $filePath = $file->storeAs('public/chat_attachments', 'download_test.txt');

        $message = Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => $this->user->id,
            'content' => 'Message with attachment',
        ]);

        $attachment = ChatAttachment::create([
            'message_id' => $message->id,
            'file_path' => $filePath,
            'file_name' => 'download_test.txt',
            'file_mime_type' => 'text/plain',
            'file_size' => 100,
        ]);

        // First, visit the chat page to establish session
        $this->actingAs($this->user)->get("/chat/{$this->chat->id}");

        $response = $this->actingAs($this->user)
            ->get("/chat/attachment/{$attachment->id}");

        $response->assertStatus(200);
    }

    public function test_attachment_validation(): void
    {
        Storage::fake('public');

        // Test with file exceeding max size (10MB limit)
        $largeFile = UploadedFile::fake()->create('large_file.txt', 11000, 'text/plain'); // 11MB - exceeds 10MB limit

        // First, visit the chat page to establish session
        $this->actingAs($this->user)->get("/chat/{$this->chat->id}");

        $response = $this->actingAs($this->user)
            ->post("/chat/{$this->chat->id}/messages", [
                'content' => 'Test message',
                'attachment' => $largeFile,
            ]);

        // Should return an error due to file size validation
        // The controller validates with 'max:10240' which is 10MB
        $response->assertStatus(422); // Validation error expected
    }
}