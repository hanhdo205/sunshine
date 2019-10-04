<?php
include str_replace('\\', '/', dirname(__FILE__)) . '/class.DBFUNCTION.php';
class BUSINESSLOGIC extends DBFUNCTION {
/*******************************************************************/
  function price($price) {
    $str = number_format($price, 0, ",", ".");
    return $str;
  }
  
   function filter($data) { // step2...
        $data = strip_tags($data);
        $data = trim(htmlentities($data, ENT_QUOTES, "UTF-8"));
        if (get_magic_quotes_gpc())
            $data = stripslashes($data);
        return $data;
    }
  
  function signout() {
    //session_unset();
    //session_destroy();
    $_SESSION["login"] = session_id();
	$array_log = array("member_id"=>$_SESSION["member_id"],"name_member"=>$_SESSION["member_hovaten"],"content_log"=>"at " . date("Y-m-d H:i:s"),"type_query"=>"logout","table_name"=>"member","sqlquery"=>"User logout","datecreated"=>time());
	if($_SESSION["member_id"]!=1)
		$this->insertTable_2("history_logs", $array_log);
	$info_account = $this->getInfoColum("member",$_SESSION["member_id"]);
	$role_id = $info_account["roles_id"];
    unset($_SESSION["member_id"]);
    unset($_SESSION["member_email"]);
    unset($_SESSION["member_hovaten"]);
    unset($_SESSION["Free"]);
    unset($_SESSION["currentmember"]);
    unset( $_SESSION["password2"]);
	unset($_SESSION["member_active"]);
    $_SESSION["Free"] = 1;
	
	// if($role_id<15) echo "<script type='text/javascript'>window.location='/admin';</script>";
	// else echo "<script type='text/javascript'>window.location='/home';</script>";
	
	echo "<script type='text/javascript'>window.location='/home';</script>";
  }
/********************************************************************/
  function Button($idName, $arrayOption) {
    return "<table border='0' cellpadding='0' cellspacing='0' ALPHA8>

    					<tr><td class='btnleftSearch'></td>

						<td>" . $this->input($idName, $arrayOption) . "</td>

    					<td class='btnrightSearch'></td>

    			</tr>

    	</table>";
  }
/*******************************************************************/
  function takeShortText($longText, $numWords) {
    $ret = "";
    if ($longText != "") {
      $longText = trim($longText);
      $longText = stripslashes($longText);
      $longText = strip_tags($longText);
      if (str_word_count($longText) > $numWords) {
        $arrayText = explode(" ", $longText);
        for ($i = 0; $i < $numWords; $i++) {
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
  function returnUser() {
    if ((!session_is_registered("login")) || ($_SESSION['login'] == "")) {
      $_SESSION["login"] = session_id();
    }
    else {
      return $_SESSION['login'];
    }
  }
  function make_protect_image($image = "", $font = "", $protect = "", $color = "#000000", $size = 15) {
    if (eregi("([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $color, $ca)) {
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
      $a = rand(45, - 45);
      $t = substr($pass, $i, 1);
      imagettftext($img, $size, $a, $w, 30, $text_color, $font, $t);
      $w = $w + 15;
    }
    imagejpeg($img, $protect);
    @ imagedestroy($img);
    return ($pass);
  }
/*

Paging on one table

*******************************************************************/
  function paging($tablename, $where, $orderby, $url, $PageNo, $PageSize, $Pagenumber, $ModePaging) {
    if ($PageNo == "") {
      $StartRow = 0;
      $PageNo = 1;
    }
    else
      $StartRow = ($PageNo - 1) * $PageSize;
    if ($PageSize < 1 || $PageSize > 50)
      $PageSize = 15;
    if ($PageNo % $Pagenumber == 0)
      $CounterStart = $PageNo - ($Pagenumber - 1);
    else
      $CounterStart = $PageNo - ($PageNo % $Pagenumber) + 1;
    $CounterEnd = $CounterStart + $Pagenumber;
    $TRecord = $this->getArray($tablename, $where, $orderby, "stdObject");
    $RecordCount = count($TRecord);
    $result = $this->getDynamic($tablename, $where, $orderby . " LIMIT " . $StartRow . "," . $PageSize);
    if ($RecordCount % $PageSize == 0)
      $MaxPage = $RecordCount / $PageSize;
    else
      $MaxPage = ceil($RecordCount / $PageSize);
    $gotopage = "";
    switch ($ModePaging) {
      case "Full" :
        $gotopage = '<div class="paging_meneame">';
        if ($MaxPage > 1) {
          if ($PageNo != 1) {
            $PrevStart = $PageNo - 1;
            $gotopage .= ' <a href="' . $url . '&PageNo=1" tile="First page"> &laquo; </a>';
            $gotopage .= ' <a href="' . $url . '&PageNo=' . $PrevStart . '" title="Previous page"> &lsaquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &laquo; </span>';
            $gotopage .= ' <span class="paging_disabled"> &lsaquo; </span>';
          }
          $c = 0;
          for ($c = $CounterStart; $c < $CounterEnd;++$c) {
            if ($c <= $MaxPage)
              if ($c == $PageNo)
                $gotopage .= '<span class="paging_current"> ' . $c . ' </span>';
              else
                $gotopage .= ' <a href="' . $url . '&PageNo=' . $c . '" title="Page ' . $c . '"> ' . $c . ' </a>';
          }
          if ($PageNo < $MaxPage) {
            $NextPage = $PageNo + 1;
            $gotopage .= ' <a href="' . $url . '&PageNo=' . $NextPage . '" title="Next page"> &rsaquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &rsaquo; </span>';
          }
          if ($PageNo < $MaxPage)
            $gotopage .= ' <a href="' . $url . '&PageNo=' . $MaxPage . '" title="Last page"> &raquo; </a>';
          else
            $gotopage .= ' <span class="paging_disabled"> &raquo; </span>';
        }
        $gotopage .= ' </div>';
        break;
    }
    $arr[0] = $result;
    $arr[1] = $gotopage;
    return $arr;
  }
  function getidCat($table, $value) {
    $result = $this->getDynamic($table, "id=" . $value, "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row["parentid"];
    }
  }
  
  
  function getInfoColum_crf($crf_token) {
    $result = $this->getDynamic("member", "crf_token_login='" . $crf_token . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
  
  function getInfoColum_username($username) {
    $result = $this->getDynamic("member", "tendangnhap='" . $username . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
  
  function getInfoColum_eth($eth_address) {
    $result = $this->getDynamic("member", "eth_address='" . $eth_address . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
  
  function getInfoColum($table, $id) {
    $result = $this->getDynamic($table, "id='" . $id . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
	return false;
  }
  
  function getActualColum($table, $id, $col) {
    
    $result = $this->getSum($table,$col,"member_id =".$id."" , "");
    $total = $this->totalRows($result);
	$sum = 0;
    if ($total > 0) {

      $strParentId = "";

      while ($row = $this->nextData($result)) {
        $sum = $row['value_sum'];
      }
    }
    return $sum;
  
  }
  
  

  function getInfoWithdrawdaily($member_id,$datecurrent){
    $result = $this->getDynamic("all_withdrawals", "member_id=" . (int)$member_id . " and datecreated =".$datecurrent."", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }

  function getInfoColumBalance($table,$member_id) {
    $result = $this->getDynamic($table, "member_id='".$member_id."'", "id desc limit 0,1");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }else
    {
      return 0;
    }
  }
  
  function getInfoColumBalance02($member_id) {
    $result = $this->getDynamic("balance_wallet","member_id='".$member_id."'", "");
    if ($this->totalRows($result)>0) 
	{
      $row = $this->nextData($result);
      return $row["price"];
    }else
    {
      return 0;
    }
  }
  
  
  function getInfoVerifyMember($table, $member_id) {
    $result = $this->getDynamic($table, "member_id='" . $member_id . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }else
    {
      return 0;
    }
  }


  function getInfoColumShipping($table, $id) {
    $result = $this->getDynamic($table, "member_id='" . $id . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
  function checkIsCategory($table, $id) {
    $result = $this->getDynamic($table, "parentid='" . $id . "'", "");
    if ($this->totalRows($result) > 0) {
      return 1;
    }
    else {
      return 0;
    }
  }
  function VndText($amount) {
    if ($amount <= 0) {
      return $textnumber = "Tiền phải là số nguyên dương lớn hơn số 0";
    }
    $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
    $TextLuythua = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
    $textnumber = "";
    $length = strlen($amount);
    for ($i = 0; $i < $length; $i++)
      $unread[$i] = 0;
    for ($i = 0; $i < $length; $i++) {
      $so = substr($amount, $length - $i - 1, 1);
      if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
        for ($j = $i + 1; $j < $length; $j++) {
          $so1 = substr($amount, $length - $j - 1, 1);
          if ($so1 != 0)
            break;
        }
        if (intval(($j - $i) / 3) > 0) {
          for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
            $unread[$k] = 1;
        }
      }
    }
    for ($i = 0; $i < $length; $i++) {
      $so = substr($amount, $length - $i - 1, 1);
      if ($unread[$i] == 1)
        continue;
      if (($i % 3 == 0) && ($i > 0))
        $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;
      if ($i % 3 == 2)
        $textnumber = 'trăm ' . $textnumber;
      if ($i % 3 == 1)
        $textnumber = 'mươi ' . $textnumber;
      $textnumber = $Text[$so] . " " . $textnumber;
    }
//Phai de cac ham replace theo dung thu tu nhu the nay
    $textnumber = str_replace("không mươi", "lẻ", $textnumber);
    $textnumber = str_replace("lẻ không", "", $textnumber);
    $textnumber = str_replace("mươi không", "mươi", $textnumber);
    $textnumber = str_replace("một mươi", "mười", $textnumber);
    $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
    $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
    $textnumber = str_replace("mười năm", "mười lăm", $textnumber);
    return ucfirst($textnumber . " đồng chẵn");
  }
  function AUDText($amount) {
    if ($amount <= 0) {
      return $textnumber = "Money must be a positive integer greater than 0";
    }
    $Text = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
    $TextLuythua = array("", "thousand", "million", "billion", "trillion", "million billion", "billion billion");
    $textnumber = "";
    $length = strlen($amount);
    for ($i = 0; $i < $length; $i++)
      $unread[$i] = 0;
    for ($i = 0; $i < $length; $i++) {
      $so = substr($amount, $length - $i - 1, 1);
      if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
        for ($j = $i + 1; $j < $length; $j++) {
          $so1 = substr($amount, $length - $j - 1, 1);
          if ($so1 != 0)
            break;
        }
        if (intval(($j - $i) / 3) > 0) {
          for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
            $unread[$k] = 1;
        }
      }
    }
    for ($i = 0; $i < $length; $i++) {
      $so = substr($amount, $length - $i - 1, 1);
      if ($unread[$i] == 1)
        continue;
      if (($i % 3 == 0) && ($i > 0))
        $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;
      if ($i % 3 == 2)
        $textnumber = 'percent ' . $textnumber;
      if ($i % 3 == 1)
        $textnumber = 'fifty ' . $textnumber;
      $textnumber = $Text[$so] . " " . $textnumber;
    }
//Phai de cac ham replace theo dung thu tu nhu the nay
    $textnumber = str_replace("zero fifty", "retail", $textnumber);
    $textnumber = str_replace("retail zero", "", $textnumber);
    $textnumber = str_replace("fifty zero", "fifty", $textnumber);
    $textnumber = str_replace("fifty one", "teen", $textnumber);
    $textnumber = str_replace("fifty five", "fifty five", $textnumber);
    $textnumber = str_replace("fifty one", "fifty one", $textnumber);
    $textnumber = str_replace("fifty five", "teen five", $textnumber);
    return ucfirst($textnumber . " AUD");
  }
// Get: Ip
  function get_ip() {
    if (isset($_SERVER['X_FORWARDED_FOR'])) {
      if (strpos($_SERVER['X_FORWARDED_FOR'], ',') === false) {
        return $_SERVER['X_FORWARDED_FOR'];
      }
      return trim(reset(explode(',', $_SERVER['X_FORWARDED_FOR'])));
    }
    return $_SERVER['REMOTE_ADDR'];
  }

  function convertBTC($price)
  {
      return file_get_contents("https://blockchain.info/tobtc?currency=USD&value=".(int)$price."");
  }

  function getbuyBTC()
  {
      $result = file_get_contents("https://blockchain.info/ticker");
      if($result)
      {
          return json_decode($result);
      }
      return NULL;
  }

  function getVCB($account)
  {
      $result = file_get_contents("https://santienao.com/api/v1/bank_accounts/".$account);
      if($result)
      {
          return json_decode($result);
      }
      return NULL;
  }

/****************************************************/
  function getConfig() {
    $result = $this->getDynamic("setting", "", "");
    $info = array();
    while ($rowinfo = $this->nextData($result)) {
      $index = $rowinfo["key_name"];
      $info[$index] = stripslashes($rowinfo["value"]);
    }
    return $info;
  }
//get level member
  function getLevel($parentid, $level = 0) {
    $result = $this->getDynamic("member", "id =" . $parentid . "", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $row = $this->nextData($result);
      $level++;
      return $this->getLevel($row["parentid"], $level);
    }
    else {
      return $level;
    }
  }
/**************************************************/
  function getLevelMember($parentid, $level) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      echo "<div class='level-" . $level . "'>";
      echo '<div style="background: #FAFAFA; color: #2D82C3; font-size:16px; padding:10px;cursor: Pointer;">

                          Level ' . $level . '  -  Member <span style="float:right;margin-right:29px">Total: <span style="color:#f00;">' . $total . '</span> member</span>

               </div>';
      echo '<table id="mainTable" cellpadding="1" cellspacing="1">

                        <thead>

                          <tr style="background:#848484; color: #fff;">

                           <th class="itemText" width="30"><b>No</b></th>

                           <th class="itemText" width="80"><b>ID</b></th>

                           <th class="itemText" width="150"><b>Fullname</b></th>

                           <th class="itemText" width="200"><b>Upline</b></th>

                           <th class="itemText"><b>Date</b></th>

                          </tr>

                        </thead>

                        <tbody>';
      $strParentId = "";
      $i = 1;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $ponser = $this->getInfoColum("member", $row['parentid']);
		
		if($row["packages_id"]==9)
		{
			$class = "member_tran";
		}else
		{
			$class = "";
		}
		
        echo '<tr class="cell1 '.$class.'">

                        <td class="itemText">' . $i . '</td>

                        <td class="itemText">' . $row['ma_id'] . '</td>

                        <td class="itemText">' . $row['hovaten'] . '</td>

                        <td class="itemText">' . $ponser['ma_id'] . '-' . $ponser['hovaten'] . '</td>

                        <td class="itemText">' . date('d-m-Y', $row['datecreated']) . '</td>

                     </tr>';
        $i++;
      }
      $level++;
      $strParentId .= '-1';
      echo "</tbody></table>";
      echo "</div>";
//Goi lai get thanh vien
      return $this->getLevelMember($strParentId, $level);
    }
    else {
      if ($level == 1) {
        echo "<div class='level-" . $level . "'>";
        echo '<div style="background: #FAFAFA; color: #2D82C3; font-size:16px; padding:10px;cursor: Pointer;">

                                Level ' . $level . '  -  Thành Viên <span style="float:right;">Total: <span style="color:#f00;">0</span> member</span>

                     </div>';
      }
      return true;
    }
  }

  /**************************************************/
  function getMemberList($parentid,$rowgetInfo) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total = $this->totalRows($result);
    if ($total > 0) {

      $strParentId = "";
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $ponser = $this->getInfoColum("member", $row['parentid']);
        $package = $this->getInfoColum("packages", $row['packages_id']);
        if($row["is_del"]!=1)
        {

        echo'<tr role="row">
                 <td class="text-center">' . $row['ma_id'] . '</td>
                 <td class="text-left"> ' . $row['hovaten'] . ' <i class="fa fa-'.$row['gioitinh'].'"></i> </td>
                 <td class="text-left"> ' . $row['email'] . '<br> ' . $row['didong'] . '<br> ' . $row['diachi'] . ' </td>
                 <td class="text-center">' . (($row["status"]==1)?date("d-m-Y",$row['datecreated']):"Not Active") . '</td>
                 <td class="text-center"> '.$package["title"].' </td>
                 <td class="text-center">' . $ponser['ma_id'] . '<br>' . $ponser['hovaten'] . '</td>
                 <td class="text-center">
                    <a href="' .HOST. 'member-login.html?admin-login-by=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-info" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Login"><i class="fa fa-sign-in"></i></a>';
                    if($rowgetInfo["roles_id"]!=15)
                    {
                    echo '<a href="' .HOST. 'edit_member_create.html?id=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-warning" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil"></i></a>
                    <a href="' .HOST. 'member_list.html?id_del=' . $row['id'] . '&delete=true" class="btn btn-effect-ripple btn-xs btn-primary" data-toggle="tooltip" title="" onclick="return confirm(\'Are you really want to delete?\');" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-times"></i></a>';
                    }
                 echo'</td>
              </tr>';
        }
      }

      $strParentId .= '-1';
      return $this->getMemberList($strParentId,$rowgetInfo);
    }
    else {
      return true;
    }
  }

   /*
   function getMemberListArray($parentid,$rowgetInfo,$arrayMember) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total = $this->totalRows($result);
    if ($total > 0) {

      $strParentId = "";
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $arrayMember[$row['id']]=$row;
      }

      $strParentId .= '-1';
      return $this->getMemberListArray($strParentId,$rowgetInfo,$arrayMember);
    }
    else {
      return $arrayMember;
    }
  }*/
  
  function getMemberListArray($parentid,$rowgetInfo,$arrayMember) {
    $result = $this->getDynamic("member", "id<>1 AND roles_id>5 AND roles_id<11", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      while ($row = $this->nextData($result)) {        
        $arrayMember[$row['id']]=$row;
      }
    }
     return $arrayMember;
  }
  
  function getCustomerListArray($arrayMember = array()) 
  {
    $result = $this->getDynamic("member", "id<>1 AND roles_id=15", "datecreated DESC");
    $total = $this->totalRows($result);
    if ($total > 0) {
      while ($row = $this->nextData($result)) {        
        $arrayMember[$row['id']]=$row;
      }
    }
     return $arrayMember;
  }
  
  function getPatientListArray($parentid,$rowgetInfo,$arrayMember) {
    $result = $this->getDynamic("member", "parentid=$parentid AND roles_id=16", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      while ($row = $this->nextData($result)) {        
        $arrayMember[$row['id']]=$row;
      }
    }
     return $arrayMember;
  }
  
  function getMemberListArrayByDate($parentid,$rowgetInfo,$arrayMember,$from_date,$from_to) {
    $result = $this->getDynamic("member", "roles_id=15 and datecreated>=".$from_date." and datecreated<=".$from_to."", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      while ($row = $this->nextData($result)) {        
        $arrayMember[$row['id']]=$row;
      }
    }
     return $arrayMember;
  }
  

  function getMemberListArrayLevel($parentid,$rowgetInfo,$arrayMember,$tang=1) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and status=1 and packages_id<>1", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {

      $strParentId = "";
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $arrayMember[$tang][]=$row;
      }
      $tang++;
      $strParentId .= '-1';
      if($tang<=5)
      {
         return $this->getMemberListArrayLevel($strParentId,$rowgetInfo,$arrayMember,$tang);
      }else
      {
         return $arrayMember;
      }

    }
    else {
      return $arrayMember;
    }
  }

  function getMemberListArrayLevelALL($parentid,$rowgetInfo,$arrayMember,$tang=1) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and status=1", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {

      $strParentId = "";
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $arrayMember[$tang][]=$row;
      }
      $tang++;
      $strParentId .= '-1';
      if($tang<=5)
      {
         return $this->getMemberListArrayLevelALL($strParentId,$rowgetInfo,$arrayMember,$tang);
      }else
      {
         return $arrayMember;
      }

    }
    else {
      return $arrayMember;
    }
  }
  
  
  function get_Parent_Last_Level($parentid,$total_level=1) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total_pa = $this->totalRows($result);
    
		if($total_level==1 && $total_pa < 2)
		{      
		  return $parentid; 
		}
      
      if ($total_pa > 0) {
        $strParentId = '';
        while ($row = $this->nextData($result)) {
          $strParentId .= $row['id'] . ",";          
          $result2 = $this->getDynamic("member", "parentid =" . $row['id'] . "", "");
          $total = $this->totalRows($result2);
          if ($total < 2) {            
            return $row['id'];
          }        
        }
        $strParentId .= '-1';
        $total_level++;      
        return $this->get_Parent_Last_Level($strParentId,$total_level);
      }
      else {
        return $parentid;
      }
    
  }
  
