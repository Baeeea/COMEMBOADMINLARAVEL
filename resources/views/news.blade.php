<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>News</title>
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
      <div class="container mt-5">
        <!-- Success and Error Messages -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <h1 class="text-center mt-4 mb-5 display-4 fw-bolder text-primary">News</h1>
        <div class="">
          <div>
            <div class="d-flex justify-content-end mb-3">
              <button class="btn btn-primary px-5" data-bs-toggle="modal" data-bs-target="#addNewsModal">Add News</button>
            </div>
            <div class="table-responsive">
              <table class="table table-hover rounded-4 overflow-hidden shadow">
                <thead class="table-info">
                  <tr>
                    <th scope="col" class="text-primary py-4" style="width: 60%;">News Title</th>
                    <th scope="col" class="text-primary py-4" style="width: 25%;">Date</th>
                    <th scope="col" class="text-primary py-4" style="width: 15%;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($news as $item)
                    <tr>
                      <td class="py-4">{{ $item->Title }}</td>
                      <td class="py-4">{{ \Carbon\Carbon::parse($item->createdAt)->format('m / d / Y') }}</td>
                      <td class="py-4">
                        <a href="{{ route('news.edit', $item->id) }}" class="btn btn-primary btn-sm px-3 me-2">Edit</a>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                onclick="setDeleteForm('{{ route('news.delete', $item->id) }}')">Delete</button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center py-4">No news found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Add News Modal -->
      <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-lg modal-xl">
          <div class="modal-content">
            <div class="modal-header justify-content-center">
              <h5 class="modal-title fw-bold fs-1 text-primary text-center" id="addNewsModalLabel">Create News</h5>
              <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" id="addNewsForm">
                @csrf
                <div class="mb-3">
                  <label for="newsTitle" class="form-label fw-bold fs-5">News Title:</label>
                  <input type="text" class="form-control" id="newsTitle" name="Title" placeholder="Enter news title" required>
                </div>
                <div class="mb-3">
                  <label for="newsDescription" class="form-label fw-bold fs-5">Description:</label>
                  <textarea class="form-control" id="newsDescription" name="content" rows="8" placeholder="Enter description" required></textarea>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="newsDate" class="form-label fw-bold fs-5">News Date:</label>
                    <input type="date" class="form-control" id="newsDate" name="createdAt" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="pictureAttachment" class="form-label fw-bold fs-5">Picture Attachment:</label>
                    <input type="file" class="form-control" id="pictureAttachment" name="image" accept="image/*">
                    <div class="mt-3">
                      <img id="picturePreview" src="#" alt="Preview" style="display: none; width: 75px; height: 75px; object-fit: cover;">
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-secondary" style="width: 15%;" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" form="addNewsForm" class="btn btn-primary" style="width: 15%;">Save</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Add News Modal End -->

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="deleteModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete this news item?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary text-light" data-bs-dismiss="modal">Cancel</button>
              <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Delete Confirmation Modal End -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('js/live-updates.js') }}"></script>
      <script>
        function setDeleteForm(actionUrl) {
          document.getElementById('deleteForm').action = actionUrl;
        }

        document.getElementById('pictureAttachment').addEventListener('change', function(event) {
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
      <!-- BODY END -->

    </div>
    
    <!-- Dynamic Dropdown Functionality Script -->
    <script>
      console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Loaded' : 'Not loaded');
      
      document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const dropdownToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
        
        console.log('DOM loaded, found dropdown toggles:', dropdownToggles.length);
        
        function updateDropdownBehavior() {
          const isExpanded = sidebar.classList.contains('expand');
          
          dropdownToggles.forEach(toggle => {
            const targetId = toggle.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            
            if (isExpanded) {
              // When expanded: enable Bootstrap collapse
              toggle.setAttribute('data-bs-toggle', 'collapse');
              // Remove any hover event listeners
              toggle.onclick = null;
            } else {
              // When collapsed: disable Bootstrap collapse, use hover
              toggle.removeAttribute('data-bs-toggle');
              // Prevent default click behavior when collapsed
              toggle.onclick = function(e) {
                e.preventDefault();
                return false;
              };
            }
          });
          
          console.log('Dropdown behavior updated. Sidebar expanded:', isExpanded);
        }
        
        // Initial setup
        updateDropdownBehavior();
        
        // Listen for sidebar toggle changes
        const toggleBtn = document.getElementById('toggleSidebar');
        if (toggleBtn) {
          toggleBtn.addEventListener('click', function() {
            // Wait for the toggle animation to complete
            setTimeout(updateDropdownBehavior, 100);
          });
        }
        
        // Also listen for direct sidebar class changes
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
              updateDropdownBehavior();
            }
          });
        });
        
        observer.observe(sidebar, {
          attributes: true,
          attributeFilter: ['class']
        });
      });
    </script>
    
    <!-- Live Updates Script -->
    <script src="{{ asset('js/live-updates.js') }}"></script>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  </body>
</html>
