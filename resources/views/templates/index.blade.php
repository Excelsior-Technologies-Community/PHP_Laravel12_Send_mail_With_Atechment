@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')

<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                📧 Email Templates
            </h2>
            <p class="text-muted mb-0">
                Create reusable email templates for faster email sending.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-dark">
            ← Dashboard
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">

        {{-- Left Side --}}
        <div class="col-lg-5 mb-4">

            <div class="card shadow border-0 rounded-4">

                <div class="card-header bg-primary text-white rounded-top-4">

                    <h5 class="mb-0">
                        ➕ Create New Template
                    </h5>

                </div>

                <div class="card-body">

                    <form method="POST"
                        action="{{ route('templates.store') }}">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Template Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Example: Welcome Email"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Email Subject
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="Enter subject"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Email Body
                            </label>

                            <textarea
                                rows="7"
                                class="form-control"
                                name="body"
                                placeholder="Write email content..."
                                required>{{ old('body') }}</textarea>

                        </div>

                        <button class="btn btn-primary w-100">

                            💾 Save Template

                        </button>

                    </form>

                </div>

            </div>

        </div>

        {{-- Right Side --}}
        <div class="col-lg-7">

            <div class="card shadow border-0 rounded-4">

                <div class="card-header bg-dark text-white rounded-top-4 d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        📄 Saved Templates
                    </h5>

                    <span class="badge bg-light text-dark">
                        {{ $templates->count() }} Templates
                    </span>

                </div>

                <div class="card-body p-0">

                    @if($templates->count())

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th width="120">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($templates as $template)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>

                                        <strong>
                                            {{ $template->name }}
                                        </strong>

                                    </td>

                                    <td>

                                        {{ $template->subject }}

                                    </td>

                                    <td>

                                        @if($template->status=="Active")

                                            <span class="badge bg-success">

                                                Active

                                            </span>

                                        @else

                                            <span class="badge bg-secondary">

                                                Inactive

                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        <form
                                            method="POST"
                                            action="{{ route('templates.delete',$template->id) }}"
                                            onsubmit="return confirm('Delete this template?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="btn btn-danger btn-sm">

                                                🗑 Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    @else

                    <div class="text-center py-5">

                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png"
                            width="90"
                            class="mb-3">

                        <h5>

                            No Templates Found

                        </h5>

                        <p class="text-muted">

                            Create your first email template using the form.

                        </p>

                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection