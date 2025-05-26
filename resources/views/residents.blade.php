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
        <h1 class="text-center mt-4 mb-5 display-4 fw-bolder text-primary">Residents</h1>
        <div class="">
          <div>
            <div class="d-flex justify-content-end mb-3">
              <input type="text" class="form-control w-25" placeholder="Search Residents" id="searchInput" onkeyup="searchResidents()">
            </div>
            <div class="table-responsive">
              <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                  <tr>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Name</th>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Username</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Contact Number</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Verification</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($residents as $resident)
                    <tr>
                      <td class="py-4">{{ isset($resident->lastname) && isset($resident->firstname) ? $resident->lastname . ', ' . $resident->firstname : ($resident->name ?? 'N/A') }}</td>
                      <td class="py-4">{{ isset($resident->username) ? '@' . $resident->username : ($resident->email ?? 'N/A') }}</td>
                      <td class="py-4">{{ $resident->contact_number ?: ($resident->phone ?: ($resident->mobile ?: ($resident->phone_number ?: 'N/A'))) }}</td>
                      <td class="py-4 {{ ($resident->verified ?? 0) ? 'text-success' : 'text-danger' }}">
                        {{ ($resident->verified ?? 0) ? 'Verified' : 'Not Verified' }}
                      </td>
                      <td class="py-4">
                        <a href="{{ route('residents.view', $resident->user_id) }}" class="btn btn-primary btn-sm px-3 me-2">View</a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                onclick="setDeleteAction('{{ route('residents.destroy', $resident->user_id) }}', '{{ isset($resident->firstname) ? $resident->firstname . ' ' . ($resident->lastname ?? '') : ($resident->name ?? 'this resident') }}')">
                          Delete
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">No residents found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- BODY END -->

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="deleteModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete the resident account for <span id="residentName" class="fw-bold text-danger"></span>?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary text-light" data-bs-dismiss="modal">Cancel</button>
              <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Delete Confirmation Modal End -->

      <!-- Success Message -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1055;" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <script>
        function setDeleteAction(actionUrl, residentName) {
          document.getElementById('deleteForm').action = actionUrl;
          document.getElementById('residentName').textContent = residentName;
        }
        
        // Auto-hide success messages after 5 seconds
        setTimeout(function() {
          const alert = document.querySelector('.alert-success');
          if (alert) {
            alert.remove();
          }
        }, 5000);

        // Search functionality
        function searchResidents() {
          const input = document.getElementById('searchInput');
          const filter = input.value.toLowerCase();
          const table = document.querySelector('table tbody');
          const rows = table.querySelectorAll('tr');

          rows.forEach(row => {
            if (row.cells.length > 1) { // Skip empty state row
              const name = row.cells[0].textContent.toLowerCase();
              const username = row.cells[1].textContent.toLowerCase();
              const contact = row.cells[2].textContent.toLowerCase();
              
              if (name.includes(filter) || username.includes(filter) || contact.includes(filter)) {
                row.style.display = '';
              } else {
                row.style.display = 'none';
              }
            }
          });
        }
      </script>

    </div>

  </body>
</html>
