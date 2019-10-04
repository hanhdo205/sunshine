<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(0);
// This code is use for delete order's uploaded files after x time
	 $path = "upload/orders/";
		  if ($handle = opendir($path)) {

			while (false !== ($file = readdir($handle))) {
				if ((time()-filectime($path.'/'.$file)) > 7776000) {  // 7776000 = 60*60*24*90
					echo time() . '<br>'.filectime($path.'/'.$file);
						unlink($path.'/'.$file);
				}
			}
		  }

?>
		