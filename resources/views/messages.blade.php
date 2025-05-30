<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css', 'resources/js/script.js'])
    <style>
      .chat-container {
        height: calc(100vh - 80px);
        margin-top: 80px;
        overflow: hidden;
      }
      .conversation-list {
        height: 100%;
        overflow-y: auto;
        border-right: 1px solid rgba(0, 0, 0, 0.1);
      }
      .conversation-items {
        height: calc(100% - 150px); /* Adjusted for new header elements */
        overflow-y: auto;
      }
      @media (max-width: 768px) {
        .conversation-list {
          height: auto;
          max-height: 30vh;
        }
        .chat-container {
          flex-direction: column;
        }
      }
      .conversation-item {
        cursor: pointer;
        border-radius: 8px;
        margin-bottom: 8px;
      }
      .conversation-item:hover {
        background-color: rgba(13, 110, 253, 0.1);
      }
      .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
      }
      .chat-messages {
        height: calc(100% - 135px);
        overflow-y: auto;
      }
      .message-bubble {
        max-width: 70%;
        border-radius: 18px;
        padding: 10px 15px;
        margin-bottom: 2px;
      }
      .user-message {
        background-color: #f1f1f1;
      }
      .admin-message {
        background-color: #0d6efd;
        color: white;
      }
      .admin-message .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
      }
      
      /* New Chat Modal Styles */
      .search-result-item {
        transition: background-color 0.2s;
      }
      .search-result-item:hover {
        background-color: rgba(13, 110, 253, 0.1);
      }
      #searchResults {
        max-height: 300px;
        overflow-y: auto;
      }
      #initialSearchPrompt, #searchingIndicator, #noResultsMessage {
        padding: 30px 0;
      }
      .modal-body {
        padding: 20px;
      }
      .conversation-search-results {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
      }
    </style>
  </head>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/live-updates.js') }}"></script>

  <body>

        <!-- HEADER -->
        <header id="header" class="header fixed-top d-flex align-items-center bg-primary">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <a href="#" class="logo d-flex align-items-center">
          <button class="toggle-btn" type="button" id="toggleSidebar">
            <img src="/images/logo.png" alt="Logo" width="40" height="38" class="d-inline-block align-text-top toggle-img">
            <span class="text-secondary ms-2 mb-1"><strong class="fs-4">iServeComembo</strong></span>
          </button>
        </a>
        <ul class="d-flex align-items-center">
          <!-- Notification Dropdown -->
           <li class="nav-item dropdown dropdown-center">
            <a class="nav-link text-light position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-bell-fill"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                4
                <span class="visually-hidden">unread messages</span>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end notification-dropdown text-primary ps-3 bg-secondary" aria-labelledby="notificationDropdown"> Notification
              <li class="dropdown-item">
                <div class="notification-content">
                  <p>You have a new complaint request awaiting your attention. Please review and take action.</p>
                  <small>11:11AM, November 6, 2024</small>
                </div>
              </li>
              <li class="dropdown-item">
                <div class="notification-content">
                  <p>You have a new complaint request awaiting your attention. Please review and take action.</p>
                  <small>11:11AM, November 3, 2024</small>
                </div>
              </li>
            </ul>
          </li>

          <!-- Admin Account Dropdown -->
          <li class="nav-item dropdown dropdown-center">
        <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ Auth::user()->getAvatarUrl(30, 'ui-avatars') }}" alt="Admin Avatar" width="30" height="30" class="rounded-circle me-2">
            <span>{{ Auth::user()->name ?? 'K. Anderson' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end admin-dropdown bg-secondary" aria-labelledby="adminDropdown">
            <li class="dropdown-header text-center">
                <strong class="text-primary">{{ Auth::user()->name ?? 'Kevin Anderson' }}</strong><br>
            </li>
            <li><a class="dropdown-item fw-normal me-5" href="{{ route('admin.show', Auth::user()->id ?? '') }}"><i class="bi bi-person me-2 fs-5"></i> My Profile</a></li>
            <li><a class="dropdown-item fw-normal me-5" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Sign Out</a></li>
        </ul>
    </li>
      </div>
    </header>
    <!-- HEADER ENDS -->

    <!-- SIDEBAR -->
    <div class="wrapper expand">
      <aside id="sidebar" class="expand">
        <ul class="sidebar-nav">
          <li class="sidebar-item">
            <a href="{{ route('dashboard') }}" class="sidebar-link">
              <i class="bi bi-trello fs-4"></i>
              <span class="fs-5 lead text-secondary">Dashboard</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#task" aria-expanded="false" aria-controls="task">
              <i class="bi bi-file-earmark-ruled-fill fs-4"></i>
              <span class="fs-5 lead text-secondary">Services</span>
            </a>
            <ul id="task" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="{{ route('documentrequest') }}" class="sidebar-link text-secondary ms-3">Document</a>
              </li>
              <li class="sidebar-item">
                <a href="{{ route('complaint') }}" class="sidebar-link text-secondary ms-3">Complaint</a>
              </li>
            </ul>
          </li>
          <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
              <i class="bi bi-megaphone-fill fs-4"></i>
              <span class="fs-5 lead text-secondary">Publish</span>
            </a>
            <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="{{ route('news') }}" class="sidebar-link text-secondary ms-3">News</a>
              </li>
              <li class="sidebar-item">
                <a href="{{ route('announcements') }}" class="sidebar-link text-secondary ms-3">Announcements</a>
              </li>
              <li class="sidebar-item">
                <a href="{{ route('faqs') }}" class="sidebar-link text-secondary ms-3">FAQs</a>
              </li>
            </ul>
          </li>
          <li class="sidebar-item">
            <a href="{{ route('messages') }}" class="sidebar-link">
              <i class="bi bi-chat-left-text-fill fs-4"></i>              
                <span class="fs-5 lead text-secondary">Messages</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a href="{{ route('feedback') }}" class="sidebar-link">
              <i class="bi bi-chat-quote-fill fs-4"></i>
              <span class="fs-5 lead text-secondary">Feedback</span>
            </a>
          </li>
          
          <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#acc" aria-expanded="false" aria-controls="acc">
              <i class="bi bi-person-vcard-fill fs-4"></i>
              <span class="fs-5 lead text-secondary">Accounts</span>
            </a>
            <ul id="acc" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
              <li class="sidebar-item">
                <a href="{{ route('residents') }}" class="sidebar-link text-secondary ms-3">Residents</a>
              </li>
              <li class="sidebar-item">
                <a href="{{ route('admin') }}" class="sidebar-link text-secondary ms-3">Admin</a>
              </li>
            </ul>
          </li>
        </ul>
      </aside>
      <!-- SIDEBAR ENDS -->

      <!-- BODY -->

    <div class="container-fluid p-0">
        <div class="chat-container d-flex ">
            <!-- Left Sidebar - Conversation List -->
            <div class="conversation-list col-md-3 bg-light border-end p-3 ">
                <!-- Conversations Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Conversations</h5>
                    <button type="button" class="btn btn-primary btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#newChatModal" title="Start new conversation">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-secondary"></i></span>
                        <input type="text" class="form-control" placeholder="Search conversations..." id="conversationSearch">
                    </div>
                </div>
                
                <!-- New Chat Button (Mobile Friendly) -->
                <div class="d-grid mb-3">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="bi bi-pencil-square me-1"></i> New Conversation
                    </button>
                </div>

                <!-- Conversations -->
                <div class="overflow-auto conversation-items">
                    @forelse($conversations as $conversation)
                        @php
                            // Determine the other user in the conversation
                            $otherUser = ($conversation->sender_id == Auth::id()) 
                                ? $conversation->receiver 
                                : $conversation->sender;
                            
                            // Get the last message for preview
                            $lastMessage = App\Models\Message::where(function($query) use ($otherUser) {
                                    $query->where('sender_id', Auth::id())
                                          ->where('receiver_id', $otherUser->id);
                                })
                                ->orWhere(function($query) use ($otherUser) {
                                    $query->where('sender_id', $otherUser->id)
                                          ->where('receiver_id', Auth::id());
                                })
                                ->latest()
                                ->first();
                            
                            // Check for unread messages
                            $unreadCount = App\Models\Message::where('sender_id', $otherUser->id)
                                ->where('receiver_id', Auth::id())
                                ->where('is_read', false)
                                ->count();
                                
                            // Get display name
                            $displayName = $otherUser->name;
                            if ($otherUser->firstname && $otherUser->lastname) {
                                $displayName = $otherUser->firstname . ' ' . $otherUser->lastname;
                            }
                        @endphp
                        
                        <a href="{{ route('messages.show', $otherUser->id) }}" class="text-decoration-none">
                            <div class="conversation-item d-flex align-items-center p-2 {{ $activeConversation && $activeConversation->id == $otherUser->id ? 'bg-primary-subtle' : '' }}">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}" class="user-avatar me-2" alt="{{ $displayName }}">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 {{ $unreadCount > 0 ? 'fw-bold' : '' }}">{{ $displayName }}</h6>
                                        @if($unreadCount > 0)
                                            <span class="badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-0 {{ $unreadCount > 0 ? 'fw-bold' : '' }}">
                                        {{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 30) : 'No messages yet' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-3 text-muted">
                            <p>No conversations yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Side - Chat Area -->
            <div class="col-md-9 d-flex flex-column">
                @if($activeConversation)
                    <!-- Chat Header -->
                    <div class="p-3 border-bottom d-flex align-items-center bg-white">
                        @php
                            $displayName = $activeConversation->name;
                            if ($activeConversation->firstname && $activeConversation->lastname) {
                                $displayName = $activeConversation->firstname . ' ' . $activeConversation->lastname;
                            }
                        @endphp
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}" class="user-avatar me-2" alt="{{ $displayName }}">
                        <div>
                            <h6 class="mb-0">{{ $displayName }}</h6>
                            <p class="text-success small mb-0">{{ $activeConversation->last_seen_at ? 'Last seen: ' . $activeConversation->last_seen_at->diffForHumans() : 'Online' }}</p>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="flex-grow-1 overflow-auto p-3 chat-messages bg-light" id="chat-messages">
                        @forelse($messages as $message)
                            @if($message->sender_id == Auth::id())
                                <!-- My message -->
                                <div class="d-flex align-items-start justify-content-end mb-3">
                                    <div class="message-bubble admin-message">
                                        <p>{{ $message->message }}</p>
                                        <p class="text-muted small mb-0">{{ $message->created_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            @else
                                <!-- Their message -->
                                <div class="d-flex align-items-start mb-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}" class="user-avatar me-2" alt="{{ $displayName }}">
                                    <div class="message-bubble user-message">
                                        <p>{{ $message->message }}</p>
                                        <p class="text-muted small mb-0">{{ $message->created_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-5 text-muted">
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Message Input -->
                    <div class="p-3 border-top bg-white">
                        <form class="d-flex" action="{{ route('messages.store') }}" method="POST" id="messageForm">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $activeConversation->id }}">
                            <input type="text" class="form-control me-2 rounded-pill" name="message" placeholder="Type your message..." required>
                            <button type="submit" class="btn btn-primary rounded-circle">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- No active conversation -->
                    <div class="d-flex flex-column justify-content-center align-items-center h-100">
                        <div class="text-center p-5">
                            <i class="bi bi-chat-square-text fs-1 text-muted"></i>
                            <h5 class="mt-3">No conversation selected</h5>
                            <p class="text-muted">Select a conversation from the list to start chatting</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

      <!-- BODY END -->
    </div>
    
    <!-- New Chat Modal -->
    <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="newChatModalLabel">Start New Conversation</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control" id="userSearchInput" placeholder="Search by name or email..." autocomplete="off">
            </div>
            <div id="searchResults" class="list-group mt-3">
              <!-- Search results will appear here -->
              <div class="text-center text-muted py-4" id="initialSearchPrompt">
                <i class="bi bi-person-plus fs-2 mb-2"></i>
                <p class="mb-0">Search for users to start a conversation</p>
                <small>Type at least 2 characters to search</small>
              </div>
              <div class="text-center py-4 d-none" id="searchingIndicator">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Searching...</p>
              </div>
              <div class="text-center text-muted py-4 d-none" id="noResultsMessage">
                <i class="bi bi-exclamation-circle fs-2 mb-2"></i>
                <p class="mb-0">No users found</p>
                <small>Try a different search term</small>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <small class="text-muted me-auto">Select a user to start or continue a conversation</small>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/live-updates.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Scroll chat to bottom on page load
      const chatMessages = document.getElementById('chat-messages');
      if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
      
      // Handle form submission with AJAX
      const messageForm = document.getElementById('messageForm');
      if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          const messageInput = this.querySelector('input[name="message"]');
          const submitButton = this.querySelector('button[type="submit"]');
          const message = messageInput.value;
          
          // Disable the button and input to prevent multiple submissions
          messageInput.disabled = true;
          submitButton.disabled = true;
          
          console.log('Sending message:', message);
          console.log('To receiver:', formData.get('receiver_id'));
          
          // Clear input field immediately
          messageInput.value = '';
          
          // Send message via AJAX
          console.log('Sending message form with CSRF token:', document.querySelector('meta[name="csrf-token"]').content);
          
          fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin' // Include cookies
          })
          .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
              // Attempt to parse error message from response
              return response.text().then(text => {
                console.error('Error response body:', text);
                try {
                  // Try to parse error as JSON
                  const errorJson = JSON.parse(text);
                  if (errorJson && errorJson.message) {
                    throw new Error(errorJson.message);
                  }
                } catch (e) {
                  // Not JSON or no message property
                }
                throw new Error('Server error: ' + response.status);
              });
            }
            
            // Try to parse as JSON, but handle text responses too
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
              return response.json();
            } else {
              // For HTML or other responses, we'll simulate a successful JSON response
              return response.text().then(text => {
                console.log('Non-JSON response:', text.substring(0, 100) + '...'); // Show first 100 chars
                return { success: true, message: 'Message sent', isHtmlResponse: true };
              });
            }
          })
          .then(data => {
            console.log('Message sent response:', data);
            
            if (data && data.success === false) {
              throw new Error(data.message || 'Unknown error occurred');
            }
            
            // Message sent successfully, reload the chat
            window.location.reload();
          })
          .catch(error => {
            console.error('Error sending message:', error);
            // Re-add the message text in case of error
            messageInput.value = message;
            
            // Show detailed error to help debugging
            const errorMsg = error.message || 'Failed to send message. Please try again.';
            alert(errorMsg);
          })
          .finally(() => {
            // Re-enable the button and input
            messageInput.disabled = false;
            submitButton.disabled = false;
          });
        });
      }
      
      // User Search for New Chat
      const userSearchInput = document.getElementById('userSearchInput');
      const searchResults = document.getElementById('searchResults');
      const initialPrompt = document.getElementById('initialSearchPrompt');
      const searchingIndicator = document.getElementById('searchingIndicator');
      const noResultsMessage = document.getElementById('noResultsMessage');
      
      let searchTimeout;
      let lastQuery = '';
      
      if (userSearchInput) {
        // Focus the search input when the modal is shown
        document.getElementById('newChatModal').addEventListener('shown.bs.modal', function() {
          userSearchInput.focus();
          userSearchInput.value = '';
          
          // Reset the search results
          initialPrompt.classList.remove('d-none');
          searchingIndicator.classList.add('d-none');
          noResultsMessage.classList.add('d-none');
          
          // Remove previous results
          const previousResults = searchResults.querySelectorAll('.search-result-item');
          previousResults.forEach(item => item.remove());
        });
        
        userSearchInput.addEventListener('input', function() {
          const query = this.value.trim();
          
          // Don't search again if the query hasn't changed
          if (query === lastQuery) {
            return;
          }
          
          lastQuery = query;
          
          // Clear previous timeout
          clearTimeout(searchTimeout);
          
          if (query.length < 2) {
            // Reset the results area when input is too short
            initialPrompt.classList.remove('d-none');
            searchingIndicator.classList.add('d-none');
            noResultsMessage.classList.add('d-none');
            
            // Remove previous results
            const previousResults = searchResults.querySelectorAll('.search-result-item');
            previousResults.forEach(item => item.remove());
            return;
          }
          
          // Show searching indicator
          initialPrompt.classList.add('d-none');
          searchingIndicator.classList.remove('d-none');
          noResultsMessage.classList.add('d-none');
          
          // Set a timeout to prevent too many requests
          searchTimeout = setTimeout(function() {
            console.log('Searching for users with query:', query);
            
            // Make AJAX call to search users
            fetch('/messages/search?query=' + encodeURIComponent(query), {
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            })
            .then(response => {
              console.log('Search response status:', response.status);
              if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
              }
              return response.json();
            })
            .then(users => {
              console.log('Users found:', users);
              
              // Hide searching indicator
              searchingIndicator.classList.add('d-none');
              
              // Remove previous results
              const previousResults = searchResults.querySelectorAll('.search-result-item');
              previousResults.forEach(item => item.remove());
              
              if (!users || users.length === 0) {
                console.log('No users found in search results');
                noResultsMessage.classList.remove('d-none');
                return;
              }
              
              // Display results
              users.forEach(user => {
                const userItem = document.createElement('a');
                userItem.href = `/messages/${user.id}`;
                userItem.className = 'list-group-item list-group-item-action search-result-item';
                
                // Check if there's an existing conversation
                const conversationStatus = user.hasExistingConversation 
                  ? '<span class="badge bg-info text-white ms-2">Existing chat</span>' 
                  : '<span class="badge bg-success text-white ms-2">New chat</span>';
                
                userItem.innerHTML = `
                  <div class="d-flex align-items-center">
                    <img src="${user.avatar}" class="rounded-circle me-2" width="40" height="40" alt="${user.name}">
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">${user.name}</h6>
                        ${conversationStatus}
                      </div>
                      <small class="text-muted">${user.email}</small>
                    </div>
                  </div>
                `;
                
                // Add click event to start conversation
                userItem.addEventListener('click', function(e) {
                  e.preventDefault();
                  
                  // Close the modal
                  const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
                  modal.hide();
                  
                  // Navigate to conversation
                  window.location.href = this.href;
                });
                
                searchResults.appendChild(userItem);
              });
            })
            .catch(error => {
              console.error('Error searching users:', error);
              searchingIndicator.classList.add('d-none');
              noResultsMessage.classList.remove('d-none');
            });
          }, 300); // Reduced timeout for better responsiveness
        });
        
        // Handle Enter key in search
        userSearchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            
            // Click the first result if there is one
            const firstResult = searchResults.querySelector('.search-result-item');
            if (firstResult) {
              firstResult.click();
            }
          }
        });
      }
      
      // Filter conversations with the search bar
      const conversationSearch = document.getElementById('conversationSearch');
      if (conversationSearch) {
        conversationSearch.addEventListener('input', function() {
          const query = this.value.toLowerCase().trim();
          const conversations = document.querySelectorAll('.conversation-item');
          
          conversations.forEach(function(conversation) {
            const name = conversation.querySelector('h6').textContent.toLowerCase();
            const message = conversation.querySelector('.text-muted').textContent.toLowerCase();
            
            if (name.includes(query) || message.includes(query)) {
              conversation.parentElement.style.display = '';
            } else {
              conversation.parentElement.style.display = 'none';
            }
          });
        });
      }
      
      // Poll for new messages every 5 seconds
      setInterval(function() {
        const activeConversation = document.querySelector('input[name="receiver_id"]');
        if (activeConversation) {
          const userId = activeConversation.value;
          
          // Make an AJAX request to check for new messages
          fetch(`/messages/${userId}?format=json`, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => {
            if (response.ok && response.headers.get('content-type').includes('application/json')) {
              return response.json();
            }
            return null;
          })
          .then(data => {
            if (data && data.newMessages) {
              // If there are new messages, reload the page
              window.location.reload();
            }
          });
        }
      }, 5000);
    });
  </script>
  </body>
</html>
