<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <h2>{{ $subject }}</h2>
    
    <div class="content">
        {!! nl2br(e($body)) !!}
    </div>

    <div class="footer">
        <p>This email was sent from {{ config('app.name') }}</p>
        <p>Sent at: {{ now()->format('F j, Y, g:i a') }}</p>
    </div>
</body>
</html>