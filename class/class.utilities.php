<?php
class UTILITIES {
	/* 	*******************************************************************/
	function checkBrowser() {
		$browser = $_SERVER["HTTP_USER_AGENT"];
		if(strstr($browser, "Firefox") !="")
			return "FF";
		elseif (strstr($browser, "MSIE 7.0") !="")
			return "IE7.0";
		elseif (strstr($browser, "MSIE 6.0") !="")
			return "IE6.0";
	}
	/*	-----------------------------------------------------------------*/
    function format($price) {
                if((int)$price==0)
						return $price;
				else
						return number_format($price,0,".",",");
	}

    function price($price){
        $str = number_format($price,0,",",".");
        return $str;
   }
    function generate_url_from_text($strText)
    {
      $strText = preg_replace('/[^A-Za-z0-9-]/', ' ', $strText);
      $strText = preg_replace('/ +/', ' ', $strText);
      $strText = trim($strText);
      $strText = str_replace(' ', '-', $strText);
      $strText = preg_replace('/-+/', '-', $strText);
      $strText=  preg_replace("/-$/","",$strText);
      return $strText;
    }
	/*  ******************************************************************/
	function checkFile($name, $arrayExt, $arrayUpload, $capacity, & $fname, $pathupload) {
		//status=-1: File không có,status=1: upload thanh cong;
        //status=2: upload that bai
		//status=3: kieu file khong duoc phep;status=4: Dung luong file vuot qua 100kb;
		$status = 1;
		$tmp_name = $_FILES[$name]['tmp_name'];
		$fname = $_FILES[$name]['name'];
		if($fname == "") {
			$status = - 1;
			$fname = "";
			return $status;
		}
        $part = pathinfo($fname);
        $fname=$this->stripUnicode($fname);
        $fname=$this->generate_url_from_text($fname);
        $ext=".".strtolower($part["extension"]);
        $fname.=$ext;
		if(in_array($ext, $arrayExt)) {
			$xsmall = date("YmdHis") . $fname;
			if($_FILES[$name]['size'] <= $capacity) {
				if(move_uploaded_file($tmp_name, $arrayUpload[$pathupload] . $xsmall))
					$status = 1;
				else
					$status = 2;
			}
			else
				$status = 4;
		}
		else {
			$status = 3;
		}
        $fname=$xsmall;
		return $status;
	}
    /*  ******************************************************************/
     function stripUnicode($str){
        if(!$str) return false;
        $str=strip_tags($str);
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
         );
        foreach($unicode as $nonUnicode=>$uni) $str = preg_replace("/($uni)/i",$nonUnicode,$str);
        return $str;
    }
	/*  ******************************************************************/
	function createTextImage($image,$text="",$font="",$color="#000000",$size=10,$x=0,$y=0,$gocquay=0,$folder="") {
	    $type = strtolower(substr($image,-3));
        $wh=getimagesize($image);
        $originalWidth=$wh[0];
        $orginalHeight=$wh[1];
    /*  ******************************************************************/
        if(eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $color, $ca)) {
			$red = hexdec($ca[1]);
			$green = hexdec($ca[2]);
			$blue = hexdec($ca[3]);
		}
        switch($type)
        {
          case "jpg":
          case "jpeg":
            $im=imagecreatefromjpeg($image);
            $img=imagecreatetruecolor($originalWidth,$orginalHeight);
            imagecopyresized($img,$im,0,0,0,0,$originalWidth,$orginalHeight,$originalWidth,$orginalHeight);
            $color=imagecolorallocate($im,$red,$green,$blue);
            imagettftext($img,$size,$gocquay,$x,$y,$color,$font,$text);
            $text=str_replace(" ","",$this->stripUnicode($text));
            imagejpeg($img,$folder.$text.".jpg",100);
            imagedestroy($img);
            $filename=$folder.$text.".jpg";
            break;
          case "png":
            $im=imagecreatefrompng($image);
            $img=imagecreatetruecolor($originalWidth,$orginalHeight);
            imagecopyresized($img,$im,0,0,0,0,$originalWidth,$orginalHeight,$originalWidth,$orginalHeight);
            $color=imagecolorallocate($im,$red,$green,$blue);
            imagettftext($img,$size,$gocquay,$x,$y,$color,$font,$text);
            $text=str_replace(" ","",$this->stripUnicode($text));
            imagepng($img,$folder.$text.".png");
            imagedestroy($img);
            $filename=$folder.$text.".png";
            break;
          case "gif":
            $im=imagecreatefromgif($image);
            $img=imagecreatetruecolor($originalWidth,$orginalHeight);
            imagecopyresized($img,$im,0,0,0,0,$originalWidth,$orginalHeight,$originalWidth,$orginalHeight);
            $color=imagecolorallocate($im,$red,$green,$blue);
            imagettftext($img,$size,$gocquay,$x,$y,$color,$font,$text);
            $text=str_replace(" ","",$this->stripUnicode($text));
            imagegif($img,$folder.$text.".gif");
            imagedestroy($img);
            $filename=$folder.$text.".gif";
            break;
        }
        return $filename;
	}
	/* 	*******************************************************************/
    function takeShortText($longText,$numWords){
		$ret="";
		if($longText!=""){
			$longText=trim($longText);
            $longText=stripslashes($longText);
			$longText=strip_tags($longText);
			if(str_word_count($longText)>$numWords){
				$arrayText=explode(" ",$longText);
				for($i=0;$i<$numWords;$i++){
					$ret.=$arrayText[$i]." ";
				}
				$ret=trim($ret)."... ";
				return $ret;
			}
			else{
				return $longText;
			}
		}
	}
	/* *******************************************************************/
	function takeShortTextWithTag($longText, $numWords) {
		$ret = "";
		if($longText != "") {
			$longText = trim($longText);
			if(str_word_count($longText) > $numWords) {
				$arrayText = split(" ", $longText, $numWords);
				for ($i = 0; $i < $numWords - 1; $i++) {
					$ret .= $arrayText[$i] . " ";
				}
				$ret = trim($ret) . "... ";
				return $ret;
			}
			else {
				return $longText;
			}
		}
	}
	/* *******************************************************************/
	function encodeHTML($sHTML) {
		$sHTML = stripslashes($sHTML);
		$sHTML = ereg_replace("&", "&amp;", $sHTML);
		$sHTML = ereg_replace("<", "&lt;", $sHTML);
		$sHTML = ereg_replace(">", "&gt;", $sHTML);
		return $sHTML;
	}
	/* *******************************************************************/
	function fileExtension($filename) {
		$pathInfo = pathinfo($filename);
		return $pathInfo["extension"];
	}
	/* *******************************************************************/
	function stringToArray($char = ',', $value) {
		return explode($char, $value);
	}
	/* *******************************************************************/
	function iff($expression, $returntrue = '', $returnfalse = '') {
		return ($expression ? $returntrue : $returnfalse);
	}
	/* *******************************************************************/
	function getDate() {
		$date = getdate();
		return $date["year"] . "." . $date["mon"] . "." . $date["mday"];
	}
	/* *******************************************************************/
	function formatDate($stringFormat, $stringDate) {
		return date($stringFormat, strtotime($stringDate));
	}
	/* *******************************************************************/
	function make_pass() {
		$pass = "";
		$chars = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "a", "A", "b", "B", "c",
		              "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J",
		              "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r",
		              "R", "s", "S", "t", "T", "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y",
		              "z", "Z");
		$count = count($chars) - 1;
		srand((double) microtime() * 1000000);
		for ($i = 0; $i < 6; $i++)
			$pass .= $chars[rand(0, $count)];
		for ($i = 0; $i < 6; $i++) {
			if(is_numeric(substr($pass, $i, 1)))
				break;
		}
		if($i == 6)
			$pass = substr($pass, 0, 2) . rand(0, 9) . substr($pass, 3, 3);
		return ($pass);
	}
	/* *******************************************************************/
	function make_protect_image($image="",$font="",$protect="",$color="#000000",$size=15) {
        if(eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $color, $ca)) {
			$red = hexdec($ca[1]);
			$green = hexdec($ca[2]);
			$blue = hexdec($ca[3]);
        }
        $pass = "";
		$chars = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
		$count = count($chars) - 1;
		srand((double) microtime() * 1000000);
		for ($i = 0; $i < 5; $i++)
			$pass .= $chars[rand(0, $count)];
		$img = imagecreatefromjpeg($image);
		$text_color = imagecolorallocate($img, $red, $green, $blue);
		$w = 10;
		srand((double) microtime() * 1000000);
		for ($i = 0; $i < strlen($pass); $i++) {
			$a = rand(0, 0);
			$t = substr($pass, $i, 1);
			imagettftext($img,$size,$a,$w,30,$text_color,$font,$t);
			$w = $w + 15;
		}
		imagejpeg($img, $protect);
		@ imagedestroy($img);
		return ($pass);
	}
	/* *******************************************************************/
	function chk_email($email) {
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3}[,]?)+$",
		  $email)) {
			return false;
		}
		return true;
	}
	/* *******************************************************************/
    function watermark($srcfilename, $newname, $watermark, $quality) {
    	$imageInfo = getimagesize($srcfilename);
    	$width = $imageInfo[0];
    	$height = $imageInfo[1];
    	$logoinfo = getimagesize($watermark);
    	$logowidth = $logoinfo[0];
    	$logoheight = $logoinfo[1];
    	$horizextra =$width - $logowidth;
    	$vertextra =$height - $logoheight;
    	$horizmargin = round($horizextra / 2);
    	$vertmargin = round($vertextra / 2);
    	$photoImage = ImageCreateFromJPEG($srcfilename);
    	ImageAlphaBlending($photoImage, true);
    	$logoImage = ImageCreateFromPNG($watermark);
    	$logoW = ImageSX($logoImage);
    	$logoH = ImageSY($logoImage);
    	ImageCopy($photoImage, $logoImage, $horizmargin, $vertmargin, 0, 0, $logoW, $logoH);
    	ImageJPEG($photoImage,"",$quality); // output to browser
    	//uncomment next line to save the watermarked image to a directory. need write access(changed directory to anything)
    	//ImageJPEG($photoImage, "../stock_photos/" . $newname, $quality);
    	//ImageDestroy($photoImage);
    	ImageDestroy($logoImage);
    }
	/* *******************************************************************/
    function getimage ($image)
    {
    	switch ($image)
        {
    	case 'file':
    		return base64_decode('R0lGODlhEQANAJEDAJmZmf///wAAAP///yH5BAHoAwMALAAAAAARAA0AAAItnIGJxg0B42rsiSvCA/REmXQWhmnih3LUSGaqg35vFbSXucbSabunjnMohq8CADsA'); break;
    	case 'folder':
    		return base64_decode('R0lGODlhEQANAJEDAJmZmf///8zMzP///yH5BAHoAwMALAAAAAARAA0AAAIqnI+ZwKwbYgTPtIudlbwLOgCBQJYmCYrn+m3smY5vGc+0a7dhjh7ZbygAADsA'); break;
    	case 'hidden_file':
    		return base64_decode('R0lGODlhEQANAJEDAMwAAP///5mZmf///yH5BAHoAwMALAAAAAARAA0AAAItnIGJxg0B42rsiSvCA/REmXQWhmnih3LUSGaqg35vFbSXucbSabunjnMohq8CADsA'); break;
    	case 'link':
    		return base64_decode('R0lGODlhEQANAKIEAJmZmf///wAAAMwAAP///wAAAAAAAAAAACH5BAHoAwQALAAAAAARAA0AAAM5SArcrDCCQOuLcIotwgTYUllNOA0DxXkmhY4shM5zsMUKTY8gNgUvW6cnAaZgxMyIM2zBLCaHlJgAADsA');break;
    	case 'smiley':
    		return base64_decode('R0lGODlhEQANAJECAAAAAP//AP///wAAACH5BAHoAwIALAAAAAARAA0AAAIslI+pAu2wDAiz0jWD3hqmBzZf1VCleJQch0rkdnppB3dKZuIygrMRE/oJDwUAOwA=');break;
    	case 'arrow':
    		return base64_decode('R0lGODlhDwAMAIAAAP39/ZmZmSH5BAEHAAAALAAAAAAPAAwAAAIjBIKmqxjn3JJvsgZfjfT4HGWfllTeJpLaVDJmyr3r5X40UAAAOw==');break;
        case "":
        break;
    	}
    }
	
	function randomPassword($length,$count, $characters) {
 
		// $length - the length of the generated password
		// $count - number of passwords to be generated
		// $characters - types of characters to be used in the password
		 
		// define variables used within the function    
		$symbols = array();
		$passwords = array();
		$used_symbols = '';
		$pass = '';
		 
		// an array of different character types    
		$symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
		$symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$symbols["numbers"] = '1234567890';
		$symbols["special_symbols"] = '!?~@#-_+<>[]{}';
	 
		$characters = explode(",",$characters); // get characters types to be used for the passsword
		foreach ($characters as $key=>$value) {
			$used_symbols .= $symbols[$value]; // build a string with all characters
		}
		$symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
		 
		for ($p = 0; $p < $count; $p++) {
			$pass = '';
			for ($i = 0; $i < $length; $i++) {
				$n = rand(0, $symbols_length); // get a random character from the string with all characters
				$pass .= $used_symbols[$n]; // add the character to the password string
			}
			$passwords[] = $pass;
		}
		 
		return $passwords; // return the generated password
	}
	
	/**
     * Human-friendly Time Ago PHP Function
     *
     * @return string
     */
	public function time_ago( $timestamp = 0, $now = 0 ) {

		// Set up our variables.
		$minute_in_seconds = 60;
		$hour_in_seconds   = $minute_in_seconds * 60;
		$day_in_seconds    = $hour_in_seconds * 24;
		$week_in_seconds   = $day_in_seconds * 7;
		$month_in_seconds  = $day_in_seconds * 30;
		$year_in_seconds   = $day_in_seconds * 365;

		// Get the current time if a reference point has not been provided.
		if ( 0 === $now ) {
			$now = time();
		}

		// Make sure the timestamp to check is in the past.
		if ( $timestamp > $now ) {
			throw new Exception( 'Timestamp is in the future' );
		}

		// Calculate the time difference between the current time reference point and the timestamp we're comparing.
		$time_difference = (int) abs( $now - $timestamp );

		// Calculate the time ago using the smallest applicable unit.
		if ( $time_difference < $hour_in_seconds ) {

			$difference_value = round( $time_difference / $minute_in_seconds );
			$difference_label = T_('minute');

		} elseif ( $time_difference < $day_in_seconds ) {

			$difference_value = round( $time_difference / $hour_in_seconds );
			$difference_label = T_('hour');

		} elseif ( $time_difference < $week_in_seconds ) {

			$difference_value = round( $time_difference / $day_in_seconds );
			$difference_label = T_('day');

		} elseif ( $time_difference < $month_in_seconds ) {

			$difference_value = round( $time_difference / $week_in_seconds );
			$difference_label = T_('week');

		} elseif ( $time_difference < $year_in_seconds ) {

			$difference_value = round( $time_difference / $month_in_seconds );
			$difference_label = T_('month');

		} else {

			$difference_value = round( $time_difference / $year_in_seconds );
			$difference_label = T_('year');
		}

		if ( $difference_value <= 1 ) {
			$time_ago = sprintf( T_('one %s ago'),
				$difference_label
			);
		} else {
			$time_ago = sprintf( T_('%s %ss ago'),
				$difference_value,
				$difference_label
			);
		}

		return $time_ago;
	}
	
	/**
     *  Text Excerpt
     *
     * @return string
     */
	function shorten_text($text, $max_length = 200, $cut_off = '...', $keep_word = false) {
		$text = preg_replace("/<img[^>]+\>/i", "(image) ", $text);
		$text = preg_replace( "/\r|\n/", "", $text );
		if(strlen($text) <= $max_length) {
			return $text;
		}

		if(strlen($text) > $max_length) {
			if($keep_word) {
				$text = substr($text, 0, $max_length + 1);

				if($last_space = strrpos($text, ' ')) {
					$text = substr($text, 0, $last_space);
					$text = rtrim($text);
					$text .=  $cut_off;
				}
			} else {
				$text = substr($text, 0, $max_length);
				$text = rtrim($text);
				$text .=  $cut_off;
			}
		}

		return $text;
	}
	
	
	function checked($pattern, $value) {
		if(in_array($value,$pattern))
			return true;
		return false;
	}

	function selected($pattern, $value) {
		if($value==$pattern) return 'selected';
		return '';
	}
	
	/**
     * get month options
     *
     * @return months
     */
    function get_month_options() {
		$months = '<option value="1">'.__('Jan').'</option>
					<option value="2">'.__('Feb').'</option>
					<option value="3">'.__('Mar').'</option>
					<option value="4">'.__('Apr').'</option>
					<option value="5">'.__('May').'</option>
					<option value="6">'.__('Jun').'</option>
					<option value="7">'.__('Jul').'</option>
					<option value="8">'.__('Aug').'</option>
					<option value="9">'.__('Sep').'</option>
					<option value="10">'.__('Oct').'</option>
					<option value="11">'.__('Nov').'</option>
					<option value="12">'.__('Dec').'</option>';
        return $months;
    }
	
	/**
     * get year options
     *
     * @return years
     */
    function get_year_options() {
		$nowY = date('Y');
        $nextY = date('Y', strtotime('+1 year'));
        $next2Y = date('Y', strtotime('+2 year'));
        $next3Y = date('Y', strtotime('+3 year'));
		$years = '<option value="'.$nowY.'">'.$nowY.'</option>
					<option value="'.$nextY.'">'.$nextY.'</option>
					<option value="'.$next2Y.'">'.$next2Y.'</option>
					<option value="'.$next3Y.'">'.$next3Y.'</option>';
        return $years;
    }
	
	/**
     * convert string to slug
     *
     * @return string
     */
	function create_slug($string) {
				
                                $string = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $string);
                                $string = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $string);
                                $string = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $string);
                                $string = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $string);
                                $string = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $string);
                                $string = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $string);
                                $string = preg_replace("/(đ)/", 'd', $string);
                                $string = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'a', $string);
                                $string = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'e', $string);
                                $string = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $string);
                                $string = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'o', $string);
                                $string = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'u', $string);
                                $string = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'y', $string);
                                $string = preg_replace("/(Đ)/", 'd', $string);
                                $string = preg_replace("/(%|&|#|$|(|)|!)/", '', $string);
                                //$string = str_replace(" ", "-", str_replace("&*#39;","",$string));
                                $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $string);
                                $slug = strtolower($slug);
				return $slug;
		}
		
	function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

	function random_color() {
		return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
	}
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	function encode_filename($string) {
	   
	   //$string = mb_convert_encoding($string, 'SJIS-win', mb_internal_encoding());
	   //return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
	   //$string = preg_replace('/[^.\-\w\s]+/u','' ,$string); // Removes special chars.
	   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   return urlencode($string);
	}
	
	function decode_filename($string) {
	   return urldecode($string);
	}
	
	function get_next_reminder_date($start_date, $frequency, $selected_time)
	{
		$date = new DateTime($start_date);
		$selected = new DateTime($selected_time);
		switch ($frequency) {
			case 'weekly' :
				$interval = 'P1W';
				break;
			case 1 :
				$interval = 'P1M';
				break;
			case 2 :
				$interval = 'P2M';
				break;
			case 3 :
				$interval = 'P3M';
				break;
			case 4 :
				$interval = 'P4M';
				break;
			case 5 :
				$interval = 'P5M';
				break;
			case 6 :
				$interval = 'P6M';
				break;
			case 7 :
				$interval = 'P7M';
				break;
			case 8 :
				$interval = 'P8M';
				break;
			case 9 :
				$interval = 'P9M';
				break;
			case 10 :
				$interval = 'P10M';
				break;
			case 11 :
				$interval = 'P11M';
				break;
			case 12 :
				$interval = 'P1Y';
				break;
		}

		$date->add(new DateInterval($interval));

		//if ( time() > $date->getTimestamp() ) {
		if ( $selected->getTimestamp() > $date->getTimestamp() ) {
			//echo $date->format('Y-m-d');
			//echo ' Not there yet' . PHP_EOL;
			return $this->get_next_reminder_date($date->format('Y/m/d'), $frequency, $selected_time);
		} else {
			return $date->format('Y/m/d');
		}
	}
}
?>