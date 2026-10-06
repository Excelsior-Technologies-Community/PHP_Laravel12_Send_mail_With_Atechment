@extends('layouts.app')

@section('title', 'Send Email with Multi-Attachment Studio')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-paper-plane text-primary me-2"></i>Send Email with Multi-Attachment & Tracking
                    </h4>
                    <p class="text-muted small mb-0">Multi-file drag-and-drop uploader, ZIP bundler & 1-pixel open tracking pixel</p>
                </div>
                <a href="{{ route('email.history') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> View History
                </a>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('send.email') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small text-dark">Recipient Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="recipient@example.com">
                            @error('email')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Apply Email Template</label>
                            <select class="form-select" name="template_id" id="template">
                                <option value="">Select Pre-built Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" data-subject="{{ $template->subject }}" data-body="{{ $template->body }}">
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold small text-dark">Email Subject *</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Quarterly Business Report & Attachments">
                        @error('subject')
                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-bold small text-dark">Email Message Body *</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Type your email message content here...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Multi-Attachment Drag & Drop Studio -->
                    <div class="card border border-dashed rounded-3 p-3 bg-light mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">
                            <i class="fa-solid fa-paperclip text-primary me-1"></i> Multi-File Attachments (PDF, Images, DOCX, ZIP)
                        </label>
                        <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted extra-small">Hold Ctrl/Cmd to select multiple files (Max 25MB total payload).</small>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="zip_attachments" id="zipAttachments" value="1">
                                <label class="form-check-input-label fw-bold small text-dark" for="zipAttachments">
                                    Compress into single .zip bundle
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Schedule Email (Optional)</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="p-2 bg-info bg-opacity-10 rounded-3 border border-info w-100 text-info small">
                                <i class="fa-solid fa-eye me-1"></i> 1-Pixel Open Tracking Pixel automatically attached.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fa-solid fa-paper-plane me-1"></i> Dispatch Email
                        </button>
                        <a href="{{ route('send.email.programmatically') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-code me-1"></i> Send Programmatic Test Email
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('template').addEventListener('change', function() {
    let selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('subject').value = selected.getAttribute('data-subject');
        document.getElementById('message').value = selected.getAttribute('data-body');
    }
});
</script>
@endpush