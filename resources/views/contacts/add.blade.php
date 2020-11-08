<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    </head>
    <body class="antialiased">
        <div class="alert alert-primary" role="alert">
            <a href="/contacts" class="alert-link">All contacts</a> |
            <a href="/contacts/create" class="alert-link">Add contacts</a> |
            <a href="/products" class="alert-link">All products</a> |
            <a href="/products/create" class="alert-link">Add products</a>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
            </ul>
        </div>
        @endif

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <form method="POST" action="{{ route('contacts.store') }}">
                            @csrf

                            <br>
                            <div class="form-group row">
                                <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Type') }}</label>

                                <div class="col-md-10">
                                    <select id="type" class="form-control @error('name') is-invalid @enderror" name="type">
                                        <option value="company">{{ __('Company') }}</option>
                                        <option value="person">{{ __('Person') }}</option>
                                    </select>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-10">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="country_id" class="col-md-2 col-form-label text-md-right">{{ __('Country') }}</label>

                                <div class="col-md-10">
                                    <input id="country_id" type="text" class="form-control @error('country_id') is-invalid @enderror" name="country_id" value="{{ old('country_id') }}" required autocomplete="country_id" autofocus>

                                    @error('country_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="payment_terms_days" class="col-md-2 col-form-label text-md-right">{{ __('Payment Terms Days') }}</label>

                                <div class="col-md-10">
                                    <input id="payment_terms_days" type="text" class="form-control @error('payment_terms_days') is-invalid @enderror" name="payment_terms_days" value="{{ old('payment_terms_days') }}" required autocomplete="payment_terms_days" autofocus>

                                    @error('payment_terms_days')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-5">
                                    <a href="{{ route('contacts.index') }}" class="btn btn-warning">
                                        {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>