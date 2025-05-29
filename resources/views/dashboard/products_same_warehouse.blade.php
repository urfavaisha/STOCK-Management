@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('Products in Same Warehouses') }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h2 class="h5 mb-0"><i class="fas fa-warehouse me-2"></i>{{ __('Products Stored in Same Warehouses as') }} {{ $supplier ? $supplier->first_name . ' ' . $supplier->last_name : 'Scottie Crona' }}'s {{ __('Products') }}</h2>
        </div>
        <div class="card-body p-0">
            @if(!$supplier)
                <div class="alert alert-warning m-3">{{ __('Supplier "Scottie Crona" not found in the database.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product ID') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Supplier') }}</th>
                                <th>{{ __('Warehouse') }}</th>
                                <th>{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->supplier->first_name }} {{ $product->supplier->last_name }}</td>
                                    <td>{{ $product->stock->store->name }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">{{ __('No products found') }}</td>
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