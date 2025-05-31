@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h2 class="h3 mb-0">Cookies & Sessions</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary d-flex align-items-center gap-2">
                <svg class="bi" width="16" height="16" fill="currentColor">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
    <div class="justify-content-center gap-3">
        <div>
            <h1>
                Hello
                @if(Cookie::has("UserName"))
                        {{Cookie::get("UserName")}}
                @endif
            </h1>
        </div>
        <div>
            <form method="POST" action="saveCookie">
                @csrf
                <label for="txtCookie">{{__('Type your name')}}</label>
                <input type="text" id = "txtCookie" name = "txtCookie" />
                <button class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{__('Save Cookie') }}
                </button>
            </form>
        </div>
    </div>
    <br><br><br><br><br>
    <div>
            <div>
                <h1>
                    Hello
                    @if(Session::has("SessionName"))
                            {{Session("SessionName")}}
                    @endif
                </h1>
            </div>
            <div>
                <form method="POST" action="saveSession">
                    @csrf
                    <label for="txtSession">{{__('Type your name')}}</label>
                    <input type="text" id = "txSession" name = "txtSession" />
                    <button class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{__('Save Session') }}
                    </button>
                </form>
            </div>
    </div>
    <div>

<form method="POST" action="{{ route('saveAvatar') }}" enctype="multipart/form-data">
    @csrf
    <label for="avatarFile">@lang('Choose your picture')</label>
    <input type="file" id="avatarFile" name="avatarFile" required />
    <button type="submit" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        {{ __('Save picture') }} {{ trans("for your account") }}
    </button>
    @if(isset($pic))
        <img style="width:200px; border-radius:50%" src="{{ 'storage/avatars/' . $pic }}" alt="">
    @endif
</form>
    </div>
</div>
@endsection
