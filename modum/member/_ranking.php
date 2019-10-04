<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{

   /*if(!isset($_SESSION["password2"]) || $_SESSION["password2"]=="")
  {
      $html->redirectURL("/confirm_by_password.aspx?redirect_page=member_list");
      exit();
  }*/
	$userid = $rowgetInfo["id"];
?>

<script src="/emoji/js/emojionearea.min.js"></script>
<script src="/emoji/js/emojione.min.js"></script>
<link rel="stylesheet" href="/emoji/css/emojione.sprites.css">
<link rel="stylesheet" href="/emoji/css/emojionearea.min.css">
<link rel="stylesheet" href="/emoji/css/emojione.min.css">
<script src="/emoji/js/main.js"></script>

<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<style>.progress {margin-bottom:10px !important;margin-top:10px;}.fb-comments iframe {width:100% !important;}</style>
<style>#playerWrap{display: inline-block; position: relative;}#playerWrap.shown::after{content:""; position: absolute; top: 0; left: 0; bottom: 0; right: 0; cursor: pointer; background-color: black; background-repeat: no-repeat; background-position: center; background-size: 64px 64px; background-image: url(data:image/svg+xml;utf8;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiB2aWV3Qm94PSIwIDAgNTEwIDUxMCI+PHBhdGggZD0iTTI1NSAxMDJWMEwxMjcuNSAxMjcuNSAyNTUgMjU1VjE1M2M4NC4xNSAwIDE1MyA2OC44NSAxNTMgMTUzcy02OC44NSAxNTMtMTUzIDE1My0xNTMtNjguODUtMTUzLTE1M0g1MWMwIDExMi4yIDkxLjggMjA0IDIwNCAyMDRzMjA0LTkxLjggMjA0LTIwNC05MS44LTIwNC0yMDQtMjA0eiIgZmlsbD0iI0ZGRiIvPjwvc3ZnPg==);}</style>
<script>
function fixAspect(img) {
  var $img = $(img),
    width = $img.width(),
    height = $img.height(),
    tallAndNarrow = width / height < 1;
  if (tallAndNarrow) {
    $img.addClass('tallAndNarrow');
  }
  $img.addClass('loaded');
}
var likes = "<?php echo _LIKES;?>";
var viewmore = "<?php echo _VIEWMORE;?>";
var replies = "<?php echo _REPLIES;?>";
</script>
<section id="main">
	<!-- WRAP -->
	<div class="wrap">

     <!-- USERMENU -->
		
		<!-- /USERMENU -->
		<!-- CONTENT -->
	    <section id="content">
            <div id="main-container">
                <div id="page-content">				    
					<div class="row">
						<div class="col-md-12">
							<div class="block full">
		                               

		                                  <?php

		                                    $arrayMemberCurrent= array();
		                                    $arrayMemberCurrent = $dbf->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
		                                    $arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");
											$arrayRanking = $dbf->MemberRanking('actual_sales',false);
											if(!empty($arrayRanking)){ ?>
											
												<div class="block-title">
													<h2><?php echo $rowgetInfo["roles_id"]==15  ? _TOP15USER : _TOP15;?></h2>
												</div>
												<div class="form-group">
											<?php
											$arrayRanking = array_values($arrayRanking);
											$arrayRanking = array_slice($arrayRanking, 0, 15);
											$array_medal = array('1vang-8.png','1bac-8.png','1dong_1-8.png');
											//printf('<pre>%s</pre>',print_r($arrayRanking,true));
												$i=0;
												$medal=true;
		                                        foreach($arrayRanking as $row)
		                                        {
													$memberInfo = $dbf->getInfoColum("member",$row["member_id"]);
													$picture = $memberInfo['picture'] ? $memberInfo['picture'] : HOST . '/style/images/packages/user.png';
													$percent = ($row["sum_value"] * 100) / $arrayRanking[0]["sum_value"];
													echo '<style>
														.member_'.$row['member_id'].' {
														  width: 0;
														  animation: progress_'.$row['member_id'].' 1.6s ease-in-out forwards;
														  
														  .title_'.$row['member_id'].' {
															opacity: 0;
															animation: show_'.$row['member_id'].' 0.5s forwards ease-in-out 0.5s;
														  }
														} 

														@keyframes progress_'.$row['member_id'].' {
														  from {
															width: 0;
														  }
														  to {
															width: '.$percent.'%;
														  }
														} 
														@keyframes show_'.$row['member_id'].'  {
														  from {
															opacity: 0;
														  }
														  to {
															opacity: 1;
														  }
														}</style>';
													
													echo '<div class="row">';
													echo '<div class="col-md-4 control-label user-ranking d-flex" data-id="'.$row['member_id'].'" data-name="'.$memberInfo["hovaten"].'" data-avatar="'.$memberInfo["picture"].'" data-desc="'.$memberInfo["description"].'" data-facebook="'.$memberInfo["fb"].'"><a  href="javascript:void(0)" class=""><div class="col-ranking-avatar"><div class="div-avatar circle"><img src="'.$picture.'" class="align-self-center" onload="fixAspect(this);"></div></div></a><p class="flex-grow-1 mt-1"><a  href="javascript:void(0)">'.$memberInfo["hovaten"].'</a></p>';
													echo $medal ? '<div class="medal"><img src="'.HOST . 'images/' . $array_medal[$i].'" class="align-self-center"></div></div>' : '<div class="medal"></div></div>';
													echo '<div class="col-md-8"><div class="wrap-progress">';
													echo '<div class="progress">
														  <div class="progress-bar member_'.$row['member_id'].'" role="progressbar" style="width: '.$percent.'%" aria-valuenow="'.$row["sum_value"].'" aria-valuemin="0" aria-valuemax="'.$arrayRanking[0]["sum_value"].'"><span class="title_'.$row['member_id'].'">'.number_format($row["sum_value"]).'</span></div>
														</div>';
													echo '</div></div>';
													echo '</div>';
													if($i<2) {
														$i++;
													} else {
														$medal = false;
													}
		                                        }
		                                    
		                                  ?>
		                               

	                         		</div>
									<?php } //end TOP15?>
                   			</div>


							<div class="alert alert-success total-member" role="alert">
								<?php if($rowgetInfo["roles_id"]==15) { ?>
								  <h4 class="alert-heading text-sm-center"><?php echo _YOUNOW;?></h4>
								  <?php echo $dbf->getMemberRanking('actual_sales',$rowgetInfo["id"]);?>
								<?php } else {
										$rstmb = $dbf->getDynamic("member", "status=1 AND roles_id=15", ""); ?>
										<h4 class="alert-heading text-sm-center"><?php echo _TOTALMEMBER;?></h4>
											<h2 class="text-sm-center"><?php echo $dbf->totalRows($rstmb);?></h2>
								<?php } ?>
							</div>


						<?php 										
							$result = $dbf->getDynamic("case_studies","status=1 AND FIND_IN_SET($userid, read_list)=0","id desc");
							$casestudies = $dbf->totalRows($result);
							
							if($casestudies>0) { ?>
								<div class="card">
									<div class="card-header">
										<strong class="card-title"><?php echo _CASESTUDIES;?></strong>
									</div>
									<div class="card-body">
										<?php 

												$str="";
												$i=1;
												while( $row = $dbf->nextData($result))
												{
												?>
												
												   <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
														<h4 class="mb-2"><?php echo $row["title"];?></h4>
														<?php echo $row["content"];?>
														<button type="button" class="close dismis_casestudy" data-toggle="tooltip" data-placement="right" title="<?php echo _DISMISS;?>" data-dismiss="alert" data-user="<?php echo $userid;?>" data-id="<?php echo $row["id"];?> " aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
												   
												<?php								
													
												}
										?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">

							<?php
								$arrayMemberCurrent= array();
								$arrayMemberCurrent = $dbf->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
								$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");
								$currentDate = date('Y-m-d',time());
								$arrayRanking = $dbf->YesterdayRanking('history_payment',$currentDate);
								if(!empty($arrayRanking)){
								$arrayRanking = array_values($arrayRanking);
								//$arrayRanking = array_filter($arrayRanking, function ($x) { return $x < 0; });
								$arrayRanking = array_slice($arrayRanking, 0, 5);
								
							?>
							<div class="block full">

							<div class="block-title">
								<h2><?php echo _TOP5;?></h2>
							</div>
             

							<div class="form-group">
							   

							      <?php

							        
									//printf('<pre>%s</pre>',print_r($arrayRanking,true));
							            foreach($arrayRanking as $row)
							            {
											if($row["sum_value"]<0) continue;
											$memberInfo = $dbf->getInfoColum("member",$row["member_id"]);
											$percent = ($row["sum_value"] * 100) / $arrayRanking[0]["sum_value"];
											echo '<style>
												.top5_'.$row['member_id'].' {
												  width: 0;
												  animation: progress_top5_'.$row['member_id'].' 1.6s ease-in-out forwards;
												  
												  .top5_title_'.$row['member_id'].' {
													opacity: 0;
													animation: show_top5_'.$row['member_id'].' 0.5s forwards ease-in-out 0.5s;
												  }
												} 

												@keyframes progress_top5_'.$row['member_id'].' {
												  from {
													width: 0;
												  }
												  to {
													width: '.$percent.'%;
												  }
												} 
												@keyframes show_top5_'.$row['member_id'].'  {
												  from {
													opacity: 0;
												  }
												  to {
													opacity: 1;
												  }
												}</style>';
											echo '<div class="row">';
											echo '<div class="col-md-3 control-label user-ranking" data-id="'.$row['member_id'].'" data-avatar="'.$memberInfo["picture"].'" data-desc="'.$memberInfo["description"].'" data-facebook="'.$memberInfo["fb"].'"><a href="javascript:void(0)">'.$memberInfo["hovaten"].'</a></div>';
											echo '<div class="col-md-9">';
											echo '<div class="progress">
												  <div class="progress-bar top5_'.$row['member_id'].'" role="progressbar" style="width: '.$percent.'%" aria-valuenow="'.$row["sum_value"].'" aria-valuemin="0" aria-valuemax="'.$arrayRanking[0]["sum_value"].'"><span class="top5_title_'.$row['member_id'].'">'.number_format($row["sum_value"]).'</span></div>
												</div>';
											echo '</div>';
											echo '</div>';
											
												
							            }
							        
							      ?>
							   

							</div>
                   			</div>
								<?php } //end TOP5?>
								<?php
								
								$result = $dbf->getDynamic("informations","status=1 AND FIND_IN_SET($userid, read_list)=0","id desc");
								$informations = $dbf->totalRows($result);
								
								if($informations>0) { ?>
									<div class="card">
										<div class="card-header">
											<strong class="card-title"><?php echo _INFOMATION;?></strong>
										</div>
										<div class="card-body">
											<?php 
												
												$str="";
												$i=1;
												while( $row = $dbf->nextData($result))
												{
												?>
												
												   <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
														<!--<span class="badge badge-pill badge-primary"><?php echo $row["title"];?></span>-->
														<h4 class="mb-2"><?php echo $row["title"];?></h4>
														<?php echo $row["content"];?>
														<button type="button" class="close dismis_infomation" data-toggle="tooltip" data-placement="right" title="<?php echo _DISMISS;?>" data-dismiss="alert" data-user="<?php echo $userid;?>" data-id="<?php echo $row["id"];?>" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
												   
											<?php } ?>
										</div>
									</div>
							<?php } ?>
							<!--<div class="fb-comments" data-href="<?php echo HOST;?>" data-width="100%" data-numposts="5"></div>-->
							<?php if($rowgetInfo["roles_id"]==15) {
								$settings = array(
									'public' => false,
									'user_details' => array(
										'name' => $rowgetInfo["hovaten"],
										'email' => $rowgetInfo["email"]
										)
									);
							} else {
								$settings = array(
									'isAdmin' => true,
									'public' => false,
									'user_details' => array(
										'name' => $rowgetInfo["hovaten"],
										'email' => $rowgetInfo["email"]
										)
									);
							} // that is all you need to specify to be in admin mode :D ;
							
							?>
							<div class="card">
								<div class="card-header">
									<strong class="card-title">Add a comment...</strong>
								</div>
								<div class="card-body">
									<?php
									$page_id = 1;

									$comments = new Comments_System($settings);

									$comments->grabComment($page_id);

									if($comments->success)
										echo "<div class='alert alert-success' id='comm_status'>".$comments->success."</div>";
									else if($comments->error)
										echo "<div class='alert alert-warning' id='comm_status'>".$comments->error."</div>";

									// a simple form
									echo $comments->generateForm();

									// we show the posted comments
									echo '<div class="comment_content_div">'. $comments->generateComments($page_id) .'</div>'; // we pass the page id
									?>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="card">
										<div class="card-header">
											<strong class="card-title">Facebook</strong>
										</div>
										<div class="card-body">
											<div class="fb-page" data-href="https://www.facebook.com/neonagashimavn/" data-tabs="timeline, events, messages" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/neonagashimavn/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/neonagashimavn/">ネオナガシマ開発</a></blockquote></div>
										</div>
									</div>
							<div class="row">								
								<div class="col-md-5">
									
								</div>
								<div class="col-md-7">
									<!--<div class="card">
										<div class="card-header">
											<strong class="card-title">Youtube</strong>
										</div>
										<div class="card-body">
											<style>#playerWrap{display: inline-block; position: relative;}#playerWrap.shown::after{content:""; position: absolute; top: 0; left: 0; bottom: 0; right: 0; cursor: pointer; background-color: black; background-repeat: no-repeat; background-position: center; background-size: 64px 64px; background-image: url(data:image/svg+xml;utf8;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiB2aWV3Qm94PSIwIDAgNTEwIDUxMCI+PHBhdGggZD0iTTI1NSAxMDJWMEwxMjcuNSAxMjcuNSAyNTUgMjU1VjE1M2M4NC4xNSAwIDE1MyA2OC44NSAxNTMgMTUzcy02OC44NSAxNTMtMTUzIDE1My0xNTMtNjguODUtMTUzLTE1M0g1MWMwIDExMi4yIDkxLjggMjA0IDIwNCAyMDRzMjA0LTkxLjggMjA0LTIwNC05MS44LTIwNC0yMDQtMjA0eiIgZmlsbD0iI0ZGRiIvPjwvc3ZnPg==);}</style><div id="playerWrapOuter"> <div id="playerWrap" class="embed-responsive embed-responsive-16by9"> <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/hROjsiJ9rR8?rel=0&enablejsapi=1" frameborder="0" ></iframe></div></div><script>(function(){let playerFrame=document.currentScript.previousElementSibling.querySelector("iframe"); let tag=document.createElement('script'); tag.src="https://www.youtube.com/iframe_api"; let firstScriptTag=document.getElementsByTagName('script')[0]; firstScriptTag.parentNode.insertBefore(tag, firstScriptTag); let player; window.onYouTubeIframeAPIReady=function(){player=new YT.Player(playerFrame,{events:{'onStateChange': onPlayerStateChange}});}; window.onPlayerStateChange=function(event){if (event.data==YT.PlayerState.ENDED){document.getElementById("playerWrap").classList.add("shown");}}; document.getElementById("playerWrap").addEventListener("click", function(){player.seekTo(0); document.getElementById("playerWrap").classList.remove("shown");});})();</script>
										</div>
									</div>-->
								</div>
							</div>
						</div>
				
                   
                </div>
            </div>

<div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php
}
?>

<script>
 $(document).ready(function() {
      /*$('.progress .progress-bar').css("width",
                function() {
                    return $(this).attr("aria-valuenow") + '%';
                }
        )*/
    });
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});
$( ".user-ranking,.user_comment_info" ).click(function()
{
	var member_id = $(this).attr("data-id"); 
	$.confirm({
		boxWidth: '50%',
		useBootstrap: false,
		title: '',
		type: 'blue',
		draggable: true,
		backgroundDismiss: true,
		closeIcon: true,
		closeIconClass: 'fa fa-close',
		content: '<div class="form-group"><div class="row"><div class="col-md-3"><div class="div-avatar circle"><img class="rounded-circle mx-auto d-block select-image" src="'+ $(this).attr("data-avatar") +'" alt="'+$(this).text()+'"></div></div><div class="col-md-9"> <h5 class="mb-2">'+$(this).attr("data-name")+'</h5><p>'+ $(this).attr("data-desc") +'</p><p class="mt-2">Facebook: <a href="'+ $(this).attr("data-facebook") +'">'+ $(this).attr("data-facebook") +'</a></p></div></div></div>',
		buttons: {
			close: function () {
				//close
			},
		},
		onContentReady: function () {
			// bind to events
			var jc = this;
			var $img = $("img"),
				width = $img.width(),
				height = $img.height(),
				tallAndNarrow = width / height < 1;
				  //if (tallAndNarrow) {
					$img.addClass('tallAndNarrow');
				  //}
			  $img.addClass('loaded');
		}
	});
});

$(document).on('click', '.comm_del', function() {

	var comm_id = parseInt($(this).attr( 'data-id'));
	$.confirm({
		title: false,
		type: 'blue',
		draggable: true,
		content: '<?php echo _RUSUREDEL;?>',
		buttons: {
			confirm: {
				text: '<?php echo _YES;?>',
				action: function(){

						/*begin ajax >>*/
						$.ajax({
							type: "GET",
							data:{comm_del_id:comm_id},  
							dataType: 'json',
							url: ajax_url,
							success: function(response) {
								if(response["status"]==1) {
									console.log(response["data"]);
									$('#' + response["data"]).remove();
									
								} else {
									$.alert({
										title: false,
										closeIcon: false,
										//autoClose: 'confirm|6000',
										content: '<?php echo _CANNOTDEL;?>',
										buttons: {
											confirm: {
												text: '<?php echo _CLOSE;?>',
											}
										}
										});						
								}
							}
						});
						/*End ajax << */
				}
											
			},
			cancel: {
				text: '<?php echo _NOT;?>',
			}
		}
	});
});
</script>