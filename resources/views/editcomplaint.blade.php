<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Complaint</title>
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
            <h5 class="my-3 fs-1 fw-bold text-primary">EDIT COMPLAINT REQUEST</h5>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
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
          </div>
          <div>
            <form id="complaint-form" action="{{ route('complaint.update', $complaint->user_id) }}" method="POST" onsubmit="return validateForm(event)">
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
                  <td style="width: 80%;">{{ $complaint->lastname }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">First Name</th>
                  <td style="width: 80%;">{{ $complaint->firstname }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Middle Name</th>
                  <td style="width: 80%;">{{ $complaint->middle_name }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Birthdate</th>
                  <td style="width: 80%;">{{ $complaint->birthdate }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Age</th>
                  <td style="width: 80%;">{{ $complaint->age }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Contact Number</th>
                  <td style="width: 80%;">{{ $complaint->contact_number }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Address</th>
                  <td style="width: 80%;">{{ $complaint->home_address }}</td>
                </tr>
              </tbody>
            </table>

            <!-- Complaint Information Table -->
            <table class="table table-borderless shadow-none">
              <tbody>
                <tr>
                  <th colspan="2" class="text-primary fw-bold fs-3 my-5 pt-3 border-bottom">Complaint Information</th>
                </tr>
                <tr>
                  <th style="width: 20%;">Type of Complaint</th>
                  <td style="width: 80%;">{{ $complaint->complaint_type }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Type of Disturbance</th>
                  <td style="width: 80%;">{{ $complaint->disturbance_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Location</th>
                  <td style="width: 80%;">
                    <input type="text" class="form-control" name="location" value="{{ $complaint->location }}" style="width: 70%;">
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Date and Time</th>
                  <td style="width: 80%;">{{ $complaint->dateandtime }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Description</th>
                  <td style="width: 80%;">
                    <textarea class="form-control" name="specific_description" id="specific_description" rows="3" style="width: 80%;">{{ $complaint->specific_description }}</textarea>

                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Frequency</th>
                  <td style="width: 80%;">{{ $complaint->frequency }}</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Sentiment</th>
                  <td style="width: 80%;">
                    <div id="sentiment-summary-badge" class="d-flex align-items-center">
                        @php
                            $badgeClass = match ($complaint->sentiment ?? 'neutral') {
                                'positive' => 'bg-success',
                                'negative' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $sentimentText = ucfirst($complaint->sentiment ?? 'neutral');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $sentimentText }}</span>
                        <small class="text-muted ms-2">(Auto-analyzed based on complaint text)</small>
                        <button type="button" class="btn btn-info ms-2" id="analyzeSentimentsBtn" title="Re-analyze sentiment">
                            <i class="bi bi-brain"></i> <span id="analyzeBtnText">Analyze Sentiment</span>
                        </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Priority Level</th>
                  <td style="width: 80%;">Medium</td>
                </tr>
                <tr>
                  <th style="width: 20%;">Status</th>
                  <td style="width: 80%;">
                    <select class="form-select" style="width: 50%;" name="status">
                      <option value="pending" {{ $complaint->status == 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="phase1" {{ $complaint->status == 'phase1' ? 'selected' : '' }}>Phase 1: To be Reviewed</option>
                      <option value="phase2" {{ $complaint->status == 'phase2' ? 'selected' : '' }}>Phase 2: Additional Requirements</option>
                      <option value="phase3" {{ $complaint->status == 'phase3' ? 'selected' : '' }}>Phase 3: Complaint Investigation</option>
                      <option value="phase4" {{ $complaint->status == 'phase4' ? 'selected' : '' }}>Phase 4: Action Taken/Resolution</option>
                      <option value="phase5" {{ $complaint->status == 'phase5' ? 'selected' : '' }}>Phase 5: Final Status and Feedback</option>
                      <option value="completed" {{ $complaint->status == 'completed' ? 'selected' : '' }}>Completed</option>
                      <option value="rejected" {{ $complaint->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Status Explanation</th>
                  <td style="width: 80%;">
                    <textarea class="form-control" rows="5" name="status_explanation" placeholder="Enter status explanation here...">{{ $complaint->status_explanation }}</textarea>
                  </td>
                </tr>
                <tr>
                  <th style="width: 20%;">Valid ID Attachment</th>
                  <td style="width: 80%;">
                    <div class="d-flex">
                      <div class="border p-3 text-center me-2" style="width: 25%;">
                        <i class="bi bi-file-earmark-image fs-1"></i>
                        <p>Valid ID Attachment</p>
                      </div>
                      <div class="border p-3 text-center me-2" style="width: 25%;">
                        <i class="bi bi-file-earmark-image fs-1"></i>
                        <p>Evidence #1</p>
                      </div>
                      <div class="border p-3 text-center" style="width: 25%;">
                        <i class="bi bi-file-earmark-image fs-1"></i>
                        <p>Evidence #2</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            </form>
            <div class="text-center my-5">
              <a href="{{ route('complaint') }}" class="btn btn-secondary me-2" style="width: 15%;">Cancel</a>
              <button type="submit" form="complaint-form" class="btn btn-primary me-2" style="width: 15%;">Save</button>
              <button type="button" class="btn btn-danger" style="width: 15%;" onclick="confirmDeleteComplaint({{ $complaint->user_id }})">Delete</button>
            </div>
          </div>
        </div>
      </div>
      <!-- BODY END -->


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/live-updates.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing sentiment analysis...');
            
            // Get all necessary elements
            const descTextarea = document.getElementById('specific_description');
            const summaryBadge = document.getElementById('sentiment-summary-badge');
            const statusSelect = document.querySelector('select[name="status"]');
            const userId = {{ $complaint->user_id }};
            let analyzeTimeout = null;
            
            // First ensure elements exist
            if (!descTextarea || !summaryBadge || !statusSelect) {
                console.error('Required elements not found:', { 
                    textarea: !!descTextarea,
                    badge: !!summaryBadge,
                    status: !!statusSelect 
                });
                return;
            }

            console.log('Elements found:', {
                textarea: !!descTextarea,
                badge: !!summaryBadge,
                status: !!statusSelect,
                userId: userId
            });

            if (!descTextarea || !summaryBadge) {
                console.error('Required elements not found');
                return;
            }
            
            // Keep track of original status to know if it was manually changed
            let originalStatus = statusSelect ? statusSelect.value : null;

            function updateBadgeUI(sentiment, isLoading = false, error = null) {
                console.log('Updating badge UI:', { 
                    sentiment: sentiment,
                    isLoading: isLoading,
                    error: error,
                    timeStamp: new Date().toISOString()
                });
                
                if (isLoading) {
                    summaryBadge.innerHTML = `
                        <span class="badge bg-info">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Analyzing...
                        </span>
                    `;
                    return;
                }

                if (error) {
                    summaryBadge.innerHTML = `
                        <span class="badge bg-warning text-dark">Analysis Failed</span>
                        <small class="text-muted ms-2">(${error})</small>
                    `;
                    return;
                }

                let badgeClass, text;
                switch(String(sentiment).toLowerCase()) {
                    case 'positive':
                        badgeClass = 'bg-success';
                        text = 'Positive';
                        break;
                    case 'negative':
                        badgeClass = 'bg-danger';
                        text = 'Negative';
                        break;
                    default:
                        badgeClass = 'bg-secondary';
                        text = 'Neutral';
                }

                // Prepare sentiment details if available
                const detailsHTML = window.lastAnalysisResult ? `
                    <div class="mt-2 small">
                        <div>Scores:</div>
                        <div class="progress" style="height: 20px; width: 300px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: ${window.lastAnalysisResult.scores.negative}%" 
                                title="Negative: ${window.lastAnalysisResult.scores.negative}%">
                                ${window.lastAnalysisResult.scores.negative}%
                            </div>
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${window.lastAnalysisResult.scores.positive}%"
                                title="Positive: ${window.lastAnalysisResult.scores.positive}%">
                                ${window.lastAnalysisResult.scores.positive}%
                            </div>
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: ${window.lastAnalysisResult.scores.neutral}%"
                                title="Neutral: ${window.lastAnalysisResult.scores.neutral}%">
                                ${window.lastAnalysisResult.scores.neutral}%
                            </div>
                        </div>
                        ${window.lastAnalysisResult.matched_tokens.negative.length > 0 ? 
                            `<div class="mt-1">Negative words: <span class="text-danger">${window.lastAnalysisResult.matched_tokens.negative.join(', ')}</span></div>` : ''}
                        ${window.lastAnalysisResult.matched_tokens.positive.length > 0 ? 
                            `<div class="mt-1">Positive words: <span class="text-success">${window.lastAnalysisResult.matched_tokens.positive.join(', ')}</span></div>` : ''}
                    </div>
                ` : '';

                // Create a div to hold the sentiment information
                summaryBadge.innerHTML = `
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="badge ${badgeClass}">${text}</span>
                            <small class="text-muted ms-2">(Auto-analyzed based on complaint text)</small>
                            <button type="button" class="btn btn-info ms-2" id="analyzeSentimentsBtn" title="Re-analyze sentiment">
                                <i class="bi bi-brain"></i> <span id="analyzeBtnText">Analyze Sentiment</span>
                            </button>
                        </div>
                        ${detailsHTML}
                    </div>
                `;

                // Re-attach event listener since we replaced the button
            const newAnalyzeBtn = document.getElementById('analyzeSentimentsBtn');
            if (newAnalyzeBtn) {
                newAnalyzeBtn.addEventListener('click', () => analyzeSentiment(false));
            }
            }

            function analyzeSentiment(isInitialLoad = false) {
                const text = descTextarea.value.trim();
                console.log('Analyzing text:', text);

                if (!text) {
                    updateBadgeUI('neutral');
                    return;
                }

                // Show loading state
                updateBadgeUI(null, true);

                fetch('/api/sentiment/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        text,
                        user_id: userId,
                        is_initial_load: isInitialLoad
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Analysis result:', data);
                    
                    // Validate and normalize the response data
                    const normalizedData = {
                        sentiment: data?.sentiment || 'neutral',
                        success: !!data?.success,
                        scores: {
                            negative: parseFloat(data?.scores?.negative || 0),
                            positive: parseFloat(data?.scores?.positive || 0),
                            neutral: parseFloat(data?.scores?.neutral || 0)
                        },
                        matched_tokens: {
                            negative: Array.isArray(data?.matched_tokens?.negative) ? data.matched_tokens.negative : [],
                            positive: Array.isArray(data?.matched_tokens?.positive) ? data.matched_tokens.positive : [],
                            neutral: Array.isArray(data?.matched_tokens?.neutral) ? data.matched_tokens.neutral : []
                        }
                    };

                    // Store normalized result globally
                    window.lastAnalysisResult = normalizedData;
                    updateBadgeUI(normalizedData.sentiment);
                    
                    // Log additional info
                    console.log('Analysis details:', {
                        scores: data.scores,
                        tokens: data.tokens,
                        matched_tokens: data.matched_tokens
                    });

                    // Update status based on sentiment if it hasn't been manually changed
                    if (statusSelect && statusSelect.value === originalStatus && data.status) {
                        statusSelect.value = data.status;
                        // Trigger change event to update any listeners
                        statusSelect.dispatchEvent(new Event('change'));
                    }
                    
                    // Only update original status during initial load
                    if (isInitialLoad) {
                        originalStatus = statusSelect.value;
                    }
                })
                .catch(error => {
                    console.error('Analysis error:', error);
                    updateBadgeUI(null, false, 'Please try again');
                });
            }

            // Initialize with any existing sentiment
            const initialSentiment = '{{ $complaint->sentiment ?? "neutral" }}';
            updateBadgeUI(initialSentiment);

            // Set up the initial analyze button click handler
            const analyzeBtn = document.getElementById('analyzeSentimentsBtn');
            if (analyzeBtn) {
                analyzeBtn.addEventListener('click', function() {
                    const text = document.getElementById('specific_description').value.trim();
                    if (!text) {
                        alert('Please enter a description to analyze');
                        return;
                    }
                    console.log('Analyze button clicked, analyzing text:', text);
                    analyzeSentiment(false);
                });
            }

            // Then do a fresh analysis
            analyzeSentiment(true);

            // Track manual status changes only
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    // If status was manually changed by user (not by our code), update originalStatus
                    if (!this.hasAttribute('data-auto-update')) {
                        originalStatus = this.value;
                    }
                    this.removeAttribute('data-auto-update');
                });
            }

            // No duplicate click handler needed
        });

        // Form validation and submission handling
        function validateForm(e) {
            e.preventDefault(); // Always prevent default form submission

            const status = document.querySelector('select[name="status"]').value;
            const statusExplanation = document.querySelector('textarea[name="status_explanation"]').value;

            // Basic validation
            if (!status) {
                alert('Please select a status.');
                return false;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
            submitBtn.disabled = true;

            // Get the form and submit it programmatically
            const form = document.getElementById('complaint-form');
            form.submit();

            // Re-enable button after a timeout in case of errors
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);

            return false; // Prevent form from submitting naturally
        }

        // Auto-resize textarea
        const textarea = document.querySelector('textarea[name="status_explanation"]');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // Character counter for status explanation
        const statusTextarea = document.querySelector('textarea[name="status_explanation"]');
        if (statusTextarea) {
            const maxLength = 500;
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-muted small mt-1';
            counterDiv.innerHTML = `<span id="char-count">${statusTextarea.value.length}</span>/${maxLength} characters`;
            statusTextarea.parentNode.appendChild(counterDiv);

            statusTextarea.addEventListener('input', function() {
                const charCount = this.value.length;
                document.getElementById('char-count').textContent = charCount;

                if (charCount > maxLength) {
                    counterDiv.className = 'text-danger small mt-1';
                } else if (charCount > maxLength * 0.8) {
                    counterDiv.className = 'text-warning small mt-1';
                } else {
                    counterDiv.className = 'text-muted small mt-1';
                }
            });
        }

        // Removed duplicate event listener - the first one above handles everything

        // Removed duplicate sentiment analysis logic

        // Function to handle delete confirmation
        function confirmDeleteComplaint(user_id) {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/complaint/${user_id}/delete`;
            deleteModal.show();
        }
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this complaint? This action cannot be undone.
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

  </body>
</html>
