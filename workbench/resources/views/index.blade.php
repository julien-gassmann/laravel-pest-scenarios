<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dummies list</title>
</head>
    <body>
        <h1>Dummies list</h1>

        @if ($errors->any())
            <div class="validation-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul>
            @foreach ($dummiesPaginated as $dummy)
                <li>{{ $dummy->name }}</li>
            @endforeach
        </ul>
    </body>
</html>