  function getMa_id_Parent($parentid) { 
    $arrayLastParent = $this->get_Parent_Last_Level($parentid,1);
    $tem = explode(",", $arrayLastParent);
	
    if(count($tem)>0)
    {
      $result = $this->getDynamic("member", "id=" . (int)$tem[0] . "", "");
      if ($this->totalRows($result) > 0) {
        $row = $this->nextData($result);
        return $row["tendangnhap"];     
      }
    }else
    {
      return 0;
    }  
    
  }
  
  function getMemberDelivery($table,$userid,$col) {
    $result = $this->getSum($table,$col,"member_id =".$userid."" , "");
    $total = $this->totalRows($result);
	$sum = 0;
    if ($total > 0) {

      $strParentId = "";

      while ($row = $this->nextData($result)) {
        $sum = $row['value_sum'];
      }
    }
    return $sum;
  }

	function YesterdayRanking($table,$currentDate) {
		//$currentDate = date("d-m-Y",time());
		$date = new DateTime($currentDate);
		$date->sub(new DateInterval('P1D'));
		$yesterday = $date->format('Y-m-d');
		$condition = "FROM_UNIXTIME(S.datecreated,'%Y-%m-%d') like'%".$yesterday."%'";
		$result = $this->getRanking($table,$condition);
		$sum = array();
		if(!empty($result)) {
			$total = $this->totalRows($result);
			//$sum = array();
			if ($total > 0) {

			  $strParentId = "";

			  while ($row = $this->nextData($result)) {
				$sum[] = array(
						'member_id' => $row['member_id'],
						'sum_value' => $row['sum_value']
					);
			  }
			}
			$this->sortBySubArrayValue($sum, 'sum_value', 'desc');
		}
		return $sum;
	  }
	  
