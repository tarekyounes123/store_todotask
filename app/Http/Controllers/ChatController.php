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
        // Ensure the authenticated user is a participant of the chat
        abort_unless($chat->users->contains(Auth::id()), 403);

        $messages = $chat->messages()->with('user')->get();
        return view('chat.show', compact('chat', 'messages'));
    }

    /**
     * Send a message to the specified chat.
     */
    public function sendMessage(Request $request, Chat $chat): \Illuminate\Http\JsonResponse
    {
        // Ensure the authenticated user is a participant of the chat
        abort_unless($chat->users->contains(Auth::id()), 403);

        $request->validate([
            'content' => 'nullable|string|max:1000', // Content can be nullable if only sending attachment
            'attachment' => 'nullable|file|max:10240', // Max 10MB
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
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/chat_attachments', $fileName); // Store in storage/app/public/chat_attachments

            if ($filePath) {
                $attachment = $message->attachments()->create([
                    'file_path' => $filePath, // Store the path in storage
                    'file_name' => $fileName,
                    'file_mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            } else {
                // Log the error if file storage failed
                \Log::error('Failed to store chat attachment: ' . $fileName, [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType()
                ]);

                return response()->json(['status' => 'error', 'message' => 'Failed to upload attachment.'], 500);
            }
        }
        
        // We are disabling broadcasting for now as per user request for polling
        // broadcast(new MessageSent(Auth::user(), $message))->toOthers();

        return response()->json(['status' => 'success', 'message' => $message->load(['user', 'attachments'])]);
    }

    /**
     * Fetch new messages for a chat.
     */
    public function fetchNewMessages(Request $request, Chat $chat)
    {
        // Ensure the authenticated user is a participant of the chat
        abort_unless($chat->users->contains(Auth::id()), 403);

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
        // Check if the authenticated user is part of the chat that contains this attachment
        $message = $attachment->message;
        $chat = $message->chat;

        abort_unless($chat->users->contains(Auth::id()), 403);

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
        // Check if the authenticated user is part of the chat that contains this attachment
        $message = $attachment->message;
        $chat = $message->chat;

        abort_unless($chat->users->contains(Auth::id()), 403);

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

        // Ensure the authenticated user is a participant of the chat
        abort_unless($chat->users->contains(Auth::id()), 403);

        $messageIds = $request->input('message_ids');

        // Allow users to delete any messages in the chat they participate in
        $messages = $chat->messages()->whereIn('id', $messageIds)->get();

        foreach ($messages as $message) {
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
        // Ensure the authenticated user is a participant of the chat
        abort_unless($chat->users->contains(Auth::id()), 403);

        // Get all messages in the chat to delete their attachments first
        $messages = $chat->messages;

        foreach ($messages as $message) {
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
