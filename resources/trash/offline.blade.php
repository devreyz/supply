<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline</title>
  <script src="{{asset('assets/js/theme.js')}}" type="text/javascript" charset="utf-8"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" type="text/css" media="all" />
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen text-center">
  <div>
    <h1 class="text-4xl font-bold text-gray-800">Você está offline</h1>
    <p class="text-gray-600 mt-4">Parece que você perdeu sua conexão com a internet.</p>
    <p class="text-gray-600">Por favor, reconecte-se para acessar todos os recursos.</p>
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-32 mx-auto mt-6">
  </div>
</body>

</html>