<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contract Termination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .termination-card {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .signature-pad {
            border: 2px dashed #dc3545;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: crosshair;
        }
        .signature-box {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
        }
        .status-timeline {
            position: relative;
            padding-left: 50px;
        }
        .status-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }
        .timeline-icon {
            position: absolute;
            left: -30px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract text-danger me-2"></i>Contract Termination</h2>
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

        <!-- Termination Alert -->
        <div class="termination-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-exclamation-triangle me-2"></i>Contract Termination in Progress</h3>
                    <p class="mb-2"><strong>House:</strong> {{ $contract->house->title }}</p>
                    <p class="mb-2"><strong>Location:</strong> {{ $contract->house->location }}</p>
                    <p class="mb-0"><strong>Initiated By:</strong> {{ ucfirst($contract->termination_initiated_by) }} on {{ \Carbon\Carbon::parse($contract->termination_initiated_at)->format('M d, Y') }}</p>
                </div>
                <div class="col-md-4 text-end">
                    @if($contract->termination_status === 'completed')
                        <span class="badge bg-success" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-check-circle me-1"></i>Completed
                        </span>
                    @elseif($contract->termination_status === 'partial')
                        <span class="badge bg-info" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-hourglass-half me-1"></i>Partially Signed
                        </span>
                    @else
                        <span class="badge bg-warning text-dark" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-clock me-1"></i>Pending
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Signature Status -->
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Termination Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="status-timeline">
                            <!-- Landlord Signature Status -->
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $contract->landlord_termination_signature ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas {{ $contract->landlord_termination_signature ? 'fa-check' : 'fa-clock' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Landlord Signature</h6>
                                    @if($contract->landlord_termination_signature)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Signed on {{ \Carbon\Carbon::parse($contract->landlord_signed_termination_at)->format('M d, Y h:i A') }}
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-clock me-1"></i>Awaiting signature
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Tenant Signature Status -->
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $contract->tenant_termination_signature ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas {{ $contract->tenant_termination_signature ? 'fa-check' : 'fa-clock' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Your Signature</h6>
                                    @if($contract->tenant_termination_signature)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Signed on {{ \Carbon\Carbon::parse($contract->tenant_signed_termination_at)->format('M d, Y h:i A') }}
                                        </p>
                                    @else
                                        <p class="text-warning mb-0">
                                            <i class="fas fa-exclamation-circle me-1"></i>Your signature required
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Info -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contract Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Original Contract:</strong></p>
                        <a href="{{ route('tenant.contract.download', $contract->id) }}"
                           class="btn btn-outline-primary btn-sm w-100 mb-3" target="_blank">
                            <i class="fas fa-download me-1"></i>Download Original Contract
                        </a>
                        <p><strong>Landlord:</strong> {{ $contract->landlord->name }}</p>
                        <p><strong>Email:</strong> {{ $contract->landlord->email }}</p>
                        @if($contract->landlord->phone)
                            <p><strong>Phone:</strong> {{ $contract->landlord->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Signature Pad -->
            <div class="col-lg-7">
                @if(!$contract->tenant_termination_signature && $contract->termination_status !== 'completed')
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-pen-fancy me-2"></i>Sign Termination Agreement</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> By signing this termination agreement, you acknowledge that you agree to terminate this contract. This action cannot be undone once both parties have signed.
                            </div>

                            <form id="signatureForm" action="{{ route('tenant.termination.sign', $contract->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="signature" id="signatureInput">

                                <label class="form-label fw-bold">Draw your signature below:</label>
                                <canvas id="signaturePad" class="signature-pad" width="650" height="250"></canvas>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" id="clearBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-eraser me-1"></i>Clear
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-danger flex-grow-1">
                                        <i class="fas fa-signature me-1"></i>Sign Termination Agreement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Show Signatures -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-file-signature me-2"></i>Termination Signatures</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6>Landlord Signature</h6>
                                        @if($contract->landlord_termination_signature)
                                            <img src="{{ asset('signatures/' . $contract->landlord_termination_signature) }}"
                                                 alt="Landlord Signature"
                                                 class="img-fluid"
                                                 style="max-height: 150px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mt-2">
                                                Signed {{ \Carbon\Carbon::parse($contract->landlord_signed_termination_at)->format('M d, Y') }}
                                            </p>
                                        @else
                                            <p class="text-muted">Awaiting signature</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6>Your Signature</h6>
                                        @if($contract->tenant_termination_signature)
                                            <img src="{{ asset('signatures/' . $contract->tenant_termination_signature) }}"
                                                 alt="Your Signature"
                                                 class="img-fluid"
                                                 style="max-height: 150px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mt-2">
                                                Signed {{ \Carbon\Carbon::parse($contract->tenant_signed_termination_at)->format('M d, Y') }}
                                            </p>
                                        @else
                                            <p class="text-muted">Awaiting signature</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($contract->termination_status === 'completed')
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Termination Completed!</strong> This contract was officially terminated on {{ \Carbon\Carbon::parse($contract->terminated_at)->format('M d, Y h:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(!$contract->tenant_termination_signature && $contract->termination_status !== 'completed')
    <script>
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

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

        document.getElementById('clearBtn').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        document.getElementById('signatureForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const isEmpty = !imageData.data.some(channel => channel !== 0);
            if (isEmpty) {
                alert('Please provide your signature before submitting.');
                return;
            }
            const signatureData = canvas.toDataURL('image/png');
            document.getElementById('signatureInput').value = signatureData;
            e.target.submit();
        });
    </script>
    @endif
</body>
</html>
