<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contract</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contract-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .contract-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .file-upload-wrapper {
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s;
        }
        .file-upload-wrapper:hover {
            border-color: #764ba2;
            background: #f0f0ff;
        }
        .file-upload-wrapper.active {
            border-color: #28a745;
            background: #f0fff0;
        }
        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Back Button -->
                <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary mb-4">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>

                <div class="card contract-card">
                    <div class="contract-header">
                        <h3 class="mb-2"><i class="fas fa-file-contract me-2"></i>Create New Contract</h3>
                        <p class="mb-0 opacity-75">Upload a contract document for your tenant</p>
                    </div>

                    <div class="card-body p-4">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Tenant Information -->
                        <div class="info-box">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user-circle me-2"></i>Tenant Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Name:</strong> {{ $tenant->user->name }}</p>
                                    <p class="mb-2"><strong>Email:</strong> {{ $tenant->user->email }}</p>
                                    <p class="mb-0"><strong>Phone:</strong> {{ $tenant->user->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Property:</strong> {{ $tenant->house->title }}</p>
                                    <p class="mb-2"><strong>Location:</strong> {{ $tenant->house->location }}</p>
                                    <p class="mb-0"><strong>Monthly Rent:</strong> KSh {{ number_format($tenant->house->price, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contract Upload Form -->
                        <form method="POST" action="{{ route('landlord.contract.store') }}" enctype="multipart/form-data" id="contractForm">
                            @csrf
                            <input type="hidden" name="tenant_id" value="{{ $tenant->user_id }}">
                            <input type="hidden" name="house_id" value="{{ $tenant->house_id }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>Upload Contract Document (PDF)
                                </label>
                                <div class="file-upload-wrapper" id="fileUploadWrapper">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <h5>Drag & Drop your PDF here</h5>
                                    <p class="text-muted mb-3">or click to browse</p>
                                    <input type="file"
                                           name="contract_pdf"
                                           id="contract_pdf"
                                           class="d-none"
                                           accept=".pdf"
                                           required>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('contract_pdf').click()">
                                        <i class="fas fa-folder-open me-2"></i>Browse Files
                                    </button>
                                    <div id="fileName" class="mt-3 text-success fw-bold" style="display: none;"></div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Maximum file size: 10MB. Only PDF files are accepted.
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Once you upload the contract, the tenant will be notified via email
                                and can review and sign it from their dashboard.
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-create text-white">
                                    <i class="fas fa-paper-plane me-2"></i>Create Contract & Notify Tenant
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload handling
        const fileInput = document.getElementById('contract_pdf');
        const wrapper = document.getElementById('fileUploadWrapper');
        const fileNameDisplay = document.getElementById('fileName');

        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Check file type
                if (file.type !== 'application/pdf') {
                    alert('Please upload a PDF file only.');
                    this.value = '';
                    return;
                }

                // Check file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB.');
                    this.value = '';
                    return;
                }

                wrapper.classList.add('active');
                fileNameDisplay.innerHTML = `<i class="fas fa-check-circle me-2"></i>Selected: ${file.name}`;
                fileNameDisplay.style.display = 'block';
            }
        });

        // Drag and drop
        wrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#28a745';
        });

        wrapper.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#667eea';
        });

        wrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#667eea';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        // Form submission validation
        document.getElementById('contractForm').addEventListener('submit', function(e) {
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload a contract document before submitting.');
                return false;
            }
        });
    </script>
</body>
</html>
