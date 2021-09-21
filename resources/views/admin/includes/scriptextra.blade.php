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
   });
</script>