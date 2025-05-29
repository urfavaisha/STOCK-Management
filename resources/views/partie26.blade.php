@extends('layouts.app')

@section('content')
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

<form method="POST" action="/saveAvatar" enctype="multipart/form-data">
    @csrf
    <label for="avatarFile">@lang('Choose your picture')</label>
    <input type="file" id="avatarFile" name="avatarFile" required />
    <button type="submit" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        {{ __('Save picture') }} {{ trans("for your account") }}
    </button>
            {{--  il faut executer php artisan storage:link pour assosier le racourcis storage --}}
            <img style = "width:200px; border-radius:50%" src="{{"storage/avatars/".$pic}}" alt="">
        </form>
    </div>
</div>
@endsection
