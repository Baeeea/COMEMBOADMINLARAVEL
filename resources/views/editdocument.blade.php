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
            <img src="{{ asset('img/person1.png') }}" alt="Admin Avatar" width="30" height="30" class="rounded-circle me-2">
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
    <div class="wrapper">
      <aside id="sidebar">
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
      <div class="container mt-5">
        <div class="card border-0">
          <div class="card border-0">
            <h5 class="my-3 fs-1 fw-bold text-primary">EDIT DOCUMENT REQUEST</h5>
          </div>
          <div>
            <form action="{{ route('documentrequest.update', ['id' => $document->id]) }}" method="POST" enctype="multipart/form-data">
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
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="lastname" value="{{ $document->lastname }}" required>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">First Name</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="firstname" value="{{ $document->firstname }}" required>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Middle Name</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="middle_name" value="{{ $document->middle_name }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Birthdate</th>
                  <td style="width: 80%;">
                    <input type="date" class="form-control" name="birthdate" value="{{ $document->birthdate ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Age</th>
                  <td style="width: 80%;">
                    <input type="number" class="form-control" name="age" value="{{ $document->age ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Years of Residency</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="years_residency" value="{{ $document->years_residency ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Contact Number</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="contact_number" value="{{ $document->contact_number ?? '' }}" placeholder="Numbers only (e.g. 9171234567 without leading zero)" maxlength="10" pattern="[0-9]{1,10}">
                    <small class="text-muted">Enter only numbers without spaces or special characters (up to 10 digits). Remove the leading zero (e.g., use 9171234567 instead of 09171234567)</small>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Address</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="home_address" value="{{ $document->home_address ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Civil Status</th>
                  <td style="width: 80%;">
                    <select class="form-select" name="civil_status">
                      <option value="Single" {{ ($document->civil_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                      <option value="Married" {{ ($document->civil_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                      <option value="Widowed" {{ ($document->civil_status ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                      <option value="Separated" {{ ($document->civil_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
                      <option value="Divorced" {{ ($document->civil_status ?? '') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Local Employment</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="local_employment" value="{{ $document->local_employment ?? '' }}">
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Business Information (if applicable) -->
            <table class="table table-borderless shadow-none">
              <tbody>
                <tr>
                  <th colspan="2" class="text-primary fw-bold fs-3 my-5 pt-3 border-bottom">Business Information (if applicable)</th>
                </tr>
                <tr>
                  <th style="width: 20%;">Business Name</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="business_name" value="{{ $document->business_name ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Business Type</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="business_type" value="{{ $document->business_type ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Business Owner</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="business_owner" value="{{ $document->business_owner ?? '' }}">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Business Address</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="business_address" value="{{ $document->business_address ?? '' }}">
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Document Information Table -->
            <table class="table table-borderless shadow-none">
              <tbody>
                <tr>
                  <th colspan="2" class="text-primary fw-bold fs-3 my-5 pt-3 border-bottom">Document Information</th>
                </tr>
                <tr>
                  <th style="width: 20%;">Type of Document</th>
                  <td style="width: 80%;">
                    <select class="form-select" name="document_type">
                      <option value="Barangay Residency" {{ $document->document_type == 'Barangay Residency' ? 'selected' : '' }}>Barangay Residency</option>
                      <option value="Barangay Clearance for Renovation/Extension" {{ $document->document_type == 'Barangay Clearance for Renovation/Extension' ? 'selected' : '' }}>Barangay Clearance for Renovation/Extension</option>
                      <option value="Barangay Business Clearance" {{ $document->document_type == 'Barangay Business Clearance' ? 'selected' : '' }}>Barangay Business Clearance</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Purpose</th>
                  <td style="width: 80%;">
                    <textarea class="form-control" name="purpose">{{ $document->purpose ?? '' }}</textarea>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Project Description</th>
                  <td style="width: 80%;">
                    <textarea class="form-control" name="project_description">{{ $document->project_description ?? '' }}</textarea>
                  </td>
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
                  <td style="width: 80%;">
                    <textarea class="form-control" name="status_explanation">{{ $document->status_explanation ?? '' }}</textarea>
                  </td>
                </tr>

                <!-- Image attachments -->
                <tr>
                  <th style="width: 20%;">Valid ID (Front)</th>
                  <td style="width: 80%;">
                    @if(isset($document->validIDFront))
                      <div class="mb-2">
                        <img src="{{ asset($document->validIDFront) }}" alt="Valid ID Front" style="max-width: 200px;" class="img-thumbnail">
                      </div>
                    @endif
                    <input type="file" class="form-control" name="validIDFront">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Valid ID (Back)</th>
                  <td style="width: 80%;">
                    @if(isset($document->validIDBack))
                      <div class="mb-2">
                        <img src="{{ asset($document->validIDBack) }}" alt="Valid ID Back" style="max-width: 200px;" class="img-thumbnail">
                      </div>
                    @endif
                    <input type="file" class="form-control" name="validIDBack">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Additional Image 1</th>
                  <td style="width: 80%;">
                    @if(isset($document->image))
                      <div class="mb-2">
                        <img src="{{ asset($document->image) }}" alt="Image 1" style="max-width: 200px;" class="img-thumbnail">
                      </div>
                    @endif
                    <input type="file" class="form-control" name="image">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Additional Image 2</th>
                  <td style="width: 80%;">
                    @if(isset($document->image2))
                      <div class="mb-2">
                        <img src="{{ asset($document->image2) }}" alt="Image 2" style="max-width: 200px;" class="img-thumbnail">
                      </div>
                    @endif
                    <input type="file" class="form-control" name="image2">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Additional Image 3</th>
                  <td style="width: 80%;">
                    @if(isset($document->image3))
                      <div class="mb-2">
                        <img src="{{ asset($document->image3) }}" alt="Image 3" style="max-width: 200px;" class="img-thumbnail">
                      </div>
                    @endif
                    <input type="file" class="form-control" name="image3">
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Hidden ID field -->
            <input type="hidden" name="id" value="{{ $document->id }}">

            <div class="text-center my-5">
              <a href="{{ route('documentrequest') }}" class="btn btn-secondary me-2" style="width: 15%;">Cancel</a>
              <button type="submit" class="btn btn-primary" style="width: 15%;">Save Changes</button>
            </div>
            </form>
            
            <!-- Delete Document Form -->
            <form action="{{ route('documentrequest.delete', ['id' => $document->id]) }}" method="POST" class="mt-3" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="text-center">
                    <button type="button" class="btn btn-danger" style="width: 15%;" onclick="confirmDelete()">Delete Document</button>
                </div>
            </form>
            
            <script>
                function confirmDelete() {
                    if (confirm('Are you sure you want to delete this document request? This action cannot be undone.')) {
                        document.getElementById('deleteForm').submit();
                    }
                }
            </script>
          </div>
        </div>
      </div>
      <!-- BODY END -->


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/live-updates.js') }}"></script>
  </body>
</html>
