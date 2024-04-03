<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Show tweets</title>
</head>
<body>
    @foreach ($tweets as $tweet)
        {{ $tweet->tweet }}
    @endforeach
</body>
</html>