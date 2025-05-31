@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Orders with Total Greater Than Average Order Value (Average: ${{ number_format($averageOrderValue, 2) }})</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->order_date }}</td>
                <td>${{ number_format($order->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
@endsection
