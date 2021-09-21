
<div class="header">
   <nav class="navbar navbar-expand-lg navbar-light">
      <div class="d-flex justify-content-between container-fluid">
         <button type="button" id="sidebarCollapse" class="btn btn-info">
            <i class="fas fa-align-left"></i>
            <span>Menu</span>
         </button>
 
         <div style="display: table; height:100%;">
            <h3 style="display: table-cell; vertical-align: middle;"><a href="{{ route('admin.dashboard') }}" class="astyle">Afroza Traders</a></h3>
         </div>
         <div>
            @if(isset(Auth::user()->id) && !empty(Auth::user()->id))
               <a href="{{ route('logout') }}" style="margin-left: 5px;"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
               </a>
               <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
               </form> 
            @else
               <a data-target="#login" data-toggle="modal" style="cursor: pointer;">{{ __('Login') }}</a>
            @endif
         </div>
      </div> 
   </nav>      
 </div>



                