<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Forbidden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #1a202c;
            color: #cbd5e0;
            -webkit-font-smoothing: antialiased;
        }
        .error-wrap {
            text-align: center;
            padding: 2rem 1.5rem;
            max-width: 42rem;
        }
        .error-line {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.75rem 1rem;
            margin-bottom: 2rem;
        }
        .error-code {
            font-size: 1.125rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: #a0aec0;
            padding-right: 1rem;
            border-right: 1px solid #4a5568;
        }
        .error-message {
            font-size: 1.125rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #a0aec0;
            line-height: 1.5;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            background-color: transparent;
            border: 2px solid #dc3545;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.15s, color 0.15s;
        }
        .btn-home:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .error-hint {
            margin-top: 1.25rem;
            font-size: 0.875rem;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="error-wrap">
        <div class="error-line">
            <span class="error-code">403</span>
            <span class="error-message">{{ $exception->getMessage() ?: 'Forbidden' }}</span>
        </div>
        <a href="{{ route('home') }}" class="btn-home">
            <i class="fas fa-home" aria-hidden="true"></i>
            Return Home
        </a>
        <p class="error-hint">Contact your administrator if you need access to this page.</p>
    </div>
</body>
</html>
