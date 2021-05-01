<html>

<head>
  <meta charset="utf-8">
  <title>{{ config('app.name') }}</title>
</head>

<body>
  <script>
    window.opener.postMessage({
      token: "{{ $token }}"
    }, "{{ config('app.client_url') }}")
    window.close()
  </script>
</body>

</html>