	  function MemberRanking($table,$userid=false) {
		$condition = $userid ? "member_id =".$userid : false;
		$result = $this->getRanking($table,$condition);
		$total = $this->totalRows($result);
		$sum = array();
		if ($total > 0) {

		  $strParentId = "";

		  while ($row = $this->nextData($result)) {
			$sum[] = array(
					'member_id' => $row['member_id'],
					'sum_value' => $row['sum_value']
				);
		  }
		}
		$this->sortBySubArrayValue($sum, 'sum_value', 'desc');
		return $sum;
	  }

	function getMemberRanking($table,$userid) {
		$result = $this->getRanking($table,false);
		$total = $this->totalRows($result);
		$sum = array();
		if ($total > 0) {

		  $strParentId = "";

		  while ($row = $this->nextData($result)) {
			$sum[] = array(
					'member_id' => $row['member_id'],
					'sum_value' => $row['sum_value']
				);
		  }
		}
		
		$this->sortBySubArrayValue($sum, 'sum_value', 'desc');
		$count = count($sum);
		$key = array_search($userid, array_column($sum, 'member_id'));
		if(false !== $key) {
			$rank = $key + 1;
			if(isset($_SESSION['language']) && $_SESSION['language']=='ja') return '<h4 class="text-sm-center">' . _URLEVEL . $count . _ONTOTAL . $rank . _MEMBER . '</h4>';
			else return '<h4 class="text-sm-center">' . $rank . _ONTOTAL . $count . _MEMBER . '</h4>';
		} else {return '<h2 class="text-sm-center">0</h2>';}
	  }

	function sortBySubArrayValue(&$array, $key, $dir='asc') {
						$sorter=array();
						$rebuilt=array();

						//make sure we start at the beginning of $array
						reset($array);

						//loop through the $array and store the $key's value
						foreach($array as $ii => $value) {
						  $sorter[$ii]=$value[$key];
						}

						//sort the built array of key values
						if ($dir == 'asc') asort($sorter);
						if ($dir == 'desc') arsort($sorter);

						//build the returning array and add the other values associated with the key
						foreach($sorter as $ii => $value) {
						  $rebuilt[$ii]=$array[$ii];
						}

						//assign the rebuilt array to $array
						$array=$rebuilt;
					}
  
  
  function checktotalUpline($parentid){    
	  
      $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
      $total  = $this->totalRows($result);
      return $total;
  }

  function getParentPonser($array_parentid,$stt){    
	  
      $result = $this->getDynamic("member", "parentid in(" . $array_parentid[$stt] . ")", "");
      $total  = $this->totalRows($result);
      if ($total >= 2){
        while ($row = $this->nextData($result)) {
			$array_parentid[] = $row['id'];
        }
        $stt++;		
        return $this->getParentPonser($array_parentid,$stt);
      }else
      {
        return $array_parentid[$stt] ;
      }    
  }

