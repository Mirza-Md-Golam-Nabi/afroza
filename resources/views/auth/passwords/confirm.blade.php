<!DOCTYPE html> 
<html lang="en" style="height: 100%;">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Afroza Traders</title>   
   @include('includes.style') 
   @include('includes.script')
</head>
<body style="height: 100%;">
   <div class="d-flex justify-content-center align-items-center" style="height: 100%">      
      <div>
        <form action="{{ route('user.new.password.store') }}" method="post">
            @csrf
            <input type="hidden" name="mobile" value="{{ $mobile }}">
            <div class="modal-body">
               <div style="text-align: center;margin-top:50px;">
                  <img src="{{ asset('uploads/image/login.png') }}" alt="img" style="width: 35%; border-radius: 50%;">
               </div>
               
               <div style="margin-top:25px;">
                  <label>Information</label>
                  <div class="form-group">
                     <input type="password" inputmode="numeric" class="form-control" name="password" id="newpass" placeholder="New PIN Number" autocomplete="off" autofocus required>
                   </div>
                   <div class="form-group">
                    <input type="password" inputmode="numeric" class="form-control" name="confirmpass" id="confirmpass" placeholder="Confirm PIN Number" autocomplete="off" required>
                  </div>
                  <small id="pass" class="text-danger"></small>
               </div>
            </div>
            <div class="modal-footer">
               <input type="submit" class="btn btn-success" value="Change" id="change" disabled>
            </div>
         </form>         
      </div>
   </div>

   @include('includes.scriptextra')

</body>
</html>