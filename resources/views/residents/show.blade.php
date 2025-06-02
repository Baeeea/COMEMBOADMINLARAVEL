<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resident Details - iServeComembo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/styles.scss', 'resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>
                            Resident Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name:</label>
                                    <p class="form-control-plaintext">
                                        {{ isset($resident->firstname) && isset($resident->lastname) 
                                            ? $resident->firstname . ' ' . ($resident->middle_name ? $resident->middle_name . ' ' : '') . $resident->lastname
                                            : ($resident->name ?? 'N/A') }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Username:</label>
                                    <p class="form-control-plaintext">{{ $resident->username ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="form-control-plaintext">{{ $resident->email ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Contact Number:</label>
                                    <p class="form-control-plaintext">{{ $resident->contact_number ?? $resident->phone ?? $resident->mobile ?? $resident->phone_number ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Gender:</label>
                                    <p class="form-control-plaintext">{{ $resident->gender ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Age:</label>
                                    <p class="form-control-plaintext">{{ $resident->age ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Birthdate:</label>
                                    <p class="form-control-plaintext">{{ $resident->birthdate ? \Carbon\Carbon::parse($resident->birthdate)->format('F j, Y') : 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Home Address:</label>
                                    <p class="form-control-plaintext">{{ $resident->home_address ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Religion:</label>
                                    <p class="form-control-plaintext">{{ $resident->religion ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Verification Status:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge {{ ($resident->verified ?? 0) ? 'bg-success' : 'bg-danger' }}">
                                            {{ ($resident->verified ?? 0) ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Voter Status:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge {{ ($resident->voter ?? 0) ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ($resident->voter ?? 0) ? 'Registered Voter' : 'Not a Voter' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Resident Status:</label>
                                    <p class="form-control-plaintext">{{ $resident->resident_status ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Working Status:</label>
                                    <p class="form-control-plaintext">{{ $resident->working_status ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Student Status:</label>
                                    <p class="form-control-plaintext">{{ $resident->student_status ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Joined:</label>
                                    <p class="form-control-plaintext">{{ $resident->created_at ? $resident->created_at->format('F j, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('residents') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Residents
                        </a>
                        <button class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Delete Resident
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the resident account for 
                    <span class="fw-bold text-danger">
                        {{ isset($resident->firstname) ? $resident->firstname . ' ' . ($resident->lastname ?? '') : ($resident->name ?? 'this resident') }}
                    </span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('residents.destroy', $resident->user_id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
