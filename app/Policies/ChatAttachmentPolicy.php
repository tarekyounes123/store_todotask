<?php

namespace App\Policies;

use App\Models\ChatAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChatAttachment $chatAttachment): bool
    {
        // Check if the user is a participant of the chat that contains this attachment
        return $chatAttachment->message->chat->users->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChatAttachment $chatAttachment): bool
    {
        // Allow users to update attachments they created or admins
        return $user->id === $chatAttachment->message->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChatAttachment $chatAttachment): bool
    {
        // Allow users to delete attachments they created or admins
        return $user->id === $chatAttachment->message->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChatAttachment $chatAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChatAttachment $chatAttachment): bool
    {
        return false;
    }
}
