<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contract Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .detail-row {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .signature-pad {
            border: 2px dashed #007bff;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: crosshair;
        }
        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        .badge-status {
            font-size: 1rem;
            padding: 8px 15px;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract me-2"></i>Contract Details</h2>
            <a href="{{ route('tenant.contracts') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Contracts
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Contract Info Card -->
        <div class="info-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-home me-2"></i>{{ $contract->house->title }}</h3>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $contract->house->location }}</p>
                    <p class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Rent: Ksh {{ number_format($contract->house->price) }}/month</p>
                </div>
                <div class="col-md-4 text-end">
                    @if($contract->status === 'signed')
                        <span class="badge bg-success badge-status">
                            <i class="fas fa-check-circle me-1"></i>Signed
                        </span>
                        @if($contract->signed_at)
                            <p class="mb-0 mt-2 small">Signed on: {{ \Carbon\Carbon::parse($contract->signed_at)->format('M d, Y') }}</p>
                        @endif
                    @else
                        <span class="badge bg-warning text-dark badge-status">
                            <i class="fas fa-clock me-1"></i>Pending Signature
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contract Details -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contract Information</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="detail-row">
                            <strong><i class="fas fa-user-tie me-2 text-primary"></i>Landlord</strong>
                            <p class="mb-0 mt-1">{{ $contract->landlord->name }}</p>
                            <small class="text-muted">{{ $contract->landlord->email }}</small>
                        </div>
                        <div class="detail-row">
                            <strong><i class="fas fa-calendar-plus me-2 text-success"></i>Created Date</strong>
                            <p class="mb-0 mt-1">{{ $contract->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="detail-row">
                            <strong><i class="fas fa-file-pdf me-2 text-danger"></i>Contract Document</strong>
                            <p class="mb-0 mt-1">
                                <a href="{{ route('tenant.contract.download', $contract->id) }}" class="btn btn-sm btn-outline-danger mt-2" target="_blank">
                                    <i class="fas fa-download me-1"></i>Download PDF
                                </a>
                            </p>
                        </div>
                        @if($contract->status === 'signed' && $contract->tenant_signature)
                            <div class="detail-row">
                                <strong><i class="fas fa-signature me-2 text-info"></i>Your Signature</strong>
                                <img src="{{ asset('signatures/' . $contract->tenant_signature) }}"
                                     alt="Signature"
                                     class="img-fluid mt-2"
                                     style="max-height: 100px; border: 1px solid #dee2e6; border-radius: 4px; padding: 5px;">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Landlord Contact -->
                @if($contract->landlord->phone)
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact Landlord</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Phone:</strong> {{ $contract->landlord->phone }}</p>
                            <a href="tel:{{ $contract->landlord->phone }}" class="btn btn-success btn-sm w-100 mb-2">
                                <i class="fas fa-phone me-1"></i>Call
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contract->landlord->phone) }}"
                               target="_blank"
                               class="btn btn-outline-success btn-sm w-100">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- PDF Viewer & Signature -->
            <div class="col-lg-8">
                <!-- PDF Preview -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Contract Document</h5>
                    </div>
                    <div class="card-body">
                        <iframe src="{{ asset('contracts/' . $contract->contract_pdf) }}"
                                class="pdf-viewer">
                        </iframe>
                    </div>
                </div>

                <!-- Signature Section -->
                @if($contract->status === 'pending')
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-pen-fancy me-2"></i>Sign Contract</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Please review the contract above carefully. Once you're ready, sign below to accept the terms.</p>

                            <form id="signatureForm" action="{{ route('tenant.contract.sign', $contract->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="signature" id="signatureInput">

                                <label class="form-label fw-bold">Draw your signature below:</label>
                                <canvas id="signaturePad" class="signature-pad" width="700" height="200"></canvas>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" id="clearBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-eraser me-1"></i>Clear
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-signature me-1"></i>Sign & Submit Contract
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if($contract->status === 'pending')
    <script>
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Drawing functions
        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            [lastX, lastY] = [e.clientX - rect.left, e.clientY - rect.top];
        }

        function draw(e) {
            if (!isDrawing) return;

            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();

            [lastX, lastY] = [x, y];
        }

        function stopDrawing() {
            isDrawing = false;
        }

        // Touch support
        function getTouchPos(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = getTouchPos(e);
            isDrawing = true;
            [lastX, lastY] = [touch.x, touch.y];
        });

        canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!isDrawing) return;
            const touch = getTouchPos(e);

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(touch.x, touch.y);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();

            [lastX, lastY] = [touch.x, touch.y];
        });

        canvas.addEventListener('touchend', stopDrawing);

        // Clear button
        document.getElementById('clearBtn').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Form submission
        document.getElementById('signatureForm').addEventListener('submit', (e) => {
            e.preventDefault();

            // Check if signature is empty
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const isEmpty = !imageData.data.some(channel => channel !== 0);

            if (isEmpty) {
                alert('Please provide your signature before submitting.');
                return;
            }

            // Convert canvas to base64
            const signatureData = canvas.toDataURL('image/png');
            document.getElementById('signatureInput').value = signatureData;

            // Submit form
            e.target.submit();
        });
    </script>
    @endif
</body>
</html>

