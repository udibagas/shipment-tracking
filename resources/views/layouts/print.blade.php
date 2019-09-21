<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style type="screen">
        body {
            width: 210mm;
        }
        .container {
            width: 210mm;
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="container">@yield('content')</div>
    <script type="text/javascript">
        // window.print()
    </script>
</body>
</html>
