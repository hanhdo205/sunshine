   <?php
	  $rst = $dbf->getDynamic("banner", "banner_category_id=1 and status=1", "position asc");
	  if($dbf->totalRows($rst)>0)
	  {
	  echo '<div class="row fix-box-height-byrow" style="margin-bottom: 20px;">';
	  $i=1;
	  while ($row = $dbf->nextData($rst)){
		$picture=stripslashes($row['picture']);
		$url=stripslashes($row['url']);
		if($i%2==0){
			$style_css = "style='padding-left:5px'";
		}else 
		{
			$style_css = "style='padding-right:5px'";
		}
	 ?>
		<div class="col-lg-6 content_img_banner" <?php echo $style_css ;?>>
		     <img src="<?php echo $picture;?>" alt="" border="0" />
		</div>
   <?php
		$i++;
	   }
	    echo "</div>"; 
	  }
   ?>

