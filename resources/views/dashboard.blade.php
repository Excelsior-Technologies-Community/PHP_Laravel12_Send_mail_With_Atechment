@extends('layouts.app')

@section('title', 'Email Analytics Dashboard')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i>Live Email Analytics & Tracking Dashboard</h3>
            <p class="text-muted mb-0 small">Real-time open rates, 1-pixel tracking telemetry & attachment download metrics</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('email.form') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="fa-solid fa-paper-plane me-1"></i> Send Email
            </a>
            <a href="{{ route('email.history') }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Email History
            </a>
        </div>
    </div>

    <!-- Primary Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-white-50 small">Total Dispatched</span>
                        <h2 class="fw-bold my-1">{{ $totalEmails }}</h2>
                        <small class="text-white-50 extra-small">All Mail Operations</small>
                    </div>
                    <i class="fa-solid fa-paper-plane fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-white-50 small">Successfully Sent</span>
                        <h2 class="fw-bold my-1">{{ $sentEmails }}</h2>
                        <small class="text-white-50 extra-small">Delivered Mails</small>
                    </div>
                    <i class="fa-solid fa-circle-check fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-white-50 small">Delivery Failures</span>
                        <h2 class="fw-bold my-1">{{ $failedEmails }}</h2>
                        <small class="text-white-50 extra-small">Mail Exception Failures</small>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-dark-50 small">Sent Today</span>
                        <h2 class="fw-bold my-1">{{ $todayEmails }}</h2>
                        <small class="text-dark-50 extra-small">{{ today()->format('d M Y') }}</small>
                    </div>
                    <i class="fa-solid fa-calendar-day fa-2x text-dark-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Tracking & Attachment Analytics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3 me-3">
                        <i class="fa-solid fa-envelope-open fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Total Emails Opened</h6>
                        <small class="text-muted">Detected via 1-pixel tracking pixel</small>
                    </div>
                </div>
                <h2 class="fw-bold text-info my-2">{{ $openedEmails }}</h2>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: {{ $openRate }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                        <i class="fa-solid fa-chart-pie fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Overall Open Rate</h6>
                        <small class="text-muted">Percentage of delivered mails opened</small>
                    </div>
                </div>
                <h2 class="fw-bold text-success my-2">{{ $openRate }}%</h2>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $openRate }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                        <i class="fa-solid fa-file-arrow-down fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Attachment Downloads</h6>
                        <small class="text-muted">Total file download clicks</small>
                    </div>
                </div>
                <h2 class="fw-bold text-warning text-dark my-2">{{ $totalDownloads }}</h2>
                <small class="text-muted">Tracked across stored attachments & zip bundles</small>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-bolt text-warning me-2"></i>Email Operations Quick Actions</h5>
        </div>
        <div class="card-body p-4 d-flex gap-2">
            <a href="{{ route('email.form') }}" class="btn btn-primary fw-bold px-4">
                <i class="fa-solid fa-paper-plane me-1"></i> Send New Email with Attachments
            </a>
            <a href="{{ route('email.history') }}" class="btn btn-outline-dark fw-bold px-4">
                <i class="fa-solid fa-list me-1"></i> Inspect Email Tracking Radar
            </a>
            <a href="{{ route('templates.index') }}" class="btn btn-warning text-dark fw-bold px-4">
                <i class="fa-solid fa-file-lines me-1"></i> Manage Email Templates
            </a>
        </div>
    </div>
</div>
@endsection