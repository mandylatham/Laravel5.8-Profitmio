<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Profit Miner Technology">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ url('css/registration.css') }}">
</head>
<body>
<div id="registration" class="wrapper" v-cloak>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8 offset-lg-2">
                <div class="card mt-6">
                    <div class="card-body">
                        <div class="page-header">
                            <i class="fa fa-user-circle page-icon"></i>
                            <h1 class="page-title">Profitminer</h1>
                        </div>
                        <registration-form :form_data="form_data"></registration-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core  -->
<script>
    window.form_data = @json($data);
</script>
<script src="{{ url('js/registration.js') }}"></script>
</body>
</html>