  function getTotalLevel($parentid, $total) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    if ($this->totalRows($result) > 0) {
      $strParentId = '';
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
      }
      $strParentId .=-1;
      $total++;
      return $this->getTotalLevel($strParentId, $total);
    }
    else {
      return $total;
    }
  }

  function getTotalMemberdownline($parentid, $arrayDownline,$packages) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total_level = $this->totalRows($result);

    if ($total_level > 0) {
      $strParentId = '';
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        if($row['status']==1)
        {
            if((int)$row['packages_id']>0)
			{
			  $arrayDownline['totalprice']+= $packages[$row['packages_id']]["price"];	
			}	
			
			$arrayDownline['id_member'][]= $row['id'];
			$arrayDownline['totalmember']+=1;
        }

      }
      $strParentId .=-1;
      
      return $this->getTotalMemberdownline($strParentId, $arrayDownline,$packages);
    }
    else {
      return $arrayDownline;
    }
  }

  function TotalPriceSystem($parentid, $arrayprice,$packages) {
    date_default_timezone_set('Asia/Bangkok');
    $today = date("d-m-Y",time());
    $date  = new DateTime($today);
    $date->sub(new DateInterval('P1D'));
    $yesderday = $date->format('d-m-Y');

    $day = date('w');
    $week_start = date('d-m-Y', strtotime('-'.($day-1).' days'));
    $week_end = date("d-m-Y",time());

    $d = new DateTime('first day of this month');
    $datefirstMonth = $d->format('d-m-Y');
    $datelastMonth = $today;

    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total_level = $this->totalRows($result);

    if ($total_level > 0) {
      $strParentId = '';
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        if($row['status'])
        {
            $datecreated = date("d-m-Y",$row["datecreated"]);
            $datecreated = strtotime($datecreated);

            if($datecreated>=strtotime($datefirstMonth) && $datecreated<=strtotime($datelastMonth))
            {
               $arrayprice["monthPriceSystem"]+= $packages[$row['packages_id']]["price"];
            }

            if($datecreated>=strtotime($week_start) && $datecreated<=strtotime($week_end))
            {
               $arrayprice["weekPriceSystem"]+= $packages[$row['packages_id']]["price"];
            }

            if($datecreated==strtotime($yesderday))
            {
               $arrayprice["yesterdayPriceSystem"]+= $packages[$row['packages_id']]["price"];
            }

            if($datecreated==strtotime($today))
            {
               $arrayprice["todayPriceSystem"]+= $packages[$row['packages_id']]["price"];
            }


        }

      }
      $strParentId .=-1;

      return $this->TotalPriceSystem($strParentId, $arrayprice,$packages);
    }
    else {
      return $arrayprice;
    }
  }

  function getTotalMemberLevel($parentid, $level,$arrayTotal=array()) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    if ($this->totalRows($result) > 0) {
      $strParentId = '';
      $totalmember = 0;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $totalmember++;
      }
      $strParentId.= "-1";
      $level++;
      $arrayTotal[]= $totalmember;
      if($level < 6)
      {

         return $this->getTotalMemberLevel($strParentId, $level,$arrayTotal);
      }else
      {
          return $arrayTotal;
      }
    }
    else {
      return $arrayTotal;
    }
  }

  function checkEditMember($parentid,$edit_id)
  {
        $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            if($edit_id==$row['id'])
            {
                return 1;
            }
          }
          $strParentId.= "-1";
          return $this->checkEditMember($strParentId, $edit_id);
        }
        else {
          return 0;
        }
  }
  
  function getEditMember($edit_id)
  {
        $result = $this->getDynamic("member", "id=$edit_id", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            if($edit_id==$row['id'])
            {
                return 1;
            }
          }
          $strParentId.= "-1";
          return $this->checkEditMember($strParentId, $edit_id);
        }
        else {
          return 0;
        }
  }


  function TotalPriceMember($table,$member_id){
    $totalprice = 0;
    $result = $this->getDynamic($table, "member_id =". (int)$member_id ."", "");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["price"];
          }
    }
    return $totalprice;
  }
  
  function TotalPriceMember_invest($member_id){
    $totalprice = 0;
    $result = $this->getDynamic("member_invest", "member_id =". (int)$member_id ."", "");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["quantity_usd"];
          }
    }
    return $totalprice; 
  }
  
  function TotalNumberMemberInvest($member_id){
    $totalprice = 0;
    $result = $this->getDynamic("member_invest", "member_id =". (int)$member_id ."", "");
	return (int) $this->totalRows($result);    
  }
  
  function TotalPriceMember_invest_last($member_id){
    $totalprice = 0;
    $result = $this->getDynamic("member_invest", "member_id =". (int)$member_id ."", "id desc limit 0,1");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["quantity_usd"];
          }
    }
    return $totalprice;
  }
  
  function TotalPriceMember_tranfer($table,$member_id,$type){
    $totalprice = 0;
    $result = $this->getDynamic($table, "type_id = ".(int)$type." and member_id =". (int)$member_id ."", "");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["price"];
          }
    }
    return $totalprice;
  }
  
  
  function TotalPriceMember_value($table,$member_id){
    $totalprice = 0;
    $result = $this->getDynamic($table, "member_id =". (int)$member_id ."", "");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["price4"];
          }
    }
    return $totalprice;
  }
  
  
  

  function TotalPriceMemberLast($table,$member_id){
    $totalprice = 0;
    $result = $this->getDynamic($table, "member_id =". (int)$member_id ." and status=1", "id desc limit 0,1");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["price"];
          }
    }
    return $totalprice;
  }
  
  function TotalPricePeeding($table,$member_id){
    $totalprice = 0;
    $result = $this->getDynamic($table, "member_id =". (int)$member_id ." and status=0", "");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $totalprice+=$row["price"];
          }
    }
    return $totalprice;
  }
  
  function TotalMemberF1All($id){
     $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
     $arrayF1 = array();
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arrayF1[] = $row;
         }
     }
     return $arrayF1;
  }
  
  function TotalsponserActive($id){
     $result = $this->getDynamic("member", "sponser_id =". (int)$id ." and status=1", "id asc");
     return $this->totalRows($result);   
     
  }
  
   function Totalsponser($id){
     $result = $this->getDynamic("member", "sponser_id =". (int)$id ."", "id asc");
     return $this->totalRows($result);   
     
  }
  
  function TotalTransactionHistory($table,$member_id){
     $result = $this->getDynamic($table, "member_id =". (int)$member_id ."", "datecreated ASC"); 
     $arraytran = array();
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraytran[] = $row;
         }
     }
     return $arraytran;
  }

  function TotalMemberF1($id){
     $result = $this->getDynamic("member", "parentid =". (int)$id ."", "id asc"); 
     $arrayF1 = array();
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arrayF1[] = $row;
         }
     }
     return $arrayF1;
  }

  function ArrayIDMember_F1($id){
     $result = $this->getDynamic("member", "sponser_id =". (int)$id ." and status=1", "id asc");
     $arraymemberF1 = array();
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF1[]= $row["id"];
         }
     }
     return $arraymemberF1;
  }
  
  function checkLevel_cannhanh($id){
    date_default_timezone_set('Asia/Bangkok');
    $level = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ."", "id asc");
    if ($this->totalRows($result) >= 2) {
       $level = 1;
    }
    return $level;
  }
  
  function ArrayIDMember_F1Min500($id){
     $result = $this->getDynamic("member", "sponser_id =". (int)$id ." and status=1 and packages_id>1", "id asc");
     $arraymemberF1 = "";
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF1[]= $row["id"];
         }
     }
     return $arraymemberF1;
  }
  
  function ArrayIDMember_F1_FULL($id){
     $result = $this->getDynamic("member", "sponser_id =". (int)$id ." and status=1", "id asc");
     $arraymemberF1 = array();
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF1[]= $row;
         }
     }
     return $arraymemberF1;
  }



  function checkLevelMember($id)
  {
     $result = $this->getDynamic("member", "parentid =". (int)$id ." and packages_id<>1 and status=1", "id asc");
     $total = $this->totalRows($result);
     if ($total >=5)
     {
         $level =1;

     }
     return 0;
  }





  function TotalIncomeF1($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = time(); // or your date as well

          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < $packages[$packages_id]["circle"] && $datediff>0)
            {
               $f1 =  ($packages[$packages_id]["f1"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
            }
            else
            {
               $f1 =  ($packages[$packages_id]["f1"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
            }
            $totalIncome+=$f1;
          }
    }

    return $totalIncome;
  }

  function TotalIncomeF1_90($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = time(); // or your date as well
          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < 7 && $date_re > 0)
            {
               $total_price_f1 = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100;
               $total_priace_1_7 =$total_price_f1/7;
               $f1 =  $date_re * $total_priace_1_7;
            }
            else
            {
               $f1 = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100 ;
            }
            $totalIncome+=$f1;
          }
    }
    return $totalIncome;
  }
  
  function TotalPriceIncome100f1($id){
     $totalIncome = 0;
    $result = $this->getDynamic("incomef1", "ponser_id =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }

  function TotalPriceIncome100f1ByDate($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("incomef1", "ponser_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
  
  function TotalPriceIncomeProfitDailyByDate($id,$date){
	date_default_timezone_set('Asia/Bangkok');  
    $totalIncome = 0;
	
    $result = $this->getDynamic("incomeprofitdaily", "member_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
  

  function TotalPriceIncomeDirect($id){
     $totalIncome = 0;
    $result = $this->getDynamic("incomedirect", "ponser_id =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
  function CheckMaxoutIncome($id,$date)
  {
    $totalIncome1 = $this->TotalPriceRoiByDate($id,$date);
	$totalIncome2 = $this->TotalPriceIncomeDirectByDate($id,$date);
	$totalIncome3 = $this->TotalPriceIncomeInDirectByDate($id,$date);    
    return ($totalIncome1+$totalIncome2+$totalIncome3);
  }
  
  function CheckMaxoutIncome250($id)
  {
    $totalIncome1 = $this->TotalPriceIncomeDirect($id);
	$totalIncome2 = $this->TotalPriceIncomeInDirect($id);
	$totalIncome3 = $this->TotalPriceMember("incomeprofitdaily",$id);
    return ($totalIncome1+$totalIncome2+$totalIncome3);
  }
  
  function TotalPriceRoiByDate($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("incomeprofitdaily", "member_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }

  function TotalPriceIncomeDirectByDate($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("incomedirect", "ponser_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
  function TotalPriceIncomeleadershipByDate($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("leadership_bonusdaily", "member_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }

  function TotalPriceIncomeInDirectByDate($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("Incomeindirect", "ponser_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
  function TotalPriceIncomeInDirectByDate_1($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("Incomeindirect", "ponser_id =". (int)$id ." and status=1 and member_id=0 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }
  
   function TotalPriceIncomeInDirectByDate_2($id,$date){
    $totalIncome = 0;
    $result = $this->getDynamic("Incomeindirect", "ponser_id =". (int)$id ." and status=1 and member_id<>0 and datecreated='".$date."'", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }


  function TotalPriceIncomeInDirect($id){
     $totalIncome = 0;
    $result = $this->getDynamic("Incomeindirect", "ponser_id =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $price = $row["price"];
            $totalIncome+=$price;
          }
    }
    return $totalIncome;
  }

  function TotalIncomeF1_90_1($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $f1 = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100 ;
            $totalIncome+=$f1;
          }
    }
    return $totalIncome;
  }
  
  function ArrayIDMemberF1_2($id){
     $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
     $arraymemberF1 = "";
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF1[]= $row["id"];
         }
     }
     return $arraymemberF1;
  }

 
  function checkNoneMember($id){ 
    $result = $this->getDynamic("member", "parentid =". (int)$id ."", "");
    if ($this->totalRows($result) >0) {
        return 1;
    }else
	{
		return 0;
	}
   
  }
  
  function checkNoneMemberOne($id){   
    $result = $this->getDynamic("member", "parentid =". (int)$id ."", "");
    if ($this->totalRows($result) >= 2) 
	{
        return 1;
    }else
	{
		return 0;
	}
    
  }
  
  
  function checkCoinDCMM($id){   
    $result = $this->getDynamic("coin_wallet", "member_id =". (int)$id ." and status=1", "id asc limit 0,1");
    if ($this->totalRows($result) >0) 
	{
       $row = $this->nextData($result);
	   return $row;
    }
    return 0;
   }
  

  function checkLevel_I($id){
    date_default_timezone_set('Asia/Bangkok');
    $level = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) >= 2) {
       $level = 1;
    }
    return $level;
  }


  function ArrayIDMemberF1($id){
     $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
     $arraymemberF1 = "";
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF1.= $row["id"].",";
         }
     }
     return $arraymemberF1;
  }

  function checkLevel_II($arrayF1){
    date_default_timezone_set('Asia/Bangkok');
    $level = 1;
    $result = $this->getDynamic("member", "id in (". $arrayF1 .") and status=1 ", "id asc");
    if ($this->totalRows($result) >0) {
       $no = 0;
       while ($row = $this->nextData($result)) {
           //check level 1,
           if($this->checkLevel_I($row["id"]))
           {
               $no++;
           }
       }
       echo "<input type='hidden' name='level' value='".$no."'>";
       if($no>=2)
       {
           $level = 2;
       }
    }
    return $level;
  }


  function ArrayIDMemberF2($arrayF1){
     $result = $this->getDynamic("member", "parentid in (". $arrayF1 .") and status=1", "id asc");
     $arraymemberF2 = "";
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF2.= $row["id"].",";
         }
     }
     return $arraymemberF2;
  }


  function checkLevel_III($arrayF2){
    date_default_timezone_set('Asia/Bangkok');
    $level = 2;
    $result = $this->getDynamic("member", "id in (". $arrayF2 .") and status=1 ", "id asc");
    if ($this->totalRows($result) >0) {

       $no = 0;
       while ($row = $this->nextData($result)) {
           //check level 1,
           if($this->checkLevel_I($row["id"]))
           {
               $no++;
           }
       }
       if($no>=4)
       {
           $level = 3;
       }
    }
    return $level;
  }


  function ArrayIDMemberF3($arrayF2){
     $result = $this->getDynamic("member", "parentid in (". $arrayF2 .") and status=1", "id asc");
     $arraymemberF3 = "";
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
            $arraymemberF3.= $row["id"].",";
         }
     }
     return $arraymemberF3;
  }


  function checkLevel_IV($arrayF3){
    date_default_timezone_set('Asia/Bangkok');
    $level = 3;
    $result = $this->getDynamic("member", "id in (". $arrayF3 .") and status=1 ", "id asc");
    if ($this->totalRows($result) >0) {

       $no = 0;
       while ($row = $this->nextData($result)) {
           //check level 1,
           if($this->checkLevel_I($row["id"]))
           {
               $no++;
           }
       }
       if($no>=8)
       {
           $level = 4;
       }
    }
    return $level;
  }
  
    function checkInsertIncomeDirectMember($ponser_id,$member_id,$price_indirect){
		
		$result = $this->getDynamic("incomedirect", "ponser_id=".$ponser_id." and member_id=".$member_id." and price=".$price_indirect."", "");
		if ($this->totalRows($result) >0) {
			return true;
		}
		return false;
	}
	
	function Income100F1Member($id,$rowgetInfo,$price_indirect,$level=0){
		if((int)$id > 0)
		{			
			$infoMB = $this->getInfoColum("member",$id);
			
			if($infoMB["status"]==1)
			{
					
				$array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
				$this->insertTable_2("incomef1", $array_col_indirect);				
				return true;
				/*
				$levelsPoner = $level + 1;
				if($levelsPoner < 9)
				{
					return $this->Income100F1Member((int)$infoMB["sponser_id"],$infoMB,$price_indirect,$levelsPoner);
				}else
				{
					return true;
				}
				*/	
				
			}else
		    {
				return true;
			}			
					

		}else
		{
			return true;
		}
    }
	

    function IncomeDirectMember($id,$rowgetInfo,$info_packages,$level=0){
		if((int)$id > 0)
		{
			
			$infoMB = $this->getInfoColum("member",$id);
			if($infoMB["status"]==1)
			{
				if($level==0)
				{
					$price_indirect = 25;
				}else if($level==1 || $level==2)
				{
					$price_indirect = 10;
				}else
				{
					$price_indirect = 5;
				}
				
				/*check huong heu hong */
				$totalF1member = $this->TotalsponserActive($id);
				$total_price_withdraw = $this->TotalPriceMember("all_withdrawals",$id);
				$check = true;
				if($totalF1member==0)
			    {
					if($total_price_withdraw>=125)
					{
						$check = false;
					}
				}		
				
				if($check)
				{				
					$array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
					if(!$this->checkInsertIncomeDirectMember($id,$rowgetInfo["id"],$price_indirect))
					{
						$this->insertTable_2("incomedirect", $array_col_indirect);
					}
				}
				/*100% f1
				$this->Income100F1Member((int)$infoMB["sponser_id"],$infoMB,$price_indirect,0);
				 */
			
			}	
			
			$levelsPoner = $level + 1;
			if($levelsPoner < 4)
			{
				return $this->IncomeDirectMember((int)$infoMB["sponser_id"],$rowgetInfo,$info_packages,$levelsPoner);
			}else
			{
				return true;
			}			

		}else
		{
			return true;
		}
    }
	
	
	function checkInsertIncomeInDirectMember($ponser_id,$member_id,$price_indirect){
		
		$result = $this->getDynamic("Incomeindirect", "ponser_id=".$ponser_id." and member_id=".$member_id." and price=".$price_indirect."", "");
		if ($this->totalRows($result) >0) {
			return true;
		}
		return false;
	}
  

    function IncomeIndirectMember($id,$rowgetInfo,$info_packages,$level=0){
        if((int)$id > 0)
        {
            $infoMB = $this->getInfoColum("member",$id); 
            if($infoMB["status"]==1)
			{ 			
				/*check huong heu hong */
				$totalF1member = $this->TotalsponserActive($id);
				$total_price_withdraw = $this->TotalPriceMember("all_withdrawals",$id);
				$check = true;
				if($totalF1member==0)
			    {
					if($total_price_withdraw>=125)
					{
						$check = false;
					}
				}		
				
				if($check)
				{				
					$price_indirect = 5;
					$array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
					
					if(!$this->checkInsertIncomeInDirectMember($id,$rowgetInfo["id"],$price_indirect))
					{
						$this->insertTable_2("Incomeindirect", $array_col_indirect);					
						/*100% f1 */
						$this->Income100F1Member((int)$infoMB["sponser_id"],$infoMB,$price_indirect,0);
					}
				}
				
			}
			
            $levelsPoner = $level + 1;
			if($levelsPoner < 10)
			{				
									
					return $this->IncomeIndirectMember((int)$infoMB["parentid"],$rowgetInfo,$info_packages,$levelsPoner);
				
				
			}else
            {
				return true;
			}			

        }else
        {
            return true;
        }
    }
    /*Thuy.TQ 2016-12-08 */
	
	
	function IncomeIndirectMember_10($id,$rowgetInfo,$packages_id,$arrayPackeges,$package_pump,$level=0){
        if((int)$id > 0)
        {
            $infoMB = $this->getInfoColum("member",$id); 
            if($infoMB["status"]==1)
			{	
				if($infoMB["packages_id"] < $packages_id)
				{
					$price_indirect = $arrayPackeges[$infoMB["packages_id"]]["price"]/100;
					
				}else
				{
					$price_indirect = $package_pump/100;
				}
				
				$array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);				
				if(!$this->checkInsertIncomeInDirectMember($id,$rowgetInfo["id"],$price_indirect))
				{
				  $this->insertTable_2("Incomeindirect", $array_col_indirect);
				}
			}
			
            $levelsPoner = $level + 1;
			if($levelsPoner < 10)
			{			
				return $this->IncomeIndirectMember_10((int)$infoMB["parentid"],$rowgetInfo,$packages_id,$arrayPackeges,$package_pump,$levelsPoner);
			}else
            {
				return true;
			}			

        }else
        {
            return true;
        }
    }

	function TotalMemberF1_cannhanh($id){
		 $result = $this->getDynamic("member", "parentid =". (int)$id ."", "id asc");
		 $arrayF1 = array();
		 if ($this->totalRows($result) > 0)
		 {
			 while ($row = $this->nextData($result)) {
				$arrayF1[] = $row;
			 }
		 }
		 return $arrayF1;
	 }	
	
	function IncomeIndirectMember_F2_F8($id,$rowgetInfo,$packages_id,$arrayPackeges,$valuePackeges,$level)
	{
        if((int)$id > 0)
        {
            $infoMB = $this->getInfoColum("member",$id); 
            if($infoMB["status"]==1)
			{	
				if($this->checkLevel_cannhanh($infoMB["id"]))
				{
				    $arrayF1_Referrals  = $this->ArrayIDMember_F1Min500($infoMB["id"]);
					$arrayF1 			= $this->TotalMemberF1_cannhanh($infoMB["id"]);
					$total_f1_Referrals = count($arrayF1_Referrals);
					if($total_f1_Referrals>1)
					{
					
						$total_f1_nhanhtrai = 0;
						$total_f1_nhanhphai = 0;
						$total_nhanhtrai = array("totalprice"=>0,"totalmember"=>0,"id_member"=>array());
						if(isset($arrayF1[0]["id"]) && $arrayF1[0]["id"]!=0)
						{		  
						  $total_nhanhtrai = $this->getTotalMemberdownline($arrayF1[0]["id"], $total_nhanhtrai,$arrayPackeges,$infoMB);	
						  if($arrayF1[0]["status"]==1)
						  {
								$total_nhanhtrai["id_member"][]= $arrayF1[0]["id"];	
						  }						  
						  $array_merge_trai = array_intersect($arrayF1_Referrals, $total_nhanhtrai['id_member']);
						  $total_f1_nhanhtrai=count($array_merge_trai);		
						}
						
						if(isset($arrayF1[1]["id"]) && $arrayF1[1]["id"]!=0)
						{
							
						    $total_nhanhphai = $this->getTotalMemberdownline($arrayF1[1]["id"], $total_nhanhphai,$arrayPackeges,$infoMB);						  
							
							if($arrayF1[1]["status"]==1)
							{
							  $total_nhanhphai["id_member"][] = $arrayF1[1]["id"];	
							}							
							$array_merge_phai = array_intersect($arrayF1_Referrals, $total_nhanhphai['id_member']);
							$total_f1_nhanhphai+=count($array_merge_phai);
						}
						
						if($total_f1_nhanhtrai>0 && $total_f1_nhanhphai>0)
		                {
							$total_nhanhphai = array("totalprice"=>0,"totalmember"=>0,"id_member"=>array());						
							
							$F = "f".$level;
							$price_indirect = ($arrayPackeges[$infoMB["packages_id"]][$F]*$valuePackeges["price"])/100;				
							
							$datenow        = date("d-m-Y",time());
							$totalMaxout    = $this->CheckMaxoutIncome($infoMB["id"],strtotime($datenow));
							$totalMaxout250 = $this->CheckMaxoutIncome250($infoMB["id"]);														
							/*$totalMaxout250_member = ($arrayPackeges[$infoMB["packages_id"]]["price"]*250)/100;*/			    
							if((int)$infoMB["max_out"]==0)
							{
								$totalMaxout250_member = ($arrayPackeges[$infoMB["packages_id"]]["price"]*250)/100;
							}else
							{
								$totalMaxout250_member = $infoMB["max_out"];
							}
							
							if($totalMaxout250<$totalMaxout250_member)
							{
								if(($totalMaxout250+$price_indirect) > $totalMaxout250_member)
								{
									$price_indirect = $totalMaxout250_member - $totalMaxout250;
								}
								
								/* check maxout by day */
								if($totalMaxout < $arrayPackeges[$infoMB["packages_id"]]["price"])
								{
									if(($totalMaxout+$price_indirect) > $arrayPackeges[$infoMB["packages_id"]]["price"])
									{
										$price_indirect = $arrayPackeges[$infoMB["packages_id"]]["price"] - $totalMaxout;
									}
									
									$array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);				
									if(!$this->checkInsertIncomeInDirectMember($id,$rowgetInfo["id"],$price_indirect))
									{
									  $this->insertTable_2("Incomeindirect", $array_col_indirect);
									}
								}
							
							}
						}
					}
				}/*end check */
				
			}
			
            $levelsPoner = $level + 1;
			if($levelsPoner <= 8)
			{			
				return $this->IncomeIndirectMember_F2_F8((int)$infoMB["sponser_id"],$rowgetInfo,$packages_id,$arrayPackeges,$valuePackeges,$levelsPoner);
			}else
            {
				return true;
			}			

        }else
        {
            return true;
        }
    } 
    /*Thuy.TQ 2016-12-08 */


  function TotalIncomeF1Daily($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = time(); // or your date as well
          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < $packages[$packages_id]["circle"] && $date_re > 0)
            {
               $f1 =  ($packages[$packages_id]["f1"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
            }
            else
            {
               $f1 =  0;
            }
            $totalIncome+=$f1;
          }
    }

    return $totalIncome;
  }


  function TotalIncomeF1Daily_90($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = time(); // or your date as well
          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < 7 && $date_re > 0)
            {
               $total_price_f1 = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100;
               $total_priace_1_7 =$total_price_f1/7;
               $f1 =  $total_priace_1_7;
            }
            else
            {
               $f1 =  0;
            }
            $totalIncome+=$f1;
          }
    }

    return $totalIncome;
  }


  function TotalIncomeF1Daily_90_1($id,$packages){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $datenow = date("d-m-Y",time());
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1", "id asc");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
            $datecreated = date("d-m-Y",$row["datecreated"]);
            if($datecreated==$datenow)
            {
                $packages_id = $row["packages_id"];
                $total_price_f1 = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100;
                $totalIncome+=$total_price_f1;
            }
          }
    }

    return $totalIncome;
  }


  function TotalIncomeF1DailyByDate($id,$packages,$datenow){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and datecreated <= ".$datenow." and  status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = $datenow;
          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
            {
               $f1 =  ($packages[$packages_id]["f1"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
            }
            else
            {
               $f1 =  0;
            }
            $totalIncome+=$f1;
          }
    }

    return $totalIncome;
  }

  function TotalIncomeF1DailyByDate_90($id,$packages,$datenow){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and datecreated <= ".$datenow." and  status=1", "id asc");
    if ($this->totalRows($result) > 0) {

          $now = $datenow;
          while ($row = $this->nextData($result)) {
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff  = $now - $your_date;
            $date_re   = floor($datediff/(60*60*24));

            if($date_re < 7 && $date_re>0)
            {
               $total_price_f1   = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100;
               $total_priace_1_7 = $total_price_f1/7;
               $f1 = $total_priace_1_7;
            }
            else
            {
               $f1 =  0;
            }
            $totalIncome+=$f1;
          }
    }

    return $totalIncome;
  }


  function TotalIncomeF1DailyByDate_90_1($id,$packages,$datenow){
    date_default_timezone_set('Asia/Bangkok');
    $totalIncome = 0;
    $datenow     = date("d-m-Y",$datenow);
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and  status=1", "id asc");
    if ($this->totalRows($result) > 0) {
          while ($row = $this->nextData($result)) {
               $datecreated = date("d-m-Y",$row["datecreated"]);
               if($datecreated==$datenow)
               {
                   $packages_id = $row["packages_id"];
                   $total_price_f1   = ($packages[$packages_id]["f1"] * $packages[$packages_id]["price"])/100;
                   $totalIncome+=$total_price_f1;
               }
            }
      }

    return $totalIncome;
  }



  function TotalIncomeIndirect($parentid,$packages,$level,$totalIncomeAll){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $result = $this->getDynamic("member", "parentid in(".$parentid.") and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = time();
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";


            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));

            $price_f = 0;
            //cap 2
            if($level==2)
            {

                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f1_2"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  ($packages[$packages_id]["f1_2"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
            }

            if($level==3)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f2"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  ($packages[$packages_id]["f2"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
            }

            if($level==4)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f3"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  ($packages[$packages_id]["f3"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
            }

            if($level==5)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f4"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  ($packages[$packages_id]["f4"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
            }

            if($level==6)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f5"] * ($date_re * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  ($packages[$packages_id]["f5"] * ( $packages[$packages_id]["circle"] * (($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
            }


          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirect($strParentId, $packages, $level,$totalIncomeAll);
          }else
          {

             return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }


  function TotalIncomeIndirect_90($parentid,$packages,$level,$totalIncomeAll,$array_f1=array()){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $total_member_f1 = count($array_f1);

        $result = $this->getDynamic("member", "parentid in(".$parentid.") and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = time();
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";

            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));

            $price_f = 0;
            //cap 2
            if($level==2)
            {
                    if($total_member_f1>=3)
                    {
                        $date_last_member = $array_f1[1]["datecreated"];
                        if($date_last_member<=$row["datecreated"])
                        {
                            if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                            {
                                $total_f2 = ($packages[$packages_id]["f2"] * $packages[$packages_id]["price"])/100;
                                $total_f2_90 =  $total_f2/120;
                                $price_f =  $date_re * $total_f2_90;
                            }
                            else
                            {
                                $price_f =  ($packages[$packages_id]["f2"] * $packages[$packages_id]["price"])/100;
                            }
                        }
                    }
            }

            if($level==3)
            {
                    if($total_member_f1>=5)
                    {
                    $date_last_member = $array_f1[2]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f3"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f =  $date_re * $total_f2_90;
                        }
                        else
                        {
                            $price_f =  ($packages[$packages_id]["f3"] * $packages[$packages_id]["price"])/100;
                        }
                    }
                    }

            }

            if($level==4)
            {
                    if($total_member_f1>=5)
                    {
                    $date_last_member = $array_f1[3]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f4"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f =  $date_re * $total_f2_90;
                        }
                        else
                        {
                            $price_f =  ($packages[$packages_id]["f4"] * $packages[$packages_id]["price"])/100;
                        }
                    }
                    }
            }

            if($level==5)
            {

                if($total_member_f1>=5)
                {
                    $date_last_member = $array_f1[4]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {

                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f5"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f =  $date_re * $total_f2_90;
                        }
                        else
                        {
                            $price_f =  ($packages[$packages_id]["f5"] * $packages[$packages_id]["price"])/100;
                        }
                    }
                    }

            }

            if($level==6)
            {
                if($total_member_f1>=5)
                {
                    $date_last_member = $array_f1[5]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = (1 * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f =  $date_re * $total_f2_90;
                        }
                        else
                        {
                            $price_f =  (1 * $packages[$packages_id]["price"])/100;
                        }
                    }
                }

            }


          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirect_90($strParentId, $packages, $level,$totalIncomeAll,$array_f1);

          }else
          {
             return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }


  function TotalIncomeIndirectDaily($parentid,$packages,$level,$totalIncomeAll){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $result = $this->getDynamic("member", "parentid in(" . $parentid . ")  and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = date("d-m-Y",time());
          $now = strtotime($now);
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));
            $price_f = 0;
           //cap 2
            if($level==2)
            {

                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f1_2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==3)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==4)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f3"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==5)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f4"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==6)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f5"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          //echo "<br>";
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDaily($strParentId, $packages, $level,$totalIncomeAll);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
     }


   function TotalIncomeIndirectDaily_90($parentid,$packages,$level,$totalIncomeAll,$array_f1=array()){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $total_member_f1 = count($array_f1);
        $result = $this->getDynamic("member", "parentid in(" . $parentid . ")  and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = date("d-m-Y",time());
          $now = strtotime($now);
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));
            $price_f = 0;
           //cap 2
           //cap 2
            if($level==2)
            {
                 if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[1]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f2"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                  }
            }

            if($level==3)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[2]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f3"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==4)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[3]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f4"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==5)
            {
                 if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[4]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f5"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==6)
            {
                if($total_member_f1>=6)
                 {
                    $date_last_member = $array_f1[5]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = (1 * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          //echo "<br>";
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDaily_90($strParentId, $packages, $level,$totalIncomeAll,$array_f1);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
     }

    function TotalIncomeIndirectDailyByDate($parentid,$packages,$level,$totalIncomeAll,$datenow){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and datecreated <= '".$datenow."' and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = $datenow;
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            //$totalmember++;
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));

            $price_f = 0;
            //cap 2
            if($level==2)
            {

                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f1_2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==3)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==4)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f3"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==5)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f4"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==6)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f5"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                }
                else
                {
                   $price_f =  0;
                }
            }


          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDailyByDate($strParentId, $packages, $level,$totalIncomeAll,$datenow);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }


  function TotalIncomeIndirectDailyByDate_90($parentid,$packages,$level,$totalIncomeAll,$datenow,$array_f1=array()){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $total_member_f1 = count($array_f1);

        $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and datecreated <= '".$datenow."' and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = $datenow;
          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            //$totalmember++;
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));

             $price_f = 0;
              if($level==2)
             {
                 if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[1]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f2"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f = $total_f2_90;
                        }
                     }
                  }
            }

            if($level==3)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[2]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f3"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==4)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[3]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f4"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==5)
            {
                 if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[4]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f5"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/90;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }

            if($level==6)
            {
                if($total_member_f1>=6)
                 {
                    $date_last_member = $array_f1[5]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = (1 * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f = $total_f2_90;
                        }
                     }
                }
            }


          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDailyByDate_90($strParentId, $packages, $level,$totalIncomeAll,$datenow,$array_f1);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }

  function TotalIncomeIndirectDailyByDate_Show ($parentid,$packages,$level,$totalIncomeAll,$datenow,$no=1){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and datecreated <= '".$datenow."' and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = $datenow;

          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            //$totalmember++;
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));
            $price_f = 0;

            //$F1_info = $this->getInfoColum('member',$row["parentid"]);

            //cap 2
            if($level==2)
            {

                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f1_2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                    echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                            </td>
                            <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                       </tr>';
                       $no++;
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==3)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                   echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
                            <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                       </tr>';
                       $no++;
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==4)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f3"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                   echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
                            <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                       </tr>';
                       $no++;
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==5)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f = ($packages[$packages_id]["f4"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                   echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
                            <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                       </tr>';
                       $no++;
                }
                else
                {
                   $price_f =  0;
                }
            }

            if($level==6)
            {
                if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                {
                   $price_f =  ($packages[$packages_id]["f5"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                   echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
                            <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                       </tr>';
                       $no++;
                }
                else
                {
                   $price_f =  0;
                }
            }

          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDailyByDate_Show($strParentId, $packages, $level,$totalIncomeAll,$datenow,$no);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }


  function TotalIncomeIndirectDailyByDate_Show_90 ($parentid,$packages,$level,$totalIncomeAll,$datenow,$no=1,$array_f1=array()){
        date_default_timezone_set('Asia/Bangkok');
        $totalIncome = 0 ;
        $total_member_f1 = count($array_f1);

        $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and datecreated <= '".$datenow."' and status=1", "");
        if ($this->totalRows($result) > 0) {
          $strParentId = '';
          //$totalmember = 0;
          $now = $datenow;

          while ($row = $this->nextData($result)) {
            $strParentId .= $row['id'] . ",";
            //$totalmember++;
            $packages_id = $row["packages_id"];
            $your_date = date('d-m-Y',$row["datecreated"]);
            $your_date = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));
            $price_f = 0;

            //cap 2
            if($level==2)
            {

                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[1]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            //$price_f = ($packages[$packages_id]["f1_2"] * ((($packages[$packages_id]["price"] *  $packages[$packages_id]["pecent_circle"])/100))/100);
                            $total_f2 = ($packages[$packages_id]["f2"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f     = $total_f2_90;
                            echo '<tr class="cell2">
                                    <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                                    <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                                    </td>
                                    <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                               </tr>';
                               $no++;
                        }

                    }
                }
            }

            if($level==3)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[2]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f3"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f     = $total_f2_90;
                            echo '<tr class="cell2">
                                    <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                                    <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                                    </td>
                                    <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                               </tr>';
                               $no++;
                        }

                    }
                }
            }

            if($level==4)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[3]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f4"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f     = $total_f2_90;
                            echo '<tr class="cell2">
                                    <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                                    <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                                    </td>
                                    <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                               </tr>';
                               $no++;
                        }

                    }
                }
            }

            if($level==5)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[4]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = ($packages[$packages_id]["f5"] * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f     = $total_f2_90;
                            echo '<tr class="cell2">
                                    <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                                    <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                                    </td>
                                    <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                               </tr>';
                               $no++;
                        }

                    }
                }
            }

            if($level==6)
            {
                if($total_member_f1>=5)
                 {
                    $date_last_member = $array_f1[5]["datecreated"];
                    if($date_last_member<=$row["datecreated"])
                    {
                        if($date_re < $packages[$packages_id]["circle"] && $date_re>0)
                        {
                            $total_f2 = (1 * $packages[$packages_id]["price"])/100;
                            $total_f2_90 =  $total_f2/120;
                            $price_f     = $total_f2_90;
                            echo '<tr class="cell2">
                                    <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                                    <td class="itemText">+$'.$price_f.' from to '.$row["ma_id"].'- '.$row['hovaten'].'

                                    </td>
                                    <td class="itemText">'.date("d-m-Y",$datenow).'</td>
                               </tr>';
                               $no++;
                        }

                    }
                }
           }

          $totalIncome+=$price_f;
          }
          $strParentId.= "-1";
          $level++;
          $totalIncomeAll+= $totalIncome;
          if($level < 7)
          {
             return $this->TotalIncomeIndirectDailyByDate_Show_90($strParentId, $packages, $level,$totalIncomeAll,$datenow,$no,$array_f1);
          }else
          {
              return $totalIncomeAll;
          }
        }
        else {
          return $totalIncomeAll;
        }
  }


  function getdateRe($datecreated)
  {
        date_default_timezone_set('Asia/Bangkok');
        $now = date("d-m-Y h:i:s",time());
        $now = strtotime($now);
        $your_date = $datecreated;
        $datediff = $now - $your_date;
        $date_re = floor($datediff/(60*60*24));

        return $date_re;
  }

  function getHaveLevel($parentid) {
    $result = $this->getDynamic("member", "parentid =" . $parentid . "", "");
    if ($this->totalRows($result) > 0) {
      return true;
    }
    else {
      return false;
    }
  }

  function Balancewallet($arrayPackeges)
  {
      $result = $this->getDynamic("member", "status=1", "id asc");
      $total = $this->totalRows($result);
      $price_total = 0;
      if ($total > 0) {
          while ($row = $this->nextData($result)) {
              $price_total+= $arrayPackeges[$row["packages_id"]]["price"];
          }
      }
      return $price_total;
  }

  function getTreeMember($parentid,$ms=null,$array_info_goi=null) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      $parentidback = 0;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        //echo $row['parentid']."==".$parentidback."<br/>";
          if($row["status"]==1)
          {
             //$_SESSION["status_active"]++;
			 if($array_info_goi[(int)$row["packages_id"]]["picture"])
			 {
				 $image_goi='<img src="'.HOST.''.$array_info_goi[$row["packages_id"]]["picture"].'" alt="'.$row["packages_id"].'" width="20" height="20" align="absmiddle" />'; 
			 }else
			 {
				 $image_goi='<img src="'.HOST.'style/images/packages/2.png" alt="" width="20" height="20" align="absmiddle" />';
			 }
			 
          }else
          {
             $image_goi='<img src="'.HOST.'style/images/packages/1.png" alt="" width="20" height="20" align="absmiddle" />';
			 //$_SESSION["status_notactive"]++;
          }

        if ($parentidback == 0) {

          $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li></ul>";
          echo "<script>

                          var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');

                  </script>";
          $parentidback = $row['parentid'];
        }
        else
          if ($parentidback == $row['parentid']) {
            $str = "<li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li>";
            echo "<script>

                            var branches = $('" . $str . "').appendTo('#ul_" . $row['parentid'] . "');

                         </script>";
            $parentidback = $row['parentid'];
          }
          else {
            $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li></ul>";
            echo "<script>

                              var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');

                      </script>";
            $parentidback = $row['parentid'];
        }
      }
      $strParentId .= '-1';
      return $this->getTreeMember($strParentId,$ms,$array_info_goi);
    }
    else {
      return true;
    }
  }
  
  
  function getTreeMemberReferal($parentid,$ms=null,$array_info_goi=null) {
    $result = $this->getDynamic("member", "sponser_id in(" . $parentid . ")", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      $parentidback = 0;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        
          if($row["status"]==1)
          {
            
			 if($array_info_goi[(int)$row["packages_id"]]["picture"])
			 {
				 $image_goi='<img src="'.HOST.''.$array_info_goi[$row["packages_id"]]["picture"].'" alt="'.$row["packages_id"].'" width="20" height="20" align="absmiddle" />'; 
			 }else
			 {
				 $image_goi='<img src="'.HOST.'style/images/packages/2.png" alt="" width="20" height="20" align="absmiddle" />';
			 }
			 
          }else
          {
             $image_goi='<img src="'.HOST.'style/images/packages/1.png" alt="" width="20" height="20" align="absmiddle" />';
			 
          }

        if ($parentidback == 0) {

          $str = "<ul id=\'ul_" . $row['sponser_id'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li></ul>";
          echo "<script>

                          var branches = $('" . $str . "').appendTo('#" . $row['sponser_id'] . "');

                  </script>";
          $parentidback = $row['sponser_id'];
        }
        else
          if ($parentidback == $row['sponser_id']) {
            $str = "<li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li>";
            echo "<script>

                            var branches = $('" . $str . "').appendTo('#ul_" . $row['sponser_id'] . "');

                         </script>";
            $parentidback = $row['sponser_id'];
          }
          else {
            $str = "<ul id=\'ul_" . $row['sponser_id'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[(int)$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li></ul>";
            echo "<script>

                              var branches = $('" . $str . "').appendTo('#" . $row['sponser_id'] . "');

                      </script>";
            $parentidback = $row['sponser_id'];
        }
      }
      $strParentId .= '-1';
      return true;
    }
    else {
      return true;
    }
  }
  
  
  
  function getTreeMember2($parentid,$array_info_goi=null,$str_member_tree="") {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      $parentidback = 0;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        
          if($row["status"]==1)
          {
             $_SESSION["status_active"]++;
			 if($array_info_goi[$row["packages_id"]]["picture"])
			 {
				 $image_goi='<img src="'.HOST.''.$array_info_goi[$row["packages_id"]]["picture"].'" alt="'.$row["packages_id"].'" width="20" height="20" align="absmiddle" />'; 
			 }else
			 {
				 $image_goi='<img src="'.HOST.'style/images/goi/2.png" alt="" width="20" height="20" align="absmiddle" />';
			 }
			 
          }else
          {
             $image_goi='<img src="'.HOST.'style/images/goi/1.png" alt="" width="20" height="20" align="absmiddle" />';
			 $_SESSION["status_notactive"]++;
          }

        if ($parentidback == 0) {

          $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[$row["packages_id"]]["price"])." | ".date("d-m-Y",$row["datecreated"]) . "</li></ul>";
          $str_member_tree.="<script>
							var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');
                         </script>";
          $parentidback = $row['parentid'];
        }
        else
          if ($parentidback == $row['parentid']) {
            $str = "<li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[$row["packages_id"]]["price"])." | ".date("d-m-Y H:s:i",$row["datecreated"]) . "</li>";
            $str_member_tree.="<script>
								var branches = $('" . $str . "').appendTo('#ul_" . $row['parentid'] . "');
                               </script>";
            $parentidback = $row['parentid'];
          }
          else {
            $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>".$image_goi."" . $row['ma_id']." | ".$row["hovaten"]." | ".$this->price($array_info_goi[$row["packages_id"]]["price"])." | ".date("d-m-Y H:s:i",$row["datecreated"]) . "</li></ul>";
            $str_member_tree.="<script>
                              var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');
                            </script>";
            $parentidback = $row['parentid'];
        }
      }
      $strParentId .= '-1';
      return $this->getTreeMember2($strParentId,$array_info_goi,$str_member_tree);
    }
    else {
      return $str_member_tree;
    }
  }
  
  
  
  /*******************************************************/
  
  function cayhethongmember($parentid, $tang, $level,$sponsor_id) {	
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ") ", "id asc");
    $total = $this->totalRows($result);
    $_SESSION["total_all"] += $total;
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $id = $row["id"];
        $ma_id      = $row["ma_id"];
        $ponser     = $this->getInfoColum("member", $row['parentid']);
        $packages   = $this->getInfoColum("packages", $row['packages_id']);
        $status     = $row["status"];
        if($status)
        {
           $images = "<a onmouseover=\'ajax_showTooltip(window.event,\"info_member_tooltip.php?member_id=".$row['id']."\",this);return false\' onmouseout=\'ajax_hideTooltip()\' href=\'network-tree.aspx?sponson_id=" . $row['id'] . "\'><img src=\'style/images/packages/2.png\' width=\'40\' height=\'40\' /></a>";
        }else
        {
           $images = "<a onmouseover=\'ajax_showTooltip(window.event,\"info_member_tooltip.php?member_id=".$row['id']."\",this);return false\' onmouseout=\'ajax_hideTooltip()\' href=\'network-tree.aspx?sponson_id=" . $row['id'] . "\'><img src=\'style/images/packages/2.png\' width=\'40\' height=\'40\' /></a>";
        }

        $parentid_text = $ponser['ma_id'] . "-" . $ponser['hovaten'];
		$parentid_text_none = $ma_id . "-" . $row["hovaten"];
        $_SESSION["str_member"] .= "[{v:'" . $ma_id . "-" . $row["hovaten"] . "',f:'" .$ma_id . "-" . $row["hovaten"] . "<br/>" . $images . "'}, '" . $parentid_text . "', ''],";
		
		/* add add user */
		if($this->checkNoneMemberOne($row['parentid'])==0)
		{
			$images_add_user = "<a href=\'register.aspx?sponson_id=" . $sponsor_id . "&uplive_id=" . $ponser['tendangnhap'] . "\'> <img src=\'style/images/packages/package-add.png\' width=\'40\' height=\'40\' /></a>";
			$_SESSION["str_member"] .= "[{v:'" . $ma_id . "0-Add member',f:'Add member <br/>" . $images_add_user . "'}, '" . $parentid_text . "', ''],";
		}
		
		if($this->checkNoneMember($row['id'])==0)
		{
			
			$images_add_user = "<a href=\'register.aspx?sponson_id=" . $sponsor_id . "&uplive_id=" . $row['tendangnhap'] . "\'> <img src=\'style/images/packages/package-add.png\' width=\'40\' height=\'40\' /></a>";
			$_SESSION["str_member"] .= "[{v:'" . $ma_id.$row['id']. "1-Add member',f:'Add member <br/>" . $images_add_user . "'}, '" . $parentid_text_none . "', ''],";
			$_SESSION["str_member"] .= "[{v:'" . $ma_id.$row['id']. "2-Add member',f:'Add member <br/>" . $images_add_user . "'}, '" . $parentid_text_none . "', ''],";
		}		
		
        $i++;
      }
      $strParentId .= '-1';

      if ($tang > 0 && $level >= $tang) {
        return true;
      }
      else {
        $level++;
        return $this->cayhethongmember($strParentId, $tang, $level,$sponsor_id);
      }
    }
    else 
	{
		if($parentid>0)
		{
			$memeber     = $this->getInfoColum("member", $parentid);		
			$images_add_user = "<a href=\'register.aspx?sponson_id=" . $sponsor_id . "&uplive_id=" . $memeber["tendangnhap"] . "\'> <img src=\'style/images/packages/package-add.png\' width=\'40\' height=\'40\' /></a>";
			
			$parentid_text_none = $memeber["ma_id"] . "-" . $memeber["hovaten"];
			if($this->checkNoneMember($parentid)==0)
			{
				
				$_SESSION["str_member"] .= "[{v:'" . $memeber["ma_id"].$memeber['id']. "1-Add member',f:'Add member <br/>" . $images_add_user . "'}, '" . $parentid_text_none . "', ''],";
				$_SESSION["str_member"] .= "[{v:'" . $memeber["ma_id"].$memeber['id']. "2-Add member',f:'Add member <br/>" . $images_add_user . "'}, '" . $parentid_text_none . "', ''],";
			}
        }	
		
      return true;
    }
  }

  //count
  function countmember($parentid, $tang, $level, $count) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ") and status=1", "");
    $total = $this->totalRows($result);
    $_SESSION["total_all"] += $total;
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $count++;
        $i++;
      }
      $strParentId .= '-1';
      //Goi lai get thanh vien
      if ($tang > 0 && $level >= $tang) {
        return $count;
      }
      else {
        $level++;
        return $this->countmember($strParentId, $tang, $level, $count);
      }
    }
    else {
      return $count;
    }
  }
  function countmember2($arrayobject, $parentid, $level, $count) {
    $strParentId = array();
    foreach ($arrayobject as $v) {
      if (in_array($v["parentid"], $parentid)) {
        $strParentId[] = $v['id'];
        $count++;
      }
    }
    //khong co
    if (count($strParentId) == 0) {
      return $count;
    }
    else {
      //Goi lai get thanh vien
      if ($level >= 3) {
        return $count;
      }
      else {
        $level++;
        return $this->countmember2($arrayobject, $strParentId, $level, $count);
      }
    }
  }

  function general_ma_id()
  {
     $team_int = rand(MIN_INT, MAX_INT);
     if(!$this->checkMaID("member",$team_int))
     {
         return $team_int;
     }else
     {
         return $this->general_ma_id();
     }
  }

  function generate_pin() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pin = 'LLM';
    for ($i = 0; $i < 10; $i++) {
      $pin .= $characters[rand(0, strlen($characters))];
    }
    return $pin;
  }
  
  function generate_pin_active() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pin = 'LLM';
    for ($i = 0; $i < 30; $i++) {
      $pin .= $characters[rand(0, strlen($characters))];
    }
    return $pin;
  }

  function checkMaID($table, $ma_id) {
    $result = $this->getDynamic($table, "ma_id='" . $ma_id . "'", "");
    $result = $this->totalRows($result);
    if ($result > 0) {
      return 1;
    }
    else {
      return 0;
    }
  }
  
  function checkUsername($table, $username) {
    $result = $this->getDynamic($table, "tendangnhap='" . $username . "'", "");
    $result = $this->totalRows($result);
    if ($result > 0) {
      return 1;
    }
    else {
      return 0;
    }
  }

  function getlastlogin($table, $member_id) {
    $result = $this->getDynamic($table, "member_id='" . $member_id . "'", "id desc limit 0,2");
    $total = $this->totalRows($result);
    $last_login = array();
    if ($total > 0) {
      while ($row = $this->nextData($result)) {
        $last_login = $row;
      }
    }
    return $last_login;
  }


  function cayhethongmember2($parentid, $tang, $level) {
    $result = $this->getDynamic("member", "sponser_id in(" . $parentid . ") ", "id asc");
    $total = $this->totalRows($result);
    $_SESSION["total_all"] += $total;
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $id = $row["id"];
        $ma_id      = $row["ma_id"];
        $ponser     = $this->getInfoColum("member", $row['sponser_id']);
        $packages   = $this->getInfoColum("packages", $row['packages_id']);
        $status     = $row["status"];
        if($status)
        {
           $images = "<img src=\'".$packages["picture"]."\' width=\'40\' height=\'40\' />";
        }else
        {
           $images = "<img src=\'/style/images/packages/1.png\' width=\'40\' height=\'40\' />";
        }
        $parentid_text = $ponser['ma_id'] . "-" . $ponser['hovaten'];
        $_SESSION["str_member"] .= "[{v:'" . $ma_id . "-" . $row["hovaten"] . "',f:'" . $ma_id . "-" . $row["hovaten"] . "<br/>" . $images . "'}, '" . $parentid_text . "', ''],";
        $i++;
      }
      $strParentId .= '-1';
      //Goi lai get thanh vien
      if ($tang > 0 && $level >= $tang) {
        return true;
      }
      else {
        $level++;
        return $this->cayhethongmember2($strParentId, $tang, $level);
      }
    }
    else {
      return true;
    }
  }  
  
 
  
