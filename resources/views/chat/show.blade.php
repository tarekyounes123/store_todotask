@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <div class="w-100">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-2">{{ __('Chat with') }}</span>
                            @foreach($chat->users as $user)
                                @if($user->id !== Auth::id())
                                    <span class="chat-participant text-break" data-user-id="{{ $user->id }}" data-last-seen="{{ $user->last_seen ? $user->last_seen->toISOString() : '' }}">
                                        {{ $user->name }}
                                        <span id="status-icon-{{ $user->id }}" class="status-icon"></span>
                                        <small id="last-seen-{{ $user->id }}" class="text-muted"></small>
                                    </span>
                                    @if(!$loop->last)<span class="me-1">,</span>@endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Delete Selected Messages Button -->
                    <div class="d-flex flex-wrap gap-2">
                        <button id="clearChatBtn" class="btn btn-outline-warning btn-sm" title="Clear entire chat">
                            <i class="bi bi-x-circle d-md-none"></i><span class="d-none d-md-inline">{{ __('Clear Chat') }}</span>
                        </button>
                        <button id="deleteSelectedBtn" class="btn btn-outline-danger btn-sm" style="display: none;" title="Delete selected messages">
                            <i class="bi bi-trash d-md-none"></i><span class="d-none d-md-inline">{{ __('Delete Selected') }}</span> (<span id="selectedCount">0</span>)
                        </button>
                        <button id="cancelSelectionBtn" class="btn btn-outline-secondary btn-sm" style="display: none;">{{ __('Cancel') }}</button>
                    </div>
                </div>

                <div class="card-body p-2 p-md-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="chat-box" id="chatBox" style="height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 8px;">
                        @foreach($messages as $message)
                            <div class="mb-2 message-item {{ $message->user_id === Auth::id() ? 'text-end' : 'text-start' }}" data-message-id="{{ $message->id }}">
                                <div class="message-content">
                                    <strong>{{ $message->user->name }}:</strong> {{ $message->content }}
                                    @if($message->attachments->count() > 0)
                                        <div class="attachments mt-1">
                                            @foreach($message->attachments as $attachment)
                                                @if(in_array($attachment->file_mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']))
                                                    <a href="javascript:void(0);" class="image-preview-link d-block" data-image-url="{{ route('chat.showAttachment', $attachment->id) }}" data-download-url="{{ route('chat.downloadAttachment', $attachment->id) }}" data-file-name="{{ $attachment->file_name }}">
                                                        <img src="{{ route('chat.showAttachment', $attachment->id) }}" alt="{{ $attachment->file_name }}" class="img-fluid" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                                                    </a>
                                                @else
                                                    <a href="{{ route('chat.downloadAttachment', $attachment->id) }}" target="_blank" class="badge bg-secondary text-decoration-none d-inline-flex align-items-center my-1 me-1">
                                                        <i class="bi bi-file-earmark me-1"></i> {{ $attachment->file_name }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <small class="text-muted d-block">{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                                <!-- Selection checkbox (hidden by default) -->
                                <div class="message-selection" style="display: none; margin-top: 5px;">
                                    <input type="checkbox" class="message-checkbox" value="{{ $message->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="messageForm" action="{{ route('chat.sendMessage', $chat->id) }}" method="POST" enctype="multipart-form-data">
                        @csrf
                        <div class="input-group flex-nowrap flex-md-wrap">
                            <input type="text" name="content" id="messageContent" class="form-control flex-grow-1" placeholder="{{ __('Type your message...') }}">
                            <input type="file" name="attachment" id="messageAttachment" class="form-control" style="display: none;">
                            <button class="btn btn-outline-secondary flex-shrink-0" type="button" id="attachButton" title="Attach file">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <button class="btn btn-primary flex-shrink-0" type="submit">{{ __('Send') }}</button>
                        </div>
                        <div id="selectedFile" class="mt-2" style="display: none;">
                            <span class="badge bg-light text-dark border d-inline-flex align-items-center">
                                <span id="fileName" class="text-truncate" style="max-width: 200px;"></span>
                                <button type="button" class="btn-close ms-2" aria-label="Remove" onclick="removeSelectedFile()"></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Lightbox -->
    <div id="imageLightbox" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="lightboxImage" src="" alt="Preview" class="img-fluid" style="max-width: 100%; max-height: 70vh;">
                    <div class="mt-3">
                        <a id="downloadImageButton" class="btn btn-primary" target="_blank">
                            <i class="bi bi-download me-1"></i>{{ __('Download') }}
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="deleteConfirmationModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirm Deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to delete') }} <span id="deleteCountText">0</span> {{ __('message(s)?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Clear Chat -->
    <div id="clearChatModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Clear Chat') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to clear the entire chat? All messages will be permanently deleted.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmClearChatBtn">{{ __('Clear Chat') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const chatBox = document.getElementById('chatBox');
    const messageForm = document.getElementById('messageForm');
    const messageContentInput = document.getElementById('messageContent');
    const messageAttachmentInput = document.getElementById('messageAttachment'); // Get attachment input
    let lastMessageId = chatBox.lastElementChild ? chatBox.lastElementChild.dataset.messageId : 0;

    // --- Last Seen / Online Status Logic ---
    const chatParticipants = [];
    document.querySelectorAll('.chat-participant').forEach(participantEl => {
        chatParticipants.push({
            id: participantEl.dataset.userId,
            name: participantEl.textContent.trim().split(/\s*\(.*\)\s*|\s*,|\s*$/)[0], // Extract name without last seen text
            lastSeen: participantEl.dataset.lastSeen ? new Date(participantEl.dataset.lastSeen) : null,
            statusIconEl: document.getElementById(`status-icon-${participantEl.dataset.userId}`),
            lastSeenEl: document.getElementById(`last-seen-${participantEl.dataset.userId}`)
        });
    });

    function updateLastSeenStatus() {
        const now = new Date();
        chatParticipants.forEach(participant => {
            if (participant.lastSeen) {
                const diffSeconds = Math.floor((now - participant.lastSeen) / 1000);
                const diffMinutes = Math.floor(diffSeconds / 60);
                const diffHours = Math.floor(diffMinutes / 60);
                const diffDays = Math.floor(diffHours / 24);

                let lastSeenText = '';
                let isOnline = false;

                if (diffSeconds < 60) {
                    lastSeenText = 'Online';
                    isOnline = true;
                } else if (diffMinutes < 60) {
                    lastSeenText = `${diffMinutes}m ago`;
                } else if (diffHours < 24) {
                    lastSeenText = `${diffHours}h ago`;
                } else if (diffDays < 7) {
                    lastSeenText = `${diffDays}d ago`;
                } else {
                    lastSeenText = participant.lastSeen.toLocaleDateString(); // Or any other desired format
                }

                // For "Online" status, consider users active within the last 5 minutes
                if (diffMinutes < 5) { // Threshold for "online" can be adjusted
                    isOnline = true;
                    lastSeenText = 'Online'; // Override if within online threshold
                } else {
                    isOnline = false;
                }

                if (participant.statusIconEl) {
                    if (isOnline) {
                        participant.statusIconEl.innerHTML = '<i class="bi bi-circle-fill text-success" style="font-size: 0.7em;"></i>'; // Green dot for online
                        participant.statusIconEl.title = 'Online';
                    } else {
                        participant.statusIconEl.innerHTML = '<i class="bi bi-circle text-secondary" style="font-size: 0.7em;"></i>'; // Empty circle for offline
                        participant.statusIconEl.title = `Last seen ${lastSeenText}`;
                    }
                }
                if (participant.lastSeenEl) {
                    participant.lastSeenEl.textContent = `(${lastSeenText})`;
                }
            } else {
                if (participant.statusIconEl) {
                    participant.statusIconEl.innerHTML = '<i class="bi bi-circle text-secondary" style="font-size: 0.7em;"></i>';
                    participant.statusIconEl.title = 'Never seen';
                }
                if (participant.lastSeenEl) {
                    participant.lastSeenEl.textContent = '(Never seen)';
                }
            }
        });
    }

    // Initial update and then every second
    updateLastSeenStatus();
    setInterval(updateLastSeenStatus, 1000);

    // Image Lightbox functionality
    function openImageLightbox(imageUrl, downloadUrl, fileName) {
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('downloadImageButton').href = downloadUrl;
        document.getElementById('imageModalTitle').textContent = fileName;
        const lightbox = new bootstrap.Modal(document.getElementById('imageLightbox'));
        lightbox.show();
    }

    // Set up click handlers for image preview links
    function setupImagePreviewHandlers() {
        document.querySelectorAll('.image-preview-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const imageUrl = this.getAttribute('data-image-url');
                const downloadUrl = this.getAttribute('data-download-url');
                const fileName = this.getAttribute('data-file-name');
                openImageLightbox(imageUrl, downloadUrl, fileName);
            });
        });
    }

    // Message selection functionality
    let isSelectionMode = false;
    let selectedMessages = new Set();

    function toggleSelectionMode() {
        isSelectionMode = !isSelectionMode;
        const selectButtons = document.getElementById('deleteSelectedBtn');
        const cancelButtons = document.getElementById('cancelSelectionBtn');

        if (isSelectionMode) {
            // Show the selection controls
            selectButtons.style.display = 'inline-block';
            cancelButtons.style.display = 'inline-block';

            // Show checkboxes for all messages
            document.querySelectorAll('.message-selection').forEach(selectionDiv => {
                selectionDiv.style.display = 'block';
            });

            // Add click handlers to all checkboxes
            document.querySelectorAll('.message-checkbox').forEach(checkbox => {
                checkbox.checked = false; // Uncheck all by default
                checkbox.addEventListener('change', handleCheckboxChange);
            });
        } else {
            // Hide the selection controls
            selectButtons.style.display = 'none';
            cancelButtons.style.display = 'none';

            // Hide checkboxes and uncheck all
            document.querySelectorAll('.message-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Hide selection areas
            document.querySelectorAll('.message-selection').forEach(selectionDiv => {
                selectionDiv.style.display = 'none';
            });

            // Clear the selected messages set
            selectedMessages.clear();
            updateSelectedCount();
        }
    }

    function handleCheckboxChange(event) {
        const messageId = parseInt(event.target.value);

        if (event.target.checked) {
            selectedMessages.add(messageId);
        } else {
            selectedMessages.delete(messageId);
        }

        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = selectedMessages.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('deleteCountText').textContent = count;

        // Update the delete button state
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        if (count > 0) {
            deleteBtn.classList.remove('btn-outline-danger');
            deleteBtn.classList.add('btn-danger');
        } else {
            deleteBtn.classList.remove('btn-danger');
            deleteBtn.classList.add('btn-outline-danger');
        }
    }

    function deleteSelectedMessages() {
        if (selectedMessages.size === 0) return;

        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        modal.show();
    }

    // Function to actually delete the messages
    async function confirmDelete() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`{{ route('chat.deleteMessages', $chat->id) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message_ids: Array.from(selectedMessages)
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Remove the deleted messages from the UI
                selectedMessages.forEach(messageId => {
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageElement) {
                        messageElement.remove();
                    }
                });

                // Clear selected messages and exit selection mode
                selectedMessages.clear();
                toggleSelectionMode();

                // Hide the confirmation modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
                modal.hide();

                // Show success message
                alert(data.message);
            } else {
                alert('Error: ' + (data.message || 'Failed to delete messages'));
            }
        } catch (error) {
            console.error('Error deleting messages:', error);
            alert('Error occurred while deleting messages');
        }
    }

    // Clear Chat functionality
    function clearChat() {
        const modal = new bootstrap.Modal(document.getElementById('clearChatModal'));
        modal.show();
    }

    // Function to actually clear the entire chat
    async function confirmClearChat() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`{{ route('chat.clear', $chat->id) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Clear all messages from the UI
                document.getElementById('chatBox').innerHTML = '';

                // Hide the confirmation modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('clearChatModal'));
                modal.hide();

                // Show success message
                alert(data.message);
            } else {
                alert('Error: ' + (data.message || 'Failed to clear chat'));
            }
        } catch (error) {
            console.error('Error clearing chat:', error);
            alert('Error occurred while clearing chat');
        }
    }

    // Add event listeners to the new buttons
    document.addEventListener('DOMContentLoaded', function() {
        setupImagePreviewHandlers();

        // Add event listener for the clear chat button
        document.getElementById('clearChatBtn').addEventListener('click', clearChat);

        // Add event listener for the delete button
        document.getElementById('deleteSelectedBtn').addEventListener('click', deleteSelectedMessages);

        // Add event listener for the cancel button
        document.getElementById('cancelSelectionBtn').addEventListener('click', toggleSelectionMode);

        // Add event listener for the confirm delete button
        document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

        // Add event listener for the confirm clear chat button
        document.getElementById('confirmClearChatBtn').addEventListener('click', confirmClearChat);

        // Add context menu (right-click) to allow selection for any message
        document.querySelectorAll('.message-item').forEach(messageDiv => {
            messageDiv.addEventListener('contextmenu', function(e) {
                // Enable selection mode for any message
                e.preventDefault();
                if (!isSelectionMode) {
                    toggleSelectionMode();
                    // Pre-select this message
                    const checkbox = this.querySelector('.message-checkbox');
                    if (checkbox) {
                        checkbox.checked = true;
                        selectedMessages.add(parseInt(checkbox.value));
                        updateSelectedCount();
                    }
                }
            });
        });
    });

    function createMessageElement(message) {
        let messageDiv = document.createElement('div');
        messageDiv.classList.add('mb-2', 'message-item');
        messageDiv.dataset.messageId = message.id;

        if (message.user_id === {{ Auth::id() }}) {
            messageDiv.classList.add('text-end');
        } else {
            messageDiv.classList.add('text-start');
        }

        let contentHtml = `<div class="message-content"><strong>${message.user.name}:</strong> ${message.content || ''}`;

        if (message.attachments && message.attachments.length > 0) {
            contentHtml += `<div class="attachments mt-1">`;
            const imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            message.attachments.forEach(attachment => {
                const downloadUrl = `/chat/attachment/${attachment.id}`;
                const showUrl = `/chat/attachment/${attachment.id}/show`;
                if (imageMimeTypes.includes(attachment.file_mime_type)) {
                    contentHtml += `<a href="javascript:void(0);" class="image-preview-link" data-image-url="${showUrl}" data-download-url="${downloadUrl}" data-file-name="${attachment.file_name}" style="display:block; margin-top: 5px;">
                                        <img src="${showUrl}" alt="${attachment.file_name}" style="max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                    </a>`;
                } else {
                    contentHtml += `<a href="${downloadUrl}" target="_blank" class="badge bg-secondary text-decoration-none d-inline-flex align-items-center mt-1 me-1">
                                        <i class="bi bi-file-earmark me-1"></i> ${attachment.file_name}
                                    </a>`;
                }
            });
            contentHtml += `</div>`;
        }

        contentHtml += `<small class="text-muted d-block">${moment(message.created_at).fromNow()}</small></div>`; // Using moment.js for diffForHumans equivalent

        // Add the selection checkbox div
        contentHtml += `<div class="message-selection" style="display: none; margin-top: 5px;"><input type="checkbox" class="message-checkbox" value="${message.id}"></div>`;

        messageDiv.innerHTML = contentHtml;

        // Set up image preview handlers for the new message
        setTimeout(() => {
            setupImagePreviewHandlers();

            // Add selection functionality if in selection mode
            if (isSelectionMode) {
                const selectionDiv = messageDiv.querySelector('.message-selection');
                if (selectionDiv) {
                    selectionDiv.style.display = 'block';
                }

                const checkbox = messageDiv.querySelector('.message-checkbox');
                if (checkbox) {
                    checkbox.addEventListener('change', handleCheckboxChange);
                }
            }
        }, 10);

        return messageDiv;
    }

    // --- Existing Message Fetching Logic ---
    function fetchNewMessages() {
        fetch(`{{ route('chat.fetchMessages', $chat->id) }}?last_message_id=${lastMessageId}`)
            .then(response => response.json())
            .then(messages => {
                messages.forEach(message => {
                    // Check if message already exists to prevent duplicates (though lastMessageId should handle most)
                    if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                        let messageElement = createMessageElement(message);
                        chatBox.appendChild(messageElement);
                        chatBox.scrollTop = chatBox.scrollHeight;
                        lastMessageId = message.id; // Update lastMessageId after appending
                    }
                });
            })
            .catch(error => console.error('Error fetching new messages:', error));
    }

    // Handle file attachment button click
    document.getElementById('attachButton').addEventListener('click', function() {
        messageAttachmentInput.click();
    });

    // Handle file selection
    messageAttachmentInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('selectedFile').style.display = 'block';
        }
    });

    // Function to remove selected file
    function removeSelectedFile() {
        messageAttachmentInput.value = '';
        document.getElementById('selectedFile').style.display = 'none';
    }

    // Handle message form submission via AJAX
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const content = messageContentInput.value.trim();
        const attachment = messageAttachmentInput.files[0];

        if (!content && !attachment) {
            alert('Please enter a message or select a file to send.');
            return;
        }

        const formData = new FormData();
        if (content) {
            formData.append('content', content);
        }
        if (attachment) {
            formData.append('attachment', attachment);
        }
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));


        fetch(messageForm.action, {
            method: 'POST',
            // headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, // No JSON header for FormData
            headers: { 'Accept': 'application/json' }, // Laravel handles CSRF for FormData, just need Accept
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                // If response is not ok, parse error message and throw
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            messageContentInput.value = ''; // Clear input field
            messageAttachmentInput.value = ''; // Clear file input
            document.getElementById('selectedFile').style.display = 'none'; // Hide selected file display
            if (data.message) {
                let messageElement = createMessageElement(data.message);
                chatBox.appendChild(messageElement);
                chatBox.scrollTop = chatBox.scrollHeight;
                lastMessageId = data.message.id; // Update lastMessageId after appending
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            let errorMessage = 'Failed to send message.';
            if (error.errors) { // Handle validation errors from Laravel
                errorMessage += '\n' + Object.values(error.errors).map(e => e.join(', ')).join('\n');
            } else if (error.message) {
                errorMessage = error.message;
            }
            alert(errorMessage);
        });
    });

    // Fetch new messages every 1 second
    setInterval(fetchNewMessages, 1000);

    // Initial scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endpush
