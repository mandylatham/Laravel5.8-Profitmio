<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profit Miner</title>
    <link href="{{ asset('css/select-company.css') }}" rel="stylesheet">
    <script>
        window.selectCompanyUrl = "{{ route('selector.update-active-company') }}";
        @if ($activeCompanyId)
            window.activeCompanyId = @json($activeCompanyId);
        @endif
    </script>
</head>
<body style="background-image: url('/img/background-{{ rand(1,6)  }}.jpg')">
<div class="card" id="selector" v-cloak>
    <div class="card-body">
        <div class="logo">
            <img class="brand-img" src="/img/logo-large.png" alt="...">
        </div>
        <p class="text-primary">Select a Company</p>
        <p class="text-danger" v-for="error in errors">@{{ error }}</p>
        <p class="text-danger" v-if="errorMessage">@{{ errorMessage }}</p>
        <form method="post" @submit.prevent="selectCompany()">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="company">Company</label>
                <select name="company" required class="form-control" v-model="companyForm.company">
                    <option disabled>Choose a Company...</option>
                    @foreach (auth()->user()->getActiveCompanies() as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block waves-effect" :disabled="loading">
                <span v-if="!loading">Select</span>
                <spinner-icon v-if="loading"></spinner-icon>
            </button>
        </form>
    </div>
</div>
<script src="{{ asset('js/select-company.js') }}"></script>
</body>
</html>
