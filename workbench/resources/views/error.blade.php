<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error {{ $status ?? 'Error' }}</title>
</head>
    <body>
    {{ dump($exception->getMessage() }}
        <h1>Error {{ $exception->getCode() ?: 500 }}</h1>

        <p>{{ $exception->getMessage() }}</p>
    </body>
</html>