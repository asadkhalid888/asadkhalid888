<?php
header("Content-Type: text/html;charset=utf-8");
error_reporting(E_ALL);
session_start();
ini_set('display_errors',0);
ini_set('max_execution_time', 0);
define(ADMIN_PASSWORD,'test@dmin');
define(EMAIL_SUBJECT, 'Video Survey');
//define(EMAIL_FROM,'admin@jstore.one');

//echo __FILE__;
$site_url = 'http://'.$_SERVER['HTTP_HOST'].'/video-survey/';
$site_url = 'http://j-vids.com/v2/';

$dir = dirname(__FILE__).'/';
$email_message = file_get_contents($dir.'email_message.php');
define(EMAIL_MESSAGE,$email_message);



array_walk($_POST, 'escapeString');
array_walk($_GET, 'escapeString');
require_once($dir.'db.php');
require_once($dir.'model.php');
$model= new Model();
$surveyList = $model->getSurveyMaster();
$device =  isMobile();

//die($_SERVER['QUERY_STRING'] ."||". !(strpos($_SERVER['QUERY_STRING'],'=')) . "//" . (strlen($_SERVER['QUERY_STRING'])>0 && !strpos($_SERVER['QUERY_STRING'],'=')));
//[rc-20161218: default behavior
if (strlen($_SERVER['QUERY_STRING'])>0 && !strpos($_SERVER['QUERY_STRING'],'=')){
	//assume querystring is ID and action =s
	$_REQUEST['a'] = 's';
	
	
	 $query_string = explode("/",$_SERVER['QUERY_STRING']);
	 $_REQUEST['sid'] = $query_string[0];
	 $_GET['sid'] = $query_string[0];
	 $_REQUEST['lang'] = $query_string[1];
	
}
//rc]



//$device =  'android';

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout'){
	//$_SESSION['users'] = '';
	require_once('logout.php');
	//echo 'Logged out successfully!';
	exit;

}else if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'log'){
	//$survey_id = $_GET['sid'];
	require_once('check_login.php');
	$redirectUrl = $dir.'template/login.php';
	require_once($redirectUrl);


}

