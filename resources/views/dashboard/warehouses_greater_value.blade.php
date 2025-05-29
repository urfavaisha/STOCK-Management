@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
        </div>
        <div class="card-body p-0">
            @if(!$referenceWarehouse)
                <div class="alert alert-warning m-3">{{ __('Warehouse "Lind-Gislason" not found in the database.') }}</div>
            @else
                <div class="alert alert-info m-3">
                    {{ __('Reference warehouse') }}: <strong>{{ $referenceWarehouse }}</strong> - {{ __('Value') }}: <strong>${{ number_format($referenceValue, 2) }}</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Warehouse ID') }}</th>
                                <th>{{ __('Warehouse Name') }}</th>
                                <th>{{ __('Total Value') }}</th>
                                <th>{{ __('Difference') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $warehouse['id'] }}</td>
                                    <td>{{ $warehouse['name'] }}</td>
                                    <td>${{ number_format($warehouse['total_value'], 2) }}</td>
                                    <td class="text-success">+${{ number_format($warehouse['total_value'] - $referenceValue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">{{ __('No warehouses with greater value found') }}</td>
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
