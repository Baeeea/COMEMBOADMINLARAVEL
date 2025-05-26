<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DASHBOARD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css'])
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
    <li class="nav-item">
  <a class="nav-link d-flex align-items-center"
     data-bs-toggle="collapse"
     href="#profileDropdown"
     role="button"
     aria-expanded="false"
     aria-controls="profileDropdown">
    <img src="{{ asset('img/person1.png') }}" alt="Admin Avatar" width="30" height="30" class="rounded-circle me-2">
    <span class="fs-5 lead text-light">{{ Auth::user()->name ?? 'Admin' }}</span>
    <i class="bi bi-chevron-down ms-auto text-light"></i> <!-- optional chevron -->
  </a>

  <ul class="collapse list-unstyled ps-4 bg-secondary" id="profileDropdown">
    <li class="sidebar-item">
      <a href="{{ route('admin.show', Auth::user()->id) }}" class="sidebar-link text-light">
        <i class="bi bi-person me-2"></i> My Profile
      </a>
    </li>
    <li class="sidebar-item">
      <a href="{{ route('logout') }}" class="sidebar-link text-light">
        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
      </a>
    </li>
  </ul>
</li>





      </div>
    </header>
    <!-- HEADER ENDS -->

    <!-- SIDEBAR -->
    <div class="wrapper">
      <aside id="sidebar">
        <ul class="sidebar-nav">
          <li class="sidebar-item">
            <a href="{{ route('dashboard') }}" class="sidebar-link">
              <i class="bi bi-trello fs-4"></i>
              <span class="fs-5 lead text-secondary">Dashboard</span>
            </a>
          </li>
          <ul class="list-unstyled" id="sidebar">
  <!-- Static Link -->
  <li class="sidebar-item">
    <a href="/" class="sidebar-link">
      <i class="bi bi-house fs-4"></i>
      <span class="fs-5 lead text-secondary">Dashboard</span>
    </a>
  </li>

  <!-- Dropdown Trigger -->
  <li class="sidebar-item">
  <a class="sidebar-link d-flex align-items-center"
     data-bs-toggle="collapse"
     href="#servicesDropdown"
     role="button"
     aria-expanded="false"
     aria-controls="servicesDropdown">
    <i class="bi bi-file-earmark-ruled-fill fs-4 me-2"></i>
    <span class="fs-5 lead text-secondary">Services</span>
  </a>

  <ul class="collapse list-unstyled ps-4" id="servicesDropdown">
    <li class="sidebar-item">
      <li class="sidebar-item">
  <a href="{{ route('documentrequest') }}" class="sidebar-link text-secondary">Document</a>
</li>
<li class="sidebar-item">
  <a href="{{ route('complaint') }}" class="sidebar-link text-secondary">Complaint</a>
</li>
  </ul>
</li>



  <li class="sidebar-item">
    <a href="#auth" class="sidebar-link d-flex align-items-center collapsed"
       data-bs-toggle="collapse"
       role="button"
       aria-expanded="false"
       aria-controls="auth">
      <i class="bi bi-megaphone-fill fs-4 me-2"></i>
      <span class="fs-5 lead text-secondary">Publish</span>
      <i class="bi bi-chevron-down ms-auto"></i> <!-- optional chevron icon -->
    </a>
    <ul id="auth" class="collapse list-unstyled ps-3" data-bs-parent="#sidebar">
      <li class="sidebar-item">
        <a href="{{ route('news') }}" class="sidebar-link text-secondary">News</a>
      </li>
      <li class="sidebar-item">
        <a href="{{ route('announcements') }}" class="sidebar-link text-secondary">Announcements</a>
      </li>
      <li class="sidebar-item">
        <a href="{{ route('faqs') }}" class="sidebar-link text-secondary">FAQs</a>
      </li>
    </ul>
  </li>
          <li class="sidebar-item">
  <a href="{{ route('messages') }}" class="sidebar-link">
    <i class="bi bi-chat-left-text-fill fs-4"></i>
    <span class="fs-5 lead text-secondary">Messages</span>
  </a>
</li>

          </li>
          <li class="sidebar-item">
  <a href="{{ route('feedback') }}" class="sidebar-link">
    <i class="bi bi-chat-quote-fill fs-4"></i>
    <span class="fs-5 lead text-secondary">Feedback</span>
  </a>
</li>

          <li class="sidebar-item">
  <a href="#acc" class="sidebar-link d-flex align-items-center collapsed"
     data-bs-toggle="collapse"
     role="button"
     aria-expanded="false"
     aria-controls="acc">
    <i class="bi bi-person-vcard-fill fs-4 me-2"></i>
    <span class="fs-5 lead text-secondary">Accounts</span>
    <i class="bi bi-chevron-down ms-auto"></i> <!-- optional chevron icon -->
  </a>
  <ul id="acc" class="collapse list-unstyled ps-3" data-bs-parent="#sidebar">
    <li class="sidebar-item">
      <a href="{{ route('residents') }}" class="sidebar-link text-secondary">Residents</a>
    </li>
    <li class="sidebar-item">
      <a href="{{ route('admin') }}" class="sidebar-link text-secondary">Admin</a>
    </li>
  </ul>
</li>



      </aside>


      <!-- BODY -->

      <div class="container mt-5">
        <h1 class="text-center mt-4 mb-5 display-4 fw-bolder text-primary">Feedback</h1>
        
        <!-- Success/Error Messages -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="text-primary">All Feedback</h4>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeedbackModal">
            <i class="bi bi-plus-circle me-2"></i>Add Feedback
          </button>
        </div>

        <div class="">
          <div>
            <div class="table-responsive">
              <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                  <tr>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Username</th>
                    <th scope="col" class="text-primary py-4" style="width: 50%;">Feedback Message</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Date</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($feedbacks as $feedback)
                  <tr>
                    <td class="py-4">{{ $feedback->user->name ?? 'Unknown User' }}</td>
                    <td class="py-4">{{ $feedback->content }}</td>
                    <td class="py-4">{{ $feedback->created_at ? $feedback->created_at->format('m / d / Y') : 'N/A' }}</td>
                    <td class="py-4">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteFeedbackModal{{ $feedback->user_id }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No feedback found</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Add Feedback Modal -->
        <div class="modal fade" id="addFeedbackModal" tabindex="-1" aria-labelledby="addFeedbackModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addFeedbackModalLabel">Add New Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('feedback.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                      <option value="">Select a user</option>
                      @foreach(App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="content" class="form-label">Feedback Message</label>
                    <textarea class="form-control" id="content" name="content" rows="4" placeholder="Enter feedback message..." required maxlength="1000"></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Add Feedback</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Delete Feedback Modals -->
        @foreach($feedbacks as $feedback)
        <div class="modal fade" id="deleteFeedbackModal{{ $feedback->user_id }}" tabindex="-1" aria-labelledby="deleteFeedbackModalLabel{{ $feedback->user_id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteFeedbackModalLabel{{ $feedback->user_id }}">Delete Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this feedback?</p>
                <p><strong>User:</strong> {{ $feedback->user->name ?? 'Unknown User' }}</p>
                <p><strong>Message:</strong> {{ Str::limit($feedback->content, 100) }}</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('feedback.destroy', $feedback->user_id) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <!-- BODY END -->


    </div>

  </body>
</html>
