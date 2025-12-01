<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dummy data</title>
</head>
    <body>
        <h1>Dummy data</h1>

        @if ($errors->any())
            <div class="validation-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p><strong>Name:</strong> {{ $dummy->name }}</p>
        <p><strong>Email:</strong> {{ $dummy->email }}</p>
        <p><strong>Age:</strong> {{ $dummy->age }}</p>
        <p><strong>Children's count:</strong> {{ $dummy->children->count() }}</p>
    </body>
</html>