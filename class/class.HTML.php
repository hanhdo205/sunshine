<?php
	class HTML
	{
		function docType()
		{
			//echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' ;
            echo '<!DOCTYPE html>';
		}

		function title($title)
		{
            try
            {
                echo "<title>$title</title>\n" ;
            }catch(Exception $ex)
            {

            }
		}

        function meta($arrayOption=null)
        {
            if(is_array($arrayOption))
            return "<meta name=\"".$arrayOption["name"]."\" content=\"".$arrayOption["content"]."\" />\n" ;
        }

		function description($description)
		{
            try
            {
                echo $this->meta(array("name"=>"description","content"=>$description));
            }catch(Exception $ex)
            {

            }
		}

		function metaEncoding($charset)
		{
            try
            {
                echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=$charset\"/>" ;
            }catch(Exception $ex)
            {

            }
		}

		function metaRefresh($second)
		{
            try
            {
                echo "\n<meta http-equiv=\"refresh\" content=\"$second\"/>" ;
            }catch(Exception $ex)
            {

            }
		}

		function keywords($keywords)
		{
            try
            {
                echo $this->meta(array("name"=>"keywords","content"=>$keywords));
            }catch(Exception $ex)
            {

            }
		}

		function copyright($copyright)
		{
            try
            {
                echo $this->meta(array("name"=>"copyright","content"=>$copyright));
            }catch(Exception $ex)
            {

            }
		}
		function openHead()
		{
            try
            {
                echo "\n<head>\n" ;
            }catch(Exception $ex)
            {

            }
		}

		function closeHead()
		{
            try
            {
                echo "</head>\n" ;
            }catch(Exception $ex)
            {

            }
		}

		function openHTML($arrayOption = null)
		{
            try
            {
              //echo "\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\" oncontextmenu=\"" . $arrayOption["oncontextmenu"] . "\">";
              echo "\n<html class=\"js no-touch\">";

            }catch(Exception $ex)
            {

            }
		}

		function closeHTML()
		{
            try
            {
                echo "</html>" ;
            }catch(Exception $ex)
            {

            }
		}

		function openBody($arrayOption)
		{
            try
            {
                echo "<body style=\"".$arrayOption["style"]."\" onload=\"" . $arrayOption["onload"] . "\" oncontextmenu=\"" . $arrayOption["oncontextmenu"] . "\">\n" ;

            }catch(Exception $ex)
            {

            }
		}

		function closeBody()
		{
            try
            {
                
                echo "</body>\n" ;
            }catch(Exception $ex)
            {

            }
		}

        function linkIcon($url)
		{
			echo "<link rel=\"icon\" type=\"image/x-icon\" href=\"$url\"/>\n" ;
			echo "<link rel=\"shortcut icon\" type=\"image/x-icon\"  href=\"$url\"/>\n" ;
		}

		function clear()
		{
			return "<div id=\"clear\"></div>" ;
		}

		function link($title, $url, $array= null)
		{
			return "<a href=\"$url\" id=\"" . $array['id'] . "\" class=\"" . $array['class'] . "\" target=\"" . $array['target'] . "\"
		            onclick=\"" . $array['onclick'] . "\" onmouseover=\"" . $array['onmouseover'] . "\"
		            onmouseout=\"" . $array['onmouseout'] . "\" style=\"" . $array['style'] . "\">" . stripcslashes($title) . "</a>" ;
		}

		function image($url, $array= null)
		{
			return "<img id=\"" . $array["id"] . "\" name=\"" . $array["id"] . "\" src=\"" . $url . "\" border=\"0\" class=\"" . $array["class"] . "\"
                    style=\"" . $array["style"] . "\" alt=\"" . $array["alt"] . "\" onclick=\"" . $array["onclick"] . "\" onmouseout=\"" . $array["onmouseout"] . "\"  onmouseover=\"" . $array["onmouseover"] . "\"/>" ;
		}

		function normalForm($idName, $array= null)
		{
			echo "<form id=\"$idName\" name=\"$idName\" method=\"post\" action=\"" . $array["action"] . "\" enctype=\"application/x-www-form-urlencoded\" class=\"" . $array['class'] . "\" onsubmit=\"" . $array['onsubmit'] . "\"/>" ;
		}

		function FormUpload($idName, $array= null)
		{
			echo "<form id=\"$idName\" name=\"$idName\" method=\"post\" action=\"" . $array["action"] . "\"  enctype=\"multipart/form-data\" class=\"" . $array['class'] . "\" onsubmit=\"" . $array['onsubmit'] . "\"/>" ;
		}

		function closeForm()
		{
			echo "</form>" ;
		}

		function input($idName, $array= null)
		{
			$str = "<input type=\"" . $array["type"] . "\" size=\"" . $array["size"] . "\"
            name=\"" . $idName . "\"" . (($array["id"] != "") ? "id=" . $array["id"] : "id=\"" . $idName . "\"") . "
			class=\"" . $array["class"] . "\" style=\"" . $array["style"] . "\" value=\"" . $array["value"] . "\"
			src=\"" . $array["src"] . " \" " . (($array["readonly"] == "readonly") ? "readonly='true'" : "") . "
			maxlength=\"" . $array["maxlength"] . "\" onmouseover=\"" . $array["onmouseover"] . "\"
			onmouseout=\"" . $array["onmouseout"] . "\" onclick=\"" . $array["onclick"] . "\"
			onkeypress=\"" . $array["onkeypress"] . "\" onkeyup=\"" . $array["onkeyup"] . "\"
			onchange=\"" . $array["onchange"] . " \" onblur=\"" . $array["onblur"] . "\"
			onfocus=\"" . $array["onfocus"] ."\""." ".$array["disabled"]."/>" ;
			return $str ;
		}

		function Button($idName, $arrayOption = null, $arrayClass = null)
		{
            try
            {
                return "
                <table border='0' cellpadding='0' cellspacing='0'>
            	    <tr>
                        <td class=\"" . $arrayClass[0] . "\"></td>
                		<td>" . $this->input($idName, $arrayOption) . "</td>
                		<td class=\"" . $arrayClass[0] . "\"></td>
            		</tr>
            	</table>" ;
            }catch(Exception $ex)
            {

            }
		}

		function checkbox($idName, $value, $text, $array= null)
		{
			return "<input type=\"checkbox\" name=\"$idName\" id=\"$idName\" value=\"$value\"	onclick=\"" . $array["onclick"] . "\" " . (($array["checked"] == 1) ? "checked='checked'" : "") . "
            style=\"" . $array["style"] . "\"	class=\"" . $array["class"] . "\"/> $text" ;
		}

		function radio($idName, $value, $text, $array= null)
		{
			return "<input type=\"radio\" name=\"$idName\" id=\"$idName\" value=\"$value\" onclick=\"" . $array["onclick"] . "\" " . (($array["checked"] == 1) ? "checked='checked'" : "") . "' style=\"" . $array["style"] . "\"	class=\"" . $array["class"] . "\"/> $text" ;
		}
        /*	-----------------------------------------------------------------*/
	  function redirectURL($url) {
	    echo "<script type='text/javascript'>window.location='" . $url . "';</script>";
	  }
  	/*	-----------------------------------------------------------------*/

		function textArea($idName, $array= null)
		{
			return "<textarea id=\"$idName\" name=\"$idName\"  class=\"" . $array["class"] . "\" style=\"" . $array["style"] . "\">" . $array["value"] . "</textarea>" ;
		}


        function supportJavascript()
        {
        echo "<noscript>
        <div style='width:650px;position:relative;margin:0 auto;top:100px;border:1px solid #ccc'>
        	<div style='width:600px;position:relative;margin:0 auto;margin-top:20px; color:#fff'>
        	<h3>Trình duyệt bạn đang xài không hỗ trợ Javascript.</h>
        	<p>Vui lòng kiểm tra lại các mục sau đây:</p>
        	<p class='ie'>Đối với IE:</p>
        	<p>
        		<ul>
        			<li>Vui lòng vào mục:Tool->Internet option->Chọn tab Security->Chọn Default level</li>
        			<li>Vui lòng vào mục:Tool->Internet option->Chọn tab Privacy->Hạ xuống mức Medium</li>
        			<li>Nâng cấp IE6.0 trở lên</li>
        		</ul>
        	</p>
        	<p class='ie'>Đối với FireFox:</p>
        	<p>
        		<ul>
        			<li>Vui lòng vào mục:Tool->Options->Chọn tab Content-> Chọn Enable Javascript</li>
        			<li>Nếu bạn xài Add-ons của Firefox(No Script,Yes script,...). Vui lòng disable chúng đi, vì đây là những chương trình không cho chạy javascript</li>
        			<li>Nâng cấp Firefox 1.0 trở lên</li>
        		</ul>
        	</p>
        	</div>
        	<div style='clear:both'></div>
        	</div>
        </noscript>" ;
        }
	}
?>
