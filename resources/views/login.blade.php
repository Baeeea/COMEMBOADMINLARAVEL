<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iServeComembo Login</title>

    <!-- Bootstrap Icons & Google Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">

    <!-- Load Vite Assets -->
    @vite(['resources/css/login.scss', 'resources/js/app.js','resources/css/styles.scss'])

    <style>
        body.bg {
            background-image: url('{{ asset('images/brgy2.png') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            backdrop-filter: brightness(0.35);
        }

        .login-container {
            background-color: #E1F2FF;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 1);
            overflow: hidden;
            opacity: 0.93;
            height: 600px;
        }

        .left-panel {
            background-color: rgba(47, 65, 86, 1);
            color: white;
            text-align: center;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel {
            padding: 40px;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h1, h2 {
            font-weight: bold;
        }

        .login-form input {
            margin-bottom: 15px;
        }

        .input-outline {
            outline: 2px solid #2F4156;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            backdrop-filter: brightness(0.35);
            z-index: -1;
        }
    </style>
</head>
<body class="bg">

    <div class="overlay"></div>

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container row w-75">
            <!-- Left Panel -->
            <div class="left-panel col-md-6 d-flex flex-column justify-content-center align-items-center text-center">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="100" height="100">
                    <h1 class="fw-bold ms-3">iServeComembo</h1>
                </div>
                <h2>Welcome Back Admin!</h2>
                <p class="lead">You can sign in to access your existing account.</p>
            </div>

            <!-- Right Panel -->
            <div class="right-panel col-md-6 d-flex flex-column justify-content-center align-items-center">
                <h3 class="mb-4 fw-bold">Log-in</h3>

                <form class="login-form w-100" style="max-width:500px;" method="POST" action="{{ route('login.process') }}">
                    @csrf
                    <input type="email" name="email" class="form-control input-outline" placeholder="Email" required autocomplete="email">
                    <input type="password" name="password" class="form-control input-outline" placeholder="Password" required autocomplete="current-password">
                    <button type="submit" class="btn btn-primary w-50 d-grid mx-auto">Log-In</button>

                    @if($errors->any())
                        <div class="text-danger mt-3 text-center">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

</body>
</html>
