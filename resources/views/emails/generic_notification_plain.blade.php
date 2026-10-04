<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="font-family:Arial,Helvetica,sans-serif;color:#222">
    <h2>{{ $title }}</h2>
    <p>{{ $messageText }}</p>
    @if(!empty($extra))
        <hr>
        <pre style="white-space:pre-wrap">{{ json_encode($extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    @endif
    <p style="color:#666;font-size:12px;margin-top:20px">Ini adalah notifikasi dari sistem SIPDKS.</p>
</body>
</html>