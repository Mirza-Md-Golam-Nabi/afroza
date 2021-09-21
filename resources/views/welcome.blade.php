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
         <h1 class="astyle">Afroza Traders</h1>
         <div style="width:100%; text-align: center;">
            <button data-target="#login" data-toggle="modal" type="button" class="btn btn-primary px-5 py-2 mt-2">Login</button>
         </div>         
      </div>
   </div>

   @include('includes.loginmodal')
   @include('includes.scriptextra')

</body>
</html>