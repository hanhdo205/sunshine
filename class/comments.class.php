<?php
include str_replace('\\','/',dirname(__FILE__)).'/defineConst.php';
$currentlang = '';
// Include Language file
session_start();
if(isset($_SESSION['language'])){
	//include str_replace('\\','/',dirname(__FILE__))."/../modum/languages/lang_".$_SESSION['language'].".php";
}else{
	//include str_replace('\\','/',dirname(__FILE__))."/../modum/languages/lang_en.php";
}
/**
* This will generate a comments system for you in no time...
* It includes an installed build in so all you have to do is upload the class file run a few commands
* 		and you are ready to go :)
* The intention is to make this a stand alone class so we are using ``mysqli`` to handle the mysql interactions
* 	therefor you will need to pass your mysql details also
* @author  Mihai Ionut Vilcu (ionutvmi@gmail.com)
* July-2013
*/
class Comments_System
{
	/**
	 * Default settings
	 * @var array
	 */
	var $settings = array(
			'comments_table' => 'user_comments', // the name of the table in which the comments will be hold
			'banned_table' => 'user_banned', // the name of the table in which the comments will be hold
			'auto_install' => true, // if the class is not already installed it will attempt to install it
			'public' => true, // if true unregistered users are allowed to post a comment
			'optional_email' => false, // if true users don't need to enter a valid email
			'isAdmin' => false, // if true some extra options are displyed delete
			'adminStyle' => array( // special formating to admin messages
				'username' => 'color: #051633; font-weidth: bold;', 
				//'box' => 'background-color: #FFFCDD'
			),
			'user_details' => array( // if public is false we use this user details to the added message
				'name' => 'anonymous',
				'email' => 'not_reply@gmail.com', 
				),
			'sort' => 'ORDER BY `id` DESC', // the sort, this is pasted as is so make sure it is parsed
		);
	var $connect; // it will hold the mysql connection
	var $error = false; // it will hold an error message if any
	var $success = false; // it will hold an success message if any
	var $tmp = false; // it will carry some temporary data
	var	$ignore = array('comm_edit', 'comm_del', 'comm_reply', 'comm_ban', 'comm_unban'); // keep the url clean
	var $checked_ips = array(); // will hold the ips checked if banned or not
	
	

