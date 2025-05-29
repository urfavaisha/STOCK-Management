@extends('layouts.app')

@section('content')
<div class="container">
    <h2>2 – afficher la listes des founisseurs qui ont livré les produits commandé par ‘fenny’</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product name</th>
                <th>Supplier name</th>
            </tr>
        </thead>
        <tbody>
              @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->first_name }} {{ $supplier->last_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Aucun enregistrement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">Retour au Dashboard</a>
</div>
@endsection
