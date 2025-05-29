@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('Warehouse Values') }}</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Warehouse ID') }}</th>
                            <th>{{ __('Warehouse Name') }}</th>
                            <th>{{ __('Total Value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($warehouses as $warehouse)
                            <tr>
                                <td>{{ $warehouse['id'] }}</td>
                                <td>{{ $warehouse['name'] }}</td>
                                <td>${{ number_format($warehouse['total_value'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">{{ __('No warehouses found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
