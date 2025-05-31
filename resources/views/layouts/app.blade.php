<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if (app()->getLocale()=="ar") dir="rtl" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Management </title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery UI CSS -->
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet">
    {{-- @vite(['resources/js/app.js', 'resources/js/my.js', 'resources/css/app.css']) --}}
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #560bad;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }


        .btn-warning:hover {
            background-color: var(--warning-color);
        }


        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }



        footer {
            background: linear-gradient(135deg, var(--dark-color) 0%, #000 100%);
        }
    </style>
</head>

<body class="min-vh-100 d-flex flex-column">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, var(--dark-color) 0%, #000 100%);">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                    <span>@lang("Stock Management System")</span>
                </a>

                <div class="d-flex align-items-center">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light">
                                @lang('Logout')
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light">
                            @lang('Login')
                        </a>
                    @endauth
                    <div class="language-selector ms-3">
                        <select name="selectLocale" id="selectLocale" class="form-select form-select-sm">
                            <option @if(app()->getLocale() == 'ar') selected @endif value="ar">العربية</option>
                            <option @if(app()->getLocale() == 'fr') selected @endif value="fr">Français</option>
                            <option @if(app()->getLocale() == 'en') selected @endif value="en">English</option>
                        </select>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    <script>
        $("#selectLocale").on('change',function(){
            var locale = $(this).val();
            window.location.href = "{{ url('changeLocale') }}/" + locale;
        });
    </script>

    @if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    @if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
    @endif

    <footer class="py-4 text-white mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} Stock Management. All rights reserved.</p>
                </div>

            </div>
        </div>
    </footer>
</body>

</html>
