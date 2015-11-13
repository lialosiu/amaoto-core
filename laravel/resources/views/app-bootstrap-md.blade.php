<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ $htmlTitle or env('PROJECT_NAME') }}</title>

    <link href="{{ elixir('css/app-bootstrap-md.css') }}" rel="stylesheet">
    <script src="{{ elixir('js/app-bootstrap-md.js') }}"></script>

    <!--[if lt IE 9]>
    <script src="{{ elixir('js/lt_ie9.js') }}"></script>
    <![endif]-->

    <script>
        $(function () {
            $.material.init();
        });
    </script>

</head>
<body>

</body>
</html>
