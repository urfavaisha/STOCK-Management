@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('Customer Orders') }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h2 class="h5 mb-0"><i class="fas fa-list me-2"></i>{{ __('Customer Names for Each Order') }}</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Customer Name') }}</th>
                            <th>{{ __('Order Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $order->customer_name }} </td>
                                <td>{{ $order->order_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">{{ __('No orders found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection