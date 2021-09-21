<div style="padding: 5px;background-color:#eee" class="clearfix">
   <form action="{{ route('frontend.product.search') }}" method="GET" class="form-inline float-right">
       From &nbsp;<input type="text" name="start_amount" id="start_amount" >&nbsp;
       To &nbsp;<input type="text" name="end_amount" id="end_amount" >
       <input type="submit" value="Search">
   </form>
</div>