

@extends('layouts.admin')

@section('title', 'My Profile')

@section('additional_styles')
    <link href="{{ asset('resources/css/profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid mt-5">
    <div class="row">
        <!-- Fixed Profile Section -->
        <div class="col-md-4">
            <aside class="position-fixed" style="width: 20%; height: 100vh; margin-left: -12px; border-right: 5px solid #ccc; overflow-y: auto;">
                <div class="h-100 p-4 bg-secondary-subtle">
                    <div class="text-center">
                        <!-- Profile Image -->
                        <img src="{{ Auth::user()->profile ? route('profile.image', ['id' => Auth::user()->id, 'v' => time()]) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF&size=100' }}" 
                             alt="Profile Picture" 
                             class="img-fluid rounded-circle mb-3" 
                             style="width: 100px; height: 100px; object-fit: cover;">
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
            <!-- Personal Information Table -->
            <table id="personal-information" class="table table-borderless shadow-none">
                <tbody>
                    <tr class="my-3">
                        <th colspan="2" class="text-primary fw-bold fs-3 my-5 border-bottom">Profile Information</th>
                    </tr>
                    <tr class="my-3">
                        <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Name</th>
                        <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->name ?? 'N/A' }}</td>
                    </tr>
                    <tr class="my-3">
                        <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Role</th>
                        <td class="py-3 fs-5" style="width: 70%;">{{ ucfirst(Auth::user()->role ?? 'User') }}</td>
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
                    @if(Auth::user()->email_verified_at)
                    <tr class="my-3">
                        <th scope="col" class="text-primary py-3 fs-5" style="width: 30%;">Email Verified</th>
                        <td class="py-3 fs-5" style="width: 70%;">{{ Auth::user()->email_verified_at->format('F d, Y g:i A') }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 fw-bold text-primary" id="editAccountModalLabel">Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="text-center mb-4">
                        <div class="profile-image-container">
                            <img id="profilePreview" src="{{ Auth::user()->profile ? route('profile.image', ['id' => Auth::user()->id, 'v' => time()]) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF&size=150' }}" alt="Profile Picture" class="profile-image">
                            <label for="profilePicture" class="profile-image-label">
                                <i class="bi bi-camera-fill me-1"></i> Change Photo
                            </label>
                            <input type="file" id="profilePicture" name="profile" class="profile-image-upload" accept="image/*" onchange="previewProfilePicture(event)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}">
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
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="width: 125px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="width: 125px;">Save</button>
                    </div>
                </form>
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
            </div>
            <div class="modal-footer">
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
</script>

@endsection