	/**
	 * setup the mysql connction and some settings
	 * @param array $db_details contains the database details
	 * @param array  $settings   settings to overwrite the default ones
	 */
	function __construct( $settings = array())  {
		if(session_id() == '')
			session_start();

		// we first manage the mysql connection
		$this->connect = @mysqli_connect(HOSTADDRESS,DBACCOUNT,DBPASSWORD);

		if (!$this->connect) die('Connect Error (' . mysqli_connect_errno() . ') '.mysqli_connect_error());

		mysqli_select_db($this->connect, DBNAME) or die(mysqli_error($this->connect));

		// we add the new settings if any
		$this->settings = array_merge($this->settings, $settings);

		$this->settings['comments_table'] = str_replace("`", "``", $this->settings['comments_table']);

		// auto install
		if($this->settings['auto_install'] && !@mysqli_num_rows(mysqli_query($this->connect, "SELECT `id` FROM `".$this->settings['comments_table']."`")))
			$this->install();


		// edit comment
		if(isset($_POST['comm_edit']) && ($comm = mysqli_query($this->connect, "SELECT * FROM `".$this->settings['comments_table']."` 
				WHERE `id`='".(int)$_POST['comm_edit']."'")))
		{
			// if the comment exists and the user has the rights to edit it
			if(mysqli_num_rows($comm) && $this->hasRights(mysqli_fetch_object($comm))) {
				$this->grabComment($_SESSION['comm_pageid'], (int)$_POST['comm_edit']);
				$this->tmp = "edited";
			}
		}


		// delete comment
		if(isset($_POST['comm_del']))
			if($this->delComm($_POST['comm_del']))
				$this->success = _COMMENTDELETE;

		// bann ip
		if( isset($_GET['comm_ban']) && $this->banIP($_GET['comm_ban']) ) // we banned the ip
			$this->success = _IPBAN;
		
		// UnBann ip
		if( isset($_GET['comm_unban']) && $this->unBanIP($_GET['comm_unban']) )
			$this->success = _IPUNBAN;

		return true;
	}



	function grabComment($pageid, $update_id = false) {
		if(session_id() == '')
			session_start();

		$_SESSION['comm_pageid'] = $pageid;

		// we make sure it's a valid post
		if(isset($_POST['comm_submit']) && isset($_SESSION['comm_token']) && ($_POST['comm_token'] === $_SESSION['comm_token']) &&
			($this->tmp != 'edited')) { // we make sure we don't handle the data again if it was an edit

			$name = isset($_POST['comm_name']) ? $_POST['comm_name'] : '';
			$email = isset($_POST['comm_email']) ? $_POST['comm_email'] : '';
			
			$min = 2;

			if( !$this->settings['public'] ) { // if it's not public we use the provided details
				// if it's not public and it has $_POST[name] something is wrong
				if(isset($_POST['comm_name'])){
					$this->error = "Something is wrong !";
					return false;
				}

				$min = 0;
				$name = $this->settings['user_details']['name'];
				$email = $this->settings['user_details']['email'];

			}

			if($this->isBanned($this->ip())) {
				$this->error = _YOUAREBAN;
				return false;
			}

			if(!isset($name[$min])){
				$this->error = "Invalid name !";
				return false;
			}
			$message = $_POST['comm_msg'];
			
			/*if(!isset($message[2])) {
				$this->error = _INVALIDMESSAGE;
				return false;
			}*/


			// we check in case the email is not valid
			if(!$this->settings['optional_email']) 
				if(!$this->isValidMail($email)) {
					$this->error = "Invalid email !";
					return false;
				}


			// we check if it's an update or a new message 
			if($update_id) {
				if( $this->settings['public'] )
					$upd_fields = ",`name` = '".mysqli_real_escape_string($this->connect, $name)."',
									`email` = '".mysqli_real_escape_string($this->connect, $email)."'";
				else			
					$upd_fields = '';


				if(mysqli_query($this->connect, "UPDATE `".$this->settings['comments_table']."` SET 
					`message` = '".mysqli_real_escape_string($this->connect, $message)."'
					$upd_fields
					WHERE `id` = '".(int)$update_id."'")) {

						$this->success = _COMMENTEDITSUCCESS;
						return true;
				}
			}


			// we check if this is a valid reply
			if(isset($_POST['comm_reply']) && 
				mysqli_num_rows(mysqli_query($this->connect, "SELECT `id` FROM `".$this->settings['comments_table']."` 
					WHERE `id`= '".(int)$_POST['comm_reply']."' AND `parent`  = '0'")))					
					$reply = ",`parent` = '".(int)$_POST['comm_reply']."'";
				else
					$reply = ",`parent` = '0'";



			if(mysqli_query($this->connect, "INSERT INTO `".$this->settings['comments_table']."` SET 
				`member_id` = '".$_SESSION["member_id"]."',
				`name` = '".mysqli_real_escape_string($this->connect, $name)."',
				`message` = '".mysqli_real_escape_string($this->connect, $message)."',
				`time` = '".time()."',
				`ip` = '".mysqli_real_escape_string($this->connect, $this->ip())."',
				`email` = '".mysqli_real_escape_string($this->connect, $email)."',
				`browser` = '".mysqli_real_escape_string($this->connect, $_SERVER['HTTP_USER_AGENT'])."',
				`pageid` = '".mysqli_real_escape_string($this->connect, $pageid)."',
				`isadmin` = '".(int)$this->settings['isAdmin']."',
				`read_list` = '".$_SESSION["member_id"]."'
				$reply
				")){
				$_SESSION['comm_last_id'] = mysqli_insert_id($this->connect);
				$this->success = _COMMENTPOSTSUCCESS;
				
					$dbf_cmnt = new BUSINESSLOGIC();
					$arrayMemberCurrent= array();
					$arrayMemberCurrent = $dbf_cmnt->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
					
					
					
					$subject ="Neonagashima new comment posted";

					$headers= "From: lienhe@neonaga-sys.com.vn\n";
					$headers.= "MIME-Version: 1.0\n";
					$headers.= "Content-Type: text/html; charset=\"UTF-8\"\n";
					
					$message  = $name . ' posted a new comment<br/>';
					$message  .= '<a href="'.HOST.'ranking.aspx#'.$_SESSION['comm_last_id'].'">View the comment</a>';
					
					  include("modum/class.phpmailer.php");
					  $mail = new PHPMailer();
					  
					  global $arraySMTPSERVER;
					  $from = $arraySMTPSERVER["user"];
					  $fromName = $arraySMTPSERVER["user"];

					  $mail->ContentType = 'text/html';
					  $mail->IsSMTP();
					  $mail->CharSet="UTF-8";
					  $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
					  $mail->SMTPAuth = true; // authentication enabled
					  $mail->SMTPSecure = 'tsl';//Set the encryption system to use - ssl (deprecated) or tls
					  $mail->Port = 587;

					  $mail->Host     = $arraySMTPSERVER["host"];
					  $mail->Username = $arraySMTPSERVER["user"];
					  $mail->Password = $arraySMTPSERVER["password"];
					  $mail->From     = $from;
					  $mail->FromName = $fromName;
						$mail->Sender='lienhe@neonaga-sys.com.vn';
						
						$mail->AddReplyTo("lienhe@neonaga-sys.com.vn", $fromName);
						$mail->SMTPDebug  = 1;
						$mail->IsHTML(true);
						$mail->Subject = $subject;
						$mail->Body = $message;
						
						$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
					
					
					//$mail->AddAddress('testdev0511@gmail.com');
					//$mail->AddAddress("undisclosed-recipients:;");
					foreach($arrayMemberCurrent as $row){
						//$mail->AddBCC($row['email']);
						if($row['email'] && $row['unroll'] == 'no') {
							$mail->AddBCC($row['email']);
						}
					}
					
					$mail->Send();
					
				return true;
			} else {
				$this->error = _SOMEERRORCAMEDUP;
				return false;
			}

		} else if(isset($_POST['comm_submit']) && isset($_SESSION['comm_token']) && ($this->tmp != 'edited')) {
			unset($_SESSION['comm_token']);
			$this->error = _INVALIDREQUEST;
		}
	}

	/**
	 * Lists the comments for the inserted page id
	 * @param  integer $pageid  
	 * @param  integer $perpage 
	 * @return string           the generated html code
	 */
	function generateComments($pageid = 0, $perpage = 10, $start = 0, $admin = false) {

		if(session_id() == '')
			session_start();

		$_SESSION['comm_pageid'] = $pageid;
		
		if(isset($_SESSION["member_id"]))
		$member_id = $_SESSION["member_id"];
	
		
		$html ="<ul class='comment-list media-list mt-4'>";
		if($start>0) $html ="";
		
		$comments = $this->getComments($pageid, $perpage, $start);
		$dbf_cmnt = new BUSINESSLOGIC();

		// we generate the output of the comments
		if($comments)
		foreach ($comments as $comment) {
			//if(!($name = $this->getUsername($comment->name)))
				//$name = $comment->name;
			$commentInfo = $dbf_cmnt->getInfoColum("member",$comment->member_id);
			$name = $commentInfo['hovaten'];
			$picture = $commentInfo['picture'] ? $commentInfo['picture'] : HOST . '/style/images/packages/user.png';
			$email = $commentInfo['email'];
			// show reply link or form
			if(isset($_GET['comm_reply']) && ($comment->id == $_GET['comm_reply']))
				$show_reply = $this->generateForm("ranking.aspx?".$this->queryString('', $this->ignore), 1);
			else
				//$show_reply = "&nbsp;|&nbsp;<a href='ranking.aspx?".$this->queryString('', $this->ignore)."&comm_reply=$comment->id#$comment->id' class='comm_reply' data-id='$comment->id'>"._REPLY."</a>";
			
				$show_reply = "<span class='separator'>•</span><a href='javascript:void(0)' class='comm_reply' data-id='$comment->id'>"._REPLY."</a>";

			// show normal username or with adminStyles
			$style ="";
			if($comment->isadmin) {
				$show_name = "<span style='".$this->settings['adminStyle']['username']."'>".$this->html($name)."</span>";
				$style = $this->settings['adminStyle']['box'];
			} else
				$show_name = $this->html($name);

			// show extra info only to admin
			$show_extra ="";
			if($this->settings['isAdmin']) {
				$browser = explode(" ", $comment->browser);
				$show_extra = "(".$email." | ".$browser[0]." | $comment->ip)";
			}
			$is_del = (isset($_GET['comm_del']) && ($_GET['comm_del'] === $comment->id) && $this->hasRights($comment))
						? " background-color: #FFDE89; border: 1px solid red;" : null;

			$html .= "
			<li class='row mb-2 pb-2 clearfix' id='$comment->id' style='".$style.$is_del."'>
				<div class='col-avatar'><div class='div-avatar circle user_comment_info' data-name='".$commentInfo["hovaten"]."' data-id='".$comment->member_id."' data-avatar='".$commentInfo["picture"]."' data-desc='".$commentInfo["description"]."' data-facebook='".$commentInfo["fb"]."'>
					<img class='media-object align-self-start' src='".$picture."' onload='fixAspect(this);'>
				</div></div>
				<div id='body_".$comment->id."' class='col comm_content'>";

			if(isset($_GET['comm_edit']) && ($_GET['comm_edit'] === $comment->id) && $this->hasRights($comment))
				// we generate the form in edit mode with precompleted data
				$html .= $this->generateForm('', 2, $comment);
			else
				/*$html .= "<div class='media-heading'>
						$show_name $show_extra
						<small class='muted'>".$this->tsince($comment->time)."</small>
						".$this->admin_options($comment)."
					</div>";*/
				$html .= "<div id='content_$comment->id' class='talk-bubble tri-right left-in'>
					<div class='talktext'>
					<p class='author_meta pb-1 mb-1'><span class='comment-name' data-toggle='tooltip' data-placement='right' title='$show_extra'>$show_name</span><span class='post_date'><small>".$this->tsince($comment->time)."</small></span></p>
					<p id='comm_p_$comment->id' class='emojionediv'>".nl2br($this->html($comment->message))."</p></div></div>";
				$html .= "<div id='loading_$comment->id' style='display:none;position:relative;'><span class='rotatingDiv'></span></div>";
				$html .= $comment->like;
			
				$html .= $show_reply;
				if($start>0)
					$html .= $this->admin_options($comment,$admin);
				else $html .= $this->admin_options($comment);
				//$html .= ' | ' . $this->tsince($comment->time);

				$html .= $this->generateReplies($comment->id);
				if($is_del)
					$html .= $this->gennerateConfirm('', 'comm_del', $comment->id);
			
				$html .="
				</div>
			</li>";
		}
		if($start>0) $html .= "";
		else $html .= "</ul>";
		
		$more_comm = $dbf_cmnt->getDynamic("user_comments", "parent=0", "");
		$totalCom = $dbf_cmnt->totalRows($more_comm);
		if($totalCom>$perpage && $start == 0)
		{
			$html .= "<div id='more_comment' class='text-center mt-2 mb-5'><button type='button' id='com_button' class='more_comment btn btn-light btn-sm btn-lg btn-block' data-limit='".$perpage."' data-start='".$perpage."' data-total='".$totalCom."'>" . _VIEWMORECOMMENTS . "</button></div>";
		}
		//$html .= "<div class='mt-4'>".$this->generatePages($pageid, $perpage)."</div>";

		return $html;
	}
	
	/**
	 * Get the comment for the certain comment id
	 * @param  admin boolean   
	 * @return string           the generated html code
	 */
	function generateComment($pageid = 0,$comm_id,$admin = false) {

		if(session_id() == '')
			session_start();

		$_SESSION['comm_pageid'] = $pageid;
		
		if(isset($_SESSION["member_id"]))
		$member_id = $_SESSION["member_id"];
	
		
		$html ="<ul class='comment-list media-list mt-4'>";
		if($start>0) $html ="";
		$ajax=1;
		$comments = $this->getComment($comm_id,$ajax);
		$dbf_cmnt = new BUSINESSLOGIC();

		// we generate the output of the comments
		if($comments)
		foreach ($comments as $comment) {
			//if(!($name = $this->getUsername($comment->name)))
				//$name = $comment->name;
			$commentInfo = $dbf_cmnt->getInfoColum("member",$comment->member_id);
			$name = $commentInfo['hovaten'];
			$picture = $commentInfo['picture'] ? $commentInfo['picture'] : HOST . '/style/images/packages/user.png';
			$email = $commentInfo['email'];
			// show reply link or form
			if(isset($_GET['comm_reply']) && ($comment->id == $_GET['comm_reply']))
				$show_reply = $this->generateForm("ranking.aspx?".$this->queryString('', $this->ignore), 1);
			else
				//$show_reply = "&nbsp;|&nbsp;<a href='ranking.aspx?".$this->queryString('', $this->ignore)."&comm_reply=$comment->id#$comment->id' class='comm_reply' data-id='$comment->id'>"._REPLY."</a>";
			
				$show_reply = "<span class='separator'>•</span><a href='javascript:void(0)' class='comm_reply' data-id='$comment->id'>"._REPLY."</a>";

			// show normal username or with adminStyles
			$style ="";
			if($comment->isadmin) {
				$show_name = "<span style='".$this->settings['adminStyle']['username']."'>".$this->html($name)."</span>";
				$style = $this->settings['adminStyle']['box'];
			} else
				$show_name = $this->html($name);

			// show extra info only to admin
			$show_extra ="";
			if($this->settings['isAdmin']) {
				$browser = explode(" ", $comment->browser);
				$show_extra = "(".$email." | ".$browser[0]." | $comment->ip)";
			}
			$is_del = (isset($_GET['comm_del']) && ($_GET['comm_del'] === $comment->id) && $this->hasRights($comment))
						? " background-color: #FFDE89; border: 1px solid red;" : null;

			$html .= "
			<li class='row mb-2 pb-2 clearfix' id='$comment->id' style='".$style.$is_del."'>
				<div class='col-avatar'><div class='div-avatar circle user_comment_info' data-name='".$commentInfo["hovaten"]."' data-id='".$comment->member_id."' data-avatar='".$commentInfo["picture"]."' data-desc='".$commentInfo["description"]."' data-facebook='".$commentInfo["fb"]."'>
					<img class='media-object align-self-start' src='".$picture."' onload='fixAspect(this);'>
				</div></div>
				<div id='body_".$comment->id."' class='col comm_content'>";

			if(isset($_GET['comm_edit']) && ($_GET['comm_edit'] === $comment->id) && $this->hasRights($comment))
				// we generate the form in edit mode with precompleted data
				$html .= $this->generateForm('', 2, $comment);
			else
				/*$html .= "<div class='media-heading'>
						$show_name $show_extra
						<small class='muted'>".$this->tsince($comment->time)."</small>
						".$this->admin_options($comment)."
					</div>";*/
				$html .= "<div id='content_$comment->id' class='talk-bubble tri-right left-in'>
					<div class='talktext'>
					<p class='author_meta pb-1 mb-1'><span class='comment-name' data-toggle='tooltip' data-placement='right' title='$show_extra'>$show_name</span><span class='post_date'><small>".$this->tsince($comment->time)."</small></span></p>
					<p id='comm_p_$comment->id' class='emojionediv'>".nl2br($this->html($comment->message))."</p></div></div>";
				$html .= "<div id='loading_$comment->id' style='display:none;position:relative;'><span class='rotatingDiv'></span></div>";
				$html .= $comment->like;
			
				$html .= $show_reply;
				if($start>0)
					$html .= $this->admin_options($comment,$admin);
				else $html .= $this->admin_options($comment);
				//$html .= ' | ' . $this->tsince($comment->time);

				$html .= $this->generateReplies($comment->id,0);
				if($is_del)
					$html .= $this->gennerateConfirm('', 'comm_del', $comment->id);
			
				$html .="
				</div>
			</li>";
		}
		if($start>0) $html .= "";
		else $html .= "</ul>";
		
		return $html;
	}


function generateReplies($comm_id, $limit = 3,$start = 0, $admin = false) {
	$html = "";
	$comments = $this->getReplies($comm_id, $limit, $start);
	$dbf_cmnt = new BUSINESSLOGIC();
	// we generate the output of the comments
	if($comments)
	foreach ($comments as $comment) {
		//if(!($name = $this->getUsername($comment->name)))
			//$name = $comment->name;
		$commentInfo = $dbf_cmnt->getInfoColum("member",$comment->member_id);
			$name = $commentInfo['hovaten'];
			$picture = $commentInfo['picture'] ? $commentInfo['picture'] : HOST . '/style/images/packages/user.png';
			$email = $commentInfo['email'];
			
		
		// show normal username or with adminStyles
		$style ="";
		if($comment->isadmin) {
			$show_name = "<span style='".$this->settings['adminStyle']['username']."'>".$this->html($name)."</span>";
			$style = $this->settings['adminStyle']['box'];
		} else
			$show_name = $this->html($name);

		// show reply link or form
		$user_name = $this->html($name);
		$show_reply = "<span class='separator'>•</span><a href='javascript:void(0)' class='rep_reply' data-sub='$comment->id' data-id='$comm_id' data-user='$user_name'>"._REPLY."</a>";
		
			// show extra info only to admin
			$show_extra ="";
			if($this->settings['isAdmin']) {
				$browser = explode(" ", $comment->browser);
				$show_extra = "(".$email." | ".$browser[0]." | $comment->ip)";
			}
			$is_del = (isset($_GET['comm_del']) && ($_GET['comm_del'] === $comment->id) && $this->hasRights($comment))
						? " background-color: #FFDE89; border: 1px solid red;" : null;

			$html .= "
			<div class='row mt-2 pt-2 clearfix' id='$comment->id' style='". $style. $is_del ."'>
				<div class='col-reply-avatar'><div class='div-avatar circle user_comment_info' data-name='".$commentInfo["hovaten"]."' data-id='".$comment->member_id."' data-avatar='".$commentInfo["picture"]."' data-desc='".$commentInfo["description"]."' data-facebook='".$commentInfo["fb"]."'>
					<img class='media-object align-self-start' src='".$picture."' onload='fixAspect(this);'>
				</div></div>
				<div class='col comm_content'>";
			

			if(isset($_GET['comm_edit']) && ($_GET['comm_edit'] === $comment->id) && $this->hasRights($comment))
				// we generate the form in edit mode with precompleted data
				$html .= $this->generateForm('', 2, $comment);
			else
				/*$html .= "<div class='media-heading'>
						$show_name $show_extra
						<small class='muted'>".$this->tsince($comment->time)." replied </small>
						".$this->admin_options($comment)."
					</div>";*/
					$str = $this->html($comment->message);
					$begin = strpos( $str,'@') +1;
					//this is the location/index of the ) CLOSE_PAREN.
					$end = strpos( $str,':');
					//we need the length of the substring for the third argument, not its index
					$len = ($end-$begin);
					$name = substr($str, $begin, $len );
					
					$str = preg_replace('/' . preg_quote('@') . 
                          '.*?' .
                          preg_quote(':') . '/', '<b>'.$name.'</b>' , $str);
					$html .= "<div id='content_$comment->id' class='talk-bubble tri-right left-in'>
					<div class='talktext'>
					<p class='author_meta pb-1 mb-1'><span class='comment-name' data-toggle='tooltip' data-placement='right' title='$show_extra'>$show_name</span><span class='post_date'><small>".$this->tsince($comment->time)."</small></span></p>
					<p id='comm_p_$comment->id' class='emojionediv'>".nl2br($str)."</p></div></div>";
				$html .= "<div id='loading_$comment->id' style='display:none;position:relative;'><span class='rotatingDiv'></span></div>";
				$html .= $comment->like;
				$html .= $show_reply;
				if($start>0)
					$html .= $this->admin_options($comment,$admin);
				else $html .= $this->admin_options($comment);
				//$html .= " | " . $this->tsince($comment->time);
			if($is_del)
				$html .= $this->gennerateConfirm('', 'comm_del', $comment->id);

			$html .= "</div></div>";

	}
	if($limit>0) {
		
	$more_reply = $dbf_cmnt->getDynamic("user_comments", "parent=$comm_id", "");
	$totalRep = $dbf_cmnt->totalRows($more_reply);
	
		if($totalRep>$limit && $start == 0)
		{
			if($totalRep>=$limit + 3) {
				$html .= "<div id='more_reply_".$comm_id."' class='text-center mt-2'><button type='button' id='repbutton_".$comm_id."' class='more_rep btn btn-light btn-sm' data-id='".$comm_id."' data-limit='".$limit."' data-start='".$limit."' data-total='".$totalRep."'><i class='fa fa-undo' aria-hidden='true'></i> " . _VIEWMORE . " 3 " . _REPLIES."</button></div>";
			} elseif($totalRep<$limit + 3) {
				$max = $limit + 3;
				$diff = $max - $totalRep;
				$more = $limit - $diff;
				$html .= "<div id='more_reply_".$comm_id."' class='text-center mt-2'><button type='button' id='repbutton_".$comm_id."' class='more_rep btn btn-light btn-sm' data-id='".$comm_id."' data-limit='".$limit."' data-start='".$limit."' data-total='".$totalRep."'><i class='fa fa-undo' aria-hidden='true'></i> "._VIEWMORE." ".$more ." " . _REPLIES."</button></div>";
			}
		}
	}

	return $html; 
}

	function generateForm($location =  '', $type = 0, $comment = false, $comm_id = 0, $sub_reply = '') {
		$this->setToken();
		
		if($location == '')
			$location = "ranking.aspx?".$this->queryString('', $this->ignore);


		if(!$comment)
			$comment = (object)array("name"=>"","email"=>"","message"=>"");
		 
		if($type == 1) {
			$emoji = '_reply';
			$comment_id = isset($_GET['comm_reply']) ? $_GET['comm_reply'] : $comm_id;
			$title = "<input type='hidden' name='comm_reply' value='".(int)$comment_id."'>" . _ADDREPLY;
			$label = "<label class='control-label' for='comm_msg'>$title</label>";
		} else if($type == 2) {
			$emoji = '_edit';
			$comment_id = isset($_GET['comm_edit']) ? $_GET['comm_edit'] : $comm_id;
			$title = "<input type='hidden' name='comm_edit' value='".(int)$comment_id."'>" . _EDITCOMMENT;
			$label = "<label class='control-label' for='comm_msg'>$title</label>";
		}
		else {
			$emoji = '';
			$title = _ADDCOMMENT;
			$label = "";
		}

		$show_name_email = '';
		
		if( $this->settings['public'] ) {
			$show_name_email = "<div class='control-group'>
			  <div class='controls'>
				<input id='comm_name' name='comm_name' type='text' class='input-xlarge' value='$comment->name'>
				
			  </div>
			</div>

			<div class='control-group'>
			  <label class='control-label' for='comm_email'>Email</label>
			  <div class='controls'>
				<input id='comm_email' name='comm_email' type='text' class='input-xlarge' value='$comment->email'>
			  	<p>
			  	".($this->settings['optional_email'] ? "(optional, it will not be public.)" : "")."
			  	</p>
			  </div>
			</div>";
		}

		if($sub_reply) $sub_reply = '@'.$sub_reply.': ';
		
		$html = "
	<form id='commentform$emoji' class='form-horizontal' action='$location#comm_status' method='post'>
		<fieldset>
		<!--<legend>$title</legend>-->

		<!--$show_name_email-->

		<div class='control-group'>
		  $label
		  <div class='controls'>
				<textarea class='emojionearea$emoji form-control' name='comm_msg' >$sub_reply $comment->message</textarea>
		  </div>
		</div>

		<input type='hidden' name='comm_token' value='".$_SESSION['comm_token']."'>

		<div class='control-group text-right'>
		  <div class='controls'>
			<input type='submit' id='comm_submit' name='comm_submit' class='btn btn-primary btn-sm' value='"._POST."'>
			".($type ? "<input type='submit' id='comm_cancel' value='"._CANCEL."' class='btn btn-outline-dark btn-sm'>" : "")."
		  </div>
		</div>

		</fieldset>
	</form>";
		if($this->isBanned($_SESSION["member_id"])) return '<div class="alert alert-warning" id="comm_status">' . _YOUAREBAN . '</div>';
		return $html;
	}

	/**
	 * it will create the table to hold the comments
	 * @return boolean true if the install succeeds
	 */
	function install() {

		$sql = "CREATE TABLE IF NOT EXISTS `".$this->settings['comments_table']."` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `member_id` bigint(20) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `message` text NOT NULL,
		  `time` int(11) NOT NULL,
		  `ip` varchar(255) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `browser` varchar(255) NOT NULL,
		  `pageid` int(11) NOT NULL,
		  `parent` int(11) NOT NULL,
		  `isadmin` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		);";

		$sql2 = "CREATE TABLE IF NOT EXISTS `user_banned` (
		  `ip` varchar(255) NOT NULL,
		  UNIQUE KEY `ip` (`ip`)
		);";

		if(mysqli_query($this->connect, $sql) && mysqli_query($this->connect, $sql2))
			return true;

		return false;

	}
	/**
	 * gets the comments from the db
	 * @param  integer $pageid the id for the specific page
	 * @param  integer $perpage number of comments perpage
	 * @return array		  the comments
	 */
	function getComments($pageid = 0, $perpage = 10, $start = 0) {
		$comments = array();

		$sql = "SELECT * FROM `".$this->settings['comments_table']."` WHERE `parent` = 0 ";

		if($pageid)
			$sql .= "AND `pageid` = '".mysqli_real_escape_string($this->connect, $pageid)."'";

		// some sorting options
		$sql .= " ".$this->settings['sort']." "; // this is pasted as is

		$sql .= "LIMIT $start, $perpage";
		$user_id = $_SESSION["member_id"];
		if($result = mysqli_query($this->connect, $sql)) {
			while($row = mysqli_fetch_object($result)) {
				$results = mysqli_query($this->connect, "SELECT * FROM comment_likes WHERE userid=$user_id AND postid=".$row->id."");
				$html = '<div style="padding: 2px; margin-top: 5px;display:inline-block;">';
					if (mysqli_num_rows($results) == 1 ) {
						$html .= '<span class="unlike fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="like hide fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					} else {
						$html .= '<span class="like fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="unlike hide fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					}

					$html .= '</div>';
				$row->like = $html;
				$comments[] = $row;
			}
		} else {
			return false;
		}
		return $comments;
	}
	
	function getComments_backup($pageid = 0, $perpage = 10, $start = 0) {
		$comments = array();

		$sql = "SELECT * FROM `".$this->settings['comments_table']."` WHERE `parent` = 0 ";

		if($pageid)
			$sql .= "AND `pageid` = '".mysqli_real_escape_string($this->connect, $pageid)."'";

		// some sorting options
		$sql .= " ".$this->settings['sort']." "; // this is pasted as is


		// grab the page number
		$page_number = !isset($_GET['comm_page']) || ((int)$_GET['comm_page'] <= 0) ? 1 : (int)$_GET['comm_page']; 

		$total_results = mysqli_num_rows(mysqli_query($this->connect, $sql));

		if($page_number > ceil($total_results/$perpage))
			$page_number = ceil($total_results/$perpage);

		//$start = ($page_number - 1) * $perpage;

		$sql .= "LIMIT $start, $perpage";
		$user_id = $_SESSION["member_id"];
		if($result = mysqli_query($this->connect, $sql)) {
			while($row = mysqli_fetch_object($result)) {
				$results = mysqli_query($this->connect, "SELECT * FROM comment_likes WHERE userid=$user_id AND postid=".$row->id."");
				$html = '<div style="padding: 2px; margin-top: 5px;display:inline-block;">';
					if (mysqli_num_rows($results) == 1 ) {
						$html .= '<span class="unlike fa fa-thumbs-up" data-id="'.$row->id.'"></span> 
						<span class="like hide fa fa-thumbs-o-up" data-id="'.$row->id.'"></span>';
					} else {
						$html .= '<span class="like fa fa-thumbs-o-up" data-id="'.$row->id.'"></span> 
						<span class="unlike hide fa fa-thumbs-up" data-id="'.$row->id.'"></span>';
					}

					$html .= '<span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></div>';
				$row->like = $html;
				$comments[] = $row;
			}
		} else {
			return false;
		}
		return $comments;
	}
	/**
	 * gets the replies to a certain comment
	 * @param  integer $comm_id the id for the specific comment
	 * @param  integer $limit max number of comments to be displayed as reply
	 * @return array		  the comments
	 */
	function getReplies($comm_id = 0, $limit = 3, $start = 0) {
		$comments = array();

		$sql = "SELECT * FROM `".$this->settings['comments_table']."` 
			WHERE `parent` = '".mysqli_real_escape_string($this->connect, $comm_id)."'";

		// limitation
		if($limit>0)
			$sql .= "ORDER BY id ASC LIMIT $start, $limit";
		else $sql .= "ORDER BY id ASC";
		if($result = mysqli_query($this->connect, $sql)) {
			while($row = mysqli_fetch_object($result)) {
				$results = mysqli_query($this->connect, "SELECT * FROM comment_likes WHERE userid=1 AND postid=".$row->id."");
				$html = '<div style="padding: 2px; margin-top: 5px;display:inline-block;">';
					if (mysqli_num_rows($results) == 1 ) {
						$html .= '<span class="unlike fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="like hide fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					} else {
						$html .= '<span class="like fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="unlike hide fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					}

				$html .= '</div>';
				$row->like = $html;
				$comments[] = $row;
			}
		} else {
			return false;
		}
		return $comments;
	}
	
	/**
	 * gets the certain comment
	 * @param  integer $comm_id the id for the specific comment
	 * @param  integer $limit max number of comments to be displayed as reply
	 * @return obj		  comment
	 */
	function getComment($comm_id = 0,$ajax='') {
		$comments = array();

		$sql = "SELECT * FROM `".$this->settings['comments_table']."` 
			WHERE `id` = '".mysqli_real_escape_string($this->connect, $comm_id)."'";

		// some sorting options
		$sql .= " ".$this->settings['sort']." "; // this is pasted as is

		$user_id = $_SESSION["member_id"];
		if($result = mysqli_query($this->connect, $sql)) {
			while($row = mysqli_fetch_object($result)) {
				$results = mysqli_query($this->connect, "SELECT * FROM comment_likes WHERE userid=$user_id AND postid=".$row->id."");
				$html = '<div style="padding: 2px; margin-top: 5px;display:inline-block;">';
					if (mysqli_num_rows($results) == 1 ) {
						$html .= '<span class="unlike fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="like hide fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					} else {
						$html .= '<span class="like fa fa-heart-o" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span> 
						<span class="unlike hide fa fa-heart" data-id="'.$row->id.'"><span class="likes_count">&nbsp;'.$row->likes.' '._LIKES.'</span></span>';
					}

					$html .= '</div>';
				$row->like = $html;
				$comments[] = $row;
				if($ajax==1) {
					return $comments;
				}
				return $comments[0];
			}
		} else {
			return false;
		}
		
		
	}

	/**
	 * it parses the text for output
	 * @param  string $text the text to be parsed
	 * @return string	   paesed text
	 */
	function html($text) {
		return htmlentities($text, ENT_QUOTES);
	}

	/**
	 * while developing this class a small problem camed up,
	 * 	what if i have a user system already and i want to store the userid instread of he's username(in case he changes it) ?
	 *  for this matter i made this function which by default will return false
	 *  which means that we will consider that the `name` column is a string not an integer(userid)
	 *  BUT if you store the userid in the name column you have to make sure that this function
	 *  will return the username coresponding to that id, i included an example
	 * 
	 */
	function getUsername($userid) {
		return false;
		// in case you decide to store the userid use this
		// $user = mysqli_fetch_object(mysql_query($this->link, "SELECT * FROM `users` WHERE 'userid' = '".(int)$userid."'"));
		// return $user->username;
	}

	/**
	 * Time elapes since a times
	 * @param  int $time The past time
	 * @return string	   time elapssed
	 * credits: http://stackoverflow.com/a/2916189/1579481
	 */
	function tsince($time, $end_msg = _AGO) {
 
		$time = abs(time() - $time); // to get the time since that moment

		if($time == 0)
			return "Just now";

		$tokens = array (
			31536000 => _YEAR,
			2592000 => _MONTH,
			604800 => _WEEK,
			86400 => _DAY,
			3600 => _HOUR,
			60 => _MINUTE,
			1 => _SECOND
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			if(isset($_SESSION['language']) && $_SESSION['language'] == 'en') {
				return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' '. $end_msg;
			}
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'':'').' '. $end_msg;
		}
 
	}

	function isValidMail($mail) {
	 
		if(!filter_var($mail, FILTER_VALIDATE_EMAIL))
			return FALSE;


		list($username, $maildomain) = explode("@", $mail);
		if(checkdnsrr($maildomain, "MX"))
			return TRUE;

		return FALSE;
	}

	// Returns the real IP address of the user
	function ip()
	{
		// No IP found (will be overwritten by for
		// if any IP is found behind a firewall)
		$ip = FALSE;
		
		// If HTTP_CLIENT_IP is set, then give it priority
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		
		// User is behind a proxy and check that we discard RFC1918 IP addresses
		// if they are behind a proxy then only figure out which IP belongs to the
		// user.  Might not need any more hackin if there is a squid reverse proxy
		// infront of apache.
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

			// Put the IP's into an array which we shall work with shortly.
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }

			for ($i = 0; $i < count($ips); $i++) {
				// Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and
				// 192.168.0.0/16
				if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./', $ips[$i])) {
					if (version_compare(phpversion(), "5.0.0", ">=")) {
						if (ip2long($ips[$i]) != false) {
							$ip = $ips[$i];
							break;
						}
					} else {
						if (ip2long($ips[$i]) != -1) {
							$ip = $ips[$i];
							break;
						}
					}
				}
			}
		}

		// Return with the found IP or the remote address
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	
	/*function ip() {
		return $_SESSION["member_id"];
	}*/
	/**
	 * sets a random token stored in the session used to validate the form submit
	 */
	function setToken() {
		if(session_id() == '')
			session_start();

		$_SESSION['comm_token'] = md5(time().rand());
	}
	/**
	 * it will return the query string as hidden fields or as url
	 * @param  string $type   type of output
	 * @param  array  $ignore ignored elements
	 * @return string         
	 */
	function queryString($type = '', $ignore = array()) {

		$result = '';

		foreach($_GET as $k => $v) {
			if((is_array($ignore) && in_array($k, $ignore)) 
				|| (is_string($ignore) && preg_match($ignore, $k)))
				continue;

			if($type == 'hidden') {
				$result .= "<input type='hidden' name='".urlencode($k)."' value='".urlencode($v)."'>";
			} else {
				$result[] = urlencode($k)."=".urlencode($v);
			}
		}

		if(is_array($result))
			return implode("&", $result);

		return $result;


	}


	/**
	 * generates a confirmation form
	 * @return string the html code of the form
	 */
	function gennerateConfirm($location = '', $info_name = 'comm_del', $info_value = 0, $submit = _SUREDELETE) {

		if($location == '')
			$location = "ranking.aspx?".$this->queryString('', $this->ignore);

	return "<form class='form-horizontal' action='$location' method='post'>
		<div class='control-group'>
		  <div class='controls'>
		  ".($info_name ? "<input type='hidden' name='$info_name' value='$info_value'>" : "")."
			<input type='submit' id='comm_submit' name='comm_confirm' class='btn btn-primary' value='$submit'>
			<a href='".$_SERVER['HTTP_REFERER']."' class='btn'>Cancel</a>
		  </div>
		</div>
		</form>";
	}
	/**
	 * checks if the current user has the rights to delete/edit a comment
	 * @param  object $comment data related to one comment
	 * @return boolean [description]
	 */
	function hasRights($comment,$admin=false) {
		if(session_id() == '')
			session_start();
		if(isset($_SESSION["member_id"]))
		$member_id = $_SESSION["member_id"];
	
		if($this->settings['isAdmin'] || $admin || (isset($_SESSION['comm_last_id']) && $_SESSION['comm_last_id'] == $comment->id))
			return true;
		return false;
	}
	/**
	 * returns the html code of the options available
	 * @param  object $comment data related to one comment
	 * @return string          the html code to display the options
	 */
	function admin_options($comment,$admin=false) {
		// if is admin or the person who posted the message
		if($this->hasRights($comment,$admin))
			return "<span class='separator'>•</span><a href='javascript:void(0)' data-id='$comment->id' class='comm_edit'>"._EDIT."</a><span class='separator'>•</span><a href='javascript:void(0)' data-id='$comment->id' class='comm_del'>"._DELETE."</a>";
	}

	function admin_options_backup($comment,$admin=false) {
		// if is admin or the person who posted the message
		if($this->hasRights($comment,$admin))
			return "<span class='separator'>•</span><a href='ranking.aspx?".$this->queryString('', $this->ignore)."&comm_edit=$comment->id#$comment->id'>"._EDIT."</a><span class='separator'>•</span><a href='ranking.aspx?".$this->queryString('', $this->ignore)."&comm_del=$comment->id#$comment->id' data-id='$comment->id' class='comm_del'>"._DELETE."</a>".
				(($this->settings['isAdmin'] || $admin)? //if is admin 
					"<span class='separator'>•</span><a href='ranking.aspx?".$this->queryString('', $this->ignore)."&comm_".
					($this->isBanned($comment->ip) ? "un" : "")."ban=".urlencode($comment->ip)."'>".
					($this->isBanned($comment->ip) ? _UN : "")._BAN."</a>" : "");
	}


	/**
	 * will generate the html code for page numbers
	 * @param  interger  $total   the total number of elements
	 * @param  interger  $page    current page
	 * @param  integer $perpage number of elements per page
	 * @return string           the generated html code
	 */
	function generatePages($pageid, $perpage = 10){


		$sql = "SELECT `id` FROM `".$this->settings['comments_table']."` WHERE `parent` = 0 ";

		if($pageid)
			$sql .=  " AND `pageid`='".(int)$pageid."'";


		$total = mysqli_num_rows(mysqli_query($this->connect, $sql));
		
		$total_pages = ceil($total/$perpage);
		
		$page = !isset($_GET['comm_page']) || ((int)$_GET['comm_page'] <= 0) ? 1 : (int)$_GET['comm_page']; 
		
		$query = "&".$this->queryString('', array('comm_page'));
		
		$html = "";
		
		if($total_pages > 1) {
			$html .= "<div class='pagination'><ul>";

			if($page > 4)
				$html .= "<li><a href='ranking.aspx?$query'>"._SFIRST."</a></li>";

			if($page > 1)
				$html .= "<li><a href='ranking.aspx?comm_page=".($page-1)."$query'>"._SPREVIOUS."</a> </li>";

			for($i = max(1, $page - 3); $i <= min($page + 3, $total_pages); $i++)
				$html .= ($i == $page ? "<li class='active'><a>".$i."</a></li>" : " <li><a href='ranking.aspx?comm_page=$i$query'>$i</a></li> ");

			if($page < $total_pages)
				$html .= "<li><a href='ranking.aspx?comm_page=".($page+1)."$query'>"._SNEXT."</a></li>";

			if($page < $total_pages-3)
				$html .= "<li><a href='ranking.aspx?comm_page=$total_pages$query'> "._SLAST." </a></li>";

			$html .= "</ul></div>";

		}
		
		return $html;
		
	}

	/**
	 * deletes comment based on the comment id, it also checks for the rights of the user.
	 * @param  interger $comment_id the id of the comment to be deleted
	 * @return boolean             true on success
	 */
	function delComm($comment_id) {
		$comm = mysqli_query($this->connect, "SELECT `id` FROM `".$this->settings['comments_table']."` WHERE `id` = '".(int)$comment_id."'");
		if(mysqli_num_rows($comm) && $this->hasRights(mysqli_fetch_object($comm))) {
			mysqli_query($this->connect, "DELETE FROM `".$this->settings['comments_table']."` 
				WHERE `id` = '".(int)$comment_id."' OR `parent` = '".(int)$comment_id."'");

			return true;
		}

		return false;
	}
	/**
	 * Adds the inserted ip in the banned list
	 * @param  string $ip the ip to be banned
	 */
	function banIP( $ip ) {
		if($this->settings['isAdmin'])
			if(mysqli_query($this->connect, "INSERT INTO `".$this->settings['banned_table']."` 
				SET `ip` = '".mysqli_real_escape_string($this->connect, $ip)."'"))
				return true;
		return false;
	}

	/**
	 * Deletes an ip from the banned list.
	 * @param  string $ip the ip to be unbanned
	 */
	function unBanIP( $ip ) {
		if($this->settings['isAdmin']) {
			mysqli_query($this->connect, "DELETE FROM `".$this->settings['banned_table']."` 
				WHERE `ip` = '".mysqli_real_escape_string($this->connect, $ip)."'");
			return true;
		}

		return false;
	}
	/**
	 * checks if an ip is banned
	 * @param  string $ip ip to be checked
	 * @return boolean     true if the ip is banned
	 */
	function isBanned( $ip ) {
		// no need to check the same ip 2 times in a row
		if(count($this->checked_ips) && in_array($ip, array_keys($this->checked_ips)))
			return $this->checked_ips[$ip];

		$this->checked_ips[$ip] = $ip;


		if(mysqli_num_rows(mysqli_query($this->connect, "SELECT * FROM `".$this->settings['banned_table']."` 
			WHERE `ip` = '".mysqli_real_escape_string($this->connect, $ip)."'"))) {
			$this->checked_ips[$ip] = true;
			return true;
		}
		$this->checked_ips[$ip] = false;


		return false;
	}
	
	
	/**
	 * ajax on like click
	 */
	function ajaxLike($action,$postid) {
		//like - unlike system >>
		$user_id = $_SESSION["member_id"];
		if (isset($_POST['liked'])) {
			$postid = $_POST['postid'];
			
			$result = mysqli_query($this->connect, "SELECT * FROM user_comments WHERE id=$postid");
			$row = mysqli_fetch_array($result);
			$n = $row['likes'];

			mysqli_query($this->connect, "INSERT INTO comment_likes (userid, postid) VALUES ($user_id, $postid)");
			mysqli_query($this->connect, "UPDATE user_comments SET likes=$n+1 WHERE id=$postid");

			return $n+1;
			exit();
		}
		if (isset($_POST['unliked'])) {
			$postid = $_POST['postid'];
			$result = mysqli_query($this->connect, "SELECT * FROM user_comments WHERE id=$postid");
			$row = mysqli_fetch_array($result);
			$n = $row['likes'];

			mysqli_query($this->connect, "DELETE FROM comment_likes WHERE postid=$postid AND userid=$user_id");
			mysqli_query($this->connect, "UPDATE user_comments SET likes=$n-1 WHERE id=$postid");
			
			return $n-1;
			exit();
		}
	//like - unlike system >>
	}

}
















