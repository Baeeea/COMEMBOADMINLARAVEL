
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <title>Logout</title>
</head>
<body>
    <h2>Are you sure you want to logout?</h2>

   <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
  @csrf
</form>

<a href="#"
   class="sidebar-link text-light"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
   <i class="bi bi-box-arrow-right me-2"></i> Sign Out
</a>

</body>
</html>
