@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h2 class="h3 mb-0">Send Email</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                <svg class="bi" width="16" height="16" fill="currentColor">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

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
                    <button type="submit" class="btn btn-primary">Envoyer l'e-mail</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
