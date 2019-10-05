<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>

        @font-face {
            font-family: "MyriadMM";
            src: url("/fonts/MyriadMM.ttf");
        }

        * {
            font-family: "MyriadMM", sans-serif;
        }

        body {
            font-family: "MyriadMM", sans-serif;
            color: #505050;
            padding: 0;
            font-size: 12px;
        }

        .container {
            width: 210mm;
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-spacing: 0px;
            border-collapse: separate;
        }

        table.bordered > thead > tr > th,
        table.bordered > tfoot > tr > th,
        table.bordered > tbody > tr > td {
            border-bottom: 1px solid #ddd !important;
            border-left: 1px solid #ddd !important;
            padding: 5px;
        }

        table.bordered > thead > tr > th {
            border-top: 1px solid #ddd !important;
        }

        table.bordered > thead > tr > th:last-of-type,
        table.bordered > tbody > tr > td:last-of-type,
        table.bordered > tfoot > tr > th:last-of-type {
            border-right: 1px solid #ddd !important;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
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
