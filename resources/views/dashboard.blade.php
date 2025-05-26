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
       <!-- CARD -->
      <div class="container pt-4">
        <div class="row mb-4">
          <!-- Registered Users -->
          <div class="col-lg-3 col-md-6 mb-3 mt-4">
              <div class="d-flex align-items-center bg-white rounded-pill shadow p-3 card-hover">
                  <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                      <i class="bi bi-person-fill fs-2"></i>
                  </div>
                  <div class="ms-3">
                      <h5 class="mb-0">Registered Users</h5>
                      <span class="fs-4 fw-bold text-success ps-5" id="totalResidents">{{ $totalResidents }}</span>
                  </div>
              </div>
          </div>

          <!-- Pending Document -->
          <div class="col-lg-3 col-md-6 mb-3 mt-4">
              <div class="d-flex align-items-center bg-white rounded-pill shadow p-3 card-hover">
                  <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                      <i class="bi bi-file-earmark-text-fill fs-2"></i>
                  </div>
                  <div class="ms-3">
                      <h5 class="mb-0">Pending Document</h5>
                      <span class="fs-4 fw-bold text-primary ps-5" id="totalDocuments">{{ $totalDocuments }}</span>
                  </div>
              </div>
          </div>

          <!-- Pending Complaint -->
          <div class="col-lg-3 col-md-6 mb-3 mt-4">
              <div class="d-flex align-items-center bg-white rounded-pill shadow p-3 card-hover">
                  <div class="bg-warning text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                      <i class="bi bi-flag-fill fs-2"></i>
                  </div>
                  <div class="ms-3">
                      <h5 class="mb-0">Pending Complaint</h5>
                      <span class="fs-4 fw-bold text-danger ps-5" id="totalComplaints">{{ $totalComplaints }}</span>
                  </div>
              </div>
          </div>

          <!-- Feedback & Report -->
          <div class="col-lg-3 col-md-6 mb-3 mt-4">
              <div class="d-flex align-items-center bg-white rounded-pill shadow p-3 card-hover">
                  <div class="bg-danger text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                      <i class="bi bi-blockquote-right fs-2"></i>
                  </div>
                  <div class="ms-3">
                      <h5 class="mb-0">Feedback & Report</h5>
                       <span class="fs-4 fw-bold text-info ps-5" id="totalFeedbacks">{{ $totalFeedbacks }}</span>
                  </div>
              </div>
          </div>
        </div>
      </div>
        <!-- CARD END -->

        <!-- TABLE DOCU&COMPLAINT-->
       <div class="container">
  <h5 class="mb-3 fw-bold fs-4 text-primary">Recent Document and Complaint Request</h5>
  <div class="table-responsive">
    <table class="table table-hover rounded-4 overflow-hidden shadow">
      <thead class="table-info">
        <tr>
          <th scope="col" class="text-primary py-4" style="width: 20%;">Category</th>
          <th scope="col" class="text-primary py-4" style="width: 25%;">Name</th>
          <th scope="col" class="text-primary py-4" style="width: 40%;">Type of Document/Complaint</th>
          <th scope="col" class="text-primary py-4" style="width: 15%;">Date</th>
        </tr>
      </thead>
      <tbody id="recentActivitiesBody">
        @foreach($recentActivities as $activity)
        <tr>
          <td class="py-4">{{ $activity->category }}</td>
          <td class="py-4">{{ $activity->lastname }}, {{ $activity->firstname }}</td>
          <td class="py-4">{{ $activity->type }}</td>
          <td class="py-4">{{ \Carbon\Carbon::parse($activity->timestamp)->format('m / d / y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<!-- TABLE END -->


      <div class="container mt-4">
  <div class="row">
    <!-- News and Announcement Table -->
    <div class="col-lg-8 mb-4">
      <h5 class="mb-3 fw-bold fs-4 text-primary">Recent News and Announcement</h5>
      <div class="table-responsive">
        <table class="table table-hover rounded-4 overflow-hidden shadow">
          <thead class="table-info">
            <tr>
              <th scope="col" class="py-4 text-primary" style="width: 20%;">Category</th>
              <th scope="col" class="py-4 text-primary" style="width: 65%;">Title</th>
              <th scope="col" class="py-4 text-primary" style="width: 15%;">Date</th>
            </tr>
          </thead>
          <tbody id="recentUpdatesBody">
            @foreach($recentUpdates as $item)
            <tr>
              <td class="py-4">{{ $item->category }}</td>
              <td class="py-4">{{ $item->type }}</td>
              <td class="py-4">{{ \Carbon\Carbon::parse($item->timestamp)->format('m / d / y') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Admin Table -->
    <div class="col-lg-4 mb-4">
      <h5 class="mb-3 fw-bold fs-4 text-primary">Admins</h5>
      <div class="table-responsive">
        <table class="table table-hover overflow-hidden shadow">
          <thead class="table-info">
            <tr>
              <th scope="col" class="text-primary py-4">Name</th>
              <th scope="col" class="text-primary py-4">Position</th>
            </tr>
          </thead>
          <tbody id="adminsTableBody">
            @foreach($admins as $admin)
            <tr>
              <td class="py-4">{{ $admin->name }}</td>
              <td class="py-4">{{ $admin->position }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


        <!-- BODY END -->
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Dashboard auto-refresh configuration
const REFRESH_INTERVAL = 5000; // 5 seconds
let isUpdating = false;

// Function to update dashboard statistics
function updateDashboardStats() {
    // Prevent multiple simultaneous requests
    if (isUpdating) return;

    isUpdating = true;

    // Add visual indicator - rotate refresh icon
    const refreshIcon = document.getElementById('refreshIcon');
    if (refreshIcon) {
        refreshIcon.style.animation = 'spin 1s linear infinite';
    }

    console.log('Updating dashboard data...');

    fetch('{{ route("dashboard.refresh") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update Statistics Cards
            document.getElementById('totalResidents').textContent = data.totalResidents || '0';
            document.getElementById('totalDocuments').textContent = data.totalDocuments || '0';
            document.getElementById('totalComplaints').textContent = data.totalComplaints || '0';
            document.getElementById('totalFeedbacks').textContent = data.totalFeedbacks || '0';

            // Update Recent Activities Table
            let activitiesHtml = '';
            if (data.recentActivities && data.recentActivities.length > 0) {
                data.recentActivities.forEach(activity => {
                    // Format date to match Laravel's format (m / d / y)
                    const date = new Date(activity.timestamp);
                    const formattedDate = `${(date.getMonth() + 1).toString().padStart(2, '0')} / ${date.getDate().toString().padStart(2, '0')} / ${date.getFullYear().toString().substr(-2)}`;

                    activitiesHtml += `
                        <tr>
                            <td class="py-4">${activity.category}</td>
                            <td class="py-4">${activity.lastname}, ${activity.firstname}</td>
                            <td class="py-4">${activity.type}</td>
                            <td class="py-4">${formattedDate}</td>
                        </tr>
                    `;
                });
            } else {
                activitiesHtml = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No recent activities</td>
                    </tr>
                `;
            }
            document.getElementById('recentActivitiesBody').innerHTML = activitiesHtml;

            // Update Recent Updates Table
            let updatesHtml = '';
            if (data.recentUpdates && data.recentUpdates.length > 0) {
                data.recentUpdates.forEach(item => {
                    // Format date to match Laravel's format (m / d / y)
                    const date = new Date(item.timestamp);
                    const formattedDate = `${(date.getMonth() + 1).toString().padStart(2, '0')} / ${date.getDate().toString().padStart(2, '0')} / ${date.getFullYear().toString().substr(-2)}`;

                    updatesHtml += `
                        <tr>
                            <td class="py-4">${item.category}</td>
                            <td class="py-4">${item.type}</td>
                            <td class="py-4">${formattedDate}</td>
                        </tr>
                    `;
                });
            } else {
                updatesHtml = `
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">No recent updates</td>
                    </tr>
                `;
            }
            document.getElementById('recentUpdatesBody').innerHTML = updatesHtml;

            // Update Admins Table
            let adminsHtml = '';
            if (data.admins && data.admins.length > 0) {
                data.admins.forEach(admin => {
                    adminsHtml += `
                        <tr>
                            <td class="py-4">${admin.name}</td>
                            <td class="py-4">${admin.position}</td>
                        </tr>
                    `;
                });
            } else {
                adminsHtml = `
                    <tr>
                        <td colspan="2" class="text-center py-4 text-muted">No admins found</td>
                    </tr>
                `;
            }
            document.getElementById('adminsTableBody').innerHTML = adminsHtml;
            document.getElementById('adminsTableBody').innerHTML = adminsHtml;
        })
        .catch(error => {
            console.error('Error updating dashboard:', error);
            // Show user-friendly error message
            const errorMessage = 'Unable to update dashboard data. Please refresh the page.';
            document.getElementById('totalResidents').textContent = 'Error';
            document.getElementById('totalDocuments').textContent = 'Error';
            document.getElementById('totalComplaints').textContent = 'Error';
            document.getElementById('totalFeedbacks').textContent = 'Error';
        })
        .finally(() => {
            isUpdating = false;
            // Stop refresh icon animation
            const refreshIcon = document.getElementById('refreshIcon');
            if (refreshIcon) {
                refreshIcon.style.animation = '';
            }
        });
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initial update
    updateDashboardStats();

    // Set up periodic updates
    setInterval(updateDashboardStats, REFRESH_INTERVAL);

    console.log(`Dashboard auto-refresh started (every ${REFRESH_INTERVAL/1000} seconds)`);
});

// Optional: Add manual refresh button functionality
function manualRefresh() {
    updateDashboardStats();
}
</script>
  </body>
</html>
