<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css', 'resources/js/script.js'])
  </head>

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
              <span class="position-absolute top-0 start-100 translate-middle-x badge rounded-pill bg-danger">
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
            <li><a class="dropdown-item fw-normal me-5" href="{{ route('my.profile') }}"><i class="bi bi-person me-2 fs-5"></i> My Profile</a></li>
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
          <!-- Messages link -->
          {{-- <li class="sidebar-item">
            <a href="{{ route('messages') }}" class="sidebar-link">
              <i class="bi bi-chat-left-text-fill fs-4"></i>              
                <span class="fs-5 lead text-secondary">Messages</span>
            </a>
          </li> --}}
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
      <div class="container mt-5">
        <h1 class="text-center mt-4 mb-5 display-4 fw-bolder text-primary">Admin</h1>
        <div class="">
          <div>
            <div class="d-flex justify-content-between mb-3">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-plus-circle me-2"></i>Add Admin
              </button>
              <input type="text" class="form-control w-25" placeholder="Search Admin" id="searchInput" onkeyup="searchAdmins()">
            </div>
            <div class="table-responsive">
              <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                  <tr>
                    <th scope="col" class="text-primary py-4" style="width: 25%;">Name</th>
                    <th scope="col" class="text-primary py-4" style="width: 25%;">Email</th>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Role</th>
                    <th scope="col" class="text-primary py-4" style="width: 30%;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($users as $user)
                    <tr>
                      <td class="py-4">{{ $user->name }}</td>
                      <td class="py-4">{{ $user->email }}</td>
                      <td class="py-4">
                        @if($user->role === 'super_admin')
                          <span class="badge bg-danger fs-6">
                            <i class="bi bi-star-fill me-1"></i>Super Admin
                          </span>
                        @elseif($user->role === 'admin')
                          <span class="badge bg-primary fs-6">
                            <i class="bi bi-person-badge me-1"></i>Admin
                          </span>
                        @else
                          <span class="badge bg-secondary fs-6">
                            <i class="bi bi-person me-1"></i>{{ ucfirst($user->role ?? 'User') }}
                          </span>
                        @endif
                      </td>
                      <td class="py-4">
                        <button type="button" class="btn btn-primary btn-sm px-3 me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editAdminModal"
                                data-admin-id="{{ $user->id }}"
                                data-admin-name="{{ $user->name }}"
                                data-admin-email="{{ $user->email }}"
                                data-admin-role="{{ $user->role }}">
                          Edit
                        </button>
                        @if(Auth::user()->id !== $user->id)
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                onclick="setDeleteAction('{{ route('admin.destroy', $user->id) }}', '{{ $user->name }}')">
                          Delete
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary btn-sm" disabled>
                          Cannot delete yourself
                        </button>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">No admins found.</td>
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
              Are you sure you want to delete the admin account for <span id="adminName" class="fw-bold text-danger"></span>?
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
      <!-- Add Admin Modal -->
      <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="addAdminModalLabel">Add New Admin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.store') }}" method="POST">
              @csrf
              <div class="modal-body">
                <div class="mb-3">
                  <label for="addName" class="form-label fw-bold">Full Name</label>
                  <input type="text" class="form-control" id="addName" name="name" required>
                </div>
                <div class="mb-3">
                  <label for="addEmail" class="form-label fw-bold">Email Address</label>
                  <input type="email" class="form-control" id="addEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="addPassword" class="form-label fw-bold">Password</label>
                  <input type="password" class="form-control" id="addPassword" name="password" required>
                </div>
                <div class="mb-3">
                  <label for="addRole" class="form-label fw-bold">Role</label>
                  <select class="form-select" id="addRole" name="role" required>
                    <option value="">Select Role</option>
                    <option value="user">Regular User</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Admin</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Admin Modal -->
      <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="editAdminModalLabel">Edit Admin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAdminForm" method="POST">
              @csrf
              @method('PUT')
              <div class="modal-body">
                <div class="mb-3">
                  <label for="editAdminName" class="form-label fw-bold">Full Name</label>
                  <input type="text" class="form-control" id="editAdminName" name="name" required>
                </div>
                <div class="mb-3">
                  <label for="editAdminEmail" class="form-label fw-bold">Email Address</label>
                  <input type="email" class="form-control" id="editAdminEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="editAdminRole" class="form-label fw-bold">Role</label>
                  <select class="form-select" id="editAdminRole" name="role" required>
                    <option value="user">Regular User</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Admin</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1055;" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('js/live-updates.js') }}"></script>
      <script>
        function setDeleteAction(actionUrl, adminName) {
          document.getElementById('deleteForm').action = actionUrl;
          document.getElementById('adminName').textContent = adminName;
        }
        
        // Auto-hide success messages after 5 seconds
        setTimeout(function() {
          const alert = document.querySelector('.alert-success');
          if (alert) {
            alert.remove();
          }
        }, 5000);

        // Search functionality
        function searchAdmins() {
          const input = document.getElementById('searchInput');
          const filter = input.value.toLowerCase();
          const table = document.querySelector('table tbody');
          const rows = table.querySelectorAll('tr');

          rows.forEach(row => {
            if (row.cells.length > 1) { // Skip empty state row
              const name = row.cells[0].textContent.toLowerCase();
              const email = row.cells[1].textContent.toLowerCase();
              const role = row.cells[2].textContent.toLowerCase();
              
              if (name.includes(filter) || email.includes(filter) || role.includes(filter)) {
                row.style.display = '';
              } else {
                row.style.display = 'none';
              }
            }
          });
        }

        // Edit Admin Modal
        document.addEventListener('DOMContentLoaded', function() {
            const editAdminModal = document.getElementById('editAdminModal');
            editAdminModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const adminId = button.getAttribute('data-admin-id');
                const adminName = button.getAttribute('data-admin-name');
                const adminEmail = button.getAttribute('data-admin-email');
                const adminRole = button.getAttribute('data-admin-role');
                
                const form = document.getElementById('editAdminForm');
                form.action = '/admin/' + adminId;
                
                document.getElementById('editAdminName').value = adminName;
                document.getElementById('editAdminEmail').value = adminEmail;
                document.getElementById('editAdminRole').value = adminRole;
            });
        });
      </script>

    </div>

  </body>
</html>
