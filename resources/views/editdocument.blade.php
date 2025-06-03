<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css', 'resources/js/script.js'])
    <script src="{{ asset('js/blob-image-handler.js') }}"></script>
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
        <div class="card border-0">
          <div class="card border-0">
            <h5 class="my-3 fs-1 fw-bold text-primary">EDIT DOCUMENT REQUEST</h5>
          </div>

          <!-- Success Message -->
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Error Message -->
          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('documentrequest.update', ['id' => $document->id]) }}" method="POST" enctype="multipart/form-data" id="updateForm">
@csrf
@method('PUT')
<!-- Requester Information Table -->
<table class="table table-borderless shadow-none">
  <tbody>
    <tr>
      <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Requester Information</th>
    </tr>
    <tr>
      <th style="width: 20%;">Last Name</th>
      <td style="width: 80%;">{{ $document->lastname }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">First Name</th>
      <td style="width: 80%;">{{ $document->firstname }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Middle Name</th>
      <td style="width: 80%;">{{ $document->middle_name }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Birthdate</th>
      <td style="width: 80%;">{{ $document->birthdate ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Age</th>
      <td style="width: 80%;">{{ $document->age ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Years of Residency</th>
      <td style="width: 80%;">{{ $document->years_residency ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Contact Number</th>
      <td style="width: 80%;">{{ $document->contact_number ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Address</th>
      <td style="width: 80%;">{{ $document->home_address ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Civil Status</th>
      <td style="width: 80%;">{{ $document->civil_status ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Local Employment</th>
      <td style="width: 80%;">{{ $document->local_employment ?? '' }}</td>
    </tr>
  </tbody>
</table>

<!-- Repeat the same "display only" approach for business/renovation/residency sections -->

<!-- Document Information Table -->
<table class="table table-borderless shadow-none">
  <tbody>
    <tr>
      <th colspan="2" class="text-primary fw-bold fs-3 my-5 pt-3 border-bottom">Document Information</th>
    </tr>
    <tr>
      <th style="width: 20%;">Type of Document</th>
      <td style="width: 80%;">{{ $document->document_type }}</td>
    </tr>
    <tr>
      <th style="width: 20%;">Purpose</th>
      <td style="width: 80%;">{{ $document->purpose ?? '' }}</td>
    </tr>
    <tr>
      <th style="width: 20%;" class="text-danger">Status</th>
      <td style="width: 80%;">
        <select class="form-select" name="status" style="width: 25%;">
          <option value="pending" {{ $document->status == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="inprocess" {{ $document->status == 'inprocess' ? 'selected' : '' }}>In Process</option>
          <option value="completed" {{ $document->status == 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="rejected" {{ $document->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </td>
    </tr>
    <tr>
      <th style="width: 20%;">Status Explanation</th>
      <td style="width: 80%;">{{ $document->status_explanation ?? '' }}</td>
    </tr>
    <!-- ID Images from residents table -->
    <tr>
      <th style="width: 20%;">Resident ID (Front)</th>
      <td style="width: 80%;">
        @if($document->id_front)
          <div class="mb-2">
            <img src="{{ route('residents.id.image', ['id' => $document->user_id, 'type' => 'front']) }}" 
                 alt="Resident ID Front" 
                 style="max-width: 200px;" 
                 class="img-thumbnail">
          </div>
        @else
          <div class="text-muted">No ID Front image available</div>
        @endif
      </td>
    </tr>
    <tr>
      <th style="width: 20%;">Resident ID (Back)</th>
      <td style="width: 80%;">
        @if($document->id_back)
          <div class="mb-2">
            <img src="{{ route('residents.id.image', ['id' => $document->user_id, 'type' => 'back']) }}" 
                 alt="Resident ID Back" 
                 style="max-width: 200px;" 
                 class="img-thumbnail">
          </div>
        @else
          <div class="text-muted">No ID Back image available</div>
        @endif
      </td>
    </tr>
    
    <!-- Valid ID Images from documentrequest table -->
    <tr>
      <th style="width: 20%;">Valid ID (Front)</th>
      <td style="width: 80%;">
        @if($document->validIDFront)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/valid-id-front" alt="Valid ID Front" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @else
          <div class="text-muted">No Valid ID Front image available</div>
        @endif
      </td>
    </tr>
    <tr>
      <th style="width: 20%;">Valid ID (Back)</th>
      <td style="width: 80%;">
        @if($document->validIDBack)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/valid-id-back" alt="Valid ID Back" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @endif
      </td>
    </tr>
    
    <!-- Business Clearance Photos from documentrequest table -->
    @if($document->document_type === 'Barangay Business Clearance')
    <tr>
      <th style="width: 20%;">Photo of Store</th>
      <td style="width: 80%;">
        @if($document->photo_store)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/photo-store" alt="Photo of Store" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @endif
      </td>
    </tr>
    @endif
    
    <!-- Renovation Photos from documentrequest table -->
    @if($document->document_type === 'Barangay Clearance for Renovation/Extension')
    <tr>
      <th style="width: 20%;">Photo of Current House</th>
      <td style="width: 80%;">
        @if($document->photo_current_house)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/photo-current-house" alt="Photo of Current House" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @endif
      </td>
    </tr>
    <tr>
      <th style="width: 20%;">Photo of Renovation Plans</th>
      <td style="width: 80%;">
        @if($document->photo_renovation)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/photo-renovation" alt="Photo of Renovation Plans" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @endif
      </td>
    </tr>
    <tr>
      <th style="width: 20%;">Photo Proof of Renovation</th>
      <td style="width: 80%;">
        @if($document->photo_proof)
          <div class="mb-2">
            <img src="/api/documentrequest/{{ $document->id }}/photo-proof" alt="Photo Proof of Renovation" style="max-width: 200px;" class="img-thumbnail">
          </div>
        @endif
      </td>
    </tr>
    @endif
    
    <!-- Additional Images from documentrequest table -->
    @if($document->image)
    <tr>
      <th style="width: 20%;">Additional Image 1</th>
      <td style="width: 80%;">
        <div class="mb-2">
          <img src="/api/documentrequest/{{ $document->id }}/image" alt="Additional Image 1" style="max-width: 200px;" class="img-thumbnail">
        </div>
      </td>
    </tr>
    @endif
    
    @if($document->image2)
    <tr>
      <th style="width: 20%;">Additional Image 2</th>
      <td style="width: 80%;">
        <div class="mb-2">
          <img src="/api/documentrequest/{{ $document->id }}/image2" alt="Additional Image 2" style="max-width: 200px;" class="img-thumbnail">
        </div>
      </td>
    </tr>
    @endif
    
    @if($document->image3)
    <tr>
      <th style="width: 20%;">Additional Image 3</th>
      <td style="width: 80%;">
        <div class="mb-2">
          <img src="/api/documentrequest/{{ $document->id }}/image3" alt="Additional Image 3" style="max-width: 200px;" class="img-thumbnail">
        </div>
      </td>
    </tr>
    @endif
  </tbody>
</table>
<input type="hidden" name="id" value="{{ $document->id }}">
<div class="text-center my-5">
  <a href="{{ route('documentrequest') }}" class="btn btn-secondary me-2" style="width: 15%;">Cancel</a>
  <button type="submit" class="btn btn-primary" style="width: 15%;">Save Changes</button>
</div>
</form>
            
            <script>
                function confirmDelete() {
                    if (confirm('Are you sure you want to delete this document request? This action cannot be undone.')) {
                        document.getElementById('deleteForm').submit();
                    }
                }

                function toggleDocumentFields() {
                    const documentType = document.getElementById('document_type').value;
                    const businessSection = document.getElementById('business_section');
                    const renovationSection = document.getElementById('renovation_section');
                    const residencySection = document.getElementById('residency_section');

                    // Hide all sections first
                    businessSection.style.display = 'none';
                    renovationSection.style.display = 'none';
                    residencySection.style.display = 'none';

                    // Show relevant sections based on document type
                    if (documentType === 'Barangay Business Clearance') {
                        businessSection.style.display = 'block';
                        console.log('Showing business section');
                    } else if (documentType === 'Barangay Clearance for Renovation/Extension') {
                        renovationSection.style.display = 'block';
                        console.log('Showing renovation section');
                    } else if (documentType === 'Barangay Residency') {
                        residencySection.style.display = 'block';
                        console.log('Showing residency section');
                    }
                }

                // Initialize the form when page loads
                document.addEventListener('DOMContentLoaded', function() {
                    toggleDocumentFields();
                });
            </script>
          </div>
        </div>
      </div>
      <!-- BODY END -->


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Auto-refresh disabled: <script src="{{ asset('js/live-updates.js') }}"></script> --}}
    
    <script>
      // Simple script to handle document form
      document.addEventListener('DOMContentLoaded', function() {
        console.log('Document edit page loaded');
        
        // Initialize any form validation or interactions here
        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
          statusSelect.addEventListener('change', function() {
            console.log('Status changed to:', this.value);
          });
        }
      });
    </script>
  </body>
</html>
