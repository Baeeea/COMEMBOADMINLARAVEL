<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>iServeComembo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #fcfcfc;
            width: 100vw;
            height: 100vh;
        }
        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .circle-logo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 1px solid #222;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            overflow: hidden;
        }
        .circle-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .iserve-logo-text {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 10px;
            text-align: center;
        }
        .circle-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #eee; /* Temporary for testing */
}
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "{{ url('/login') }}"; // This URL renders login.blade.php
        }, 5000);
    </script>
</head>
<body>
    <div class="center-content">
        <div class="circle-logo">
          <img src="/images/logo.png" alt="COMEMBO Makati Logo" />
        </div>
        <div class="iserve-logo-text">
            iServeComembo
        </div>
    </div>
</body>
</html>


