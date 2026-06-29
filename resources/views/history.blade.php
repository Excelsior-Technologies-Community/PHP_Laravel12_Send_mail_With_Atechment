@extends('layouts.app')

@section('title', 'Email History')

@section('content')

    <div class="container mt-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="fw-bold">📧 Email History</h2>
                <p class="text-muted">Manage all sent emails</p>
            </div>

            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-dark">
                    Dashboard
                </a>

                <a href="{{ route('email.form') }}" class="btn btn-primary">
                    Send Email
                </a>
            </div>

        </div>

        {{-- Success Message --}}
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        {{-- Search --}}
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="GET" action="{{ route('email.history') }}">

                    <div class="row">

                        <div class="col-md-10">

                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Search by Email or Subject">

                        </div>

                        <div class="col-md-2 d-grid">

                            <button class="btn btn-primary">
                                Search
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- Table --}}
        <div class="card shadow">

            <div class="card-header bg-dark text-white d-flex justify-content-between">

                <h5 class="mb-0">
                    Email Records
                </h5>

                @if($emails->count())

                    <form action="{{ route('email.clear') }}" method="POST"
                        onsubmit="return confirm('Delete all email history?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Clear All
                        </button>

                    </form>

                @endif

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>#</th>

                                <th>Email</th>

                                <th>Subject</th>

                                <th>Attachment</th>

                                <th>Status</th>

                                <th>Sent At</th>

                                <th width="120">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($emails as $email)

                                <tr>

                                    <td>
                                        {{ $loop->iteration + ($emails->currentPage() - 1) * $emails->perPage() }}
                                    </td>

                                    <td>
                                        {{ $email->email }}
                                    </td>

                                    <td>
                                        {{ $email->subject }}
                                    </td>

                                    <td>

                                        @if($email->attachment)

                                            {{ $email->attachment }}

                                        @else

                                            <span class="text-muted">
                                                No Attachment
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($email->status == 'Sent')

                                            <span class="badge bg-success">
                                                Sent
                                            </span>

                                        @else

                                            <span class="badge bg-danger">
                                                Failed
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        {{ $email->sent_at?->format('d M Y h:i A') }}

                                    </td>

                                    <td>

                                        <form action="{{ route('email.delete', $email->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this record?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center">

                                        No Email History Found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">

                    @if ($emails->lastPage() > 1)
                        <div class="d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination">

                                    @for ($i = 1; $i <= $emails->lastPage(); $i++)

                                        <li class="page-item {{ $emails->currentPage() == $i ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $emails->url($i) }}">
                                                {{ $i }}
                                            </a>
                                        </li>

                                    @endfor

                                </ul>
                            </nav>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection