<nav id="sidebar">
  <ul class="list-unstyled components">
    <p style="text-align: center;">
      <a href="{{ route('admin.dashboard') }}">Afroza Traders</a>
      @auth
        <br>
        <span>User - {{ auth()->user()->mobile }}</span>
      @endauth
    </p>
    <li>
      <a href="#type" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Create</a>
      <ul class="collapse list-unstyled" id="type">
         <li><a href="{{ route('types.index') }}">Type</a></li>
         <li><a href="{{ route('categories.index') }}">Category</a></li>
         <li><a href="{{ route('brands.index') }}">Brand</a></li>
         <li><a href="{{ route('products.index') }}">Product</a></li>
      </ul>
    </li>
    <li>
      <a href="#stockin" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Stockin</a>
      <ul class="collapse list-unstyled" id="stockin">
         <li><a href="{{ route('admin.stockin.create') }}">Stockin</a></li>
      </ul>
    </li>
     {{-- {!! SessionController::list() !!} --}}
  </ul>
</nav>
