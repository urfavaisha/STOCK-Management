@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('Suppliers by Customer') }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h2 class="h5 mb-0"><i class="fas fa-users me-2"></i>{{ __('Suppliers for Customer') }}: {{ $customer ? $customer->first_name . ' ' . $customer->last_name : 'No Customer' }}</h2>
        </div>
        <div class="card-body p-0">
            @if(!$customer)
                <div class="alert alert-warning m-3">{{ __('No customer found in the database.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Supplier Name') }}</th>
                                <th>{{ __('Product Name') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->first_name }} {{ $supplier->last_name }}</td>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">{{ __('No suppliers found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
