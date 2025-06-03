<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complaint Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn                const sentimentBadge = `<span class="badge ${badgeClass} sentiment-badge" data-complaint-id="${complaint.id}" data-user-id="${complaint.user_id}" data-sentiment="${sentiment}">${badgeText}</span>`;jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <li><a class="dropdown-item fw-normal me-5" href="{{ route('my.profile', Auth::user()->id ?? '') }}"><i class="bi bi-person me-2 fs-5"></i> My Profile</a></li>
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
              <i class="bi bi-chevron-down ms-auto dropdown-arrow"></i>
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
              <i class="bi bi-chevron-down ms-auto dropdown-arrow"></i>
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
              <i class="bi bi-chevron-down ms-auto dropdown-arrow"></i>
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
            <h1 class="text-center mb-4 display-4 fw-bolder text-primary">COMPLAINT REQUEST</h1>
            <div class="row">
            <div class="col-md-5">
                <div class="card text-center card-hover bg-secondary text-primary h-100">
                <div class="card-body shadow">
                    <h5 class="card-title display-4 fw-bold pt-4">Total Complaints</h5>
                    <p class="card-text display-2 fw-bolder">{{ $complaintCount }}</p>
                </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card bg-secondary text-primary h-100 card-hover">
                    <div class="card-body shadow">
                        <h5 class="card-title text-primary fs-3 lead fw-bold">Types of Complaint</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush m-1">
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Public Disturbance</li>
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Environment & Sanitation Issues</li>
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Vandalism</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush m-1">
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Illegal Parking</li>
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Theft</li>
                                    <li class="list-group-item bg-primary bg-opacity-25 text-primary border border-primary my-1 lead">Illegal Operation of Business</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- complaint table -->
            <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <select class="form-select w-25 border border-primary" id="statusFilter" aria-label="Filter by status">
                <option value="all" selected>All Complaints</option>
                <option value="pending">Pending</option>
                <option value="phase1">Phase 1</option>
                <option value="phase2">Phase 2</option>
                <option value="phase3">Phase 3</option>
                <option value="phase4">Phase 4</option>
                <option value="phase5">Phase 5</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                    <tr>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Name</th>
                    <th scope="col" class="text-primary py-4" style="width: 18%;">Type of Complaint</th>
                    <th scope="col" class="text-primary py-4" style="width: 10%;">Date</th>
                    <th scope="col" class="text-primary py-4" style="width: 10%;">Status</th>
                    <th scope="col" class="text-primary py-4" style="width: 10%;">Sentiment</th>
                    <th scope="col" class="text-primary py-4" style="width: 12%;">Action</th>
                    </tr>
                </thead>
                <tbody id="complaintsTableBody">
                    @foreach($complaints as $complaint)
                    <tr>
                        <td class="py-4">
                            @if($complaint->resident)
                                {{ $complaint->resident->last_name }}, {{ $complaint->resident->first_name }} {{ $complaint->resident->middle_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-4">{{ $complaint->complaint_type }}</td>
                        <td class="py-4">{{ \Carbon\Carbon::parse($complaint->created_at)->format('m / d / y') }}</td>
                        <td class="py-4">{{ $complaint->status }}</td>
                        <td class="py-4">
                            @php
                                $sentiment = strtolower($complaint->sentiment ?? 'neutral');
                                $badgeClass = match($sentiment) {
                                    'positive' => 'bg-success',
                                    'negative' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                $badgeText = ucfirst($sentiment);
                            @endphp
                            <span class="badge {{ $badgeClass }} sentiment-badge" data-complaint-id="{{ $complaint->id }}" 
                                  data-sentiment="{{ $sentiment }}">
                                {{ $badgeText }}
                            </span>
                        </td>
                        <td class="py-4">
                            <a href="{{ route('complaint.edit', $complaint->id) }}" class="btn btn-primary btn-sm px-3 me-2">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="confirmDeleteComplaint({{ $complaint->id }})">Delete</button>
                        </td>
                    </tr>
                    @endforeach
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
                Are you sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary text-light"  data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
            </div>
        </div>
        </div>

      <!-- BODY END -->


    </div>
    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  {{-- Auto-refresh disabled: <script src="{{ asset('js/live-updates.js') }}"></script> --}}
    <script>
        let deleteComplaintId = null;
        let lastUpdateTimestamp = null;
        let autoReloadInterval = null;
        let isPolling = false;
        let isDeleting = false; // Track if a delete operation is in progress

        // Auto-reload configuration
        const AUTO_RELOAD_INTERVAL = 5000; // Check every 5 seconds
        const MAX_RETRY_ATTEMPTS = 3;
        let retryCount = 0;

        // Global functions for table manipulation
        function fetchFilteredComplaints(status) {
            const complaintsTableBody = document.getElementById('complaintsTableBody');
            // Show loading state
            complaintsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>';

            fetch(`/complaints/data?status=${status}`)
                .then(response => response.json())
                .then(data => {
                    updateComplaintsTable(data);
                    retryCount = 0; // Reset retry count on success
                })
                .catch(error => {
                    console.error('Error fetching complaints:', error);
                    retryCount++;
                    if (retryCount < MAX_RETRY_ATTEMPTS) {
                        // Retry after a short delay
                        setTimeout(() => fetchFilteredComplaints(status), 2000);
                    } else {
                        complaintsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error loading complaints. Please refresh the page.</td></tr>';
                    }
                });
        }

        // Check for database updates
        function checkForUpdates() {
            if (isPolling) return; // Prevent multiple concurrent polls
            
            // Don't check for updates if we're in the middle of a delete operation
            if (deleteComplaintId !== null || isDeleting) return;
            
            isPolling = true;
            fetch('/complaints/last-update')
                .then(response => response.json())
                .then(data => {
                    if (lastUpdateTimestamp === null) {
                        // First time loading, just store the timestamp
                        lastUpdateTimestamp = data.last_update;
                    } else if (data.last_update !== lastUpdateTimestamp) {
                        // Database has been updated, reload the table
                        console.log('Database updated, reloading complaints...');
                        lastUpdateTimestamp = data.last_update;
                        
                        // Update complaint count if it changed
                        const countElement = document.querySelector('.card-text.display-2.fw-bolder');
                        if (countElement && data.complaint_count !== undefined) {
                            countElement.textContent = data.complaint_count;
                        }
                        
                        // Reload table with current filter
                        const currentFilter = document.getElementById('statusFilter').value;
                        fetchFilteredComplaints(currentFilter);
                        
                        // Show notification
                        showMessage('Complaints updated automatically', 'info');
                    }
                })
                .catch(error => {
                    console.error('Error checking for updates:', error);
                })
                .finally(() => {
                    isPolling = false;
                });
        }

        // Start auto-reload
        function startAutoReload() {
            if (autoReloadInterval) {
                clearInterval(autoReloadInterval);
            }
            
            // Initial check to set baseline
            checkForUpdates();
            
            // Set up periodic checking
            autoReloadInterval = setInterval(checkForUpdates, AUTO_RELOAD_INTERVAL);
            console.log('Auto-reload started (checking every ' + (AUTO_RELOAD_INTERVAL / 1000) + ' seconds)');
        }

        // Stop auto-reload
        function stopAutoReload() {
            if (autoReloadInterval) {
                clearInterval(autoReloadInterval);
                autoReloadInterval = null;
                console.log('Auto-reload stopped');
            }
        }

        function getSentimentBadge(sentiment) {
            const badgeClass = {
                'positive': 'bg-success',
                'negative': 'bg-danger',
                'neutral': 'bg-secondary'
            }[sentiment || 'neutral'];

            const badgeText = sentiment ? sentiment.charAt(0).toUpperCase() + sentiment.slice(1) : 'Neutral';

            return `<span class="badge ${badgeClass} sentiment-badge">${badgeText}</span>`;
        }

        function updateComplaintsTable(complaints) {
            const complaintsTableBody = document.getElementById('complaintsTableBody');
            if (complaints.length === 0) {
                complaintsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No complaints found</td></tr>';
                return;
            }

            let tableRows = '';
            complaints.forEach(complaint => {
                const date = new Date(complaint.created_at);
                const formattedDate = `${(date.getMonth() + 1).toString().padStart(2, '0')} / ${date.getDate().toString().padStart(2, '0')} / ${date.getFullYear().toString().slice(-2)}`;
                
                // Get name from resident relationship or show N/A
                let displayName = 'N/A';
                if (complaint.resident) {
                    const middleName = complaint.resident.middle_name ? complaint.resident.middle_name : '';
                    displayName = `${complaint.resident.last_name}, ${complaint.resident.first_name} ${middleName}`;
                }

                // Normalize the sentiment value
                const sentiment = (complaint.sentiment || 'neutral').toLowerCase();
                const badgeClass = {
                    'positive': 'bg-success',
                    'negative': 'bg-danger'
                }[sentiment] || 'bg-secondary';
                
                const badgeText = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
                const sentimentBadge = `<span class="badge ${badgeClass} sentiment-badge" data-complaint-id="${complaint.id}" data-sentiment="${sentiment}">${badgeText}</span>`;

                tableRows += `
                    <tr>
                        <td class="py-4">${displayName}</td>
                        <td class="py-4">${complaint.complaint_type || 'N/A'}</td>
                        <td class="py-4">${formattedDate}</td>
                        <td class="py-4">${complaint.status || 'Pending'}</td>
                        <td class="py-4">${sentimentBadge}</td>
                        <td class="py-4">
                            <a href="/complaint/${complaint.id}/edit" class="btn btn-primary btn-sm px-3 me-2">Edit</a>
                            <button class="btn btn-danger btn-sm" onclick="confirmDeleteComplaint(${complaint.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            complaintsTableBody.innerHTML = tableRows;
        }

        // Handle delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // Handle filter change
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                fetchFilteredComplaints(selectedStatus);
            });

            // Handle delete confirmation - use one-time event listener
            confirmDeleteBtn.addEventListener('click', function() {
                if (deleteComplaintId && !isDeleting) {
                    deleteComplaint(deleteComplaintId);
                }
            });

            // Clear deleteComplaintId when modal is hidden
            const deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('hidden.bs.modal', function() {
                if (!isDeleting) {
                    deleteComplaintId = null;
                    console.log('Modal closed, cleared deleteComplaintId');
                }
            });

            // Start auto-reload when page loads
            startAutoReload();
        });

        // Stop auto-reload when page is hidden (tab switched, etc.)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoReload();
            } else {
                startAutoReload();
            }
        });

        // Stop auto-reload when window is about to unload
        window.addEventListener('beforeunload', function() {
            stopAutoReload();
        });

        // Delete confirmation functions
        function confirmDeleteComplaint(id) {
            // Prevent opening multiple modals or confirming already deleted complaints
            if (isDeleting || deletedComplaints.has(id) || deleteComplaintId !== null) {
                console.log('Confirm delete blocked:', { isDeleting, alreadyDeleted: deletedComplaints.has(id), deleteComplaintId });
                return;
            }

            deleteComplaintId = id;
            console.log('Opening delete confirmation for complaint id:', id);
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Track deleted complaints to prevent duplicate deletions
        const deletedComplaints = new Set();

        function deleteComplaint(id) {
            // Multiple safeguards to prevent duplicate deletions
            if (deleteComplaintId === null || isDeleting || deletedComplaints.has(id)) {
                console.log('Delete blocked:', { deleteComplaintId, isDeleting, alreadyDeleted: deletedComplaints.has(id) });
                return;
            }

            // Immediately add to deleted set to prevent duplicate calls
            deletedComplaints.add(id);
            isDeleting = true;

            // Disable the delete button to prevent multiple clicks
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = 'Deleting...';

            // Stop auto-reload during delete operation
            stopAutoReload();

            console.log('Starting delete operation for complaint id:', id);

            fetch(`/complaint/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    console.log('Delete successful for complaint id:', id);
                    
                    // Close modal
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();

                    // Show success message
                    showMessage('Complaint deleted successfully', 'success');

                    // Remove the row from the table immediately for better UX
                    const rowToDelete = document.querySelector(`button[onclick="confirmDeleteComplaint(${id})"]`).closest('tr');
                    if (rowToDelete) {
                        rowToDelete.remove();
                        console.log('Removed row from table for complaint id:', id);
                    }

                    // Update the complaint count
                    const countElement = document.querySelector('.card-text.display-2.fw-bolder');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent);
                        countElement.textContent = Math.max(0, currentCount - 1);
                    }

                } else {
                    // If delete failed, remove from deleted set
                    deletedComplaints.delete(id);
                    throw new Error('Failed to delete complaint');
                }
            })
            .catch(error => {
                console.error('Error deleting complaint:', error);
                // If delete failed, remove from deleted set
                deletedComplaints.delete(id);
                showMessage('Error deleting complaint', 'error');
            })
            .finally(() => {
                // Reset the delete button and clear the ID
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = 'Delete';
                deleteComplaintId = null;
                isDeleting = false;
                
                // Restart auto-reload after a delay
                setTimeout(() => {
                    startAutoReload();
                }, 2000);
                
                console.log('Delete operation completed for complaint id:', id);
            });
        }

        function showMessage(message, type) {
            // Create a simple toast notification
            const toast = document.createElement('div');
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 
                              type === 'info' ? 'alert-info' : 'alert-secondary';
            
            toast.className = `alert ${alertClass} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }

    </script>

  </body>
</html>
