@extends('voyager::auth.master')

@section('content')
    <div class="login-container">

        <p>{{ __('voyager::login.signin_below') }}</p>

        <form action="{{ route('voyager.login') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group form-group-default" id="emailGroup">
                <label>{{ __('voyager::generic.email') }}</label>
                <div class="controls">
                    <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('voyager::generic.email') }}" class="form-control" required>
                </div>
            </div>

            <div class="form-group form-group-default" id="passwordGroup">
                <label>{{ __('voyager::generic.password') }}</label>
                <div class="controls" style="position:relative;">
                    <input type="password" name="password" id="password" placeholder="{{ __('voyager::generic.password') }}" class="form-control" required style="padding-right:34px;">
                    <button type="button" id="togglePassword" tabindex="-1" aria-label="Mostrar/ocultar contraseña"
                        style="position:absolute; right:0; top:-9px; width:26px; height:26px; display:flex; align-items:center; justify-content:center; background:none; border:0; padding:0; cursor:pointer; color:#9aa0a6; border-radius:50%; transition:color .15s, background .15s;"
                        onmouseover="this.style.color='#5EAF4A'; this.style.background='rgba(94,175,74,.1)';"
                        onmouseout="this.style.color='#9aa0a6'; this.style.background='none';">
                        <svg id="eyeOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group" id="rememberMeGroup">
                <div class="controls">
                    <input type="checkbox" name="remember" id="remember" value="1"><label for="remember" class="remember-me-text">{{ __('voyager::generic.remember_me') }}</label>
                </div>
            </div>

            <button type="submit" class="btn btn-block login-button">
                <span class="signingin hidden"><span class="voyager-refresh"></span> {{ __('voyager::login.loggingin') }}...</span>
                <span class="signin">{{ __('voyager::generic.login') }}</span>
            </button>

        </form>

        <div style="clear:both"></div>

        @if(!$errors->isEmpty())
            <div class="alert alert-red">
                <ul class="list-unstyled">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div> <!-- .login-container -->
@endsection

@section('post_js')

    <script>
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        var email = document.querySelector('[name="email"]');
        var password = document.querySelector('[name="password"]');
        btn.addEventListener('click', function(ev){
            if (form.checkValidity()) {
                btn.querySelector('.signingin').className = 'signingin';
                btn.querySelector('.signin').className = 'signin hidden';
            } else {
                ev.preventDefault();
            }
        });
        email.focus();
        document.getElementById('emailGroup').classList.add("focused");

        // Focus events for email and password fields
        email.addEventListener('focusin', function(e){
            document.getElementById('emailGroup').classList.add("focused");
        });
        email.addEventListener('focusout', function(e){
            document.getElementById('emailGroup').classList.remove("focused");
        });

        password.addEventListener('focusin', function(e){
            document.getElementById('passwordGroup').classList.add("focused");
        });
        password.addEventListener('focusout', function(e){
            document.getElementById('passwordGroup').classList.remove("focused");
        });

        // Mostrar/ocultar contraseña
        var togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            var eyeOpen = document.getElementById('eyeOpen');
            var eyeClosed = document.getElementById('eyeClosed');
            togglePassword.addEventListener('click', function(){
                if (password.type === 'password') {
                    password.type = 'text';
                    eyeOpen.style.display = 'none';
                    eyeClosed.style.display = '';
                } else {
                    password.type = 'password';
                    eyeOpen.style.display = '';
                    eyeClosed.style.display = 'none';
                }
                password.focus();
            });
        }

    </script>


<script type="text/javascript" src="{{ voyager_asset('js/app.js') }}"></script>




@if (setting('configuracion.navidad'))
    {{-- <link href="{{asset('navidad/css/style.css')}}" rel="stylesheet" type="text/css" /> --}}
    {{-- <script type="text/javascript" src="{{asset('navidad/js/jquery-latest.min.js')}}"></script> --}}
    {{-- <script src="{{asset('navidad/js/snowfall.jquery.js')}}"></script> --}}
    <script type="text/javascript" src="{{asset('navidad/snow.js')}}"></script>
    <script type="text/javascript">
        $(function() {
            $(document).snow({ SnowImage: "{{ asset('navidad/image/icon.png') }}", SnowImage2: "{{ asset('navidad/image/caramelo.png') }}" });
        });
    </script>
@endif
@endsection
