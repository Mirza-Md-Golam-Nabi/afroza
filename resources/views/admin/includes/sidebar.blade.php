<nav id="sidebar">
   <ul class="list-unstyled components">
      <p style="text-align: center;">
         <a href="{{ route('admin.dashboard') }}">Afroza Traders</a>
         @if(isset(Auth::user()->id))
            <br>
            <span>User - {{ Auth::user()->mobile }}</span>
         @endif
      </p>
      <li>
         <a href="#type" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Create</a>
         <ul class="collapse list-unstyled" id="type">
            <li><a href="{{ route('admin.type.list') }}">Type</a></li>
            <li><a href="{{ route('admin.brand.list') }}">Brand</a></li>
            <li><a href="{{ route('admin.category.list') }}">Category</a></li>
            <li><a href="{{ route('admin.product.list') }}">Product</a></li>
            <li><a href="{{ route('admin.stock.add') }}">Price Add</a></li>
         </ul>
      </li>
      <li>
        <a href="#price" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Price</a>
        <ul class="collapse list-unstyled" id="price">
           <li><a href="{{ route('admin.others.upcoming.price') }}">Upcoming Price</a></li>
           <li><a href="{{ route('admin.others.previous.price') }}">Previous Price</a></li>
        </ul>
     </li>
      <li>
         <a href="#stock" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Stock</a>
         <ul class="collapse list-unstyled" id="stock">
            <li><a href="{{ route('admin.stock.current') }}">Current Stock</a></li>
            <li><a href="{{ route('admin.stockin.create') }}">Stock-in</a></li>
            <li><a href="{{ route('admin.stockin.date') }}">Stock-in History</a></li>
            <li><a href="{{ route('admin.stockout.create') }}">Stock-out</a></li>
            <li><a href="{{ route('admin.stockout.date') }}">Stock-out History</a></li>
         </ul>
       </li>
       <li>
         <a href="#report" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Report</a>
         <ul class="collapse list-unstyled" id="report">
            <li><a href="{{ route('admin.report.date') }}">Daily Report</a></li>
            <li><a href="{{ route('admin.report.weekly') }}">Weekly Report</a></li>
            <li><a href="{{ route('admin.report.last.3.month') }}">Last 3 Month Report</a></li>
            <li><a href="{{ route('admin.report.product.list') }}">Monthly Report</a></li>
            <li><a href="{{ route('admin.report.monthly.profit', ['year'=>date('Y')]) }}">Monthly Profit</a></li>
            <li><a href="{{ route('admin.report.yearly') }}">Yearly Report</a></li>
            @foreach(SessionController::brandList() as $brand)
            <li><a href="{{ route('admin.report.company',['name'=>$brand->brand_name]) }}">{{ $brand->brand_name." Report" }}</a></li>
            @endforeach
         </ul>
       </li>
      {{-- <li><a href="{{ route('admin.interested.list') }}">Interested List &nbsp;<span class="badge badge-danger badge-pill" id="inns">{{ SessionController::adminPanel()['interest'] }}</span></a></li>
      <li><a href="{{ route('admin.product.special.list') }}">Special Bike &nbsp;<span class="badge badge-danger badge-pill" id="inns">{{ SessionController::adminPanel()['special'] }}</span></a></li>
      <li><a href="{{ route('admin.report.sell.product.list') }}">Sell Product List</a></li> --}}
   </ul>
 </nav>
