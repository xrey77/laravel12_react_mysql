<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">    
    <title>Diebold-Nixdorf</title>
    @viteReactRefresh
    @vite('resources/js/app.tsx')
    @inertiaHead
  </head>
  <body>
    @inertia
    <div id="app"></div>
  </body>
</html>