else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'survey-viewed'){
	$link_id = $_POST['link_id'];
	$model->addViewCount($link_id);
	echo json_encode(array('msg' => 'added'));
	exit;
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'survey-submit'){
	
	
	//$link_id = $_POST['link_id'];
	$result = $model->addSurveySubmit($_POST);
	
    $result['debug'] = $_POST['debug'] ? $_POST['debug'] : '';
	
	echo json_encode($result);
	exit;
	//print_r($result);
	
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'survey-getprepositions'){
	
	
/*	
	$sd = explode('-',$_REQUEST['sid']);
	$sid = $sd[1];
	if($qusid){
	$result = $model->getPrepostion($qusid);*/
	$formID = $_POST['formid'];	
	$prospectID = $_POST['vs_prospect_id'];	
	$video_id = null;
	$video_db_id = null;
	if($_POST['r_type_code'] == 'survey'){		
		
		$formSelectValue='`vs_form_id`,`video_id`';
		$formTableName = 'links';
		$formWhereCondtion='`id` = '.$_POST['formid'];
		
		$formTableData = $model->selectTableData($formSelectValue, $formTableName, $formWhereCondtion);
		
		$formID = $formTableData['vs_form_id'];
		$video_id = $formTableData['video_id'];
		$videoSelectValue='id as videoID';
		$videoTableName = 'links';
		$videoWhereCondtion='video_id = "'.$video_id.'"';
		
		$videoTableData = $model->selectTableData($videoSelectValue, $videoTableName, $videoWhereCondtion);
		

		$video_db_id = $videoTableData['videoID'];
		
	} 
	if($formID){

	//$formData = $model->getFormData($formid);
		$formOptions = "";
		if($video_id!=null){
			$formDetails = $model->getFormDetails($formID);
			$formOptions = $formDetails['options']!=null ? $formDetails['options'] : "";	
		}

		$formData = $model->getFormData1($formID);
		for($i=0; $i<count($formData); $i++){
				//echo "<pre>";
				if($formData[$i]['role'] == 'title'){
					$form_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'submit'){
					$submit_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'comment'){
					$comment_data = $formData[$i] ;
				}
				
				if($formData[$i]['role'] == 'instructions'){
					$instructions_data = $formData[$i];
				}
		}
		
		
		$formElementData = $model->getFormDataElement($formID);
		$k =0;
		
		for($i=0; $i<count($formElementData); $i++){
				//echo "<pre>";
				if($formElementData[$i]['type'] == 'set'){
					$form_elm['subsets'][$k] = $formElementData[$i];
					$k++;
				}
				
				if($formElementData[$i]['role'] == 'option' && $formElementData[$i]['type'] == 'elm'){
					$form_elm['subsets'][$k-1]['options'][] = $formElementData[$i];
				}
				
				
		}

		$formSelectValue='`title_label_id`, `instructions_label_id`, `submit_label_id`, `comment_label_id`';
		$formTableName = 'vs_form';
		$formWhereCondtion='`vs_form_id` = '.$formID;
		
		$formTableData = $model->selectTableData($formSelectValue, $formTableName, $formWhereCondtion);
		ob_start();
		$debugDataStr = "";
		$debug = 0;
		if(isset($_SESSION['users']) && count($_SESSION['users']) && isset($_POST['debug']) && $_POST['debug']=="1" ){
			$debug=$_POST['debug'];
			$debugData = [];
			$debugData["form_id"] = $formID;
			$debugData["labels"] = $formElementData;
			$debugDataStr .=  "<div id ='debugMode'><pre>";
			$debugDataStr .= print_r($debugData,true);
			$debugDataStr .= "</pre><br/><br/></div>";
		}

		if($_POST['r_type_code'] == 'form'){	

			$form_setSelectValue='role, html';
			$form_setTableName = 'vs_form_set';
			$form_setWhereCondtion='`vs_form_id` = '.$formID;
			
			$formSetData = $model->selectTableData($form_setSelectValue, $form_setTableName, $form_setWhereCondtion);
			if($formSetData['role'] == 'redirect'){
				$prospectID = '';
				if(isset($_GET['vs_prospect_id'])){
					$prospectID = $_GET['vs_prospect_id'];
				}elseif(isset($_POST['vs_prospect_id'])){
					$prospectID = $_POST['vs_prospect_id'];
				}

				if($prospectID !=""){
					$form_setSelectValue='vs_prospect_id, firstname, lastname, phone, email';
					$form_setTableName = 'vs_prospect';
					$form_setWhereCondtion='`vs_prospect_id` = '.$prospectID;
					
					$formProspectData = $model->selectTableData($form_setSelectValue, $form_setTableName, $form_setWhereCondtion);

					$redirectUrl = strip_tags($formSetData['html']);
					$redirectUrl = str_replace("{vs_prospect.vs_prospect_id}", $formProspectData['vs_prospect_id'], $redirectUrl);
					$redirectUrl = str_replace("{vs_prospect.lastname}", $formProspectData['lastname'], $redirectUrl);
					$redirectUrl = str_replace("{vs_prospect.firstname}", $formProspectData['firstname'], $redirectUrl);
					$redirectUrl = str_replace("{vs_prospect.phone}", $formProspectData['phone'], $redirectUrl);
					$redirectUrl = str_replace("{vs_prospect.email}", $formProspectData['email'], $redirectUrl);
					$redirectUrl = html_entity_decode($redirectUrl);

					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						echo json_encode(array('responseType' => "redirect", 'redirectUrl'=>$redirectUrl));			   
						die(); 
					}else{
						header('Location: '.$redirectUrl);
						die();
					}
				}else{
					echo "Forbidden";
					die();
				}
			}
		}

		require_once($dir.'html/prepostion.php');
	    $outhtm = ob_get_contents();
	    ob_end_clean();
		echo json_encode(array('formid' => $formID, 'prepostions' => $outhtm,'debugData'=>$debugDataStr,'instructions'=>$instructions_data['label'] , 'formOptions'=>$formOptions));
		
		}	
		exit;
	
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'setprepostion'){	
	$setperpostion = $model->setperpostion($_REQUEST);
	echo json_encode(array('msg' => 'success'));
	exit; 
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'appointment'){		
	require_once($dir.'html/appointment.php');
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'appointment2'){		
	require_once($dir.'html/appointment2.php');		
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'appointment-submit'){	
	$result = $model->addAppointmentSubmit($_POST);
	
	echo json_encode( array_merge ( $result, $_REQUEST ) );
	exit;
	
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'survey-watched'){
	$link_id = $_POST['link_id'];
	$model->addWatchCount($link_id);
	echo json_encode(array('msg' => 'survey-watched'));
	exit;
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'save-link'){
	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST['video_id'], $match)) {
		$video_id = $match[1];
	}else{
		$video_id = $_POST['video_id'];
	}
	$_POST['video_id'] = $video_id;
	$model->generateLink($_POST);
	$result = $model->listLinks();
	$_SESSION['msg']  = 'Video added successfully.';
	header('Location: index.php');
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'reports'){
	$video_id = $_GET['id'];
	if(empty($video_id)){echo 'Forbidden';exit;}
	//if(!$model->isReportAccess($video_id)){echo 'Forbidden';exit;}
	$result = $model->getVideoReport($video_id);
	
	$originalVideoID = $model->getOriginalVideoID($video_id);
	$recepientsDataResult = $model->recepientsData($video_id);
	$templateList = $model->templateList($_SESSION['users']['vs_users_id'], $_SESSION['users']['vs_team_id']);
	require_once($dir.'html/reports.php');
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add-recepients'){
	$model->addRecepients($_POST);
	echo json_encode(array('msg' => 'success'));exit;
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'send-video'){
	$addedUserData = $model->addUser($_POST);
	echo json_encode(array('msg' => 'success', 'userData' => $addedUserData ));exit;
}else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'send-mail'){
	//print_r($_POST);exit;
	sendMail($_POST);
	echo json_encode(array('msg' => 'success'));exit;
}else if(isset($_REQUEST['a']) && $_REQUEST['a'] == 's'){
	//$survey_id = $_GET['sid'];
	
	if(isset($_REQUEST['lang']) && !empty($_REQUEST['lang'])) 
		$_SESSION['lang'] = $_REQUEST['lang'];
	else 
		$_SESSION['lang'] = '';
	$surveyRec = $model->isValidSurveyID($_GET['sid']);
	if(isset($_GET['sid']) && $surveyRec != null ){
		$vs_survey_links_id = $surveyRec['survey_link_id'];
		$_SESSION['vs_survey_links_id'] = $vs_survey_links_id;
		$video_id = $surveyRec['video_id'];
		$link = explode('-',$_GET['sid']);
		$videoSelectValue='id as videoID';
		$videoTableName = 'links';
		$videoWhereCondtion='video_id = "'.$video_id.'"';
		
		$videoTableData = $model->selectTableData($videoSelectValue, $videoTableName, $videoWhereCondtion);
		

		$video_db_id = $videoTableData['videoID'];
		
		$formID = $model->getFormID($_GET['sid']);
		

		$surveyLinkDetail = $model->getSurveyLinkDetails($_GET['sid']);
		if(isset($surveyLinkDetail['vs_lang_code']) && !empty($surveyLinkDetail['vs_lang_code'])) 
			$_SESSION['vs_lang_code'] = $surveyLinkDetail['vs_lang_code'];
		else 
			$_SESSION['vs_lang_code'] = '{vs_lang_code}';

		$prospectID = $model->getProspectID($_GET['sid']);
		$formDetails = $model->getFormDetails($formID);
		$formOptions = $formDetails['options']!=null ? $formDetails['options'] : "";	
		//echo "<br>"; print_r($formOptions);exit;
		$formData = $model->getFormData1($formID);
		for($i=0; $i<count($formData); $i++){
				if($formData[$i]['role'] == 'title'){
					$form_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'submit'){
					$submit_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'comment'){
					$comment_data = $formData[$i] ;
				}
				
				if($formData[$i]['role'] == 'instructions'){
					$instructions_data = $formData[$i];
				}
		}
		
		
		$formElementData = $model->getFormDataElement($formID);
	
		$k =0;
		
		for($i=0; $i<count($formElementData); $i++){
				if($formElementData[$i]['type'] == 'set'){
					$form_elm['subsets'][$k] = $formElementData[$i];
					$k++;
				}
				
				if($formElementData[$i]['role'] == 'option' && $formElementData[$i]['type'] == 'elm'){
					$form_elm['subsets'][$k-1]['options'][] = $formElementData[$i];
				}
								
		}
		
		$formSelectValue='`title_label_id`, `instructions_label_id`, `submit_label_id`, `comment_label_id`';
		$formTableName = 'vs_form';
		$formWhereCondtion='`vs_form_id` = '.$formID;
		
		$formTableData = $model->selectTableData($formSelectValue, $formTableName, $formWhereCondtion);
		
		$surveyprospectdata =$model->getServeprsopectdata($_GET['sid'], $link['1']);
		$debug = 0;
		if(isset($_SESSION['users']) && count($_SESSION['users']) && isset($_GET['debug']) && $_GET['debug']=="1" ){
			$debug = $_GET['debug'];
			$debugData = [];
			$debugData["form_id"] = $formID;
			$debugData["labels"] = $formElementData;
			echo "<div id ='debugMode'><pre>";
			print_r($debugData);
			echo "</pre><br/><br/></div>";
		}
		require_once($dir.'html/survey.php');
	}else{
		echo "Forbidden";
		exit;
	}

}else if(isset($_REQUEST['a']) && $_REQUEST['a'] == 'form'){
	//$survey_id = $_GET['sid'];
	//echo $formData = $model->getFormData($_GET['fid']);
	//die;
		
		
	if(isset($_GET['fid']) && $_GET['fid']!=""){
		$formData = $model->getFormData1($_GET['fid']);
		
		for($i=0; $i<count($formData); $i++){
				//echo "<pre>";
				if($formData[$i]['role'] == 'title'){
					$form_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'submit'){
					$submit_data = $formData[$i];
				}
				
				if($formData[$i]['role'] == 'comment'){
					$comment_data = $formData[$i] ;
				}
				
				if($formData[$i]['role'] == 'instructions'){
					$instructions_data = $formData[$i];
				}
		}
		
		
		$formElementData = $model->getFormDataElement($_GET['fid']);
		//echo "<pre>";
		//print_r($formElementData);
		
		$k =0;
		
		for($i=0; $i<count($formElementData); $i++){
				//echo "<pre>";
				if($formElementData[$i]['type'] == 'set'){
					$form_elm['subsets'][$k] = $formElementData[$i];
					$k++;
				}
				
				if($formElementData[$i]['role'] == 'option' && $formElementData[$i]['type'] == 'elm'){
					$form_elm['subsets'][$k-1]['options'][] = $formElementData[$i];
				}
				
				
		}
				
		$formSelectValue='`title_label_id`, `instructions_label_id`, `submit_label_id`, `comment_label_id`';
		$formTableName = 'vs_form';
		$formWhereCondtion='`vs_form_id` = '.$_GET['fid'];
		
		$formTableData = $model->selectTableData($formSelectValue, $formTableName, $formWhereCondtion);
		
		
		$form_setSelectValue='role, html';
		$form_setTableName = 'vs_form_set';
		$form_setWhereCondtion='`vs_form_id` = '.$_GET['fid'];
		
		$formSetData = $model->selectTableData($form_setSelectValue, $form_setTableName, $form_setWhereCondtion);

		$debug = 0;
        if(isset($_SESSION['users']) && count($_SESSION['users']) &&  isset($_GET['debug']) && $_GET['debug']=="1" ){
			$debug = $_GET['debug'];
			$debugData = [];
			$debugData["form_id"] = $_GET['fid'];
			$debugData["labels"] = $formElementData;
			echo "<div id ='debugMode'><pre>";
			print_r($debugData);
			echo "</pre><br/><br/></div>";
		}
        $redirectUrl = $dir.'html/form.php';
        
		if($formSetData['role'] == 'redirect'){
			$form_setSelectValue='vs_prospect_id, firstname, lastname, phone, email';
			$form_setTableName = 'vs_prospect';
			$form_setWhereCondtion='`vs_prospect_id` = '.$_GET['vs_prospect_id'];
			
			$formProspectData = $model->selectTableData($form_setSelectValue, $form_setTableName, $form_setWhereCondtion);

			$redirectUrl = strip_tags($formSetData['html']);
			$redirectUrl = str_replace("{vs_prospect.vs_prospect_id}", $formProspectData['vs_prospect_id'], $redirectUrl);
			$redirectUrl = str_replace("{vs_prospect.lastname}", $formProspectData['lastname'], $redirectUrl);
			$redirectUrl = str_replace("{vs_prospect.firstname}", $formProspectData['firstname'], $redirectUrl);
			$redirectUrl = str_replace("{vs_prospect.phone}", $formProspectData['phone'], $redirectUrl);
			$redirectUrl = str_replace("{vs_prospect.email}", $formProspectData['email'], $redirectUrl);
			$redirectUrl = html_entity_decode($redirectUrl);
			header('Location: '.$redirectUrl);
			die();
		}
		
		require_once($redirectUrl);
	}else{
		echo "Forbidden";
		exit;
	}


}else{
	if(empty($_GET['u'])  || empty($_GET['p'])){
		if(empty($_SESSION['users'])){
			echo 'Forbidden';exit;
		}
	}else{
		$username = $_GET['u'];$password = $_GET['p'];
		$user = $model->checkUser($username,$password);
		if(is_array($user)){
			$_SESSION['users'] = $user;
		
		}else{
			echo 'Forbidden';exit;
		}
	}
	$result = $model->listLinks();
	
	$propactresult = $model->prospactData($_SESSION['users']['vs_users_id']);
	
	$templateList = $model->templateList($_SESSION['users']['vs_users_id'], $_SESSION['users']['vs_team_id']);
	
	
	require_once($dir.'html/home.php');
}


function escapeString(&$item1, $key)
{
	$item1 = addslashes($item1);
}


function sendMail($data){
	extract($data);
	$to = $email;
	$subject = $subject;
	$txt = nl2br($message);
	

	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	//$headers .= "From: admin@jstore.one" . "\r\n" .
	//$headers .= "From: admin@jstore.one" . "\r\n" .
	//$headers .= "Bcc: responsemee@gmail.com";
	mail($to,$subject,$txt,$headers);
}


function isMobile(){
	//Detect special conditions devices
	$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
	$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
	$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
	if($Android)return 'android';
	else if( $iPod || $iPhone || $iPad)return 'iphone';
	else return 'desktop';
}

?>