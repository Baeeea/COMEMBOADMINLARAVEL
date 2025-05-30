<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document Request</title>
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

      <main class="main-content">
        <div class="container mt-5 pt-5">
            <h1 class="text-center mb-4 display-4 fw-bolder text-primary">DOCUMENT REQUEST</h1>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="row">
            <div class="col-md-6">
                <div class="card text-center card-hover bg-secondary text-primary h-100">
                <div class="card-body shadow">
                    <h5 class="card-title display-4 fw-bold pt-4">Total No. of Request</h5>
                    <p class="card-text display-2 fw-bolder">{{ $totalRequests }}</p>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-secondary text-primary h-100 card-hover">
                <div class="card-body shadow ">
                    <h5 class="card-title text-primary fs-3 lead fw-bold">Types of Document</h5>
                    <ul class="list-group list-group-flush m-1">
                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary m-1 lead">Barangay Residency</li>
                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary m-1 lead">Barangay Clearance for Renovation/Extension</li>
                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary m-1 lead">Barangay Business Clearance</li>
                    </ul>
                </div>
                </div>
            </div>
            </div>

            <!-- document table -->
            <div class="container mt-4">
            <select id="statusFilter" class="form-select mb-3" style="width: 200px;">
        <option value="all">All Requests</option>
        <option value="pending">Pending</option>
        <option value="inprocess">In Process</option>
        <option value="completed">Completed</option>
        <option value="rejected">Rejected</option>
</select>

            <div class="table-responsive">
                <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                    <tr>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Name</th>
                    <th scope="col" class="text-primary py-4" style="width: 25%;">Type of Document</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Date</th>
                    <th scope="col" class="text-primary py-4" style="width: 20%;">Status</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Action</th>
                    </tr>
                </thead>
            <tbody>
                <!-- Table content will be loaded by JavaScript -->
            </tbody>


                </table>
            </div>
            </div>
            <!-- document table end -->
        </div>
        </main>
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
                Are you sure you want to delete this document request? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary text-light" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            </div>
        </div>
        </div>

      <!-- BODY END -->


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/live-updates.js') }}"></script>
    <!-- JavaScript for document request filtering -->
    <script>
    // Function to handle delete confirmation
    function confirmDeleteRequest(id) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/documentrequest/${id}/delete`;
        deleteModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        async function updateTable(status = 'all') {
            try {
                const response = await fetch(`{{ route('documentrequests.data') }}?status=${status}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                // Log the first record to help debug available fields
                if (data.length > 0) {
                    console.log("First record fields:", Object.keys(data[0]));
                    console.log("First record data:", data[0]);
                }

                let tbody = '';
                data.forEach(request => {
                    const formattedDate = new Date(request.timestamp).toLocaleDateString('en-US', {
                        month: '2-digit',
                        day: '2-digit',
                        year: '2-digit'
                    });

                    // Determine the ID field - could be 'id', 'documentrequest_id', 'request_id', etc.
                    const idField = request.id || request.document_id || request.request_id || request.documentrequest_id || Object.keys(request)[0];

                    tbody += `
                        <tr>
                            <td class="py-4">${request.lastname}, ${request.firstname} ${request.middle_name}</td>
                            <td class="py-4">${request.document_type}</td>
                            <td class="py-4">${formattedDate}</td>
                            <td class="py-4">${request.status}</td>
                            <td class="py-4">
                                <a href="/documentrequest/${idField}/edit" class="btn btn-primary btn-sm px-3 me-2">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDeleteRequest(${idField})">Delete</button>
                            </td>
                        </tr>
                    `;
                });

                document.querySelector('tbody').innerHTML = tbody;
            } catch (error) {
                console.error('Error fetching data:', error);
                document.querySelector('tbody').innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load data</td></tr>`;
            }
        }

        // Fetch all on page load
        updateTable();

        // Listen for dropdown changes
        document.getElementById('statusFilter').addEventListener('change', function() {
            updateTable(this.value);
        });
    });
    </script>
  </body>
</html>
