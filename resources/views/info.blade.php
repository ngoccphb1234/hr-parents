<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<div class="row" style="margin-top: 10%">
    <div class="col-3">
    </div>
    <div class="col-6">
        <a href="{{route('home')}}">Home</a>

    @if(\Illuminate\Support\Facades\Auth::check())
                <h1>{{$user2}}</h1>
            <h2>Id: {{auth()->user()['id']}}</h2>
            <h2>Name: {{auth()->user()['name']}}</h2>
            <h2>Email: {{auth()->user()['email']}}</h2>
        @else
            <h1>Unauthorized!</h1>
        @endif
    </div>
    <div class="col-3"></div>
</div>
</body>
</html>
