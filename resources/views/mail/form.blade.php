@extends('layouts.app')

@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <form action="/sendmail" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nom" class="form-label">@lang('Nom')</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">@lang('adresse E-mail')</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="sujet" class="form-label">@lang('Sujet')</label>
                    <input type="text" name="sujet" id="sujet" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">@lang('Envoyer l’e-mail')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
