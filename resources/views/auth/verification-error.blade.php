@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">@lang('Verification Error')</div>

                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        @lang('The verification link is invalid or has expired.')
                    </div>

                    <div class="text-center">
                        <a href="{{ route('verification.notice') }}" class="btn btn-primary">
                            @lang('Request New Verification Link')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 