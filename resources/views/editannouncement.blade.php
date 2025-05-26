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


      <!-- SIDEBAR ENDS -->

      <!-- BODY -->

      <div class="container mt-5">
      <h1 class="text-center mt-4 mb-5 display-4 fw-bolder text-primary">Edit Announcement</h1>      <div>
        <div>
          <form method="POST" action="{{ route('announcements.update', $announcement->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label for="title" class="form-label fw-bold fs-5">Announcement Title:</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Enter announcement title" value="{{ $announcement->title }}" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label fw-bold fs-5">Description:</label>
              <textarea class="form-control" id="description" name="description" rows="8" placeholder="Enter description" required>{{ $announcement->content }}</textarea>
            </div>
            <div class="row pb-3">
              <div class="col-md-6">
                <label for="announcement_date" class="form-label fw-bold fs-5">Announcement Date:</label>
                <input type="date" class="form-control" id="announcement_date" name="announcement_date" value="{{ $announcement->createdAt }}" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="picture" class="form-label fw-bold fs-5">Picture Attachment:</label>
                <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                <div class="mt-3">
                  @if($announcement->image)
                    <img id="picturePreview" src="{{ asset('uploads/announcements/' . $announcement->image) }}" alt="Preview" style="width: 75px; height: 75px; object-fit: cover;">
                  @else
                    <img id="picturePreview" src="#" alt="Preview" style="display: none; width: 75px; height: 75px; object-fit: cover;">
                  @endif
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center mt-5">
              <a href="{{ route('announcements') }}" class="btn btn-secondary me-3" style="width: 15%;">Cancel</a>
              <button type="submit" class="btn btn-primary" style="width: 15%;">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

      <!-- BODY END -->      <script>
        document.getElementById('picture').addEventListener('change', function(event) {
          const file = event.target.files[0];
          const preview = document.getElementById('picturePreview');
          if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
              preview.src = e.target.result;
              preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            preview.style.display = 'none';
          }
        });
      </script>

    </div>

  </body>
</html>