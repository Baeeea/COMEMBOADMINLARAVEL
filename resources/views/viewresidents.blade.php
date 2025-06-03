<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>View Resident</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css', 'resources/js/script.js'])
    
    <style>
      /* Profile Image Styles */
      .profile-picture {
        border-radius: 50%;
        border: 3px solid #007bff;
      }
      
      .profile-image-container {
        position: relative;
        display: inline-block;
      }
      
      .profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #007bff;
        transition: all 0.3s ease;
      }
      
      .profile-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      }
      
      .profile-image-label {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #007bff;
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      }
      
      .profile-image-label:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      }
      
      .profile-image-upload {
        display: none;
      }

      /* ID Image Upload Styles */
      .id-image-container {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
      }
      
      .id-image {
        width: 100%;
        max-width: 300px;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
      }
      
      .id-image-preview {
        width: 100%;
        max-width: 300px;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
      }
      
      .id-image:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      }
      
      .id-image-label {
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #007bff;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      }
      
      .id-image-label:hover {
        background: #0056b3;
        transform: translateX(-50%) translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      }
      
      .id-image-upload {
        display: none;
      }
      
      /* Add some spacing for the modal */
      .modal-body .row {
        margin: 0;
      }
      
      .modal-body .col-md-6 {
        padding: 15px;
      }
      
      /* Upload progress and loading styles */
      .upload-progress {
        position: relative;
        margin-top: 10px;
      }
      
      .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        border-radius: 8px;
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
      }
    </style>
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

      <div class="container-fluid mt-5">
        <div class="row">
          <!-- Fixed Profile Section -->
          <div class="col-md-4">
            <aside class="position-fixed" style="width: 20%; height: 100vh; margin-left: -12px; border-right: 5px solid #ccc; overflow-y: auto;">
              <div class="h-100 p-4 bg-secondary-subtle">
                <div class="text-center">
                  @php
                    // Generate name for avatar
                    $displayName = '';
                    if ($resident->firstname || $resident->lastname) {
                      $displayName = trim(($resident->firstname ?? '') . ' ' . ($resident->lastname ?? ''));
                    } else {
                      $displayName = $resident->username ?? $resident->email ?? 'User';
                    }
                  @endphp
                  <div class="profile-picture-container">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&color=7F9CF5&background=EBF4FF&size=100" 
                         alt="Resident Avatar" 
                         class="profile-picture mb-3 mt-2" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                  </div>
                  <h5 class="card-title mb-3">{{ $resident->username ? '@' . $resident->username : ($resident->email ?? 'N/A') }}</h5>
                  <p class="{{ ($resident->status === 'Verified') ? 'text-success' : 'text-danger' }}">
                    {{ $resident->status ?: 'Not Verified' }}
                  </p>
                  
                  <!-- Edit Profile Button -->
                  <button type="button" class="btn btn-outline-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#editResidentModal">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                  </button>
                  @if($resident->status !== 'Verified')
                    <form method="POST" action="{{ route('residents.verify', $resident->user_id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to verify this resident?')">
                        Verify Account
                      </button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('residents.verify', $resident->user_id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to revoke verification for this resident?')">
                        Revoke Verification
                      </button>
                    </form>
                  @endif
                </div>
                <div class="position-absolute" style="left: 51%; transform: translateX(-50%); bottom:15%;">
                  <a href="{{ route('residents') }}" class="btn btn-secondary" style="width: 125px;">Back</a>
                </div>
              </div>
            </aside>
          </div>

          <!-- Scrollable Information Section -->
          <div class="col-md-8 my-4" style="margin-left: 23%; max-height: 100vh; overflow-y: auto; overflow-x: auto;">
            <div>
              <div>
                <!-- Personal Information Table -->
                <table id="personal-information" class="table table-borderless shadow-none">
                  <tbody>
                    <tr>
                      <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Personal Information</th>
                    </tr>
                    <tr>
                      <th style="width: 20%;">First Name</th>
                      <td style="width: 80%;">{{ $resident->first_name ?? $resident->firstname ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Last Name</th>
                      <td style="width: 80%;">{{ $resident->last_name ?? $resident->lastname ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Middle Name</th>
                      <td style="width: 80%;">{{ $resident->middle_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Birthdate</th>
                      <td style="width: 80%;">{{ $resident->birthdate ? \Carbon\Carbon::parse($resident->birthdate)->format('m/d/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Age</th>
                      <td style="width: 80%;">{{ $resident->age ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Sex/Gender</th>
                      <td style="width: 80%;">{{ $resident->sex ?? $resident->gender ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Civil Status</th>
                      <td style="width: 80%;">{{ $resident->civil_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Religion</th>
                      <td style="width: 80%;">{{ $resident->religion ?? 'N/A' }}</td>
                    </tr>
                  </tbody>
                </table>

                <!-- Contact Information Table -->
                <table id="contact-information" class="table table-borderless shadow-none">
                  <tbody>
                    <tr>
                      <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Contact Information</th>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Email</th>
                      <td style="width: 80%;">{{ $resident->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Password Status</th>
                      <td style="width: 80%;">{{ $resident->password ? 'Set' : 'Not Set' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Address</th>
                      <td style="width: 80%;">{{ $resident->address ?? $resident->home_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Phone Number</th>
                      <td style="width: 80%;">{{ $resident->phone_number ?? $resident->contact_number ?? $resident->phone ?? $resident->mobile ?? 'N/A' }}</td>
                    </tr>
                  </tbody>
                </table>

                <!-- Additional Information Table -->
                <table id="additional-information" class="table table-borderless shadow-none">
                  <tbody>
                    <tr>
                      <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Additional Information</th>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Voter Status</th>
                      <td style="width: 80%;">{{ $resident->voter ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Resident Status</th>
                      <td style="width: 80%;">{{ $resident->residents_status ?? $resident->resident_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Working Status</th>
                      <td style="width: 80%;">{{ $resident->working ?? $resident->working_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Student Status</th>
                      <td style="width: 80%;">{{ $resident->student ?? $resident->student_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Years of Residency</th>
                      <td style="width: 80%;">{{ $resident->years_residency ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Security Question</th>
                      <td style="width: 80%;">{{ $resident->security_question ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th style="width: 20%;">Account Status</th>
                      <td style="width: 80%;">
                        <span class="badge {{ $resident->status === 'active' ? 'bg-success' : ($resident->status === 'inactive' ? 'bg-danger' : 'bg-warning') }}">
                          {{ ucfirst($resident->status ?? 'N/A') }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <!-- ID Images Section -->
                <div class="mt-4">
                  <table class="table table-borderless shadow-none">
                    <tbody>
                      <tr>
                        <th class="text-primary fw-bold fs-3 my-3 border-bottom">ID Verification Images</th>
                        <th class="text-end border-bottom">
                          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editIDModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit ID
                          </button>
                        </th>
                      </tr>
                    </tbody>
                  </table>
                  
                  <div class="row">
                    <!-- Valid ID Front -->
                    <div class="col-md-6 mb-4">
                      <h5 class="text-secondary mb-3">Valid ID - Front</h5>
                      @if($resident->id_front)
                        <div class="border rounded p-3 bg-light">
                          <img src="{{ route('residents.id.image', ['id' => $resident->user_id ?? $resident->id, 'type' => 'front']) }}" 
                               alt="Valid ID Front" 
                               class="img-fluid rounded shadow-sm"
                               style="max-height: 300px; width: 100%; object-fit: contain; cursor: pointer;"
                               onclick="openImageModal(this.src, 'Valid ID - Front')">
                        </div>
                      @else
                        <div class="border rounded p-4 bg-light text-center text-muted">
                          <i class="bi bi-image fs-1 mb-2"></i>
                          <p class="mb-0">No ID front image uploaded</p>
                        </div>
                      @endif
                    </div>
                    
                    <!-- Valid ID Back -->
                    <div class="col-md-6 mb-4">
                      <h5 class="text-secondary mb-3">Valid ID - Back</h5>
                      @if($resident->id_back)
                        <div class="border rounded p-3 bg-light">
                          <img src="{{ route('residents.id.image', ['id' => $resident->user_id ?? $resident->id, 'type' => 'back']) }}" 
                               alt="Valid ID Back" 
                               class="img-fluid rounded shadow-sm"
                               style="max-height: 300px; width: 100%; object-fit: contain; cursor: pointer;"
                               onclick="openImageModal(this.src, 'Valid ID - Back')">
                        </div>
                      @else
                        <div class="border rounded p-4 bg-light text-center text-muted">
                          <i class="bi bi-image fs-1 mb-2"></i>
                          <p class="mb-0">No ID back image uploaded</p>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BODY END -->

      <!-- Edit Resident Profile Modal -->
      <div class="modal fade" id="editResidentModal" tabindex="-1" aria-labelledby="editResidentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fs-4 fw-bold text-primary" id="editResidentModalLabel">Edit Resident Profile</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="{{ route('residents.update', $resident->user_id ?? $resident->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="text-center mb-4">
                  <div class="profile-image-container">
                    <img id="profilePreview" 
                         src="https://ui-avatars.com/api/?name={{ urlencode($displayName ?? 'Resident') }}&color=7F9CF5&background=EBF4FF&size=150" 
                         alt="Profile Picture" 
                         class="profile-image">
                    <label for="profilePicture" class="profile-image-label">
                      <i class="bi bi-camera-fill me-1"></i> Change Photo
                    </label>
                    <input type="file" id="profilePicture" name="profile" class="profile-image-upload" accept="image/*" onchange="previewProfilePicture(event)">
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="firstname" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstname" name="firstname" value="{{ $resident->firstname }}">
                </div>
                <div class="mb-3">
                  <label for="lastname" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $resident->lastname }}">
                </div>
                <div class="mb-3">
                  <label for="middle_name" class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $resident->middle_name }}">
                </div>
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" value="{{ $resident->username }}">
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ $resident->email }}">
                </div>
                <div class="mb-3">
                  <label for="contact_number" class="form-label">Contact Number</label>
                  <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ $resident->contact_number }}">
                </div>
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
          </div>
        </div>
      </div>

      <!-- Edit ID Verification Modal with RESTful API -->
      <div class="modal fade" id="editIDModal" tabindex="-1" aria-labelledby="editIDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fs-4 fw-bold text-primary" id="editIDModalLabel">Edit ID Verification Images</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- RESTful form - uses AJAX instead of traditional form submission -->
              <form id="idUploadForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <!-- Valid ID Front -->
                  <div class="col-md-6 mb-4">
                    <h6 class="text-secondary mb-3">Valid ID - Front</h6>
                    <div class="text-center">
                      <div class="id-image-container position-relative">
                        <img id="idFrontPreview" 
                             src="{{ $resident->id_front ? route('residents.id.image', ['id' => $resident->user_id ?? $resident->id, 'type' => 'front']) : 'https://via.placeholder.com/300x200?text=No+ID+Front' }}" 
                             alt="Valid ID Front" 
                             class="id-image">
                        <label for="idFrontFile" class="id-image-label">
                          <i class="bi bi-camera-fill me-1"></i> Change ID Front
                        </label>
                        <input type="file" id="idFrontFile" name="validIDFront" class="id-image-upload" accept="image/*" onchange="previewIDImage(event, 'idFrontPreview')">
                        <div id="frontUploadOverlay" class="upload-overlay">
                          <i class="bi bi-arrow-clockwise spin me-2"></i> Uploading...
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Valid ID Back -->
                  <div class="col-md-6 mb-4">
                    <h6 class="text-secondary mb-3">Valid ID - Back</h6>
                    <div class="text-center">
                      <div class="id-image-container position-relative">
                        <img id="idBackPreview" 
                             src="{{ $resident->id_back ? route('residents.id.image', ['id' => $resident->user_id ?? $resident->id, 'type' => 'back']) : 'https://via.placeholder.com/300x200?text=No+ID+Back' }}" 
                             alt="Valid ID Back" 
                             class="id-image">
                        <label for="idBackFile" class="id-image-label">
                          <i class="bi bi-camera-fill me-1"></i> Change ID Back
                        </label>
                        <input type="file" id="idBackFile" name="validIDBack" class="id-image-upload" accept="image/*" onchange="previewIDImage(event, 'idBackPreview')">
                        <div id="backUploadOverlay" class="upload-overlay">
                          <i class="bi bi-arrow-clockwise spin me-2"></i> Uploading...
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="alert alert-info">
                  <i class="bi bi-info-circle me-1"></i>
                  <small>
                    <strong>ID Requirements:</strong><br>
                    • Clear, readable images<br>
                    • Maximum file size: 5MB per image<br>
                    • Supported formats: JPG, PNG, GIF<br>
                    • Minimum resolution: 300x200 pixels<br>
                    • Images are stored securely in database as BLOB data
                  </small>
                </div>
                
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 125px;">Cancel</button>
                  <button type="submit" id="uploadIDBtn" class="btn btn-primary" style="width: 125px;">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload ID
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Image Modal -->
      <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="imageModalLabel">ID Image</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <img id="modalImage" src="" alt="ID Image" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Auto-refresh disabled: <script src="{{ asset('js/live-updates.js') }}"></script> --}}
    <script>
      // Global variables
      const residentId = {{ $resident->user_id ?? $resident->id }};
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      // Auto-hide success/error messages after 5 seconds
      setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
      }, 5000);

      // Function to open image modal
      function openImageModal(imageSrc, imageTitle) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalLabel').textContent = imageTitle;
        modal.show();
      }

      // RESTful API function to upload ID images using AJAX
      document.getElementById('idUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        const frontFile = document.getElementById('idFrontFile').files[0];
        const backFile = document.getElementById('idBackFile').files[0];
        
        // Check if at least one file is selected
        if (!frontFile && !backFile) {
          showMessage('Please select at least one ID image to upload.', 'warning');
          return;
        }
        
        // Add CSRF token
        formData.append('_token', csrfToken);
        
        // Add files if selected
        if (frontFile) {
          formData.append('validIDFront', frontFile);
        }
        if (backFile) {
          formData.append('validIDBack', backFile);
        }
        
        // Show loading state
        const uploadBtn = document.getElementById('uploadIDBtn');
        const originalBtnText = uploadBtn.innerHTML;
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i> Uploading...';
        
        // Show upload overlays
        if (frontFile) document.getElementById('frontUploadOverlay').style.display = 'flex';
        if (backFile) document.getElementById('backUploadOverlay').style.display = 'flex';
        
        // RESTful API call
        fetch(`/api/residents/${residentId}/id-images`, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
          // Hide loading overlays
          document.getElementById('frontUploadOverlay').style.display = 'none';
          document.getElementById('backUploadOverlay').style.display = 'none';
          
          // Reset button
          uploadBtn.disabled = false;
          uploadBtn.innerHTML = originalBtnText;
          
          if (data.success) {
            showMessage(`Successfully uploaded ${data.updated_fields.join(' and ')} to database as BLOB data!`, 'success');
            
            // Close modal and refresh the main view
            setTimeout(() => {
              bootstrap.Modal.getInstance(document.getElementById('editIDModal')).hide();
              location.reload(); // Reload to show updated images
            }, 1500);
            
          } else {
            showMessage(data.message || 'Failed to upload ID images', 'danger');
            console.error('Upload errors:', data.errors);
          }
        })
        .catch(error => {
          console.error('Upload error:', error);
          
          // Hide loading overlays
          document.getElementById('frontUploadOverlay').style.display = 'none';
          document.getElementById('backUploadOverlay').style.display = 'none';
          
          // Reset button
          uploadBtn.disabled = false;
          uploadBtn.innerHTML = originalBtnText;
          
          showMessage('Network error occurred. Please try again.', 'danger');
        });
      });

      // Enhanced verification with AJAX
      document.addEventListener('DOMContentLoaded', function() {
        const verificationForms = document.querySelectorAll('form[action*="verify"]');
        
        verificationForms.forEach(form => {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            // Show loading state
            button.disabled = true;
            button.textContent = 'Processing...';
            
            // Prepare form data
            const formData = new FormData(this);
            
            fetch(this.action, {
              method: 'POST',
              body: formData,
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                  location.reload(); // Reload to reflect changes
                }, 1000);
              } else {
                showMessage(data.message || 'Error updating verification status', 'danger');
                button.disabled = false;
                button.textContent = originalText;
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showMessage('An error occurred. Please try again.', 'danger');
              button.disabled = false;
              button.textContent = originalText;
            });
          });
        });
      });

      function showMessage(message, type) {
        // Remove existing messages
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new message
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '1055';
        alertDiv.innerHTML = `
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.remove();
          }
        }, 5000);
      }

      function previewProfilePicture(event) {
        const input = event.target;
        const reader = new FileReader();
        
        // Show loading state
        const preview = document.getElementById('profilePreview');
        preview.style.opacity = '0.5';
        
        reader.onload = function () {
          // Update preview image
          preview.src = reader.result;
          preview.style.opacity = '1';
          
          // Validate image dimensions and size
          const img = new Image();
          img.onload = function() {
            if (img.width < 100 || img.height < 100) {
              alert('Image is too small. Please choose an image that is at least 100x100 pixels.');
            }
          };
          img.src = reader.result;
        };
        
        // Handle errors
        reader.onerror = function() {
          alert('Error reading file. Please try again.');
          preview.style.opacity = '1';
        };
        
        if (input.files && input.files[0]) {
          // Check file size
          if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Image is too large. Maximum size is 2MB.');
            return;
          }
          
          // Show file name
          const fileName = input.files[0].name;
          const fileSize = Math.round(input.files[0].size / 1024); // KB
          console.log(`Selected file: ${fileName} (${fileSize}KB)`);
          
          reader.readAsDataURL(input.files[0]);
        }
      }

      function previewIDImage(event, previewId) {
        const input = event.target;
        const reader = new FileReader();
        
        // Show loading state
        const preview = document.getElementById(previewId);
        preview.style.opacity = '0.5';
        
        reader.onload = function () {
          // Update preview image
          preview.src = reader.result;
          preview.style.opacity = '1';
          
          // Validate image dimensions and size
          const img = new Image();
          img.onload = function() {
            if (img.width < 300 || img.height < 200) {
              showMessage('ID image is too small. Please choose an image that is at least 300x200 pixels for better clarity.', 'warning');
            }
          };
          img.src = reader.result;
        };
        
        // Handle errors
        reader.onerror = function() {
          showMessage('Error reading file. Please try again.', 'danger');
          preview.style.opacity = '1';
        };
        
        if (input.files && input.files[0]) {
          // Check file size (5MB limit for ID images)
          if (input.files[0].size > 5 * 1024 * 1024) {
            showMessage('ID image is too large. Maximum size is 5MB.', 'danger');
            input.value = ''; // Clear the input
            return;
          }
          
          // Check file type
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
          if (!allowedTypes.includes(input.files[0].type)) {
            showMessage('Invalid file type. Please choose a JPG, PNG, or GIF image.', 'danger');
            input.value = ''; // Clear the input
            return;
          }
          
          // Show file info
          const fileName = input.files[0].name;
          const fileSize = Math.round(input.files[0].size / 1024); // KB
          console.log(`Selected ID file: ${fileName} (${fileSize}KB)`);
          
          reader.readAsDataURL(input.files[0]);
        }
      }

      // Add CSS for spinning animation
      const style = document.createElement('style');
      style.textContent = `
        .spin {
          animation: spin 1s linear infinite;
        }
        @keyframes spin {
          from { transform: rotate(0deg); }
          to { transform: rotate(360deg); }
        }
      `;
      document.head.appendChild(style);
    </script>

  </body>
</html>
