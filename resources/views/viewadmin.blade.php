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

      <div class="container-fluid mt-5">
        <div class="row">
          <!-- Fixed Profile Section -->
          <div class="col-md-4">
            <aside class="position-fixed" style="width: 20%; height: 100vh; margin-left: -12px; border-right: 5px solid #ccc; overflow-y: auto;">
              <div class="h-100 p-4 bg-secondary-subtle">                <div class="text-center">
                  <img src="{{ asset('img/person1.png') }}" alt="Admin Avatar" class="rounded-circle mb-3 mt-2" width="100" height="100">
                  <h5 class="card-title mb-1">{{ Auth::user()->name }}</h5>
                  <p class="text-muted">{{ Auth::user()->email }}</p>
                </div>
                <div class="position-absolute" style="left: 52%; transform: translateX(-50%); bottom: 15%;">
                  <button class="btn btn-primary mb-3" style="width: 150px;" data-bs-toggle="modal" data-bs-target="#editAccountModal">Edit Account</button>
                  <button class="btn btn-secondary" style="width: 150px;" data-bs-toggle="modal" data-bs-target="#logoutModal">Log Out</button>
                </div>
              </div>
            </aside>
          </div>

          <!-- Scrollable Information Section -->
          <div class="col-md-8 my-4" style="margin-left: 25%; max-height: 100vh; overflow-y: auto; overflow-x: auto;">

                <!-- Personal Information Table -->                <table id="personal-information" class="table table-borderless shadow-none">
                  <tbody>
                    <tr class="my-3">
                      <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Admin Information</th>
                    </tr>
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Name</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->name ?? 'N/A' }}</td>
                    </tr>
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Role</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</td>
                    </tr>
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Email</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->email ?? 'N/A' }}</td>
                    </tr>
                    @if(Auth::user()->firstname || Auth::user()->lastname)
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Full Name</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</td>
                    </tr>
                    @endif
                    @if(Auth::user()->contact_number)
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Contact Number</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->contact_number }}</td>
                    </tr>
                    @endif
                    @if(Auth::user()->created_at)
                    <tr class="my-3">
                      <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Member Since</th>
                      <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->created_at->format('F d, Y') }}</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
          </div>
        </div>
      </div>

      <!-- BODY END -->

      <!-- Edit Account Modal -->
      <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fs-4 fw-bold text-primary" id="editAccountModalLabel">Edit Account</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <div class="rounded-circle border" style="width: 100px; height: 100px; overflow: hidden; display: inline-flex; align-items: center; justify-content: center;">
                  <img id="profilePreview" src="{{ asset('img/person1.png') }}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="mt-3">
                  <label for="profilePicture" class="btn btn-outline-primary">Select Photo</label>
                  <input type="file" id="profilePicture" class="d-none" accept="image/*" onchange="previewProfilePicture(event)">
                </div>
              </div>              <form action="{{ route('admin.update', Auth::user()->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}">
                </div>
                <div class="mb-3">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-control" id="role" name="role">
                    <option value="admin" {{ Auth::user()->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ Auth::user()->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
                </div>
                @if(Auth::user()->firstname !== null || Auth::user()->lastname !== null)
                <div class="mb-3">
                  <label for="firstname" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstname" name="firstname" value="{{ Auth::user()->firstname }}">
                </div>
                <div class="mb-3">
                  <label for="lastname" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lastname" name="lastname" value="{{ Auth::user()->lastname }}">
                </div>
                @endif
                <div class="mb-3">
                  <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                  <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 125px;">Cancel</button>
                  <button type="submit" class="btn btn-primary" style="width: 125px;">Save</button>
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 125px;">Cancel</button>
              <button type="button" class="btn btn-primary" style="width: 125px;">Save</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Log Out Modal -->
      <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="logoutModalLabel">Log Out</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to log out?
            </div>            <div class="modal-footer">
              <button type="button" class="btn btn-primary text-light" data-bs-dismiss="modal">Cancel</button>
              <a href="{{ route('logout') }}" class="btn btn-danger">Log Out</a>
            </div>
          </div>
        </div>
      </div>

      <script>
        function previewProfilePicture(event) {
          const input = event.target;
          const reader = new FileReader();
          reader.onload = function () {
            const preview = document.getElementById('profilePreview');
            preview.src = reader.result;
          };
          if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
          }
        }
      </script>

    </div>

  </body>
</html>