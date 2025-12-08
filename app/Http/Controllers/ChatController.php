<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatAttachment;

class ChatController extends Controller
{
    /**
     * Display a listing of the chats for the authenticated user.
     */
    public function index(): View
    {
        $user = Auth::user();
        $chats = $user->chats()->with('users')->get();
        return view('chat.index', compact('chats'));
    }

    /**
     * Show the form for creating a new chat.
     */
    public function create(): View
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.create', compact('users'));
    }

    /**
     * Store a newly created chat in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        $participants = $request->input('participants');
        $participants[] = Auth::id(); // Add authenticated user to participants

        // Check if a chat with these exact participants already exists
        // This is a simplified check and might need more robust logic for complex scenarios
        $existingChat = Chat::whereHas('users', function ($query) use ($participants) {
            $query->whereIn('user_id', $participants);
        }, '=', count($participants))
        ->has('users', count($participants))
        ->first();


        if ($existingChat) {
            return redirect()->route('chat.show', $existingChat->id)->with('info', 'You are already in this chat.');
        }

        $chat = Chat::create([]);
        $chat->users()->attach(array_unique($participants));

        return redirect()->route('chat.show', $chat->id)->with('success', 'Chat created successfully!');
    }

    /**
     * Display the specified chat.
     */
    public function show(Chat $chat): View
    {
        // Use policy authorization for the chat
        $this->authorize('view', $chat);

        $messages = $chat->messages()->with('user')->get();
        return view('chat.show', compact('chat', 'messages'));
    }

    /**
     * Send a message to the specified chat.
     */
    public function sendMessage(Request $request, Chat $chat): \Illuminate\Http\JsonResponse
    {
        // Use policy authorization for the chat
        $this->authorize('view', $chat);

        $request->validate([
            'content' => 'nullable|string|max:1000', // Content can be nullable if only sending attachment
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,zip,rar|max:10240', // Max 10MB, restricted file types
        ]);

        if (!$request->input('content') && !$request->hasFile('attachment')) {
            return response()->json(['status' => 'error', 'message' => 'Message content or an attachment is required.'], 422);
        }

        $message = $chat->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            // Additional security checks
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $extension = strtolower($file->getClientOriginalExtension());

            // Double check the file extension and MIME type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/zip', 'application/x-rar-compressed'];

            if (!in_array($extension, $allowedExtensions) || !in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json(['status' => 'error', 'message' => 'File type not allowed.'], 422);
            }

            // Additional security: verify that the file is actually what it says it is
            $this->verifyFileIntegrity($file);

            $filePath = $file->storeAs('public/chat_attachments', $fileName); // Store in storage

            if ($filePath) {
                $attachment = $message->attachments()->create([
                    'file_path' => $filePath, // Store the path in storage
                    'file_name' => $fileName,
                    'file_mime_type' => $file->getMimeType(), // Use true MIME type from file, not client
                    'file_size' => $file->getSize(),
                ]);
            } else {
                // Log the error if file storage failed
                \Log::error('Failed to store chat attachment: ' . $fileName, [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);

                return response()->json(['status' => 'error', 'message' => 'Failed to upload attachment.'], 500);
            }
        }

        // We are disabling broadcasting for now as per user request for polling
        // broadcast(new MessageSent(Auth::user(), $message))->toOthers();

        return response()->json(['status' => 'success', 'message' => $message->load(['user', 'attachments'])]);
    }

    /**
     * Verify file integrity to prevent malicious file uploads
     */
    private function verifyFileIntegrity($file)
    {
        // Additional file content inspection to prevent malicious uploads
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // For image files, verify the content matches the extension
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (!in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
                throw new \Exception('Invalid image file type.');
            }

            // Additional check to ensure it's an actual image
            $imageInfo = getimagesize($file->getRealPath());
            if (!$imageInfo) {
                throw new \Exception('Invalid image file.');
            }
        }

        // For PDF files, check for common PDF header
        if ($extension === 'pdf') {
            $handle = fopen($file->getRealPath(), 'rb');
            $header = fread($handle, 4);
            fclose($handle);
            if ($header !== '%PDF') {
                throw new \Exception('Invalid PDF file.');
            }
        }
    }

    /**
     * Fetch new messages for a chat.
     */
    public function fetchNewMessages(Request $request, Chat $chat)
    {
        // Use policy authorization for the chat
        $this->authorize('view', $chat);

        $lastMessageId = $request->input('last_message_id');

        $messages = $chat->messages()
                         ->with(['user', 'attachments']) // Eager load attachments here
                         ->where('id', '>', $lastMessageId)
                         ->oldest()
                         ->get();

        return response()->json($messages);
    }

    /**
     * Download a chat attachment.
     */
    public function downloadAttachment(ChatAttachment $attachment)
    {
        // Use policy authorization for the attachment
        $this->authorize('view', $attachment);

        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Display a chat attachment (for images to show in browser).
     */
    public function showAttachment(ChatAttachment $attachment)
    {
        // Use policy authorization for the attachment
        $this->authorize('view', $attachment);

        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::response($attachment->file_path);
    }

    /**
     * Delete selected messages.
     */
    public function deleteMessages(Request $request, Chat $chat)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id,chat_id,' . $chat->id,
        ]);

        // Use policy authorization for the chat
        $this->authorize('view', $chat);

        $messageIds = $request->input('message_ids');

        // Allow users to delete any messages in the chat they participate in
        $messages = $chat->messages()->whereIn('id', $messageIds)->get();

        foreach ($messages as $message) {
            // Use policy authorization for each message
            $this->authorize('delete', $message);

            // Delete the message's attachments first
            foreach ($message->attachments as $attachment) {
                if (Storage::exists($attachment->file_path)) {
                    Storage::delete($attachment->file_path);
                }
                $attachment->delete();
            }

            $message->delete();
        }

        return response()->json(['status' => 'success', 'message' => count($messages) . ' message(s) deleted successfully.']);
    }

    /**
     * Clear all messages in the chat.
     */
    public function clearChat(Request $request, Chat $chat)
    {
        // Use policy authorization for the chat
        $this->authorize('view', $chat);

        // Get all messages in the chat to delete their attachments first
        $messages = $chat->messages;

        foreach ($messages as $message) {
            // Use policy authorization for each message
            $this->authorize('delete', $message);

            // Delete the message's attachments first
            foreach ($message->attachments as $attachment) {
                if (Storage::exists($attachment->file_path)) {
                    Storage::delete($attachment->file_path);
                }
                $attachment->delete();
            }

            $message->delete();
        }

        return response()->json(['status' => 'success', 'message' => 'Chat cleared successfully.']);
    }
}
