@extends('layouts.app')

@section('title', 'Email History & Tracking Radar')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Banner -->
    <div class="bg-white p-4 rounded-4 shadow-sm border mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Email History & Tracking Radar</h3>
            <p class="text-muted mb-0 small">Audit sent emails, monitor 1-pixel open timestamps & download stored attachments</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
            <a href="{{ route('email.form') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="fa-solid fa-paper-plane me-1"></i> Send Email
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('email.history') }}" class="row g-2">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by recipient email or subject line...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Email History Records Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list me-2"></i>Dispatched Email Logs</h5>
            @if($emails->count())
                <form action="{{ route('email.clear') }}" method="POST" onsubmit="return confirm('Clear all email history records?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-trash-can me-1"></i> Clear All History
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Open Tracking Radar</th>
                            <th>Attachment / Downloads</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emails as $email)
                            <tr>
                                <td>{{ $loop->iteration + ($emails->currentPage() - 1) * $emails->perPage() }}</td>
                                <td>
                                    <strong class="text-dark d-block">{{ $email->email }}</strong>
                                    <span class="badge bg-light text-muted border extra-small">{{ strtoupper($email->type ?? 'INSTANT') }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $email->subject }}</span>
                                </td>

                                <!-- 1-Pixel Open Tracking Column -->
                                <td>
                                    @if($email->opened_at)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2"><i class="fa-solid fa-envelope-open me-1"></i>Opened ({{ $email->open_count }}x)</span>
                                        </div>
                                        <small class="text-muted extra-small d-block mt-1">Last: {{ $email->last_opened_at?->diffForHumans() }}</small>
                                    @else
                                        <span class="badge bg-secondary opacity-75"><i class="fa-solid fa-envelope me-1"></i>Not Opened Yet</span>
                                    @endif
                                </td>

                                <!-- Attachment & Download Column -->
                                <td>
                                    @if($email->attachment || !empty($email->multiple_attachments))
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="{{ route('email.attachment.download', $email->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                                <i class="fa-solid fa-download me-1"></i>
                                                {{ $email->attachment ? Str::limit($email->attachment, 18) : 'Download Attachment' }}
                                            </a>
                                            @if($email->is_zipped)
                                                <span class="badge bg-warning text-dark">ZIP</span>
                                            @endif
                                        </div>
                                        <small class="text-muted extra-small d-block mt-1">Downloads: <strong>{{ $email->attachment_downloads }}</strong></small>
                                    @else
                                        <span class="text-muted small">No Attachment</span>
                                    @endif
                                </td>

                                <td>
                                    @if($email->status == 'Sent')
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Sent</span>
                                    @elseif($email->status == 'Pending')
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Scheduled</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>Failed</span>
                                    @endif
                                </td>

                                <td>
                                    <small class="text-muted">{{ $email->sent_at?->format('d M Y, h:i A') ?? 'Pending' }}</small>
                                </td>

                                <td class="text-end">
                                    <form action="{{ route('email.delete', $email->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this email record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <h6>No Email Records Found</h6>
                                    <p class="small">Dispatched emails will appear here with open tracking radar and attachment downloads.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $emails->links() }}
            </div>
        </div>
    </div>
</div>
@endsection