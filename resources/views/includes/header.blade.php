<div class="header">
   <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container-fluid">
         <button type="button" id="sidebarCollapse" class="btn btn-info">
            <i class="fas fa-align-left"></i>
            <span>Menu</span>
         </button>

         <div style="display: table; height:100%;">
            <h3 style="display: table-cell; vertical-align: middle;"><a href="{{ route('admin.dashboard') }}" class="astyle">Afroza Traders</a></h3>
            @auth
               <a href="{{ route('logout') }}" style="margin-left: 5px;"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
               </a>
               <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
               </form>
            @else
                <a data-target="#login" data-toggle="modal" style="cursor: pointer; margin-left: 5px;">{{ __('Login') }}</a>
            @endauth
         </div>
      </div>
   </nav>
 </div>
