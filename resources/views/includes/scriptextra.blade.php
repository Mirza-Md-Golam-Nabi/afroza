<script>

  function sidebar(){
      var sidebar = document.getElementById('sidebar').className;
      var screenWidth = document.body.clientWidth;
      if(screenWidth <= 992){
         if(sidebar == 'active'){
            $("#sidebar").toggleClass('active');
            $("#content").toggleClass('active');
         }        
      }      
   }

  $(document).ready(function(){
    $("#sidebarCollapse").click(function(){
      $("#sidebar").toggleClass('active');
      $("#content").toggleClass('active');
    });

    $("#mobileNumber").on('keyup', function(){
      var mobileNumber = document.getElementById('mobileNumber').value.length;
      if(mobileNumber == 11){
        document.getElementById('loginSubmitButton').disabled = false;
      }else{
        document.getElementById('loginSubmitButton').disabled = true;
      }
    });

    $("#mobileNumber").on('blur', function(){
      var mobileNumber = document.getElementById('mobileNumber').value.length;
      if(mobileNumber == 11){
        document.getElementById('loginSubmitButton').disabled = false;
      }else{
        document.getElementById('loginSubmitButton').disabled = true;
      }
    });

    $("#mobile").on('keyup', function(){
      var mobileNumber = document.getElementById('mobile').value;
      var mobileLength = mobileNumber.length;
      if(mobileLength == 11){
        $.ajax({
          url: "{{ route('user.mobile.check') }}?mobile="+mobileNumber,
          method: 'GET',
          success: function(data){
            if(data == 1){
              document.getElementById('search').disabled = false;
              $('#msg').text('');
            }else{
              $('#msg').text('Mobile Number does not Match.');
            }
          }
        });        
      }else{
        $('#msg').text('');
        document.getElementById('search').disabled = true;
      }
    });

    $("#confirmpass, #newpass").on('keyup', function(){
      var newPass = document.getElementById('newpass').value;
      var conPass = document.getElementById('confirmpass').value;
      if((newPass.length > 0) && (conPass.length > 0)){
        if((newPass == conPass) && (newPass.length < 5)){
          $('#pass').text('Password must be at least 5 character.');
          document.getElementById('change').disabled = true;
        }else if((newPass == conPass) && (newPass.length >= 5)){
          $('#pass').text('');
          document.getElementById('change').disabled = false;
        }else if((newPass != conPass) && ((newPass.length >= 5) && (conPass.length >= 5))){
          $('#pass').text('Password does not match.');
          document.getElementById('change').disabled = true;
        }else{
          $("#pass").text('');
          document.getElementById('change').disabled = true;
        }
      }else{
        document.getElementById('change').disabled = true;
        $("#pass").text('');
      }
    });
  });
  
 </script>