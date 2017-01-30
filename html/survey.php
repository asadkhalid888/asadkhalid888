<?php include_once($dir.'html/header.php'); ?>
<?php include_once($dir.'html/footer.php'); ?>
    <div class="container">
      <!-- Main component for a primary marketing message or call to action -->
	  <div class = "row page-border" >
		<div class = "videoWrapper">
		    <div id="player"></div>
		</div>
        
     	<?php if($formTableData['title_label_id'] != 0){ ?> 
        <div class = "product" style='display: none;'>
			<?php  echo $form_data['label'];
         		if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>
         			{<?php echo $form_data['label_id']; ?>, 0} 
		 		<?php } ?> 
        </div>
		<?php } ?> 
		<div class="row form-content">
			
			<form role="form" id = "questionFrm" method = "POST" style="display:none;">
            <?php if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>
			 <input type = "hidden" name = "debug" value = "1" />
			 <?php } ?>
            
			<input type = "hidden" name = "vs_form_id" value = "<?php echo $form_data['vs_form_id']; ?>" />
           	<input type = "hidden" name = "action" value = "survey-submit" />
           	<input type="hidden" name="vs_prospect_id" value="<?php echo $prospectID; ?>">
           	<input type="hidden" name="vs_survey_links_id" value="<?php echo $_SESSION['vs_survey_links_id']; ?>">
           	<?php if($video_db_id != null){ ?>
           		<input type="hidden" name="links_link_id" value="<?php echo $video_db_id; ?>">
		    <?php } ?>
			<div class="col-md-6 form-content">
				
		       <?php if(!empty($form_elm['subsets'])){ 
				
					foreach($form_elm['subsets'] as $indx => $field){
					/*echo "<pre>";
					print_r($field);*/
					 ?>
                    
                    <input type = "hidden" name = "vs_form_set_id_<?php echo $indx+1;?>" value = "<?php echo $field['id']; ?>" />		    
				    <input type = "hidden" name = "fldcount" value = "<?php echo $indx+1;?>" />		
							    
				    <?php if(!empty($field['options'])){ ?>
					<div class="form-group form-backcolor">
						
						<p><?php echo $field['label']; ?></p>
						
						<?php foreach($field['options'] as $idx => $option){ ?>
						
						<?php if($field['displaytype'] == 'select-one'){ ?>						
							<div class="radio ">
								<label ><input type="radio" name="answer_<?php echo $option['parent_id']; ?>" <?php echo $idx === 0 ? 'checked' : ''; ?> value = "<?php echo $option['vs_fu_actions_id']; ?>" ><?php echo $option['label']; ?>
                                <input type = "hidden" name = "vs_fu_surveyactions_id_<?php echo $option['vs_fu_actions_id'];?>" value = "<?php echo $option['id']; ?>" /> 
                                <?php if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>
                                   {<?php echo $option['vs_fu_actions_id']; ?>, <?php echo $option['id']; ?>}
                                <?php } ?>   
                                </label>
							</div>
						<?php } ?>
                        
                        
                        
                        <?php if($field['displaytype'] == 'select-many'){ ?>						
							<div class="radio ">
								<label ><input type="checkbox" name="answer_<?php echo $option['parent_id']; ?>[<?php echo $option['vs_fu_actions_id']; ?>]" <?php echo $idx === 0 ? 'checked' : ''; ?> value = "<?php echo $option['vs_fu_actions_id']; ?>" > <?php echo $option['label']; ?>
                                <?php if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>
                                   {<?php echo $option['vs_fu_actions_id']; ?>, <?php echo $option['id']; ?>}
                                <?php } ?>   
                                </label>
							</div>
						<?php } ?>
						
						
						
						
					<?php } ?>
					</div>
				   <?php }}} ?>				
				
					<div class="form-group form-submit-backcolor"><?php if($formTableData['submit_label_id'] != 0){?><input type = "submit" id = "question_submit" value = "<?php  echo $submit_data['label'];?>" />  
                    <?php if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>{<?php echo $submit_data['label_id']; ?>, 0}<?php } ?> <?php } ?>
                    </div>

			</div>
			<div class="col-md-6">
				<div class="form-group tpadding">
					<?php
					
					 if($formTableData['comment_label_id'] != 0){?>
                    <div class = "comment-label"><?php  echo $comment_data['label'];?>: <?php if(!empty($_SESSION['users']) && !empty($_REQUEST['debug'])){?>{<?php echo $comment_data['label_id']; ?>, 0}<?php } ?> </div>
					<textarea   class = "form-control"  name = "comments" ></textarea>
                    <?php } ?>
				</div>
			</div>
			</form>
		</div><!--- row -->
        <script type="text/javascript">
        function setFormOptions(){
	    	if (formOptions.form_delay !=null) {
		    if($.isNumeric(formOptions.form_delay)){
		    	delayType = "duringPlay";
		    	delayTime = formOptions.form_delay;
		    }else if(formOptions.form_delay === "endofvideo"){
		    	delayType = "endPlay";
		    	delayTime = "";
		    }
			}else{

				delayType = "";
		    	delayTime = "" ;
		    	$('.product').show();
				$('#questionFrm').show();	
			}    
	    }
	    var formOptions = "",delayType="",delayTime="";
		<?php if($formOptions!=""){?>
		    var formOptions = $.parseJSON('<?php echo $formOptions; ?>');
		    console.log(formOptions,'formOptions');
		    setFormOptions();
		    
	    <?php }else{?>
	    		delayType = "";
			    delayTime = "";
			    $('.product').show();
				$('#questionFrm').show();
	       <?php }?>
		
     function getPrepositions(formid ,vs_fu_surveyactions_id, debug, r_type_code){
	 $.ajax({
			url: "index.php",
			type: "post",
			dataType: "json",
			cache: false,
			data: {action : 'survey-getprepositions', formid : formid , vs_fu_surveyactions_id : vs_fu_surveyactions_id, debug: <?php echo $debug; ?>, r_type_code:r_type_code,vs_prospect_id:<?php echo $prospectID; ?>},
			beforeSend: function(){},
			success: function(response){
				
				if(response.responseType == "redirect"){
					window.location.href = response.redirectUrl;
				}

				
				if (response.prepostions.length>5){
					if(response.instructions != null) {
						$('.page-border').html('<div class="alert alert-success">'+response.instructions+'</div>' );
						$('div.page-border').css({'border':'none'});
					
					}else {
						$('.page-border').html('' );
						$('div.page-border').css({'border':'none'});	
					}
				    $("#debugMode").remove();		
				 	$('.page-border').append(response.debugData);
					$('.page-border').append('<div class="prpostionval">'+response.prepostions+'</div>');
					if(response.formOptions!="" && response.formOptions!=null && $.parseJSON(response.formOptions).form_delay!=null){
						formOptions = $.parseJSON(response.formOptions);
						setFormOptions();	
					}else{
						formOptions = "";
						delayType="";
						delayTime="";
						$('.product').show();
						$('#questionFrm').show();
					}
				}else{
					$('.page-border').html('<div class="alert alert-success">Je vous remercie de votre opinion.</div>' );
					$('div.page-border').css({'border':'none'});
				}
				//$('.page-border').append('<div class="prpostionval">'+response.prepostions+'</div>');
				//$('.page-border').html('<div class="alert alert-success">Je vous remercie de votre opinion.</div>' + );
				//$('div.page-border').css({'border':'none'});
				
			},
			error: function(xhr) { // if error occured
				console.log('Error : ' + xhr.statusText);
				//alert('Error : ' + xhr.statusText )
			},
			complete: function(response){
			}
		});
	 }

