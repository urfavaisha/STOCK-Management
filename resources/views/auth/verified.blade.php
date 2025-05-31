@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">@lang('Email Verified')</div>

                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        @lang('Your email has been verified successfully.')
                    </div>

                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            @lang('Go to Dashboard')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 