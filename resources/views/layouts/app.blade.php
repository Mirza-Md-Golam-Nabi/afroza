<!DOCTYPE html> 
<html lang="en">
<head>
  <title>Afroza Traders - {{ $title }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @include('includes.style')
  @include('includes.script')
</head>
<body>

  @include('includes.header')
  
   <div class="wrapper">

      @include('includes.sidebar')

      <div class="content mb-4" id="content" onclick="sidebar()">

        @yield('maincontent')

        {{-- @include('includes.footer') --}}

      </div>
      
   </div>

   @include('includes.scriptextra')

  @yield('extrascript')

</body>
</html>