<?php include_once($dir.'html/header.php'); ?>
<div class="container">
<div class="col-lg-9">
			<!-- Modal content-->
			<form class="form-inline" id = "loginForm" action="login.php" method="post">
			  <input type = "hidden" name = "action" value = "login" />
			  <div class="form-group">
					<label>User Name</label>
					<input type="type" class="form-control"  id = "user_name" name = "user_name"  data-validation="required" >
			  </div>
              
               <div class="form-group">
					<label>Password</label>
					<input type="password" class="form-control"  id = "user_pass" name = "user_pass"  data-validation="required" >
			  </div>
              
			
             <button type="submit" class="btn btn-default" id = "send-video">Login</button>
			</form>
         </div> 
         </div>  
            
	
        
      
        	<?php include_once($dir.'html/footer.php'); ?>

		