function prepositionSubmit(){
	
		if($('input[type=checkbox]:checked').length == 0)
		{
			alert('Please select atleast one checkbox');
			return false;
		}
		
		//var surveyData = $('#prepostionFrm').serializeArray();
		
		var str=$('#prepostionFrm input:not([type="checkbox"])').serialize();
		var str1=$("#prepostionFrm input[type='checkbox']:checked").map(function(){return this.name+"="+this.value;}).get().join("&");
		if(str1!="" && str!="") str+="&"+str1;
		else str+=str1; 
		
		var txtCmt = $('#comments').val();
	   	
		var surveyData = str + '&comments=' + txtCmt;

		$.ajax({
			url: "index.php",
			type: "GET",
			cache: false,
			data: surveyData,
			beforeSend: function(){
				
			},
			success: function(response){
				if(response.responseType == "redirect"){
					window.location.href = response.redirectUrl;
				}else{

					var data = $.parseJSON(response)
					
					//alert(data.qusid);
					if(response.instructions != null) {
						$('.page-border').html('<div class="alert alert-success">'+response.instructions+'</div>' );
						$('div.page-border').css({'border':'none'});
					
					}else {
						$('.page-border').html('' );
						$('div.page-border').css({'border':'none'});	
					}
					//window.location = "?a=s&sid="+response.sid+"&Qusid="+response.Qusid;
					//getPrepositions(data.qusid,data.sid);
				}
				
			},
			error: function(xhr) { // if error occured
				console.log('Error : ' + xhr.statusText);
				//alert('Error : ' + xhr.statusText )
			},
			complete: function(response){
				//alert(response);
				// window.location = "html/prepostion.php?id"+response;
			}
		});
		return false;
}
	 
	</script>
	 </div><!-- main-row-->
    </div> <!-- /container -->
    
    
	

    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');
      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          videoId: "<?php echo $video_id; ?>",
		 playerVars: {
                    autoplay: 1,
                    showinfo: 0,
                    rel:0
		  },
		  /*width : '1168',
		  height : '441',*/
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        //event.target.playVideo();
      }

	function onPlayerStateChange(event) {
		var stateCode = event.data;
		switch (stateCode) {
			case YT.PlayerState.UNSTARTED:
				console.log('unstarted');
				break;
			case YT.PlayerState.ENDED:
				console.log('ended');
				if(delayType == "endPlay" && delayTime==""){
						$('.product').show();
						$('#questionFrm').show();	
				}
				break;
			case YT.PlayerState.PLAYING:
				console.log('playing');
				if(delayType == "duringPlay" && delayTime!=""){
					setTimeout(function(){
						$('.product').show();
						$('#questionFrm').show();	
					},parseInt(delayTime)*1000);	
				}
				<?php if(empty($_GET['preview'])){ ?>
				updateWatchStatus();
				<?php } ?>
				break;
			case YT.PlayerState.PAUSED:
				console.log('paused');
				break;
			case YT.PlayerState.BUFFERING:
				console.log('buffering');
				break;
			case YT.PlayerState.CUED:
				console.log('cued');
				break;
			default:
				console.log('unkonwn state');
		}
	}//end  function

      function stopVideo() {
        player.stopVideo();
      }
    </script>

	<script>
	$(document).ready(function(){
	
	
		$("#questionFrm").on("submit", function(){
	
		<?php /*if(!empty($_GET['preview'])){ ?>
			return false;
		<?php } */?>
		
		//alert("test");

		//Code: Action (like ajax...)
		$.ajax({
			url: "index.php",
			type: "post",
			cache: false,
			data: $('#questionFrm').serialize(),
			beforeSend: function(){
				
			},
			success: function(response){
				//alert1(response);
				console.log(response,"response");
			    var data = $.parseJSON(response)
			    if(response.instructions != null) {
						$('.page-border').html('<div class="alert alert-success">'+response.instructions+'</div>' );
						$('div.page-border').css({'border':'none'});
					
					}else {
						$('.page-border').html('' );
						$('div.page-border').css({'border':'none'});	
					}
				//window.location = "?a=s&sid="+response.sid+"&Qusid="+response.Qusid;
				
				if(data.r_type_code=='form' || data.r_type_code=='survey'){
				  getPrepositions(data.r_value, data.vs_fu_surveyactions_id, data.debug, data.r_type_code);
				}
				
			},
			error: function(xhr) { // if error occured
				console.log('Error : ' + xhr.statusText);
				//alert('Error : ' + xhr.statusText )
			},
			complete: function(response){
				//alert(response);
				// window.location = "html/prepostion.php?id"+response;
			}
		});
		return false;
	 });
	
	
	
	
		<?php if(empty($_GET['preview'])){ ?>
		$.ajax({
			url: "index.php",
			type: "post",
			dataType: "json",
			cache: false,
			data: {action : 'survey-viewed', link_id : '<?php echo $_SESSION['vs_survey_links_id']; ?>'},
			beforeSend: function(){
				
			},
			success: function(response){
				console.log(response.msg);
			},
			error: function(xhr) { // if error occured
				console.log('Error : ' + xhr.statusText);
				//alert('Error : ' + xhr.statusText )
			},
			complete: function(response){
			}
		});
		<?php } ?>
	 });

	 function updateWatchStatus(){
		$.ajax({
			url: "index.php",
			type: "post",
			dataType: "json",
			cache: false,
			data: {action : 'survey-watched', link_id : '<?php echo $_SESSION['vs_survey_links_id']; ?>'},
			beforeSend: function(){
				
			},
			success: function(response){
				console.log(response.msg);
			},
			error: function(xhr) { // if error occured
				console.log('Error : ' + xhr.statusText);
				//alert('Error : ' + xhr.statusText )
			},
			complete: function(response){
			}
		});
	 }
</script>


  </body>
</html>
