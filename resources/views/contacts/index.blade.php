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

        <a href="/sync_contacts_from" class="btn btn-primary">Sync Contacts from Billy</a>
        <a href="/sync_contacts_to" class="btn btn-primary">Sync Contacts to Billy</a><br><br>

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
                        @forelse ($contacts as $contact)
                            <div class="card-body">
                                <h5 class="card-title"><a href="/contacts/{{$contact->id}}/edit">{{$contact->name}}</a></h5>
                                <form method="POST" action="{{ route('contacts.destroy', $contact->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="card-body">
                                <p class="card-text">Contacts list is empty.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{ $contacts->links() }}
            </div>
        </div>
    </body>
</html>