/****************************************************/
// Get: Ban List
  function banlist() {
    global $page, $arrLayout, $banlist;
    if (!isset($banlist)) {
      $banlist = 0;
      $filepath = 'banlist.txt';
      $find = '/' . @ file_get_contents($filepath) . '|^$/i';
      $condition = $page . $this->get_ip() . $_SERVER['HTTP_USER_AGENT'];
      if (preg_match($find, $condition))
        $banlist = 1;
      else
        $banlist = 0;
    }
    return $banlist;
  }

  function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
        return $arr;
  }

  function showtitle($page, $url, $lang) {
    switch ($page) {
      case "home" :
        break;
      case "search" :
        echo "Search | ";
        break;
      case "login" :
        echo "Login | ";
        break;
      case "register" :
        echo "Register | ";
        break;
      case "account" :
        echo "Account | ";
        break;
      case "change-password" :
        echo "Change password | ";
        break;
      case "forgot-password" :
        echo "Forgot password | ";
        break;
      case "checkout" :
        echo "Checkout |";
        break;
      case "checkout-complete" :
        echo "Checkout complete |";
        break;
      default :
        break;
    }
  }

  function get_web_page( $url )
  {

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
	
	function wei2eth($wei)
	{
		return bcdiv($wei,'1000000000000000000',18);
	}
	
	function convertToCSVData($data) {   
		$csv_data = implode(',', $data);
		return mb_convert_encoding($csv_data, 'CP932', 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win');  
	}
}
?>