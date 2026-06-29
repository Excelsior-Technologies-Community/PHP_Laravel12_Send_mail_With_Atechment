@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">📧 Email Dashboard</h2>
                <p class="text-muted">Email Statistics Overview</p>
            </div>

            <div>
                <a href="{{ route('email.form') }}" class="btn btn-primary">
                    Send Email
                </a>

                <a href="{{ route('email.history') }}" class="btn btn-dark">
                    Email History
                </a>
            </div>
        </div>

        <div class="row">

            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Total Emails</h5>
                        <h2>{{ $totalEmails }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Sent Emails</h5>
                        <h2>{{ $sentEmails }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>Failed Emails</h5>
                        <h2>{{ $failedEmails }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-warning text-dark">
                    <div class="card-body text-center">
                        <h5>Today's Emails</h5>
                        <h2>{{ $todayEmails }}</h2>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow border-0 mt-3">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>

            <div class="card-body">

                <a href="{{ route('email.form') }}" class="btn btn-success me-2">
                    Send New Email
                </a>

                <a href="{{ route('email.history') }}" class="btn btn-secondary">
                    View Email History
                </a>

            </div>
        </div>

    </div>

@endsection