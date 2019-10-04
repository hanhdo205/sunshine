<?php
include str_replace('\\', '/', dirname(__FILE__)) . '/class.DBFUNCTION.php';
class BUSINESSLOGIC extends DBFUNCTION {
  private $html;
  private $js;
  private $utl;
  function __construct() {
    $this->html = SINGLETON_MODEL::getInstance("HTML");
    $this->js = SINGLETON_MODEL::getInstance("JAVASCRIPT");
    $this->utl = SINGLETON_MODEL::getInstance("UTILITIES");
  }
  function __destruct() {
    $this->html = null;
    $this->js = null;
    $this->utl = null;
  }
  function getHtml() {
    return $this->html;
  }
  function getJs() {
    return $this->js;
  }
  function getUtl() {
    return $this->utl;
  }
  function arrayHidden() {
    echo "\n<input type='hidden' name='arrayid' id='arrayid' value='" . $_POST["arrayid"] . "' />\n";
  }
  function SPLIT_ARRAY_QUERY_STRING($string_query) {
    $array_query = array();
    $array = explode("&", $string_query);
    foreach ($array as $value) {
      $arrayIndex = explode("=", $value);
      $array_query[$arrayIndex[0]] = $arrayIndex[1];
    }
    unset($array);
    unset($arrayIndex);
    return $array_query;
  }
  function Footer() {
    $html = $this->getHtml();
    echo $this->arrayHidden();
    echo "</div>";
    echo "

                    <div style='width:100%;margin:0 auto;background-color:#4f81b3;overflow:hidden'>

                        <div class='footer_above'>

                            <div class='left'></div>

                            <div class='right'></div>

                        </div>

                        <div class='clear'></div>

                        <div class='footer'>

                            <div class='left'>Copyright © " . date('Y') . "</div>

                            <div class='right'><a class='icon'  href='/" . ADMINISTRATOR_FOLDER . "'>Home Admin</a></div>

                            <div class='clear'></div>

                        <div>

                        <div class='clear'></div>

                    </div>";
    echo $html->closeForm();
    $this->dbClose();
    echo $html->closeBody();
    echo $html->closeHTML();
  }
  function translateField(& $fullColumn, & $arrayField) {
    $utl = $this->getUtl();
    if (is_array($fullColumn)) {
      $col = $fullColumn["Comment"];
      $dataType = $fullColumn[1];
      $dataLength = $fullColumn[2];
      if (is_array($col) && is_array($arrayField)) {
        foreach ($col as $key => $value) {
          $value_strip = strtolower($utl->stripUnicode($value));
          if (!$fullColumn[4][$key]) {
            $selectCol[$key] = ucwords($value);
          }
        }
      }
    }
    return $selectCol;
  }
  function actionPerformed($isDelete, $subInsert, $subUpdate, $isEdit, $table_name, $arrayid, & $arrayCol, & $arrayValue, $pk, $deleteParent = "", $where = "", $fieldName = "") {
    if ($isDelete)
      $msg = $this->delete($table_name, $arrayid, $arrayCol, $pk, $deleteParent);
    if (isset($_GET["multiEdit"]) && $subUpdate)
      $msg = $this->multiUpdate($table_name, $arrayCol, $pk, $arrayid);
    if (isset($_GET["multiEdit"]))
      $arrayValue = $this->multiEdit($table_name, $arrayCol, $pk, $arrayid);
    if ($subUpdate)
      $msg = $this->update($table_name, $arrayCol, $pk . "='" . $_GET['edit'] . "'", $_GET['edit']);
    if ($isEdit)
      $arrayValue = $this->edit($table_name, $arrayCol, $pk . "='" . $_GET['edit'] . "'");
    if (isset($_GET["multiInsert"]) && $subInsert)
      $msg = $this->multiInsert($table_name, $arrayCol, $pk, $arrayid);
    if ($subInsert)
      $msg = $this->insert($table_name, $arrayCol);
//if($subUpdateCat)
//$msg=$this->updateCat($table_name,$arrayid,$_GET["parentCat"],$fieldName,&$arrayCol);
    $isDuplicate = (isset($_GET['duplicate'])) ? true : false;
    if ($isDuplicate)
      $this->duplicate($table_name, $arrayid, $arrayCol, $pk);
    return $msg;
  }
  function duplicate($table, $arrayid, & $arrayCol, $pk) {
    $js = $this->getJs();
    if ($arrayid[strlen($arrayid) - 1] == ",")
      $arrayid = substr($arrayid, 0, strlen($arrayid) - 1);
    $arrayid = str_replace(",", "','", $arrayid);
    $isRecursive = false;
    if (in_array("parentid", $arrayCol))
      $isRecursive = true;
    if (!empty($arrayid)) {
      if (!$isRecursive) {
        $array_object = $this->getArray($table, $pk . " in('" . $arrayid . "')", "", "Assoc");
        $firstCol = $array_col[0];
        if ($firstCol == "id")
          array_shift($arrayCol);
// Remove id auto increment, no insert this field
        $r = 0;
        if (count($array_object) > 0)
          foreach ($array_object as $ikey => $row) {
            $array_col_value = array();
            $i = 0;
            foreach ($arrayCol as $colName) {
              if ($i <= 0 && $firstCol != "id")
                $array_col_value[$colName] = $row[$colName] . $r;
              elseif (in_array($colName, array("datecreated", "dateupdated")))
                $array_col_value[$colName] = time();
              else
                $array_col_value[$colName] = $row[$colName];
              ++$i;
            }
            $this->InsertTable($table, $array_col_value);
            ++$r;
        }
        unset($array_col_value, $array_col, $array_object, $arrayid);
      }
    }
//$js->redirectURL(queryParameter);
  }
  function delete($table, $arrayid, & $arrayCol, $pk, $deleteParent = "") {
    $js = $this->getJs();
    $arrayid_tmp = $arrayid;
    $arrayid = str_replace(",", "','", $arrayid);
    $arrayid = "'" . $arrayid . "'";
    $arrayid = str_replace("''", "'-1'", $arrayid);
    if ($deleteParent != "")
      $deleteParent = " and " . $deleteParent;
    if (!empty($arrayid_tmp))
      $this->logDelete(explode(",", $arrayid_tmp), $table);
    if (in_array("parentid", $arrayCol)) {
      if ($arrayid != "") {
        $arrayid = explode(',', $arrayid);
        foreach ($arrayid as $parentid) {
          $affect = $this->delete_parent($table, $parentid);
        }
      }
    }
    else {
      if (!empty($arrayid)) {
        $affect = $this->deleteDynamic($table, $pk . " in (" . $arrayid . ") " . $deleteParent);
      }
    }
    if ($affect > 0)
      $js->displayJS("huy();");
    else
      return ERROR_EXCEPTION;
//return str_replace("{num}",$affect,DELETED);
  }
  function logDelete($arrayid = null, $tableName = "") {
    $array = null;
    if (count($arrayid) > 0) {
      foreach ($arrayid as $value) {
        if (!empty($value)) {
          $array = $this->getArray($tableName, "id='" . $value . "'", "", "Assoc");
          if (count($array [0]) > 0) {
            foreach ($array [0] as $k => $v) {
              $content .= $k . "-&index&-" . $v . "-&column&-";
            }
            $arraytmp = $GLOBALS["arrayLog"];
            $arraytmp["action"] = "Delete";
            $arraytmp["deleting"] = 2;
            $arraytmp["content"] = $content;
            $arraytmp["tablename"] = $tableName;
//$this->writeLog($arraytmp);
          }
        }
      }
    }
  }
  function logInsert() {
    $arraytmp = $GLOBALS["arrayLog"];
    $arraytmp["action"] = "Insert";
    $arraytmp["deleting"] = 3;
    $arraytmp["content"] = "";
//$this->writeLog($arraytmp);
  }
  function logUpdate($arrayid = null, $tableName = "") {
    $array = array();
    if (count($arrayid) > 0) {
      foreach ($arrayid as $value) {
        if (!empty($value)) {
          $content = "";
          $array = $this->getArray($tableName, "id='" . $value . "'", "", "Assoc");
          if (count($array [0]) > 0) {
            foreach ($array [0] as $k => $v) {
              $content .= $k . "-&index&-" . $v . "-&column&-";
            }
            $arraytmp = $GLOBALS["arrayLog"];
            $arraytmp["action"] = "Update";
            $arraytmp["deleting"] = 1;
            $arraytmp["content"] = $content;
            $arraytmp["tablename"] = $tableName;
//$this->writeLog($arraytmp);
          }
        }
      }
    }
  }
  function updatprice($array = null) {
    if (!$this->totalRows($this->getDynamic("product_blog_price", "product_id=" . $array ["product_id"] . " AND ((SELECT price FROM product_blog_price where product_id=" . $array ["product_id"] . " order by datecreated desc limit 0,1)=" . $array ["price"] . ")", "datecreated desc limit 0,1")))
      $this->insertTable("product_blog_price", array("product_id" => $array ["product_id"], "price" => $array ["price"], "datecreated" => time()));
  }
  function checkRewite($rewrite, $table, $lang) {
    $rewrite = strtolower($rewrite);
    $result = $this->getDynamic($table, "title_rewrite_" . $lang . "='" . $rewrite . "'", "");
    $total_res = $this->totalRows($result);
    if ($total_res > 0) {
      $rewrite_tam = $rewrite . "-t";
      return $this->checkRewite($rewrite_tam, $table, $lang);
    }
    else {
      return $rewrite;
    }
  }
  function checkRewiteUpdate($rewrite, $table, $id, $lang) {
//echo "sdfjsdf".$id;
//exit();
    $rewrite = strtolower($rewrite);
    $result_id = $this->getDynamic($table, "id='" . $id . "'", "");
    $total_id = $this->totalRows($result_id);
    if ($total_id > 0) {
      $rows = $this->nextData($result_id);
      $title_rewrite_id = $rows["title_rewrite_" . $lang];
//exit();
      if ($rewrite == $title_rewrite_id) {
        return $rewrite;
      }
      else {
        return $this->checkRewite($rewrite, $table, $lang);
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
    $arrayLastParent = $this-> get_Parent_Last_Level($parentid,1);
    $tem = explode(",", $arrayLastParent);
    if(count($tem)>0)
    {
      $result = $this->getDynamic("member", "id=" . (int)$tem[0] . "", "");
      if ($this->totalRows($result) > 0) {
        $row = $this->nextData($result);
        return $row["ma_id"];      
      }
    }else
    {
      return 0;
    }  
    
  }
  
  function getID_Parent($parentid) {
    $result = $this->getDynamic("member", "parentid =0", "id asc limit 0,1");
    $total_pa = $this->totalRows($result);
    if($total_pa>0)
    {      
      $row = $this->nextData($result);
      $arrayLastParent = $this-> get_Parent_Last_Level($row["id"],1);
      $tem = explode(",", $arrayLastParent);
      if(count($tem)>0)
      {      
        $result = $this->getDynamic("member", "id=" . (int)$tem[0] . "", "");
        if ($this->totalRows($result) > 0) {
          $row = $this->nextData($result);
          return $row["id"];      
        }
      }else
      {
        return 0;
      }
    }else
    {
       return 0;
    }  
    
  }
  
  function insert($tableName, & $arrayCol) {
    $utl = $this->getUtl();
    $js = $this->getJs();
    $colRewrite = array();
    if (is_array($arrayCol))
      foreach ($arrayCol as $key => $value) {
        $data = $_POST[$value];
        if ((!is_array($data) && isset($_POST[$value])) || in_array($value, array("datecreated", "dateupdated"))) {
          if (in_array($value, array("datecreated", "dateupdated"))) {
            if (!$data)
              $colInsert[$value] = time();
            else {
              $colInsert[$value] = strtotime($data);
            }
          }
          else
            if (strstr(strtolower($value), "password") != "")
              $colInsert[$value] = md5($data);
            else
              if (strstr(strtolower($value), "date") != "")
                $colInsert[$value] = strtotime($data);
              else
                $colInsert[$value] = $data;
        }
        else
          $colRewrite[] = $value;
    }
    if ($tableName == 'member') {
      //get
      if($colInsert["parentid"]=="")
      {
        $colInsert["parentid"] = $this->getID_Parent(0);
      }
      
      if ($this->checkMaID('member', $colInsert["ma_id"]) > 0) {
        echo "<script>
            alert('Mã ID bị trùng. Vui lòng nhập Mã ID khác');
            window.history.back();
            </script>";
            exit();
      }
      /*
      if ($this->checkUsername('member', $colInsert["tendangnhap"]) > 0) {
        echo "<script>
            alert('Tên đăng nhập bị trùng. Vui lòng nhập tên khác');
                window.history.back();
            </script>";
        exit();
      }
      */
    }
    /*
    if($_POST["password_root"]!=password_root)
    {
          echo "<script>
            alert('Password root admin không đúng. Vui lòng xác nhận lại');
            window.history.back();
            </script>";
            exit();
    }else
    {
    */ 
        $affected = $this->updateTable($tableName, $colInsert, "", "insert");
        if ($affected > 0) {
          $js->displayJS("huy();");
        }
        else
          return DATA_EXIST;
    /*}*/
  }
  function update($tableName, & $arrayCol, $where, $idtmp) {

    $utl = $this->getUtl();
    $js = $this->getJs();
    $colRewrite = array();
    $colInsert = array();
    if (is_array($arrayCol))
      foreach ($arrayCol as $key => $value) {
        $data = $_POST[$value];
        if ((!is_array($data) && isset($_POST[$value])) || in_array($value, array("datecreated", "dateupdated"))) {
          if (in_array($value, array("datecreated", "dateupdated"))) {
            if (!$data)
              $colInsert[$value] = time();
            else {
              $colInsert[$value] = strtotime($data);
            }
          }
          else
            if (strstr(strtolower($value), "password") != "")
            {
               if($data!="")
               {
                  $colInsert[$value] = md5($data);
               }

            }
            elseif (strstr(strtolower($value), "date") != "") {
              $colInsert[$value] = strtotime($data);
            }
            else
              $colInsert[$value] = $data;
        }
        else
          $colRewrite[] = $value;
    }
    //kiem tra xem ma_id co bi trung
    if ($tableName == 'member') {
      $result_id = $this->getDynamic("member", "id='" . $idtmp . "'", "");
      $total_id = $this->totalRows($result_id);
      if ($total_id > 0) {
        $rows = $this->nextData($result_id);
        $ma_id_old = $rows["ma_id"];
        if ($ma_id_old != $colInsert["ma_id"]) {
          if ($this->checkMaID('member', $colInsert["ma_id"]) > 0) {
            echo "<script>
              alert('Mã ID bị trùng. Vui lòng nhập Mã ID khác');
              window.history.back();
              </script>";
            exit();
          }
        }
      }

        if($colInsert["password"]=="")
        {
           $colInsert["password"] = $rows["password"];
        }

        if($colInsert["password2"]=="")
        {
           $colInsert["password2"] = $rows["password2"];
        }

        if($colInsert["password3"]=="")
        {
           $colInsert["password3"] = $rows["password3"];
        }

    }
    /*  
    if($_POST["password_root"]!=password_root)
    {
          echo "<script>
            alert('Password root admin không đúng. Vui lòng xác nhận lại');
            window.history.back();
            </script>";
            exit();
    }else
    {
    */ 
        if (!empty($idtmp)) {
          $array [] = $idtmp;
        }
        $affect = $this->updateTable($tableName, $colInsert, $where);
        if ($affect > 0) {
          $js->displayJS("huy();");
        }
        else
          return ERROR_EXCEPTION;
    /*}*/
  }
  function updateCat($tableName, $arrayID, $parentid = 0, $fieldName = "", & $arrayCol) {
    $js = $this->getJs();
    if ($arrayID != "") {
      $arrayID = str_replace(",", "','", $arrayID);
      if (!empty($fieldName)) {
        $affect = $this->updateTable($tableName, array($fieldName => $parentid), "id in('" . $arrayID . "')");
      }
      if ($affect > 0)
        $js->displayJS("huy();");
      else
        return ERROR_EXCEPTION;
    }
  }
  function multiEdit($tableName, & $arrayCol, $pk, $arrayid) {
    $arrayid = $this->escapeStr($arrayid);
    $arrayid = str_replace(",", "','", $arrayid);
    $arrayObj = $this->getArray($tableName, $pk . " in('" . $arrayid . "')", "");
    $arrayNew = array();
    $arrayid = str_replace("','", ",", $arrayid);
    $arrayid = explode(",", $arrayid);
    if (is_array($arrayObj)) {
      foreach ($arrayObj as $key => $value) {
        foreach ($arrayid as $v)
          if ($value[0] == $v)
            $arrayNew[$v] = $value;
      }
    }
    return $arrayNew;
  }
  function edit($tableName, & $arrayCol, $where) {
    $rst = $this->getDynamic($tableName, $where, "");
    if ($this->totalRows($rst) > 0) {
      $row = $this->nextData($rst);
      if (is_array($arrayCol))
        foreach ($arrayCol as $value)
          $array [$value] = stripslashes($row[$value]);
      return $array;
    }
  }
  function generateForms($arrayConst, & $fullColumn, & $excludeCol, & $col, & $arrayValue, $isInsert, $isEdit, $msg, $option = "", $bienTemplate = "") {
    echo "<div id='panelForm' class='panelForm'>

                	<table id='mainForm' class='mainForm' cellpadding='1' cellspacing='1'>";
    echo $this->returnTitleMenu($arrayConst["titlePage"]);
    echo "

                    <tr>

                          <td class='boxGrey2'  colspan='2'>" . (($isInsert) ? $this->stateInsert() : (($isEdit) ? $this->stateUpdate() : '')) . "

                          </td>

                    </tr>";
    echo "

                    <tr>

                        <td colspan='4' class='txtdo'>$msg</td>

                    </tr>";
    echo $option;
    if (isset($_GET["multiEdit"]) || isset($_GET["multiInsert"])) {
      $arrayid = $_POST["arrayid"];
      echo $this->generateArrayControls($fullColumn, $excludeCol, $col, $arrayValue, $arrayid, $isInsert, $isEdit, $bienTemplate, $arrayConst);
    }
    else
      echo $this->generateControls($fullColumn, $excludeCol, $col, $arrayValue, $isInsert, $isEdit, $bienTemplate, $arrayConst);
    echo "</table></div>";
  }
  function generateForms_main($arrayConst) {
    $isInsert = $arrayConst['isInsert'];
    $isEdit = $arrayConst['isEdit'];
    $option = $arrayConst['option'];
    echo "<div id='panelForm' class='panelForm'>

				<table id='mainForm' class='mainForm' cellpadding='1' cellspacing='1'>";
    echo $this->returnTitleMenu($arrayConst["titlePage"]);
    echo "

				<tr>

					  <td class='boxGrey2'  colspan='2'>" . (($isInsert) ? $this->stateInsert() : (($isEdit) ? $this->stateUpdate() : '')) . "

					  </td>

				</tr>";
    echo "

				<tr>

					<td colspan='4' class='txtdo'>$msg</td>

				</tr>";
    echo $option;
    echo $this->generateControls_main($arrayConst);
    echo "</table></div>";
  }
  function generateDiv($div_id) {
    $rst = $this->getDynamic("front_div", "id=$div_id", "");
    $rs = $this->nextData($rst);
    if (!$rs['tag_name']) {
      $rs['tag_name'] = 'div';
    }
    if ($rs['is_hide']) {
      return "";
    }
    $rs['class_content'] .= $this->getValueOfQuery('SELECT class_content FROM front_class WHERE id=' . $rs['class1']);
    $rs['class_content'] .= $this->getValueOfQuery('SELECT class_content FROM front_class WHERE id=' . $rs['class2']);
    $rs['class_content'] .= $this->getValueOfQuery('SELECT class_content FROM front_class WHERE id=' . $rs['class3']);
    $rs['class1'] = $this->getValueOfQuery('SELECT class_name FROM front_class WHERE id=' . $rs['class1']);
    $rs['class2'] = $this->getValueOfQuery('SELECT class_name FROM front_class WHERE id=' . $rs['class2']);
    $rs['class3'] = $this->getValueOfQuery('SELECT class_name FROM front_class WHERE id=' . $rs['class3']);
    $style = array();
    if ($rs['colorpicker_background']) {
      $style[] = 'background-color:#' . $rs['colorpicker_background'];
    }
    if (strstr($rs['style_css'], '{'))
      $layout_css = $rs['style_css'];
    else
      if ($rs['div_id'] && $rs['style_css'])
        $style[] = $rs['style_css'];
      if ($style)
        $self_css = '#' . $rs['div_id'] . '{' . implode(';', $style) . '}';
      $html .= '<style>' . implode(' ', array($horizon_css, $self_css, $rs['class_content'])) . ' ' . $layout_css . '</style>';
    $html .= '<' . $rs['tag_name'] . ' id="' . $rs['div_id'] . '" class="' . implode(' ', array($rs['class'], $rs['class1'], $rs['class2'], $rs['class3'])) . '">';
    $parentid = $rs['id'];
    $rst = $this->getDynamic("front_div", "parentid=$parentid AND (is_hide!=1 OR ISNULL(is_hide))", "position asc");
    $is_leaf = true;
    if (!$rs['is_static_html']) {
      while ($rs_child = $this->nextData($rst)) {
        $is_leaf = false;
        if ($rs['is_component_container']) {
          if ($_GET['component_function_alias'] == $rs_child['component_url_alias'])
            $html .= $this->generateDiv($rs_child['id']);
        }
        else {
          $html .= $this->generateDiv($rs_child['id']);
        }
      }
    }
    if ($is_leaf) {
      $html .= $this->getModule($rs) . '</' . $rs['tag_name'] . '>';
    }
    else {
      if ($rs['is_horizon_div'])
        $html .= '<br style="clear:both">';
      $html .= '</' . $rs['tag_name'] . '>';
    }
    return $html;
  }
  function generateControls_main($arrayConst) {
    $Meta = $arrayConst['Meta'];
    $arrayValue = $arrayConst['arrayValue'];
    $html = $this->getHtml();
    $str = "";
    $i = 0;
    global $rules_script;
    global $messages_script;
    if ($_GET["table_name"] == 'article') {
      $id_category = $_GET["article_category_id"];
      $type_article = $this->getid_articaltype("article_category", (int) $id_category);
    }
    foreach ($Meta['Field'] as $field) {
// javascript
      $rules_script .= $field["field_name"] . " :

                                        {";
      if ($field["check_require"])
        $rules_script .= "required: true,";
      if ($field["check_email"])
        $rules_script .= "email: true,";
      if ($field["check_range_min"])
        $rules_script .= "minlength: " . $field["check_range_min"] . ",";
      if ($field["check_range_max"])
        $rules_script .= "maxlength: " . $field["check_range_min"] . ",";
      if ($field["check_url"])
        $rules_script .= "url: true,";
      if ($field["check_digit"])
        $rules_script .= "digits: true,";
      $rules_script .= "},";
      $messages_script .= $field["field_name"] . " :

                                        {";
      if ($field["check_require"])
        $messages_script .= "required: 'Vui lòng nhập " . $field['Label'] . "',";
      if ($field["check_email"])
        $messages_script .= "email: '" . $field['Label'] . " sai định dạng',";
      if ($field["check_range_min"])
        $messages_script .= "minlength: 'Nhập tối thiểu " . $field["check_range_min"] . " ký tự',";
      if ($field["check_range_max"])
        $messages_script .= "maxlength: 'Nhập tối đa " . $field["check_range_max"] . " ký tự',";
      if ($field["check_url"])
        $messages_script .= "url: 'Xin vui lòng nhập một URL hợp lệ.',";
      if ($field["check_digit"])
        $messages_script .= "digits: 'Xin vui lòng nhập  chỉ chữ số.',";
      $messages_script .= "},";
      $Field = $field['Field'];
      $Label = $field['Label'];
      if (($arrayConst['isInsert']) && (!$arrayValue[$Field])) {
        $arrayValue[$Field] = $field['Default'];
      }
      if ($_GET["table_name"] == 'article') {
        if (($field['edit_show']) && ($field['insert_show']) && ($Field != "id") && ($field['insert_show'] != '') && (strstr($field['type_id'], $type_article) != "")) {
          if ($field['fk_from']) {
            $arrayValue[$Field] = (!empty($arrayConst["iscatURL"][$Field]) ? $arrayConst["iscatURL"][$Field] : $arrayValue[$Field]);
            if ($field['fk_isMultiLevel']) {
              echo "<tr><td class='boxGrey'>" . $Label . "</td>";
              echo "<td class='boxGrey2'><div id='div_select_" . $Field . "'>";
              echo $this->generateRecursiveSelect($Field, $field["fk_text"], $field["fk_value"], $arrayValue[$Field], $field["fk_from"], 0, array("firstText" => "Root", "onchange" => $field["fk_onchange"]));
              echo "</div></td></tr>";
            }
            else {
              $field["fk_where"] = ($field["fk_where"]) ? $field["fk_where"] : "";
              $field["fk_orderby"] = ($field["fk_orderby"]) ? $field["fk_orderby"] . " asc" : $field["fk_value"] . " asc";
              echo "<tr><td class='boxGrey'>" . $Label . "</td><div id='div_select_" . $Field . "'>";
              echo "<td class='boxGrey2'>" . $this->generateNoRecursiveSelect($Field, $field["fk_text"], $field["fk_value"], $arrayValue[$Field], $field["fk_from"], $field["fk_where"], $field["fk_orderby"], array("firstText" => "Root", "onchange" => $field["fk_onchange"])) . "</div></td></tr>";
            }
          }
          else
            switch ($field['Type']) {
              case "int" :
                if ($field['Length'] <= 4) {
                  echo "<tr><td class='boxGrey'>" . $Label . "</td><td class='boxGrey2'><input name='" . $Field . "' id='" . $Field . "' type='hidden' value='" . $arrayValue[$Field] . "'>" . $html->checkbox("chk_tmp[]", "1", "", array("onclick" => "if(this.checked){document.getElementById('" . $Field . "').value=1;} else{document.getElementById('" . $Field . "').value=0;}", "checked" => $arrayValue[$Field])) . "</td></tr>";
                }
                else
                  if ($field['Length'] == 20 && strstr(strtolower($Field), "date") != "") {
                    if ($arrayValue[$Field] == 0)
                      $arrayValue[$Field] = time();
                    $arrayValue[$Field] = date("d-m-Y H:i:s", (int) $arrayValue[$Field]);

                    $endday = date("Y")-81;
                    $startday = date("Y");
                    echo "<tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'readonly' => 'readonly', 'maxlength' => '50', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . "

					  <script type=\"text/javascript\">

						$(function() {
                             var endday = ".$endday.";
                             var startday = ".$startday.";
							$('#" . $Field . "').datepicker({
								changeMonth: true,
                  				changeYear: true,
                  				dateFormat: 'dd-mm-yy',
                                yearRange: endday+':'+startday

							});

						});

					  </script>

					  </td>

				  </tr>";
                  }
                  else {
                    echo "

				  <tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . "</td>

				  </tr>";
                }
                break;
              case "real" :
                if ($arrayValue[$Field] == "")
                  $arrayValue[$Field] = 0;
                if (strstr(strtolower($Field), "percent") != "" || strstr(strtolower($Field), "vat") != "")
                  $symbol = VAT;
                elseif (strstr(strtolower($Field), "price") != "")
                  $symbol = CURRENCY;
                echo "

				<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . " " . $symbol . "</td>

				</tr>";
                break;
              case "string" :
                if (strstr($Field, "picture") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $Label . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;min-height:80px'>";
                  if ($arrayValue[$Field] == "")
                    $arrayValue[$Field] = defaultPicture;
                  echo $this->generateChoicePicture($Field . "Text", $Field, $arrayValue[$Field]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($Field, "file") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $Label . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;margin-bottom:5px'>";
                  echo $this->generateChoiceFile($Field, $arrayValue[$Field]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($Field, "url") != "") {
                  if ($arrConst['isInsert'])
                    $arrayValue[$Field] = "#";
                  echo "

				  <tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>

					  <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> Ex: http://www.vnec.com

					  </td>

				  </tr>";
                }
                elseif (strstr($Field, "password") != "") {
                  echo "<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd2', 'maxlength' => '100', 'type' => 'password', 'value' => '', 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)')) . "</td>

					</tr>";
/*	echo "<tr>

<td class='boxGrey'>".CONFIRM.$Label."</td>

<td class='boxGrey2'>".$html->input("confirm_".$Field,array('class' => 'nd2','maxlength' => '100','type' => 'password','value' =>'','onfocus' => 'fo(this)','onblur' => 'lo(this)'))."</td>

</tr>";

*/
                }
                elseif ($field['Length'] > 300) {
                  if ($field->Rewrite) {
                    echo "<input type='hidden' " . $disable . " name='" . $Field . "[" . $field->Rewrite . "]' id='" . $Field . "[" . $field->Rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> ";
                    break;
                  }
                  else {
                    $field['edit_width'] = ($field['edit_width']) ? $field['edit_width'] : "350px";
                    $field['edit_height'] = ($field['edit_height']) ? $field['edit_height'] : "60px";
                    echo "

										  <tr>

											<td class='boxGrey'>" . $Label . "</td>

											<td class='boxGrey2'>" . $html->textArea($Field, array("value" => $arrayValue[$Field], "style" => "width:350px;height:50px;padding-top:4px", "focus" => "fo(this)")) . "</td>

										  </tr>";
                    echo "<script>

										 $('#" . $Field . "').mouseover(

										 	function(){

												$('#" . $Field . "').css('width','600px');

												$('#" . $Field . "').css('height','200px');

											}

										 )



										 </script>";
                  }
                }
                else {
                  if (is_array($excludeCol[3]) && in_array($Field, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $Field) {
                        echo "<input type='hidden' name='" . $Field . "[" . $k_rewrite . "]' id='" . $Field . "[" . $k_rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> ";
                      }
                    }
                  }
                  else {
                    echo "

									  <tr>

										  <td class='boxGrey'>" . $Label . "</td>

										  <td class='boxGrey2'>

										  <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" />

										  </td>

									  </tr>";
                  }
                }
                break;
              case "blob" :
              case "longtext" :
              case "longblob" :
                $arrayObjectLabel[] = $Label;
                $arrayObjectEditor[] = $Field;
                break;
              default :
                echo "

				<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>

					 <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\"  onblur=\"return nhapso(event,'percent');\"  onKeyPress=\"return nhapso(event,'percent');\"/>

					</td>

				</tr>";
                break;
          }
          if (strstr($Field, "colorpicker") != "") {
            echo "<script>$('#" . $Field . "').ColorPicker({

	onSubmit: function(hsb, hex, rgb, el) {

		$(el).val(hex);

		$('#" . $Field . "').css('background-color','#'+hex);

		$(el).ColorPickerHide();

	},

	onBeforeShow: function () {

		$(this).ColorPickerSetColor(this.value);

	}

})

.bind('keyup', function(){

	$(this).ColorPickerSetColor(this.value);



});

var colorvalue = $('#" . $Field . "').val();

$('#" . $Field . "').css('background-color','#'+colorvalue);

</script>";
          }
        }
//end if cua table artical
      }
      else {
        if (($field['edit_show']) && ($field['insert_show']) && ($Field != "id")) {
// sinh ra ma ID
          if ($_GET["table_name"] == 'member') {
                if (isset($_GET["insert"])) {
                  /*
                  $totalmember = $this->totalRows($this->getDynamic('member', ''));
                  $totalmember = $totalmember + 1;
                  if (strlen($totalmember) < 4) {
                    $rest = substr("000000000" . $totalmember, - 4);
                  }
                  else {
                    $rest = $totalmember;
                  }
                  $arrayValue['ma_id'] = MAID . $rest;
                  */
                  //$team_int = random_int(MIN_INT, MAX_INT);
                  $arrayValue['ma_id'] = $this->general_ma_id();

                }
          }
          if ($field['fk_from']) {
            $arrayValue[$Field] = (!empty($arrayConst["iscatURL"][$Field]) ? $arrayConst["iscatURL"][$Field] : $arrayValue[$Field]);
            if ($field['fk_isMultiLevel']) {
              echo "<tr><td class='boxGrey'>" . $Label . "</td>";
              echo "<td class='boxGrey2'><div id='div_select_" . $Field . "'>";
              echo $this->generateRecursiveSelect($Field, $field["fk_text"], $field["fk_value"], $arrayValue[$Field], $field["fk_from"], 0, array("firstText" => "Root", "onchange" => $field["fk_onchange"]));
              echo "</div></td></tr>";
            }
            else {
              $field["fk_where"] = ($field["fk_where"]) ? $field["fk_where"] : "";
              $field["fk_orderby"] = ($field["fk_orderby"]) ? $field["fk_orderby"] . " asc" : $field["fk_value"] . " asc";
              echo "<tr><td class='boxGrey'>" . $Label . "</td><div id='div_select_" . $Field . "'>";
              echo "<td class='boxGrey2'>" . $this->generateNoRecursiveSelect($Field, $field["fk_text"], $field["fk_value"], $arrayValue[$Field], $field["fk_from"], $field["fk_where"], $field["fk_orderby"], array("firstText" => "Root", "onchange" => $field["fk_onchange"])) . "</div></td></tr>";
            }
          }
          else
            switch ($field['Type']) {
              case "int" :
                if ($field['Length'] <= 4) {
                  echo "<tr><td class='boxGrey'>" . $Label . "</td><td class='boxGrey2'><input name='" . $Field . "' id='" . $Field . "' type='hidden' value='" . $arrayValue[$Field] . "'>" . $html->checkbox("chk_tmp[]", "1", "", array("onclick" => "if(this.checked){document.getElementById('" . $Field . "').value=1;} else{document.getElementById('" . $Field . "').value=0;}", "checked" => $arrayValue[$Field])) . "</td></tr>";
                }
                else
                  if ($field['Length'] == 20 && strstr(strtolower($Field), "date") != "") {
                    if ($arrayValue[$Field] == 0)
                      $arrayValue[$Field] = time();
                    $arrayValue[$Field] = date("d-m-Y H:i:s", (int) $arrayValue[$Field]);
                    $endday = date("Y")-81;
                    $startday = date("Y");
                    echo "<tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'readonly' => 'readonly', 'maxlength' => '50', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . "

					  <script type=\"text/javascript\">

							$(function() {
                             var endday = ".$endday.";
                             var startday = ".$startday.";
							$('#" . $Field . "').datepicker({

								changeMonth: true,
                  				changeYear: true,
                  				dateFormat: 'dd-mm-yy',
                                yearRange: endday+':'+startday

							});

						});

					  </script>

					  </td>

				  </tr>";
                  }
                  else {
                    echo "

				  <tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . "</td>

				  </tr>";
                }
                break;
              case "real" :
                if ($arrayValue[$Field] == "")
                  $arrayValue[$Field] = 0;
                if (strstr(strtolower($Field), "percent") != "" || strstr(strtolower($Field), "vat") != "")
                  $symbol = VAT;
                elseif (strstr(strtolower($Field), "price") != "")
                  $symbol = CURRENCY;
                echo "

				<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$Field], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $Field . "')")) . " " . $symbol . "</td>

				</tr>";
                break;
              case "string" :
                if (strstr($Field, "picture") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $Label . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;min-height:80px'>";
                  if ($arrayValue[$Field] == "")
                    $arrayValue[$Field] = defaultPicture;
                  echo $this->generateChoicePicture($Field . "Text", $Field, $arrayValue[$Field]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($Field, "file") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $Label . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;margin-bottom:5px'>";
                  echo $this->generateChoiceFile($Field, $arrayValue[$Field]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($Field, "url") != "") {
                  if ($arrConst['isInsert'])
                    $arrayValue[$Field] = "#";
                  echo "

				  <tr>

					  <td class='boxGrey'>" . $Label . "</td>

					  <td class='boxGrey2'>

					  <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> Ex: http://www.vnec.com

					  </td>

				  </tr>";
                }
                elseif (strstr($Field, "password") != "") {
                  echo "<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>" . $html->input($Field, array('class' => 'nd2', 'maxlength' => '100', 'type' => 'password', 'value' => '', 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)')) . "</td>

					</tr>";
/*	echo "<tr>

<td class='boxGrey'>".CONFIRM.$Label."</td>

<td class='boxGrey2'>".$html->input("confirm_".$Field,array('class' => 'nd2','maxlength' => '100','type' => 'password','value' =>'','onfocus' => 'fo(this)','onblur' => 'lo(this)'))."</td>

</tr>";

*/
                }
                elseif ($field['Length'] > 300) {
                  if ($field->Rewrite) {
                    echo "<input type='hidden' " . $disable . " name='" . $Field . "[" . $field->Rewrite . "]' id='" . $Field . "[" . $field->Rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> ";
                    break;
                  }
                  else {
                    $field['edit_width'] = ($field['edit_width']) ? $field['edit_width'] : "350px";
                    $field['edit_height'] = ($field['edit_height']) ? $field['edit_height'] : "60px";
                    echo "

										  <tr>

											<td class='boxGrey'>" . $Label . "</td>

											<td class='boxGrey2'>" . $html->textArea($Field, array("value" => $arrayValue[$Field], "style" => "width:350px;height:50px;padding-top:4px", "focus" => "fo(this)")) . "</td>

										  </tr>";
                    echo "<script>

										 $('#" . $Field . "').mouseover(

										 	function(){

												$('#" . $Field . "').css('width','600px');

												$('#" . $Field . "').css('height','200px');

											}

										 )



										 </script>";
                  }
                }
                else {
                  if (is_array($excludeCol[3]) && in_array($Field, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $Field) {
                        echo "<input type='hidden' name='" . $Field . "[" . $k_rewrite . "]' id='" . $Field . "[" . $k_rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" /> ";
                      }
                    }
                  }
                  else {
                    echo "

									  <tr>

										  <td class='boxGrey'>" . $Label . "</td>

										  <td class='boxGrey2'>

										  <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\" />

										  </td>

									  </tr>";
                  }
                }
                break;
              case "blob" :
              case "longtext" :
              case "longblob" :
                $arrayObjectLabel[] = $Label;
                $arrayObjectEditor[] = $Field;
                break;
              default :
                echo "

				<tr>

					<td class='boxGrey'>" . $Label . "</td>

					<td class='boxGrey2'>

					 <input name='" . $Field . "' id='" . $Field . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$Field] . "\"  onblur=\"return nhapso(event,'percent');\"  onKeyPress=\"return nhapso(event,'percent');\"/>

					</td>

				</tr>";
                break;
          }
          if (strstr($Field, "colorpicker") != "") {
            echo "<script>$('#" . $Field . "').ColorPicker({

	onSubmit: function(hsb, hex, rgb, el) {

		$(el).val(hex);

		$('#" . $Field . "').css('background-color','#'+hex);

		$(el).ColorPickerHide();

	},

	onBeforeShow: function () {

		$(this).ColorPickerSetColor(this.value);

	}

})

.bind('keyup', function(){

	$(this).ColorPickerSetColor(this.value);



});

var colorvalue = $('#" . $Field . "').val();

$('#" . $Field . "').css('background-color','#'+colorvalue);

</script>";
          }
        }
//end if
      }
// end else
    } // end forech file in sytemfiled
    $lang = "";
    if (count($arrayObjectEditor) > 0) {
      echo "<tr><td class='boxGrey1' colspan='2'>";
      $spaw = new SpawEditor($arrayObjectEditor[0]);
      if (is_array($arrayObjectEditor))
        foreach ($arrayObjectEditor as $key => $Field) {
          if ($arrayConst['isInsert']) {
            if (strstr($Field, "_en") != "")
              $lang = "en";
            else
              $lang = "vn";
            $spaw->addPage(new SpawEditorPage($Field, $arrayObjectLabel[$key], $bienTemplate[$lang]));
          }
          else
            $spaw->addPage(new SpawEditorPage($Field, $arrayObjectLabel[$key], $arrayValue[$Field]));
      }
      echo $spaw->show();
      echo "</td></tr>";
    }
    
	/*
     echo "<tr>
    		<td class='boxGrey'><b clor='red'>Password Root admin</b></td>
			<td class='boxGrey2'><input type='password' placeholder='Nhập password root admin' name='password_root' id='password_root' value='123456' required style='width:400px' /></td>
			</tr>";
	*/		

    echo "

            <tr>

                  <td class='boxGrey2' colspan='2'>

                    " . (($arrayConst['isInsert']) ? $this->stateInsert() : (($arrayConst['isEdit']) ? $this->stateUpdate() : '')) . "

                  </td>

            </tr>";
  }
  function generateControls(& $fullColumn, & $excludeCol, & $col, & $arrayValue, $isInsert, $isEdit, $bienTemplate = "", $arrayConst) {
    $html = $this->getHtml();
    $str = "";
    $arrayCol_Type = $fullColumn[1];
    $arrayCol_Length = $fullColumn[2];
//create list field without the excluded field
    $selectCol = array();
    if (is_array($fullColumn["Comment"])) {
      foreach ($fullColumn["Comment"] as $key => $value) {
        if (!in_array($key, $excludeCol[0])) {
          if ($col[$key] != "") {
            $selectCol[$key] = $col[$key];
          }
          else
            $selectCol[$key] = $value;
        }
      }
    }
    $i = 0;
    if (is_array($selectCol))
      foreach ($selectCol as $value => $fName) {
        $dataType = strtolower($arrayCol_Type[$value]);
        $dataLength = $arrayCol_Length[$value];
        $arrayValue[$value] = stripslashes($arrayValue[$value]);
        if (!empty($excludeCol[1][$value])) {
          $data_source = $excludeCol[1][$value];
          if (is_array($data_source)) {
            $data_source["datatext"] = stripslashes($data_source["datatext"]);
            $arrayValue[$value] = (!empty($arrayConst["iscatURL"][$value]) ? $arrayConst["iscatURL"][$value] : $arrayValue[$value]);
            if ($data_source["isparentid"] == "parentid") {
              echo "<tr><td class='boxGrey'>" . $fName . "</td>";
              echo "<td class='boxGrey2'><div id='div_select_" . $value . "'>";
              echo $this->generateRecursiveSelect($value, $data_source["datatext"], $data_source["datavalue"], $arrayValue[$value], $data_source["table"], 0, array("firstText" => "Root", "onchange" => $data_source["onchange"]));
              echo "</div></td></tr>";
            }
            else {
              echo "<tr><td class='boxGrey'>" . $fName . "</td><div id='div_select_" . $value . "'>";
              echo "<td class='boxGrey2'>" . $this->generateNoRecursiveSelect($value, $data_source["datatext"], $data_source["datavalue"], $arrayValue[$value], $data_source["table"], "", $data_source["datavalue"] . " asc", array("firstText" => "Root", "onchange" => $data_source["onchange"])) . "</div></td></tr>";
            }
          }
          else {
            echo "<tr><td class='boxGrey'>" . $fName . "</td>";
            echo "<td class='boxGrey2'>" . $data_source . "</td></tr>";
          }
        }
        else
          if (!empty($excludeCol[2][$value])) {
            $data_source = $excludeCol[2][$value];
            $arrayValue[$value] = (!empty($arrayConst["iscatURL"][$value]) ? $arrayConst["iscatURL"][$value] : $arrayValue[$value]);
            $data_source["datatext"] = stripslashes($data_source["datatext"]);
            echo "<tr><td class='boxGrey'>" . $fName . "</td>";
            echo "<td class='boxGrey2'><div id='div_select_" . $value . "'>";
            echo $this->generateRecursiveSelect($value, $data_source["datatext"], $data_source["datavalue"], $arrayValue[$value], $data_source["table"], 0, array("firstText" => "Root", "onchange" => $data_source["onchange"]));
            echo "</div></td></tr>";
          }
          else {
            switch ($dataType) {
              case "int" :
                if ($arrayValue[$value] == "" && $value == "status")
                  $arrayValue[$value] = 1;
                elseif ($arrayValue[$value] == "")
                  $arrayValue[$value] = 0;
                if ($dataLength <= 4) {
                  echo "<tr><td class='boxGrey'>" . $fName . "</td><td class='boxGrey2'><input name='" . $value . "' id='" . $value . "' type='hidden' value='" . $arrayValue[$value] . "'>" . $html->checkbox("chk_tmp[]", "1", "", array("onclick" => "if(this.checked){document.getElementById('" . $value . "').value=1;} else{document.getElementById('" . $value . "').value=0;}", "checked" => $arrayValue[$value])) . "</td></tr>";
                }
                else
                  if ($dataLength == 20 && strstr(strtolower($value), "date") != "") {
                    if ($arrayValue[$value] == 0)
                      $arrayValue[$value] = time();
                    $arrayValue[$value] = date("d-m-Y H:i:s", (int) $arrayValue[$value]);
                    $endday = date("Y")-81;
                    $startday = date("Y");

                    echo "<tr>

					  <td class='boxGrey'>" . $fName . "</td>

					  <td class='boxGrey2'>" . $html->input($value, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'readonly' => 'readonly', 'maxlength' => '50', 'onkeypress' => "return nhapso(event,'" . $value . "')")) . "

					  <script type=\"text/javascript\">

						$(function() {
                             var endday = ".$endday.";
                             var startday = ".$startday.";
							$('#" . $value . "').datepicker({

							   changeMonth: true,
                  				changeYear: true,
                  				dateFormat: 'dd-mm-yy',
                                yearRange: endday+':'+startday

							});

						});

					  </script>

					  </td>

				  </tr>";
                  }
                  else {
                    echo "

				  <tr>

					  <td class='boxGrey'>" . $fName . "</td>

					  <td class='boxGrey2'>" . $html->input($value, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $value . "')")) . "</td>

				  </tr>";
                }
                break;
              case "real" :
                if ($arrayValue[$value] == "")
                  $arrayValue[$value] = 0;
                if (strstr(strtolower($value), "percent") != "" || strstr(strtolower($value), "vat") != "")
                  $symbol = VAT;
                elseif (strstr(strtolower($value), "price") != "")
                  $symbol = CURRENCY;
                echo "

				<tr>

					<td class='boxGrey'>" . $fName . "</td>

					<td class='boxGrey2'>" . $html->input($value, array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $value . "')")) . " " . $symbol . "</td>

				</tr>";
                break;
              case "string" :
                if (strstr($value, "picture") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $fName . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;min-height:80px'>";
                  if ($arrayValue[$value] == "")
                    $arrayValue[$value] = defaultPicture;
                  echo $this->generateChoicePicture($value . "Text", $value, $arrayValue[$value]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($value, "file") != "") {
                  echo "<tr><td class='boxGrey' style='vertical-align:middle'>" . $fName . "</td>";
                  echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
                  echo "<div style='width:160px;float:left;margin-top:5px;margin-bottom:5px'>";
                  echo $this->generateChoiceFile($value, $arrayValue[$value]);
                  echo "</div>";
                  echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
                }
                elseif (strstr($value, "url") != "") {
                  if ($isInsert)
                    $arrayValue[$value] = "#";
                  echo "

				  <tr>

					  <td class='boxGrey'>" . $fName . "</td>

					  <td class='boxGrey2'>

					  <input name='" . $value . "' id='" . $value . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" /> Ex: http://www.vnec.com

					  </td>

				  </tr>";
                }
                elseif (strstr($value, "password") != "") {
                  echo "<tr>

					<td class='boxGrey'>" . $fName . "</td>

					<td class='boxGrey2'>" . $html->input($value, array('class' => 'nd2', 'maxlength' => '100', 'type' => 'password', 'value' => '', 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)')) . "</td>

					</tr>";
/*echo "<tr>

<td class='boxGrey'>".CONFIRM.$fName."</td>

<td class='boxGrey2'>".$html->input("confirm_".$value,array('class' => 'nd2','maxlength' => '100','type' => 'password','value' =>'','onfocus' => 'fo(this)','onblur' => 'lo(this)'))."</td>

</tr>";

*/
                }
                elseif ($dataLength > 300 && $dataLength <= 500) {
                  if (is_array($excludeCol[3]) && in_array($value, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $value) {
                        echo "<input type='hidden' " . $disable . " name='" . $value . "[" . $k_rewrite . "]' id='" . $value . "[" . $k_rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" /> ";
                        break;
                      }
                    }
                  }
                  else {
                    echo "

										  <tr>

											<td class='boxGrey'>" . $fName . "</td>

											<td class='boxGrey2'>" . $html->textArea($value, array("value" => $arrayValue[$value], "style" => "width:350px;height:40px;padding-top:4px", "focus" => "fo(this)")) . "</td>

										  </tr>";
                  }
                }
                else {
                  if (is_array($excludeCol[3]) && in_array($value, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $value) {
                        echo "<input type='hidden' name='" . $value . "[" . $k_rewrite . "]' id='" . $value . "[" . $k_rewrite . "]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" /> ";
                      }
                    }
                  }
                  else {
                    echo "

									  <tr>

										  <td class='boxGrey'>" . $fName . "</td>

										  <td class='boxGrey2'>

										  <input name='" . $value . "' id='" . $value . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" />

										  </td>

									  </tr>";
                  }
                }
                break;
              case "blob" :
                $arrayObjectLabel[] = $fName;
                $arrayObjectEditor[] = $value;
                break;
              default :
                echo "

				<tr>

					<td class='boxGrey'>" . $fName . "</td>

					<td class='boxGrey2'>

					 <input name='" . $value . "' id='" . $value . "' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\"  onblur=\"return nhapso(event,'percent');\"  onKeyPress=\"return nhapso(event,'percent');\"/>

					</td>

				</tr>";
                break;
            }
        }
    }
    $lang = "";
    if (count($arrayObjectEditor) > 0) {
      echo "<tr><td class='boxGrey1' colspan='2'>";
      $spaw = new SpawEditor($arrayObjectEditor[0]);
      if (is_array($arrayObjectEditor))
        foreach ($arrayObjectEditor as $key => $value) {
          if ($isInsert) {
            if (strstr($value, "_en") != "")
              $lang = "en";
            else
              $lang = "vn";
            $spaw->addPage(new SpawEditorPage($value, $arrayObjectLabel[$key], $bienTemplate[$lang]));
          }
          else
            $spaw->addPage(new SpawEditorPage($value, $arrayObjectLabel[$key], $arrayValue[$value]));
      }
      echo $spaw->show();
      echo "</td></tr>";
    }
    echo "

            <tr>

                  <td class='boxGrey2' colspan='2'>

                    " . (($isInsert) ? $this->stateInsert() : (($isEdit) ? $this->stateUpdate() : '')) . "

                  </td>

            </tr>";
  }
  function generateArrayControls(& $fullColumn, & $excludeCol, & $col, & $arrayValue, $arrayid, $isInsert, $isEdit, $bienTemplate = "", $arrayConst) {
    $html = $this->getHtml();
    $js = $this->getJs();
    if ($isInsert) {
      $numInsert = $arrayid;
      $arrayid = array();
      for ($i = 0; $i < $numInsert;++$i)
        $arrayid[] = $i + 1;
    }
    else {
      $arrayid = explode(",", $arrayid);
      array_pop($arrayid);
    }
    $str = "";
    $arrayCol_Type = $fullColumn[1];
    $arrayCol_Length = $fullColumn[2];
    $array_editor = array();
    $array_key1 = array();
    $array_key2 = array();
    if (is_array($excludeCol[1]) && is_array($excludeCol[2])) {
      $array_key1 = array_keys($excludeCol[2]);
      $array_key2 = array_keys($excludeCol[1]);
    }
    if (is_array($excludeCol[0]) && is_array($fullColumn[0])) {
      foreach ($fullColumn["Comment"] as $key => $value) {
        if (!in_array($key, $excludeCol[0]) && !in_array($key, $array_key1) && !in_array($key, $array_key2)) {
          if ($col[$key] != "")
            $selectCol[$key] = $col[$key];
          else
            $selectCol[$key] = $value;
        }
      }
    }
    $i = 0;
    if (!is_array($arrayid) || count($arrayid) <= 0) {
      echo $js->displayJS("huy();");
    }
    else {
      foreach ($arrayid as $array_key => $array_value) {
        if ($isInsert)
          echo "<tr><td class='record' colspan='3'>Record #" . ($array_key + 1) . "</td></tr>";
        else
          echo "<tr><td class='record' colspan='3'>Record #" . ($array_key + 1) . ": Updated ID=<span class='saodo'>" . $array_value . "</span></td></tr>";
        $array_key1 = array();
        if (is_array($excludeCol[2]) && count($excludeCol[2]) > 0) {
          foreach ($excludeCol[2] as $key => $value) {
            $arrayValue[$array_value][$key] = (!empty($arrayConst["iscatURL"][$key]) ? $arrayConst["iscatURL"][$key] : $arrayValue[$array_value][$key]);
            echo "<tr><td class='boxGrey'>" . $col[$key] . "</td>";
            echo "<td class='boxGrey2'>";
            echo $this->generateRecursiveSelect("fields[" . $array_value . "][" . $key . "][]", $value["datatext"], $value["datavalue"], $arrayValue[$array_value][$key], $value["table"], 0, array("firstText" => "Root"));
            echo "</td></tr>";
          }
        }
        $array_key2 = array();
        if (is_array($excludeCol[1]) && count($excludeCol[1]) > 0) {
          foreach ($excludeCol[1] as $key => $value) {
            $arrayValue[$array_value][$key] = (!empty($arrayConst["iscatURL"][$key]) ? $arrayConst["iscatURL"][$key] : $arrayValue[$array_value][$key]);
            if ($value["isparentid"] == "parentid") {
              echo "<tr><td class='boxGrey'>" . $col[$key] . "</td>";
              echo "<td class='boxGrey2'>";
              echo $this->generateRecursiveSelect("fields[" . $array_value . "][" . $key . "][]", $value["datatext"], $value["datavalue"], $arrayValue[$array_value][$key], $value["table"], 0, array("firstText" => "Root"));
              echo "</td></tr>";
            }
            else {
              echo "<tr><td class='boxGrey'>" . $col[$key] . "</td>";
              echo "<td class='boxGrey2'>" . $this->generateNoRecursiveSelect("fields[" . $array_value . "][" . $key . "][]", $value["datatext"], $value["datavalue"], $arrayValue[$array_value][$key], $value["table"], "", $value["datavalue"] . " asc", array("firstText" => "Root")) . "</td></tr>";
            }
          }
        }
//end generate combobox
        $arrayObjectPicture = array();
        $arrayObjectFile = array();
        $arrayObjectEditor = array();
        if (is_array($selectCol))
          foreach ($selectCol as $value => $fName) {
            $dataType = strtolower($arrayCol_Type[$value]);
            $dataLength = $arrayCol_Length[$value];
            switch ($dataType) {
              case "int" :
                if (strstr($value, "status") != "" && $isInsert)
                  $arrayValue[$array_value][$value] = 1;
                elseif (strstr($value, "position") != "") {
                  $arrayValue[$array_value][$value] = $i + 1;
                  ++$i;
                }
                elseif ($arrayValue[$array_value][$value] == "")
                  $arrayValue[$array_value][$value] = 0;
                if ($dataLength <= 4) {
                  echo "

                  <tr>

                      <td class='boxGrey'>" . $fName . "</td>

                      <td class='boxGrey2'><input name='" . "fields[" . $array_value . "][" . $value . "][]" . "' id='extendChk" . $value . $array_key . "' type='hidden' value='" . $arrayValue[$array_value][$value] . "'>" . $html->checkbox("chk_tmp[]", "1", "Kích hoạt", array("onclick" => "if(this.checked){document.getElementById('extendChk" . $value . $array_key . "').value=1;} else{document.getElementById('extendChk" . $value . $array_key . "').value=0;}", "checked" => $arrayValue[$array_value][$value])) . "</td>

                  </tr>";
                }
                else
                  if ($dataLength == 20 && strstr(strtolower($value), "date") != "") {
                    if ($arrayValue[$array_value][$value] == 0)
                      $arrayValue[$array_value][$value] = time();
                    $arrayValue[$array_value][$value] = date("d-m-Y H:i:s", (int) $arrayValue[$array_value][$value]);
                    echo "<tr>

                      <td class='boxGrey'>" . $fName . "</td>

                      <td class='boxGrey2'>" . $html->input("fields[" . $array_value . "][" . $value . "][]", array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$array_value][$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'readonly' => 'readonly', 'id' => $value . $array_value, 'maxlength' => '30')) . "

                      </td>

                </tr>";
                    $array_date[] = $value . $array_value;
                  }
                  else {
                    echo "

                  <tr>

                      <td class='boxGrey'>" . $fName . "</td>

                      <td class='boxGrey2'>" . $html->input("fields[" . $array_value . "][" . $value . "][]", array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$array_value][$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $value . "')")) . "</td>

                  </tr>";
                }
                break;
              case "real" :
                if ($arrayValue[$array_value][$value] == "")
                  $arrayValue[$array_value][$value] = 0;
                if (strstr(strtolower($value), "percent") != "" || strstr(strtolower($value), "vat") != "")
                  $symbol = VAT;
                elseif (strstr(strtolower($value), "price") != "")
                  $symbol = CURRENCY;
                echo "

                <tr>

                    <td class='boxGrey'>" . $fName . "</td>

                    <td class='boxGrey2'>" . $html->input("fields[" . $array_value . "][" . $value . "][]", array('class' => 'nd', 'type' => 'text', 'value' => $arrayValue[$array_value][$value], 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)', 'maxlength' => '12', 'onkeypress' => "return nhapso(event,'" . $value . "')")) . " " . $symbol . "</td>

                </tr>";
                break;
              case "string" :
                if (strstr($value, "picture") != "") {
                  $arrayObjectPicture[] = $value;
                }
                elseif (strstr($value, "url") != "") {
                  if ($isInsert)
                    $arrayValue[$array_value][$value] = "#";
                  echo "<tr>

                                                  <td class='boxGrey'>" . $fName . "</td>

                                                  <td class='boxGrey2'>

                                                   <input name='fields[" . $array_value . "][" . $value . "][]' id='fields[" . $array_value . "][" . $value . "][]' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$array_value][$value] . "\" /> Ex: http://www.vnec.com

                                                  </td>

                                    </tr>";
                }
                elseif (strstr($value, "password") != "") {
                  echo "<tr>

                                  <td class='boxGrey'>" . $fName . "</td>

                                  <td class='boxGrey2'>" . $html->input("fields[" . $array_value . "][" . $value . "][]", array('class' => 'nd2', 'maxlength' => '100', 'type' => 'password', 'value' => '', 'onfocus' => 'fo(this)', 'onblur' => 'lo(this)')) . "</td>

                                  </tr>";
                }
                elseif ($dataLength > 300 && $dataLength <= 500) {
                  if (is_array($excludeCol[3]) && in_array($value, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $value) {
                        if ($isInsert) {
                          echo "<input type='hidden'   name='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' id='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" /> ";
                        }
                        else {
                          echo "<tr>

                                                    <td class='boxGrey'>" . $fName . "</td>

                                                    <td class='boxGrey2'>" . $html->textArea("fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]", array("value" => $arrayValue[$array_value][$value], "style" => "width:350px;height:40px;padding-top:4px", "disabled" => "disabled='true'", "focus" => "fo(this)")) . "<input type='checkbox' onclick=\"document.getElementById('fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]').disabled=!this.checked\"/>" . EDIT . "</td>

                                                    </tr>";
                        }
                        break;
                      }
                    }
                  }
                  else {
                    echo "<tr>

                                        <td class='boxGrey'>" . $fName . "</td>

                                        <td class='boxGrey2'>" . $html->textArea("fields[" . $array_value . "][" . $value . "][]", array("value" => $arrayValue[$array_value][$value], "style" => "width:350px;height:40px;padding-top:4px", "focus" => "fo(this)")) . "</td>

                                        </tr>";
                  }
                }
                else {
                  if (is_array($excludeCol[3]) && in_array($value, $excludeCol[3])) {
                    foreach ($excludeCol[3] as $k_rewrite => $v_rewrite) {
                      if ($v_rewrite == $value) {
                        if ($isInsert) {
                          echo "<input type='hidden'  name='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' id='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$value] . "\" /> ";
                        }
                        else {
                          echo "<input maxlength='300' name='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' id='fields[" . $array_value . "][" . $value . "][" . $k_rewrite . "][]' type='hidden' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$array_value][$value] . "\" />";
                        }
                        break;
                      }
                    }
                  }
                  else {
                    echo "<tr>

                                                  <td class='boxGrey'>" . $fName . "</td>

                                                  <td class='boxGrey2'>

                                                   <input name='fields[" . $array_value . "][" . $value . "][]' id='fields[" . $array_value . "][" . $value . "][]' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$array_value][$value] . "\" />

                                                  </td>

                                     </tr>";
                  }
                }
                break;
              case "blob" :
                $arrayObjectLabel[] = $fName;
                $arrayObjectEditor[] = $value;
                break;
              default :
                echo "<tr>

                    <td class='boxGrey'>" . $fName . "</td>

                    <td class='boxGrey2'>

                     <input name='fields[" . $array_value . "][" . $value . "][]' id='fields[" . $array_value . "][" . $value . "][]' type='text' " . $this->focusText() . " class='nd2' value=\"" . $arrayValue[$array_value][$value] . "\"  onblur=\"return nhapso(event,'percent');\"  onKeyPress=\"return nhapso(event,'percent');\"/>

                    </td>

                </tr>";
                break;
            }
        }
        if (count($arrayObjectPicture) > 0) {
          if (count($arrayObjectPicture) <= 4) {
            echo "<tr><td class='boxGrey' style='vertical-align:middle'>Hình ảnh</td>";
            echo "<td class='boxGrey2' colspan='2' style='padding-left:0px'>";
          }
          else {
            echo "<tr>";
            echo "<td class='boxGrey2' colspan='2' style='padding-left:15px'>";
          }
          if (is_array($arrayObjectPicture))
            foreach ($arrayObjectPicture as $key => $value) {
              echo "<div style='width:160px;float:left;margin-top:5px;min-height:80px'>";
              if ($arrayValue[$array_value][$value] == "")
                $arrayValue[$array_value][$value] = defaultPicture;
              echo $this->generateChoicePicture("fieldsText[" . $array_value . "][" . $value . "][]", "fields[" . $array_value . "][" . $value . "][]", $arrayValue[$array_value][$value], $array_key . $array_value);
              echo "</div>";
          }
          echo "<div id='clear' class='clear'></div><div id='clear' class='clear'></td></tr>";
        }
        $lang = "vn";
        if (count($arrayObjectEditor) > 0) {
          $array_editor = array();
          echo "<tr><td class='boxGrey2' colspan='2'>";
          $spaw = new SpawEditor($arrayObjectEditor[0] . "_1_" . $array_key);
          if (is_array($arrayObjectEditor))
            foreach ($arrayObjectEditor as $key => $value) {
              if ($isInsert) {
                if (strstr($value, "_en") != "")
                  $lang = "en";
                else
                  $lang = "vn";
                $spaw->addPage(new SpawEditorPage($value . "_1_" . $array_key, $arrayObjectLabel[$key], $bienTemplate[$lang]));
              }
              else
                $spaw->addPage(new SpawEditorPage($value . "_1_" . $array_key, $arrayObjectLabel[$key], $arrayValue[$array_value][$value]));
              $array_editor[] = $value . "_1_" . $array_key;
          }
          $array_editor = join(";", $array_editor);
          echo $spaw->show();
          echo "<input type='hidden' id='feditor[" . $array_value . "][]' name='feditor[" . $array_value . "][]' value='" . $array_editor . "'/>";
          echo "</td></tr>";
        }
      }
    }
    if (is_array($array_date)) {
      $endday = date("Y")-81;
      $startday = date("Y");
      echo "<script type='text/javascript'>";
      foreach ($array_date as $value)
        echo "

                    $(function() {
                        var endday = ".$endday.";
                        var startday = ".$startday.";
                		$('#" . $value . "').datepicker({
                			changeMonth: true,
                  			changeYear: true,
                  			dateFormat: 'dd-mm-yy',
                            yearRange: endday+':'+startday
                		});

                	});";
      echo "</script>";
    }
    echo "

    <tr>

          <td class='boxGrey2' colspan='2'>

            " . (($isInsert) ? $this->stateInsert() : (($isEdit) ? $this->stateUpdate() : '')) . "

          </td>

    </tr>";
  }
  function multiInsert($tableName, & $arrayCol, $pk, $arrayid) {
    $utl = $this->getUtl();
    $js = $this->getJs();
    $array_value = array();
    if ($arrayid != "") {
      $arrayPost = $_POST["fields"];
      $fEditor = $_POST["feditor"];
      if (is_array($fEditor))
        foreach ($fEditor as $key => $value) {
          $array_value = split(";", $value[0]);
          foreach ($array_value as $k => $v) {
            $fld = split("_1_", $v);
            $arrayContent[$key][$fld[0]] = $_POST[$v];
          }
      }
      $sumaffect = 0;
      if (is_array($arrayPost)) {
        foreach ($arrayPost as $key => $value)
// Loop post array
          {
          $affected = 0;
          $array_col = array();
          $colRewrite = array();
          $array_tt = $arrayContent[$key];
          if (is_array($value))
            foreach ($value as $k => $v)
//Column => Post value
              {
              foreach ($v as $kk => $vv) {
                if (is_numeric($kk) && $vv != null)
// get columns are not rewrite column
                  {
                  if (strstr($k, "password") != "")
                    $array_col[$k] = md5($vv);
                  else
                    $array_col[$k] = $vv;
                }
                else {
                  $colRewrite[$k . "<==>" . $kk] = $vv[0];
// get columns are rewrite column
                }
              }
          }
          if (in_array("dateupdated", $arrayCol))
            $array_col["dateupdated"] = time();
          if (in_array("datecreated", $arrayCol))
            $array_col["datecreated"] = time();
          if (is_array($array_tt))
            foreach ($array_tt as $k_t => $v_t)
              $array_col[$k_t] = $v_t;
          if (is_array($colRewrite) && count($colRewrite) > 0)
            foreach ($colRewrite as $kk => $vv) {
              $splitCol = array();
              $splitCol = explode("<==>", $kk);
              if (is_array($splitCol)) {
                $col_split = $splitCol[0];
                $col_splited = $splitCol[1];
                $text = $utl->stripUnicode($array_col[$col_splited]);
                $array_col[$col_split] = $utl->generate_url_from_text($text);
              }
          }
          $affected = $this->updateTable($tableName, $array_col, "", "insert");
          $sumaffect += $affected;
        }
        if ($sumaffect > 0) {
          $this->logInsert();
          $js->displayJS("huy();");
//return str_replace("{num}",$sumaffect,INSERTED);
        }
        else
          return ERROR_EXCEPTION;
      }
    }
    else {
      $js->displayJS("huy();");
    }
  }
  function multiUpdate($tableName, & $arrayCol, $pk, $arrayid) {
//echo $arrayid;
    $utl = $this->getUtl();
    $js = $this->getJs();
    if (!empty($arrayid)) {
      $this->logUpdate(explode(",", $arrayid), $tableName);
      $arrayPost = $_POST["fields"];
      $fEditor = $_POST["feditor"];
      if (is_array($fEditor))
        foreach ($fEditor as $key => $value) {
          $array_value = split(";", $value[0]);
          if (is_array($array_value))
            foreach ($array_value as $k => $v) {
              $fld = split("_1_", $v);
              $arrayContent[$key][$fld[0]] = $_POST[$v];
          }
      }
      $sumaffect = 0;
      if (count($arrayPost) > 0) {
        foreach ($arrayPost as $key => $value) {
          $colRewrite = array();
          $affected = 0;
          $where = $pk . "='" . $key . "'";
          $array_col = array();
          $array_tt = $arrayContent[$key];
          if (count($value) > 0)
            foreach ($value as $k => $v) {
              foreach ($v as $kk => $vv) {
                if (is_numeric($kk)) {
                  if (strstr($k, "password") != "")
                    $array_col[$k] = md5($vv);
                  else
                    $array_col[$k] = $vv;
                }
                else {
                  $colRewrite[$k . "<==>" . $kk] = $vv[0];
                }
              }
          }
          if (in_array("dateupdated", $arrayCol))
            $array_col["dateupdated"] = time();
          if (count($array_tt) > 0)
            foreach ($array_tt as $k_t => $v_t)
              $array_col[$k_t] = $v_t;
          if (count($colRewrite) > 0)
            foreach ($colRewrite as $kk => $vv) {
              $splitCol = array();
              $splitCol = explode("<==>", $kk);
              if (is_array($splitCol)) {
                $col_split = $splitCol[0];
                $col_splited = $splitCol[1];
                if (strip_tags($vv) != "")
                  $text = $vv;
                else
                  $text = $utl->stripUnicode($array_col[$col_splited]);
                $array_col[$col_split] = $utl->generate_url_from_text($text);
              }
          }
          $affected = $this->updateTable($tableName, $array_col, $where);
          $sumaffect += $affected;
        }
        if ($sumaffect > 0) {
          $js->displayJS("huy();");
        }
        else
          return ERROR_EXCEPTION;
      }
    }
    else {
      $js->displayJS("huy();");
    }
  }
/*

Generate select element with input is an array

*******************************************************************/
  function displayConfig($arrayType, $type, $idName, $arrayOption = null) {
    $str = "<select size='1' class='cbo custom-select' name='$idName' id='$idName'><option value=''>-- " . $arrayOption["firstText"] . " --</option>";
    if (is_array($arrayType))
      foreach ($arrayType as $key => $value) {
        $str .= "<option value='" . $key . "' " . (($type == $key) ? "selected" : "") . ">" . $value . "</option>";
    }
    $str .= "</select>";
    return $str;
  }
/*

Filter with select element no recursive

*******************************************************************/
  function displaySelect($arrayTarget, $selected, $idName, $arrayOption = null) {
    $str = "

				<div id='panelAction' class='panelAction2'>

                    <div style='float:left;height:17px;padding-top:5px;'></div>

					<div class='panelActionContent2' style='" . $arrayOption["style"] . "'>

    					<table id='panelTable' cellpadding='0' cellspacing='0'>

    						<tr>

    							<td class='cellAction2'><div style='float:left; padding: 7px;'>" . $arrayOption["text"] . ": </div>";
    $str .= "<select class='cbo custom-select' name='$idName' id='$idName'   onchange=\"" . $arrayOption["onchange"] . "\"><option value=''>-- " . $arrayOption["firstText"] . " --</option>";
    if (is_array($arrayTarget))
      foreach ($arrayTarget as $key => $value) {
        $str .= "<option value='" . $key . "' " . (($selected == $key) ? "selected='selected'" : "") . ">" . $value . "</option>";
    }
    $str .= "</select>";
    $str .= "</td>

    						</tr>

    					</table>

					</div><div id='clear'></div>

				</div><div id='clear'></div>";
    return $str;
  }
/*

Grid view for table with recursive no join

*******************************************************************/
  function viewRecursive($selectCol, $tablename, $page, $iscatURL, $statusAction, $urlstring = "") {
    $utl = SINGLETON_MODEL::getInstance("UTILITIES");
    $html = SINGLETON_MODEL::getInstance("HTML");
    $js = SINGLETON_MODEL::getInstance("JAVASCRIPT");
    $col = array();
    foreach ($selectCol as $key => $value) {
      if (strstr($key, "password") == "" && strstr(strtolower($key), "rewrite") == "")
        $col[$key] = $value;
    }
    $ObjectDataType = $this->getMetaData($tablename);
    $arrayDataType = $ObjectDataType[1];
    $arrayDataLength = $ObjectDataType[2];
    echo "<div id='panelView' class='panelView'>";
    echo "<table id='mainTable' cellpadding='1' cellspacing='1'>

            <tr class='titleBottom'>";
    $i = 1;
    if (is_array($col))
      foreach ($col as $key => $value) {
        if ($i == 1) {
          echo "<td class='itemCenter'><input type='checkbox' name='chkall' id='chkall' value='1' onclick='docheck(this.checked,0);'></td>";
        }
        else {
          if ($arrayDataType[$key] == "int")
            echo "<td class='itemCenter'>$value</td>";
          elseif ($arrayDataType[$key] == "real")
            echo "<td class='itemCenter'>$value</td>";
          elseif (strstr(strtolower($key), "picture") != "")
            echo "<td class='itemCenter'>$value</td>";
          else
            echo "<td class='itemText'>$value</td>";
        }
        ++$i;
    }
    echo "</tr>";
    $i = 0;
    if ($iscatURL > 0)
      $grid = $this->recursives($tablename, "parentid asc", $iscatURL, $col);
    else
      $grid = $this->recursives($tablename, "parentid asc", 0, $col);
    if (is_array($grid))
      foreach ($grid as $k => $rowview) {
        $k = array_keys($col);
        $id = $rowview[$k[0]];
        $class_css = ($i % 2 == 0) ? "cell2" : "cell1";
        echo "<tr class='$class_css'>";
        $j = 1;
        if (is_array($col))
          foreach ($col as $key => $value) {
            if ($j == 1)
              echo "<td class='itemCenter'>" . $html->checkbox("chk", "$id", "", array("onclick" => "docheckone();")) . "</td>";
            else {
              if (strstr(strtolower($key), "price") != "")
                echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($rowview[$key]) . CURRENCY . " </a></td>";
              elseif (strstr(strtolower($key), "vat") != "" || strstr(strtolower($key), "percent") != "")
                echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($rowview[$key]) . VAT . " </a></td>";
              elseif (strstr(strtolower($key), "picture") != "") {
                if (strstr(strtolower($key), ".swf") != "")
                  $img = $js->flashWrite("../" . $rowview[$key], 50, 20, "flashid", "#ffffff", "", "transparent");
                else
                  $img = "<img onerror=\"$(this).hide()\" src='image.php/image.jpg?image=" . $rowview[$key] . "&height=15&cropratio=3:1' border='0'>";
                echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $img . "</a></td>";
              }
              elseif (strstr(strtolower($key), "date") != "")
                echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . date("d-m-Y H:i:s", (int) $rowview[$key]) . "</a></td>";
              elseif ($arrayDataType[$key] == "int" && $arrayDataLength[$key] <= 4)
                echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . strtr($rowview[$key], $statusAction) . "</a></td>";
              elseif ($arrayDataType[$key] == "int" || $arrayDataType[$key] == "real")
                echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $rowview[$key] . "</a></td>";
              else
                echo "<td class='itemText'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->takeShortText($rowview[$key], 5) . ((strstr($key, "percent") != "") ? "%" : "") . "</a></td>";
            }
            ++$j;
        }
        echo "</tr>";
        ++$i;
    }
    echo "</table>";
    echo "</div>";
  }
/*

Grid view for table no recursive

*******************************************************************/
  function normalView($selectCol, $page, $mang, $statusAction, $urlstring = "", $arrayOption = null) {
    $utl = SINGLETON_MODEL::getInstance("UTILITIES");
    $html = SINGLETON_MODEL::getInstance("HTML");
    $js = SINGLETON_MODEL::getInstance("JAVASCRIPT");
    $col = array();
    foreach ($selectCol as $key => $value)
      if (strstr($key, "password") == "" && strstr(strtolower($key), "rewrite") == "")
        $col[$key] = $value;
      $ObjectDataType = $this->getMetaData($mang[2]);
    $arrayDataType = $ObjectDataType[1];
    $arrayDataLength = $ObjectDataType[2];
    echo "<div id='panelView' class='panelView'>";
    echo "<table id='mainTable' cellpadding='1' cellspacing='1'>";
    echo "<tr class='titleBottom'>";
    $i = 1;
    if (count($col) > 0)
      foreach ($col as $key => $value) {
        if ($i == 1) {
          echo "<td class='itemCenter'><input type='checkbox' name='chkall' id='chkall' value='1' onclick='docheck(this.checked,0);'></td>";
        }
        else {
          if ($arrayDataType[$key] == "int")
            echo "<td class='itemCenter'>$value</td>";
          elseif ($arrayDataType[$key] == "real")
            echo "<td class='itemCenter'>$value</td>";
          elseif (strstr(strtolower($key), "picture") != "")
            echo "<td class='itemCenter'>$value</td>";
          else
            echo "<td class='itemText'>$value</td>";
        }
        ++$i;
    }
    echo "</tr>";
    $rst = $mang[0];
    $i = 0;
    while ($row = $this->nextData($rst)) {
      $k = array_keys($col);
      $id = $row[$k[0]];
      $class_css = ($i % 2 == 0) ? "cell2" : "cell1";
      echo "<tr class='$class_css'>";
      $j = 1;
      if (count($col) > 0)
        foreach ($col as $key => $value) {
          if (is_array($arrayOption))
            foreach ($arrayOption as $k => $v)
              if ($k == $key && is_array($v))
                $row[$key] = $v[$row[$key]][0];
              if ($j == 1)
                echo "<td class='itemCenter'>" . $html->checkbox("chk", "$id", "", array("onclick" => "docheckone();")) . "</td>";
              else {
                if (strstr(strtolower($key), "price") != "")
                  echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($row[$key]) . CURRENCY . " </a></td>";
                elseif (strstr(strtolower($key), "tien") != "")
                  echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($row[$key]) . " </a></td>";
                elseif (strstr(strtolower($key), "vat") != "" || strstr(strtolower($key), "percent") != "")
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($row[$key]) . VAT . " </a></td>";
                elseif (strstr(strtolower($key), "picture") != "") {
                  if (strstr(strtolower($key), ".swf") != "")
                    $img = $js->flashWrite("../" . $row[$key], 50, 20, "flashid", "#ffffff", "", "transparent");
                  else
                    $img = "<img onerror=\"$(this).hide()\" src='image.php/image.jpg?image=" . $row[$key] . "&height=15&cropratio=3:1' border='0'>";
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $img . "</a></td>";
                }
                elseif (strstr(strtolower($key), "date") != "")
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . date("d-m-Y H:i:s", (int) $row[$key]) . "</a></td>";
                elseif ($arrayDataType[$key] == "int" && $arrayDataLength[$key] <= 4)
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . strtr($row[$key], $statusAction) . "</a></td>";
                elseif ($arrayDataType[$key] == "int" || $arrayDataType[$key] == "real")
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $row[$key] . "</a></td>";
                else
                  echo "<td class='itemText'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->takeShortText($row[$key], 10) . ((strstr($key, "percent") != "") ? "%" : "") . "</a></td>";
          }
          ++$j;
      }
      echo "</tr>";
      ++$i;
    }
    echo "</table>";
    echo "</div>";
    unset($ObjectDataType);
    unset($arrayDataType);
    unset($arrayDataLength);
    unset($arrayDataType);
    unset($row);
    unset($col);
  }
/*

Grid view for table no recursive

*******************************************************************/
  function normalView_main($arrayConst) {
    global $Meta;
//print_r($arrayConst);
    $utl = SINGLETON_MODEL::getInstance("UTILITIES");
    $html = SINGLETON_MODEL::getInstance("HTML");
    $js = SINGLETON_MODEL::getInstance("JAVASCRIPT");
    $statusAction = $arrayConst['statusAction'];
    $statusActive = $arrayConst['statusActive'];
    $urlstring = $arrayConst['urlstring'];
    echo "<div id='panelView' class='panelView'>";
    echo "<table id='mainTable' cellpadding='1' cellspacing='1'>";
    echo "<tr class='titleBottom'>";
    $i = 1;
    if (count($arrayConst['Meta']['Field']) > 0)
//Heading
      foreach ($arrayConst['Meta']['Field'] as $Field => $FieldInfo) {
        if (($FieldInfo['list_show']) && ((!$arrayConst['filter_parameter'][$Field]) || ($Field == 'table_name'))) {
          if ($FieldInfo['format_id']) {
            $FieldInfo['format_code'] = $this->getValueOfQuery('SELECT code FROM sys_format WHERE id=' . $FieldInfo['format_id']);
            $arrayConst['Meta']['Field'][$Field]['format_code'] = $FieldInfo['format_code'];
          }
          $Label = $FieldInfo['Label'];
          if ($FieldInfo['list_sortable']) {
            $list_sortable_direction_display = '';
            if ($_GET['list_sortable_field'] == $Field) {
              if ($_GET['list_sortable_direction'] == 'DESC') {
                $list_sortable_direction_display = ' ▲';
              }
              else {
                $list_sortable_direction_display = ' ▼';
              }
            }
            $Label = '<a href="javascript:void(0)" style="color:white" onclick="list_sortable(\'' . $Field . '\')">' . $Label . $list_sortable_direction_display . '</a>';
          }
          if ($i == 1) {
            echo "<td class='itemCenter'><input type='checkbox' name='chkall' id='chkall' value='1' onclick='docheck(this.checked,0);'></td>";
            if ($arrayConst['Meta']['tableInfo']["show_order"])
              echo "<td class='itemCenter'>STT</td>";
              //echo "<td class='itemCenter'>ID</td>";
          }
          else {
            if ($FieldInfo['fk_from']) {
              $Data = $this->RSTTOARRAY($this->getDynamic($FieldInfo['fk_from'], $FieldInfo['fk_where'], $FieldInfo['fk_orderby']), "id", array($FieldInfo['fk_text'] => $FieldInfo['fk_text']));
            }
            $DataSource[$Field] = $Data;
            if ($FieldInfo['Type'] == "int")
              echo "<td class='itemCenter'>$Label</td>";
            elseif ($FieldInfo['Type'] == "real")
              echo "<td class='itemCenter'>$Label</td>";
            elseif (strstr(strtolower($Field), "picture") != "")
              echo "<td class='itemCenter'>$Label</td>";
            else
              echo "<td class='itemText'>$Label</td>";
          }
          ++$i;
        }
    }
    echo "</tr>";
    $i = 0;
    while ($row = $this->nextData($arrayConst['rst'])) {
      $Plugin_Parameter['row'] = $row;
      $class_css = ($i % 2 == 0) ? "cell2" : "cell1";
      echo "<tr class='$class_css'>";
      $j = 1;
      foreach ($arrayConst['Meta']['Field'] as $Field => $FieldInfo) {
        if (($FieldInfo['list_show']) && ((!$arrayConst['filter_parameter'][$Field]) || ($Field == 'table_name'))) {
          if ($FieldInfo['plugin_function_id']) {
            $function_name = $this->getValueOfQuery("SELECT function_name FROM sys_plugin_function WHERE id= " . $FieldInfo['plugin_function_id']);
            $Plugin_Parameter['field_value'] = stripcslashes($row[$Field]);
            $Plugin_Parameter['field_info'] = stripcslashes($FieldInfo);
            $Plugin_Parameter['parameter'] = $FieldInfo['plufin_function_parameter'];
            eval('$row[$Field] = $this->' . $function_name . '($Plugin_Parameter);');
          }
          if ($FieldInfo['list_footer_subtotal'] && is_numeric($row[$Field])) {
            $arrayConst['Meta']['Field'][$Field]['subtotal'] += $row[$Field];
          }
          if ($j == 1) {
            $id = $row[$Field];
            echo "<td class='itemCenter'>" . $html->checkbox("chk", $id, "", array("onclick" => "docheckone();")) . "</td>";
            if ($arrayConst['Meta']['tableInfo']["show_order"])
              echo "<td class='itemCenter' style='width:20px; color:#3F5F7F'>" . (($arrayConst['StartRow'] + $i + 1)) . "</td>";
              //echo "<td class='itemCenter' style='width:20px; color:#3F5F7F'>" . $id . "</td>";
          }
          else {
            if ($FieldInfo['fk_from']) {
              if (count($DataSource[$Field]) > 0) {
                foreach ($DataSource[$Field] as $key => $value) {
                  if ($row[$Field] == $key) {
                    $row[$Field] = $value[0];
                  }
                }
              }
            }
            if ($FieldInfo['format_code']) {
              echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format_vlink($row[$Field], $FieldInfo['format_code']) . " </a></td>";
            }
            else
              if ($FieldInfo['plugin_function_id']) {
                echo "<td class='itemRight'>" . $row[$Field] . "</td>";
              }
              else
                if (strstr(strtolower($Field), "price") != "")
                  echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . round($row[$Field],2) . CURRENCY . " </a></td>";
                elseif (strstr(strtolower($Field), "tien") != "")
                  echo "<td class='itemRight'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->format($row[$Field]) . " </a></td>";
                elseif (strstr(strtolower($Field), "picture") != "") {
                  if (strstr(strtolower($Field), ".swf") != "")
                    $img = $js->flashWrite("../" . $row[$Field], 50, 20, "flashid", "#ffffff", "", "transparent");
                  else
                    $img = "<img onerror=\"$(this).hide()\" src='image.php/image.jpg?image=" . $row[$Field] . "&height=15&cropratio=3:1' border='0'>";
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $img . "</a></td>";
                }
                elseif (strstr(strtolower($Field), "date") != "")
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . date("d-m-Y H:i:s", (int) $row[$Field]) . "</a></td>";
                elseif ($FieldInfo['Type'] == "int" && $FieldInfo['Length'] <= 4) {
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . strtr($row[$Field], $statusActive) . "</a></td>";
                }
                elseif ($FieldInfo['Type'] == "int" || $FieldInfo['Type'] == "real")
                  echo "<td class='itemCenter'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $row[$Field] . "</a></td>";
                else
                  echo "<td class='itemText'><a id='itemText' href='$page?edit=" . $id . "&$urlstring'>" . $utl->takeShortText($row[$Field], 10) . ((strstr($Field, "percent") != "") ? "%" : "") . "</a></td>";
          }
          ++$j;
        }
      }
      echo "</tr>";
      ++$i;
    }
//Footer
    if ($arrayConst['Meta']['list_footer']) {
      echo "<tr class='titleBottom'>";
      if ($arrayConst['Meta']['tableInfo']["show_order"])
        echo "<td></td>";
      foreach ($arrayConst['Meta']['Field'] as $Field => $FieldInfo) {
        if (($FieldInfo['list_show']) && ((!$arrayConst['filter_parameter'][$Field]) || ($Field == 'table_name'))) {
          $subtotal = $arrayConst['Meta']['Field'][$Field]['subtotal'];
          echo "<td class='itemRight'>" . $utl->format_vlink($subtotal, $FieldInfo['format_code']) . "</td>";
        }
        ++$i;
      }
      //echo "<td></td>";
      echo "</tr>";
//End Footer
    }
    echo "</table>";
    echo "</div>";
  }
  function textFilter($idName, $datavaluefield, $text = "", $arrayOption) {
    $str = "<input type=\"text\" class='text' onchange=\"" . $arrayOption["onchange"] . "\" name='" . $idName . "' id='" . $idName . "' value='" . $datavaluefield . "'>";
    return "<div style='float:right;padding:5px;'>" . $text . $str . "</div>";
  }
  
  function dateFilter($idName, $datavaluefield, $text = "", $arrayOption) {
    $endday = date("Y")-81;
    $startday = date("Y");
		  $str = "<input type=\"text\" class='text' onchange=\"" . $arrayOption["onchange"] . "\" name='" . $idName . "' id='" . $idName . "' value='" . $datavaluefield . "'>";

		  $str.= "<script type=\"text/javascript\">
		  
				$(function() {
				 var endday = ".$endday.";
				 var startday = ".$startday.";
				$('#" . $idName . "').datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: 'dd-mm-yy',
					yearRange: endday+':'+startday

				});
			});
		  </script>		  
	    ";
    return "<div style='float:right;padding:5px;'>" . $text . $str . "</div>";
  }
  
  
  function selectFilter($idName, $datatextfield = null, $datavaluefield, $matchSelected, $text = "", $tablename, $arrayOption, $level = 0) {
    $col = $datatextfield;
    $ok = false;
    if (is_array($datatextfield)) {
      foreach ($datatextfield as $key => $value) {
        $datatextfield[] = $key;
      }
      foreach ($datatextfield as $key => $value) {
        if (strstr($key, "title") != "" || strstr($key, "name") != "") {
          $col = $key;
          $ok = true;
          break;
        }
      }
      if (!$ok)
        $col = $datatextfield[1];
    }
    if (is_array($datavaluefield))
      $col_v = $datavaluefield["id"];
    else
      $col_v = $datavaluefield;
    return "<div style='float:right;padding:5px;'><div style='float:left;padding: 7px;'>" . $text ."</div>". ($this->generateRecursiveSelect($idName, $col, $col_v, $matchSelected, $tablename, $level, $arrayOption, $iscat)) . "</div>";
    return $str;
  }
  function panelFilter($strcbo) {
    $str = "<div id='panelAction' class='panelAction2'>";
    $str .= $strcbo;
    $str .= "</div>";
    $str .= "<div id='clear'></div>";
    return $str;
  }
  function showError($msg) {
    if (!empty($msg))
      return "<div id='panelAction' class='panelAction2'><span style='float:left;color:#ad1c1c;padding:4px 5px'>" . $msg . "</span></div><div class='clear'></div>";
    return "";
  }
/*

*******************************************************************/
  function selectFilter2($idName, $datatextfield, $datavaluefield, $matchSelected, $text = "", $tablename, $arrayOption, $level = 0) {
    $col = $datatextfield;
    $ok = false;
    if (is_array($datatextfield)) {
      foreach ($datatextfield as $key => $value) {
        $datatextfield[] = $key;
      }
      foreach ($datatextfield as $key => $value) {
        if (strstr($key, "title") != "" || strstr($key, "name") != "") {
          $col = $key;
          $ok = true;
          break;
        }
      }
      if (!$ok)
        $col = $datatextfield[1];
    }
    if (is_array($datavaluefield))
      $col_v = $datavaluefield["id"];
    else
      $col_v = $datavaluefield;
    return "<div style='float:right;padding:1px;'><div style='float:left;padding: 7px;'>" . $text ."</div>". ($this->generateRecursiveSelect2($idName, $col, $col_v, $matchSelected, $tablename, $level, $arrayOption, $iscat)) . "</div>";
    return $str;
  }
  function selectFilterNoRecursive($idName, $datatextfield, $datavaluefield, $matchSelected, $text = "", $tablename, $where, $orderby, $arrayOption) {
    $col = $datatextfield;
    $ok = false;
    if (is_array($datatextfield)) {
      foreach ($datatextfield as $key => $value) {
        $datatextfield[] = $key;
      }
      foreach ($datatextfield as $key => $value) {
        if (strstr($key, "title") != "" || strstr($key, "name") != "" || strstr($key, "hovaten") != "") {
          $col = $key;
          $ok = true;
          break;
        }
      }
      if (!$ok)
        $col = $datatextfield[1];
    }
    if (is_array($datavaluefield))
      $col_v = $datavaluefield["id"];
    else
      $col_v = $datavaluefield;
    return "<div style='float:right;padding:5px;'><div style='float:left;padding: 7px;'>" . $text ."</div>". ($this->generateNoRecursiveSelect($idName, $col, $col_v, $matchSelected, $tablename, $where, $orderby, $arrayOption)) . "</div>";
  }
/*

*******************************************************************/
  function generateNoRecursiveSelect($idName, $datatextfield, $datavaluefield, $matchSelected, $tablename, $where = "", $orderby = "", $arrayOption = null) {
    $str = "<select size='1' class='cbo custom-select' onchange=\"" . $arrayOption["onchange"] . "\" name='" . $idName . "' id='" . $idName . "'>";
    if ($arrayOption["firstText"] != "")
      $str .= "<option value=''>-- " . $arrayOption["firstText"] . " --</option>";
    $col[$datatextfield] = $datatextfield;
    $col[$datavaluefield] = $datavaluefield;
    $list = $this->getDynamic($tablename, $where, $orderby);
    while ($rs = $this->nextData($list)) {
      if ($tablename == "member") {
        $ma_id = $rs['ma_id'] . " - ";
      }
      $str .= "<option value='" . $rs[$datavaluefield] . "' " . (($rs[$datavaluefield] == $matchSelected) ? "  selected='selected'" : " ") . '>&nbsp;&nbsp;&nbsp;&raquo;&nbsp;' . $ma_id . " " . $rs[$datatextfield] . "</option>";
    }
    $str .= "</select>";
    return $str;
  }
/*

*******************************************************************/
  function generateRecursiveSelect($idName, $datatextfield, $datavaluefield, $matchSelected, $tablename, $level = "0", $arrayOption = null) {
    global $Meta;
    $str = "<select size='1' class='cbo custom-select' onchange=\"" . $arrayOption["onchange"] . "\" name='" . $idName . "' id='" . $idName . "'>";
    if ($arrayOption["firstText"] != "")
      $str .= "<option value=''>-- " . $arrayOption["firstText"] . " --</option>";
    else
      $str .= "<option value=''>-- Chọn danh mục cha --</option>";
    $col[$datatextfield] = $datatextfield;
    $col[$datavaluefield] = $datavaluefield;
    $list = $this->recursive($tablename, "parentid asc", $level, $col, "", null, $idName);
    if ($Meta["Field"][$idName]["fk_count_sub_total"]) {
      if ($list) {
        foreach ($list as $key => $value) {
          $list1 = $list;
          foreach ($list1 as $key1 => $value1) {
//echo $value1['parentid']."\n";
            if ($this->checkChildNode($list1, $value1, $value)) {
              $value['total_sup'] += $value1['total_sup'];
            }
          }
          $value['total_sup'] = '(' . $value['total_sup'] . ')';
          $list[$key] = $value;
        }
      }
    }
    if (is_array($list))
      foreach ($list as $k => $rs) {
        $str .= "<option value='" . $rs[$datavaluefield] . "' " . (($rs[$datavaluefield] == $matchSelected) ? "  selected='selected'" : " ") . '>' . $rs[$datatextfield] . " " . $rs["total_sup"] . "</option>";
    }
    $str .= "</select>";
    return $str;
  }
  function checkChildNode($list, $child, $node) {
    if ($child['id'] == $node['id'])
      return 0;
    if ($list) {
      foreach ($list as $key => $value) {
        $list1_node[$value['id']] = $value;
      }
    }
    while (($child['id'] != $node['id']) && ($child['parentid'])) {
      $child = $list1_node[$child['parentid']];
    }
    if (($child['id'] == $node['id']))
      return 1;
    return 0;
  }
/*

*******************************************************************/
  function generateRecursiveSelect2($idName, $datatextfield, $datavaluefield, $matchSelected, $tablename, $level = "0", $arrayOption = null) {
    $str = "<select size='1' class='cbo custom-select' onchange=\"" . $arrayOption["onchange"] . "\" name='" . $idName . "' id='" . $idName . "'  >";
    if ($arrayOption["firstText"] != "")
      $str .= "<option value=''>-- " . $arrayOption["firstText"] . " --</option>";
    else
      $str .= "<option value=''>-- Chọn danh mục cha --</option>";
    $col[$datatextfield] = $datatextfield;
    $col[$datavaluefield] = $datavaluefield;
    $list = $this->recursive2($tablename, "parentid asc", $level, $col);
    if (is_array($list))
      foreach ($list as $k => $rs)
        $str .= "<option value='" . $rs[$datavaluefield] . "' " . (($rs[$datavaluefield] == $matchSelected) ? "  selected='selected'" : " ") . '>' . $rs[$datatextfield] . "</option>";
    $str .= "</select>";
    return $str;
  }
/*

Generate dynamic choose picture...

*******************************************************************/
  function generateChoicePicture($idPicture, $idHidden, $picture = "", $extra = "") {
    if ($extra != "")
      $danhchoid = $extra;
    else
      $danhchoid = $idHidden;
    $str = "";
    $str .= "<table border='0'  cellpadding='0' cellspacing='0'>

						  <tr>

							<td width='20%'>";
    $mangExt = pathinfo($picture);
    $ext = strtolower($mangExt['extension']);
    $str .= "<div id='pro_photo_swf'>";
    if ($ext == "swf")
      $str .= "<img style='border:1px solid #333' name='" . $idPicture . "' id='" . $idPicture . "' src='image.php/image.jpg?image=" . defaultPicture . "&width=50&cropratio=1:1'  border='0'>";
    else
      $str .= "<img style='border:1px solid #333' name='" . $idPicture . "' id='" . $idPicture . "' src='image.php/image.jpg?image=" . $picture . "&width=50&cropratio=1:1' border='0' />";
    $str .= "</div>";
    $str .= "

				</td>

				<td  valign='bottom' style='padding-left:5px;'>

					<input name='btn1' type='button' onClick=\"modelessDialogShow('" . Editor . "ffilter=image&object1=$danhchoid&object2=$idPicture&type=1','auto','auto');\" id='btn1' value='Browse...' />

				</td>

				</tr>

				<tr>

				  <td colspan='2' style='padding-top:5px;'>

				  <input style='margin-left:0px' id='chk_path$danhchoid' name='chk_path$idHidden' type='checkbox' onClick=\"showPath(this.checked,'" . $danhchoid . "');\" value='1' />Current path<br>

				  <input onblur=\"closePath(this,'chk_path" . $danhchoid . "');\" name='" . $idHidden . "' id='" . $danhchoid . "' style='width:200px;display:none' type='text' " . $this->focusText() . " class='nd2' value='" . $picture . "' />



				  </td>

				</tr>

				</table>";
    return $str;
  }
  function generateChoiceFile($idFile, $pathFile = "", $arrayOptions = null, $extra = "") {
    if ($extra != "")
      $danhchoid = $extra;
    else
      $danhchoid = $idFile;
    echo "<table border='0' cellpadding='0' cellspacing='0'>

						  <tr>

							<td width='20%'>

                <input onfocus='this.select()' name='$idFile' value='$pathFile' type='text' class='" . $arrayOptions["class"] . "' id='$danhchoid' />

				</td>

				<td  valign='bottom' style='padding-left:5px;'>

					<input name='btn1' class='btncenter' type='button' onClick=\"modelessDialogShow('" . Editor . "ffilter=all&object1=$danhchoid&type=2','auto','auto');\" id='btn1' value='Browse...' />

				</td>

				</tr>

				</table>";
  }
/*

Show menu admin

*******************************************************************/
  function showIndex() {
    $menu = $this->getArray("sys_menu_group", "status=1", "position asc");
    if (count($menu) > 0)
      foreach ($menu as $value) {
        echo "<div id='box'>

                                          <div id='boxTop' style='background:url(.." . $value['picture'] . ") 15px 2px no-repeat;'>

                                          <div id='boxTopInside'><a href=\"#\" class='' title=\"" . stripslashes($value["title"]) . "\">" . stripslashes($value["title"]) . "</a></div></div>";
        echo "<div id='boxMiddle'><div>";
        $submenu = $this->getArray('sys_table', "menu=1 AND menu_group_id='" . $value["id"] . "'", 'position asc');
        if (count($submenu) > 0)
          foreach ($submenu as $v) {
            $id = $v['id'];
            if ($_SESSION['permission'][$id]['is_list']) {
              echo '<div id="item"><a id="item" href="' . (($v['custom_link']) ? $v['custom_link'] : 'mngMain.php') . '?table_name=' . stripslashes($v["table_name"]) . '">' . $v['title'] . '</a></div>';
            }
        }
        echo "</div><div id='clear'></div></div>";
        echo "<div id='boxBottom'></div><div id='clear'></div></div>";
    }
    echo "<div id='clear'></div>";
  }
/*

Include buttons: insert, view, delete, selectall

*******************************************************************/
  function panelAction($mang, $arrayOption_1 = null, $arrayOption_2 = null, $arrayStatus = null, $table = "") {
    global $function_permission;
    if (!empty($table))
      $table = base64_encode($table . "-&&-" . $_SESSION["user"] . date("dmYH"));
    $str = "

				<div id='panelAction' class='panelAction'>

                    <div style='float:left;" . $arrayOption_2["style"] . ";'>" . $mang . "</div>

					<div style='float:right" . $arrayOption_1["style"] . "'>

					<table id='panelTable' cellpadding='1' cellspacing='1'>

					<tr>";
    for ($i = 0; $i < 10;++$i)
      $arrayButton[$i] = true;
    if (count($arrayStatus) > 0) {
      foreach ($arrayStatus as $key => $value) {
        $arrayButton[$key] = $value;
      }
    }
    if ($_SESSION["user_login"]["is_view_tabledesign"]) {
      $arrayButton[0] = true;
    }
    else {
      $arrayButton[0] = false;
    }
    if ($arrayButton[0] != false) {
      if($_SESSION["user_login"]["fullname"]=="Mr. Dan" && $_SESSION["user_login"]["role_id"]==11)
      {		
		  $selectIMG = "<a title='' href='#'><img  src='" . pathTheme . "/images/check.jpg' border='0' title='Thêm' /></a>";
		  $selectText = "<a title='Desigin' id='lnkaction' href='?table_name=sys_field&table_id=" . $this->getValueOfQuery('SELECT id FROM sys_table WHERE table_name = "' . $_GET['table_name'] . '"') . "'>Desigin</a>";
		  $str .= "<td class='cellAction1'>" . $selectIMG . "</td><td >" . $selectText . "</td>";
	  }
    }
    if ($arrayButton[1] != false) {
      if ($function_permission['is_insert']) {
        if ($_GET["table_name"] == 'article') {
          if (isset($_GET["article_category_id"]) && $_GET["article_category_id"] != '')
            $str .= "<td class='cellAction1'>" . insertIMG . "</td><td >" . insertText . "</td>";
          else {
            $str .= "<td class='cellAction1'>" . insertIMG . "</td><td >" . insertText2 . "</td>";
          }
        }
        else {
          $str .= "<td class='cellAction1'>" . insertIMG . "</td><td >" . insertText . "</td>";
        }
      }
    }
    if ($arrayButton[2] != false) {
      $str .= "<td class='cellAction1'>" . insertmultiIMG . "</td><td >" . insertmultiText . "</td>";
    }
    if ($arrayButton[3] != false) {
      $str .= "<td class='cellAction1'>" . editIMG . "</td><td >" . editText . "</td>";
    }
    if ($arrayButton[4] != false) {
      if ($function_permission['is_delete'])
        $str .= "<td class='cellAction1'>" . deleteIMG . "</td><td >" . deleteText . "</td>";
    }
    if ($arrayButton[5] != false) {
      $str .= "<td class='cellAction1'>" . duplicateIMG . "</td><td>" . duplicateText . "</td>";
    }
    if ($arrayButton[6] != false) {
      $str .= "<td class='cellAction1'>" . viewIMG . "</td><td >" . viewText . "</td>";
    }
    if ($arrayButton[7] != false) {
      $str .= "<td class='cellAction1'>" . backupIMG . "</td><td >" . bakupText . "</td>";
    }
    if ($arrayButton[8] != false) {
      $str .= "<td class='cellAction1'>" . restoreIMG . "</td><td >" . restoreText . "</td>";
    }
    if ($arrayButton[9] != false) {
      $str .= "<td class='cellAction1'>" . str_replace("{param}", $table, truncateIMG) . "</td><td >" . str_replace("{param}", $table, truncateText) . "</td>";
    }
    $str .= "</tr>

					</table>

					</div><div id='clear'></div>

				</div><div id='clear'></div>";
    return $str;
  }
/*

Only buttons view, delete no form

*******************************************************************/
  function panelActionView($mang, $arrayOption = null, $arrayOption2 = null) {
    $str = "

				<div id='panelAction' class='panelAction'>

                    <div style='width:475px;float:left;" . $arrayOption2["style"] . ";height:17px;padding-top:5px;'>" . $mang . "</div>

					<div class='panelActionContent' style='" . $arrayOption["style"] . "'>

					<table id='panelTable' cellpadding='0' cellspacing='0' width='200' align='right'>

						<tr>

							<td class='cellAction1'>" . selectIMG . "</td><td class='cellAction'>" . selectText . "</td>

							<td class='cellAction1'>" . deleteIMG . "</td><td class='cellAction'>" . deleteText . "</td><td class='cellAction1'>" . viewIMG . "</td><td class='cellAction11'>" . viewText . "</td>

						</tr>

					</table>

					</div>

				</div>";
    return $str;
  }
/*

Generate dynamic header of admin

*******************************************************************/
  function showAdminHome($page) {
    $ChangePermissionCombo = $this->generateChangePermissionCombo();
    if (!$ChangePermissionCombo) {
      $ChangePermissionCombo = "[" . $_SESSION['user_login']['role_name'] . "]";
    }
    echo "<table width='100%' align='center'  border='0'  cellpadding='0'  cellspacing='0'>

        			  	<tr><td id='topcenter'>

        					<div id='topleft'></div>
        					<div id='topright'></div>

        				</td></tr>

                        <tr>

                            <td  valign='top' class='header'>

                            <table  width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>

                              <tr>

                          	  <td width='100%' class='titleAdmin'><div id='div_infoacount_admin'>Welcome [ " . $_SESSION['user_login']['fullname'] . ",<a id='welcome' href='#' onclick=\"window.location='logout.php';\">Logout</a> ] - Level [ " . $ChangePermissionCombo . " ]&nbsp;-&nbsp;<a id='welcome' href='./'>Home</a></div>
							  
								<button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\".navbar-collapse.collapse\" data-label-expanded=\"Close\" aria-expanded=\"false\">
								<span class=\"navbar-toggle-label\">Menu</span>
								<span class=\"sr-only\">(toggle)</span>

								<span class=\"navbar-toggle-icon\">
								  <span class=\"icon-bar\"></span>
								  <span class=\"icon-bar\"></span>
								  <span class=\"icon-bar\"></span>
								</span>
							  </button>
							  
							  </td>

                              </tr>

                            </table>
							
							

                           </td>

                        </tr>";
//echo "<div style='padding-left:10px;padding-bottom:5px;text-align:right !important'>Chào mừng[<b> ".$_SESSION['user_login']['fullname']." </b>] - Quyền truy cập  ".$ChangePermissionCombo." &nbsp;-&nbsp;<a id='welcome' href='#' onclick=\"window.location='logout.php';\"><b>Thoát hệ thống</b></a></div>";
    echo "<tr><td colspan='3' class='menu'>";
    echo $this->showMenu($page);
    echo "</td></tr>";
    echo "</table>";
//echo "</table>";
/*

if($_SESSION["user_login"]["is_show_menu_left"])

{

echo "<div class='menuleft'>";

echo $this->showMenu_left($page);

echo"</div>";

}

else{

$style="style='width:100%';";

}



*/
    echo "<div>";
  }
/*

Generate dynamic menu

*******************************************************************/
  function showMenu($page) {
    $menu = $this->getArray('sys_menu_group', 'status=1', 'position asc');
    echo '<div class="navbar-collapse collapse"><div id="myslidemenu" class="jqueryslidemenu">';
    if (count($menu) > 0) {
      echo '<ul>';
      foreach ($menu as $value) {
        echo '<li><span style="float:left; padding:8px 30px; border-right:1px solid #ccc; cursor: pointer;">' . $value["title"] . '</span>';
        $submenu = $this->getArray('sys_table', "menu=1 AND menu_group_id='" . $value["id"] . "'", 'position asc');
        if (count($submenu) > 0) {
          echo '<ul class="sub">';
          foreach ($submenu as $v) {
            $id = $v['id'];
            if ($_SESSION["user_login"]["role_id"] == 11) {
              $_SESSION['permission'][$id]['is_list'] = 1;
              $_SESSION['permission'][$id]['is_edit'] = 1;
              $_SESSION['permission'][$id]['is_insert'] = 1;
              $_SESSION['permission'][$id]['is_delete'] = 1;
            }
            if ($_SESSION['permission'][$id]['is_list']) {
              echo '<li><a class="sub" href="' . (($v['custom_link']) ? $v['custom_link'] : 'mngMain.php') . '?table_name=' . stripslashes($v["table_name"]) . '">' . $v["title"] . '</a></li><br/>';
            }
//echo '<li><a class="sub" href="mngMain.php?table_name='.stripslashes($v["table_name"]).'">'.$v["table_name"].'</a></li>';
          }
          echo '</ul>';
        }
        echo '</li>';
      }
      echo '</ul>';
    }
    echo '</div></div>';
  }
/*

Inform Denied

*******************************************************************/
/*

Generate dynamic menu

*******************************************************************/
  function showMenu_left($page) {
    $menu = $this->getArray('sys_menu_group', 'status=1', 'position asc');
    $next1 = "<b>&nbsp;&rsaquo;&nbsp;</b>";
    echo '<div class="m_left_top">Danh mục</div>

                            <div class="m_left_center">';
    if (count($menu) > 0) {
      foreach ($menu as $value) {
        $submenu = $this->getArray('sys_table', "menu=1 AND menu_group_id='" . $value["id"] . "'", 'position asc');
        $groupmenu_html = '';
        if (count($submenu) > 0) {
          $submenu_html = '';
          foreach ($submenu as $v) {
            $id = $v['id'];
            if ($_SESSION["user_login"]["role_id"] == 11) {
              $_SESSION['permission'][$id]['is_list'] = 1;
              $_SESSION['permission'][$id]['is_edit'] = 1;
              $_SESSION['permission'][$id]['is_insert'] = 1;
              $_SESSION['permission'][$id]['is_delete'] = 1;
            }
            if ($_SESSION['permission'][$id]['is_list']) {
              $submenu_html .= '<div class="item_menu"><a class="sub" href="' . (($v['custom_link']) ? $v['custom_link'] : 'mngMain.php') . '?table_name=' . stripslashes($v["table_name"]) . '">' . $next1 . '' . $v["title"] . '</a></div>';
              $submenu_html .= '<div style="clear:both"></div>';
            }
          }
          if ($submenu_html) {
            $groupmenu_html .= '<div class="menu_right" id="menu_right">';
            $groupmenu_html .= $submenu_html;
            $groupmenu_html .= "</div>";
            $groupmenu_html .= '<div style="clear:both"></div>';
            $groupmenu_html .= '<div class="hr"></div>';
          }
        }
        if ($groupmenu_html) {
          echo '<div class="title_r">' . $value["title"] . '</div>';
          echo '<div class="clear"></div>';
          echo $groupmenu_html;
        }
      }
    }
    echo '</div>';
  }
/*

Inform Denied

*******************************************************************/
  function notice($user, $action, $page) {
    $str = "<p style='line-height:140%;text-align:center;font-family:arial;font-size:12px'>Hi, $user";
    $str .= "<p style='line-height:150%;text-align:center;font-family:arial;font-size:12px'>Bạn không có quyền <span style=\"color:#ff0000;font-weight:bold\">" . $action . "</span> khi vào trang <strong>$page</strong>.<br>

            Vui lòng liên hệ với administrator để được ủy quyền, chọn ở đây để ";
    $str .= "<a href=\"javascript:void(0);\" onclick=\"window.location='index.php'\">quay lại</a></p></p>";
    return $str;
  }
/*

Setting Permission

*******************************************************************/
  function setPermission($user, $permission, $page = "index.php", $arrayAction) {
    $checkPermission = "select distinct up.*,th.pageurl from " . prefixTable . "user_page up inner join " . prefixTable . "menuadmin th on up.pageid= th.id where up.username='" . $user . "' and th.pageurl='" . $page . "'";
    $rstcheckPermission = $this->doSQL($checkPermission);
    if ($this->totalRows($rstcheckPermission) > 0) {
      $rowcheckPermission = $this->nextObject($rstcheckPermission);
      $selected = $rowcheckPermission->selects;
      if ($selected == 0) {
        echo $this->notice($user, $arrayAction["select"], $page);
        exit;
      }
      $deleted = $rowcheckPermission->deletes;
      if (isset($isDelete) && $deleted == 0) {
        echo $this->notice($user, $arrayAction["delete"], $page);
        exit;
      }
      $updated = $rowcheckPermission->updates;
      if ($isEdit && $updated == 0) {
        echo $this->notice($user, $arrayAction["update"], $page);
        exit;
      }
      $inserted = $rowcheckPermission->inserts;
      if ($isInsert && $inserted == 0) {
        echo $this->notice($user, $arrayAction["insert"], $page);
        exit;
      }
    }
  }
/*

*******************************************************************/
  function returnTitleMenuTable($str) {
    $str = "<table align='center' width='100%' cellpadding='0' cellspacing='0'><tr><td colspan='5' class='boxRedInside'><div class='boxRedInside'>" . $str . "</div></tr></table>";
    return $str;
  }
/*

*******************************************************************/
  function returnTitleMenu($str) {
    $str = "<tr><td colspan='5' class='boxRed'><div class='boxRed'>" . $str . "</div></td></tr>";
    return $str;
  }
/*

*******************************************************************/
  function focusText() {
    $str = " onFocus='fo(this);' onBlur='lo(this);' ";
    return $str;
  }
/*

Button insert, cancel

*******************************************************************/
  function stateInsert() {
    global $function_permission;
    if ($function_permission['is_insert']) {
      $str = '<input name="subinsert" id="subinsert" type="submit" value="' . INSERT . '"/><input name="reset" type="reset" value="' . RESET . '"/>';
    }
    $str .= '<input name="btnhuy" id="btnhuy"  type="button" onClick="javascript:huy();"  value="' . HUY . '"/>';
    return $str;
  }
/*

Button update, cancel

*******************************************************************/
  function stateUpdate() {
    global $function_permission;
    if ($function_permission['is_edit']) {
      $str = '<input name="subupdate" id="subupdate" type="submit" value="' . UPDATE . '"/>';
    }
    $str .= '<input name="btnhuy" id="btnhuy"  type="button" onClick="javascript:huy();"  value="' . HUY . '"/>';
    return $str;
  }
/*get metadata information

-----------------------------------------------------------------*/
  function recursive1($tablename, $orderby, $parentid = 0, $col = null, $space = '', $trees = null, $categorid) {
    global $Meta;
    if (!$space)
      $space = 0;
    if (!$trees)
      $trees = array();
    $rst = $this->getDynamic($tablename, "parentid=$parentid", "parentid asc, " . $orderby);
    while ($rs = $this->nextData($rst)) {
      if (is_array($col))
        foreach ($col as $key => $value) {
          if (strstr($key, "div_id") != "" || strstr($key, "title") != "" || strstr($key, "name") != "") {
            $indent = '';
            if ($space) {
              for ($i = 0; $i < $space; $i++) {
                $indent .= '&nbsp;';
              }
              ;
              $indent .= '=>&nbsp;';
            }
            $tmp[$key] = $indent . stripslashes($rs[$key]);
          }
          else {
            $tmp[$key] = stripslashes($rs[$key]);
          }
          if ($Meta["Field"][$categorid]["fk_count_sub_total"]) {
            $total_sup = $this->totalRows($this->getDynamic($_GET["table_name"], "" . $categorid . "='" . $rs[$key] . "'", ""));
            $tmp['total_sup'] = $total_sup;
            $tmp['parentid'] = $rs["parentid"];
          }
      }
      $trees[] = $tmp;
      $trees = $this->recursive($tablename, $orderby, $rs['id'], $col, $space + 5, $trees, $categorid);
    }
    return $trees;
  }
  function recursive($tablename, $orderby, $parentid = 0, $col = null, $space = '', $trees = null, $categorid) {
    global $Meta;
    if (!$space)
      $space = 0;
    if (!$trees)
      $trees = array();
    $rst = $this->getDynamic($tablename, "parentid=$parentid", "parentid asc, " . $orderby);
    while ($rs = $this->nextData($rst)) {
      if ($tablename == "member") {
        $ma_id = $rs['ma_id'] . " - ";
      }
      if (is_array($col))
        foreach ($col as $key => $value) {
          if (strstr($key, "div_id") != "" || strstr($key, "title") != "" || strstr($key, "hovaten") != "" || strstr($key, "name") != "") {
            $indent = '';
            if ($space) {
              for ($i = 0; $i < $space; $i++) {
                $indent .= '&nbsp;';
              }
              ;
              $indent .= '=>&nbsp;';
            }
            $tmp[$key] = $indent . $ma_id . stripslashes($rs[$key]);
          }
          else {
            $tmp[$key] = stripslashes($rs[$key]);
          }
      }
      $trees[] = $tmp;
      $trees = $this->recursive($tablename, $orderby, $rs['id'], $col, $space + 5, $trees, $categorid);
    }
    return $trees;
  }
/*get metadata information

-----------------------------------------------------------------*/
  function recursives($tablename, $orderby, $parentid = 0, $col = null, $space = '', $trees = null) {
    if (!$trees)
      $trees = array();
    $rst = $this->getDynamic($tablename, "parentid=$parentid", "parentid asc, " . $orderby);
    while ($rs = $this->nextData($rst)) {
      if (is_array($col)) {
        foreach ($col as $key => $value) {
          if (strstr($key, "title") != "" || strstr($key, "name") != "" || strstr($key, "hovaten") != "")
            $tmp[$key] = $space . stripslashes($rs[$key]);
          else
            $tmp[$key] = stripslashes($rs[$key]);
        }
      }
      $trees[] = $tmp;
      if ($rs['parentid'] != 0)
        $trees = $this->recursive($tablename, $orderby, $rs['id'], $col, $space . '&nbsp;&nbsp;=>&nbsp;', $trees);
    }
    return $trees;
  }
/*

Recursive 2

*******************************************************************/
  function recursive2($tablename, $orderby, $parentid = 0, $col = null, $space = '', $trees = null) {
    if (!$trees)
      $trees = array();
    $rst = $this->getDynamic($tablename, "parentid=$parentid and iscat=1", $orderby);
    while ($rs = $this->nextData($rst)) {
      if (is_array($col)) {
        foreach ($col as $key => $value) {
          if (strstr($key, "title") != "" || strstr($key, "name") != "")
            $tmp[$key] = $space . stripslashes($rs[$key]);
          else
            $tmp[$key] = stripslashes($rs[$key]);
        }
      }
      $trees[] = $tmp;
      $trees = $this->recursive2($tablename, $orderby, $rs['id'], $col, $space . '&nbsp;&nbsp;=>&nbsp;', $trees);
    }
    return $trees;
  }
  function huy($url) {
    $js = $this->getJs();
    $contentScript = "function huy()\n";
    $contentScript .= "{\n";
    $contentScript .= "window.location='" . $url . "';";
    $contentScript .= "\n}\n";
    if (!$_GET['debug'])
      $js->displayJS($contentScript);
  }
/*

Write log

*******************************************************************/
  function writeLog($arrayLog = null) {
    if (count($arrayLog) > 0)
      return $this->insertTable(prefixTable . "viewlog", $arrayLog);
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
    $arr[2] = $tablename;
    return $arr;
  }
// paging_select
  function paging_cursor($select, $from, $where, $orderby, $url, $PageNo, $PageSize, $Pagenumber, $ModePaging) {
    if ($_REQUEST['PageSize']) {
      $PageSize = $_REQUEST['PageSize'];
    }
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
    $TRecord = $this->getArray($from, $where, $orderby, "stdObject");
    $RecordCount = count($TRecord);
    $result = $this->getDynamic_cursor($select, $from, $where, $orderby . " LIMIT " . $StartRow . "," . $PageSize);
    $result_tmp = $this->getDynamic_cursor($select, $from, $where, $orderby . " LIMIT " . $StartRow . "," . $PageSize);
    $result_array = array();
    while ($row = mysqli_fetch_array($result_tmp)) {
      $result_array[] = $row;
    }
    if ($RecordCount % $PageSize == 0)
      $MaxPage = $RecordCount / $PageSize;
    else
      $MaxPage = ceil($RecordCount / $PageSize);
    $gotopage = "";
    if (!$url)
      $delimiter = '?';
    else
      $delimiter = '&';
    $url = $_SERVER['PHP_SELF'] . $url . $delimiter;
    switch ($ModePaging) {
      case "Full" :
        $gotopage = '<div class="paging_meneame">';
        if ($MaxPage > 1) {
          if ($PageNo != 1) {
            $PrevStart = $PageNo - 1;
            $gotopage .= ' <a href="' . $url . 'PageNo=1" tile="First page"> &laquo; </a>';
            $gotopage .= ' <a href="' . $url . 'PageNo=' . $PrevStart . '" title="Previous page"> &lsaquo; </a>';
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
                $gotopage .= ' <a href="' . $url . 'PageNo=' . $c . '" title="Page ' . $c . '"> ' . $c . ' </a>';
          }
          if ($PageNo < $MaxPage) {
            $NextPage = $PageNo + 1;
            $gotopage .= ' <a href="' . $url . 'PageNo=' . $NextPage . '" title="Next page"> &rsaquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &rsaquo; </span>';
          }
          if ($PageNo < $MaxPage)
            $gotopage .= ' <a href="' . $url . 'PageNo=' . $MaxPage . '" title="Last page"> &raquo; </a>';
          else
            $gotopage .= ' <span class="paging_disabled"> &raquo; </span>';
        }
        $gotopage .= ' </div>';
        break;
    }
    $arr[0] = $result;
    $arr[1] = $gotopage;
    $arr[2] = $from;
    $arr[3] = $RecordCount;
    $arr[4] = $result_array;
    if ($_REQUEST['debug'] == 1) {
      echo '<div style="text-align:left"><pre>';
      print_r($arr);
      echo '</pre></div>';
    }
    return $arr;
  }
  function make_query($arr) {
    if (!$arr) {
      return "";
    }
    foreach ($arr as $key => $value) {
      $arr[$key] = $key . '=' . $value;
    }
    ;
    return implode('&', $arr);
  }
// paging_select
  function paging_cursor_vlink($select, $from, $where, $orderby, $url, $PageNo, $PageSize, $Pagenumber, $ModePaging) {
    $loop = 1;
    while (!$result_array && $loop <= 2) {
      if ($_GET['PageSize']) {
        $PageSize = $_GET['PageSize'];
      }
      if ($PageNo == "") {
        $StartRow = 0;
        $PageNo = 1;
      }
      else
        $StartRow = ($PageNo - 1) * $PageSize;
      if ($PageNo % $Pagenumber == 0)
        $CounterStart = $PageNo - ($Pagenumber - 1);
      else
        $CounterStart = $PageNo - ($PageNo % $Pagenumber) + 1;
      $CounterEnd = $CounterStart + $Pagenumber;
      $TRecord = $this->getArray($from, $where, $orderby, "stdObject");
      $RecordCount = count($TRecord);	 
	
      $result = $this->getDynamic_cursor($select, $from, $where, $orderby . " LIMIT " . $StartRow . "," . $PageSize);
      $result_tmp = $this->getDynamic_cursor($select, $from, $where, $orderby . " LIMIT " . $StartRow . "," . $PageSize);
      $result_array = array();
      $stt = 0;
      while ($row = mysqli_fetch_array($result_tmp)) {
        $result_array[] = $row;
        $row['list_order'] = $StartRow + $stt;
        $stt++;
      }
      if (!$result_array) {
        $PageNo = 1;
        $loop += 1;
      }
    }
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
            $_GET['PageNo'] = 1;
            $gotopage .= ' <a href="?' . $this->make_query($_GET) . '" tile="First page"> &laquo; </a>';
            $gotopage .= ' <a href="?' . $url . 'PageNo=' . $PrevStart . '" title="Previous page"> &lsaquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &laquo; </span>';
            $gotopage .= ' <span class="paging_disabled"> &lsaquo; </span>';
          }
          $c = 0;
          for ($c = $CounterStart; $c < $CounterEnd;++$c) {
            if ($c <= $MaxPage)
              if ($c == $PageNo) {
                $gotopage .= '<span class="paging_current"> ' . $c . ' </span>';
              }
              else {
                $_GET['PageNo'] = $c;
                $gotopage .= ' <a href="?' . $this->make_query($_GET) . '" title="Page ' . $c . '"> ' . $c . ' </a>';
            }
          }
          if ($PageNo < $MaxPage) {
            $NextPage = $PageNo + 1;
            $_GET['PageNo'] = $NextPage;
            $gotopage .= ' <a href="?' . $this->make_query($_GET) . '" title="Next page"> &rsaquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &rsaquo; </span>';
          }
          if ($PageNo < $MaxPage) {
            $_GET['PageNo'] = $MaxPage;
            $gotopage .= ' <a href="?' . $this->make_query($_GET) . '" title="Last page"> &raquo; </a>';
          }
          else {
            $gotopage .= ' <span class="paging_disabled"> &raquo; </span>';
          }
        }
        $gotopage .= ' </div>';
        break;
    }
	
    $arr[0] = $result;
    $arr[1] = $gotopage;
    $arr[2] = $from;
    $arr[3] = $RecordCount;
    $arr[4] = $result_array;
    $arr[5] = $StartRow;
	
	
    if ($_REQUEST['debug'] == 1) {
      echo '<div style="text-align:left"><pre>';
      print_r($arr);
      echo '</pre></div>';
    }
    return $arr;
  }
  function getInfoColum($table, $id) {
    $result = $this->getDynamic($table, "id='" . $id . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
  function getInfoColumShipping($table, $id) {
    $result = $this->getDynamic($table, "order_id='" . $id . "'", "");
    if ($this->totalRows($result)) {
      $row = $this->nextData($result);
      return $row;
    }
  }
//end pagin_select
// send mail
  function sendmail($param) {
    $mail = new phpmailer();
    $mail->IsSMTP();
    $mail->Host = hostmail; // SMTP servers
    $mail->SMTPAuth = true; // turn on SMTP authentication
    $mail->Username = usermail;
    $mail->Password = passwordmail;
    $mail->From = $param['EmailFrom'];
    $mail->FromName = $param['FromName'];
    $mail->AddAddress($param['EmailTo'], $param['ToName']);
    $cc = explode(";", $param["AddCC"]);
    if ($param["AddCC"]) {
      foreach ($cc as $value) {
        $mail->AddCC($value, $value);
      }
    }
    $bb = explode(";", $param["AddBCC"]);
    if ($param["AddBCC"]) {
      foreach ($bb as $value) {
        $mail->AddBCC($value, $value);
      }
    }
    $mail->IsHTML(true); // send as HTML
    $mail->Subject = $param['Subject'];
    $mail->Body = $param['Content'];
    if ($mail->Send()) {
      return 1;
    }
    else {
      return - 1;
    }
  }
  function generateChangePermissionCombo() {
//echo $_SESSION["user_login"]["is_change_webmaster_permission"];
    if ($_SESSION["user_login"]["is_change_webmaster_permission"]) {
      $controlChangePermission .= " <select name='doiquyen_webmaster' id='doiquyen_webmaster' onchange='doiquyen(this.value)'>

					 <option value=''>--Select--</option>";
      $result_c = $this->getDynamic("webmaster_roles", "", "");
      if ($this->totalRows($result_c) > 0) {
        while ($rowc = $this->nextData($result_c)) {
          $controlChangePermission .= "<option " . (($rowc["id"] == $_SESSION["user_login"]["role_id"]) ? "selected" : "") . " value='" . $rowc["id"] . "'>" . $rowc["title"] . "</option>";
        }
      }
      $controlChangePermission .= "</select>";
    }
    return $controlChangePermission;
  }
// ma hoa
  function numberEncode($number) {
    $number = $number + gmmktime(0, 0, 0, 10, 04, 1979);
    $number = strtoupper(dechex($number));
    $number = str_replace(array('1', '2', '3', '4', '5'), array('I', 'W', 'O', 'U', 'Z'), $number);
//$number=base64_encode($number);
    $number = str_replace('=', '1', $number);
    return $number;
  }
  function numberDecode($number) {
    $number = str_replace('1', '=', $number);
//$number=base64_decode($number);
    $number = ereg_replace('[1-5]', '%', $number);
    if (ereg('%', $number)) {
      return false;
    }
    $number = str_replace(array('I', 'W', 'O', 'U', 'Z'), array('1', '2', '3', '4', '5'), $number);
    $number = hexdec($number);
    return $number = $number - gmmktime(0, 0, 0, 10, 04, 1979);
  }


  function getMemberListArray($parentid,$rowgetInfo,$arrayMember) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "id asc");
    $total = $this->totalRows($result);
    if ($total > 0) {

      $strParentId = "";
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        if($row["status"]==1)
        {
            $arrayMember[]=$row;
        }
      }

      $strParentId .= '-1';
      return $this->getMemberListArray($strParentId,$rowgetInfo,$arrayMember);
    }
    else {
      return $arrayMember;
    }
  }

/**************************************************/
  function getLevelMember($parentid, $level) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      echo "<div class='level-" . $level . "'>";
      echo '<div style="background: #FAFAFA; color: #2D82C3; font-size:16px; padding:10px;cursor: Pointer; text-align:left">

                          Level ' . $level . '  -  Thành Viên <span style="float:right;margin-right:29px">có: <span style="color:#f00;">' . $total . '</span> TV</span>

               </div>';
      echo '<table id="mainTable">

                        <thead>

                          <tr style="background:#848484; color: #fff;">

                           <th class="itemText" width="50"><b>STT</b></th>

                           <th class="itemText" width="100"><b>MSTV</b></th>

                           <th class="itemText" width="250"><b>TÊN Thành Viên</b></th>

                           <th class="itemText" width="250"><b>XẾP SAU</b></th>

                           <th class="itemText"><b>NGÀY THAM GIA</b></th>

                          </tr>

                        </thead>

                        <tbody>';
      $strParentId = "";
      $i = 1;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $ponser = $this->getInfoColum("member", $row['parentid']);
        echo '<tr class="cell1">

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

                                Level ' . $level . '  -  Thành Viên <span style="float:right;">có: <span style="color:#f00;">0</span> TV</span>

                     </div>';
      }
      return true;
    }
  }
/*-------------------------------------------------------*/
//get tree member
  function getTreeMember($parentid) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "parentid asc");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      $parentidback = 0;
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
//echo $row['parentid']."==".$parentidback."<br/>";
        if ($parentidback == 0) {
          $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>" . $row['ma_id'] . "-" . $row['hovaten'] . "</li></ul>";
          echo "<script>

                          var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');

                  </script>";
          $parentidback = $row['parentid'];
        }
        else
          if ($parentidback == $row['parentid']) {
            $str = "<li id=\'" . $row['id'] . "\'>" . $row['ma_id'] . "-" . $row['hovaten'] . "</li>";
            echo "<script>

                            var branches = $('" . $str . "').appendTo('#ul_" . $row['parentid'] . "');

                         </script>";
            $parentidback = $row['parentid'];
          }
          else {
            $str = "<ul id=\'ul_" . $row['parentid'] . "\'><li id=\'" . $row['id'] . "\'>" . $row['ma_id'] . "-" . $row['hovaten'] . "</li></ul>";
            echo "<script>

                              var branches = $('" . $str . "').appendTo('#" . $row['parentid'] . "');

                      </script>";
            $parentidback = $row['parentid'];
        }
      }
      $strParentId .= '-1';
      return $this->getTreeMember($strParentId);
    }
    else {
      return true;
    }
  }

  /*


*******************************************************************/
  function viewtreesystem($Param)
  {
   $row = $Param['row'];
   $str="";
   $str="<a target='_blank' href='cayhethongmember.php?member_id=".$row["id"]."' class='view_sytem' style='width:79px; float:left; color:#fff'>Xem cây</a>";
   return $str;
  }

  function alertcircle120($Param)
  {
   date_default_timezone_set('Asia/Bangkok');
   $row = $Param['row'];
   $now = date("d-m-Y",time());
   $now = strtotime($now);
   $your_date= date("d-m-Y",$row["datecreated"]);
   $your_date = strtotime($your_date);
   $datediff = $now - $your_date;
   $date_re = floor($datediff/(60*60*24));
   $str="";
   if( ($date_re>120) || (120 - $date_re) < 5 )
   {
       $str.='<img src="images/time.gif" alt="" width="30" height="30"><span style ="color:red"><b>'.$date_re.'</b></span>';
   }else
   {
        $str.='<span style ="color:blue"><b>'.$date_re.'</b></span>';
   }

   return $str;
  }

  function alertWithdraw($Param)
  {
   $row = $Param['row'];
   $str="";
   if($row["datecreated"] != $row["dateupdated"])
   {
      $str.='<img src="images/update.gif" alt="">';
   }
   return $str;
  }

  function getCountLevelMember($parentid, $level,$arrayMember) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total = $this->totalRows($result);
    if ($total > 0) {
      $strParentId = "";
      $i = 1;
      $array_parentid = explode(",",$parentid);
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        $arrayMember[$level][$row["id"]]["id"]          = $row["id"];
        $arrayMember[$level][$row["id"]]["ma_id"]       = $row["ma_id"];
        $arrayMember[$level][$row["id"]]["parentid"]    = $row["parentid"];
        $arrayMember[$level][$row["id"]]["hovaten"]     = $row["hovaten"];
        $arrayMember[$level][$row["id"]]["status"]      = $row["status"];
        $arrayMember[$level][$row["id"]]["datecreated"] = date('d-m-Y', $row['datecreated']);

        //$arrayMember[$level][$row["id"]]["cap"]         = 0;
        $i++;
      }
      //echo "<pre>";
      //print_r($arrayMember);
      //echo "</pre>";

      $level++;
      $strParentId .= '-1';
      //Goi lai get thanh vien
      return $this->getCountLevelMember($strParentId, $level,$arrayMember);
    }
    else {
      return $arrayMember;
    }
  }
/*******************************************************/
  function cayhethongmember($parentid, $tang, $level) {
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
           $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
        }else
        {
           $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
        }
        $parentid_text = $ponser['ma_id'] . "-" . $ponser['hovaten'];
        $_SESSION["str_member"] .= "[{v:'" . $ma_id . "-" . $row["hovaten"] . "',f:'" . $ma_id . "-" . $row["hovaten"] . "<br/>".date("d-m-Y",$row["datecreated"]) . "<br/>" . $images . "'}, '" . $parentid_text . "', ''],";
        $i++;
      }
       $strParentId .= '-1';
       //Goi lai get thanh vien
      if ($tang > 0 && $level >= $tang) {
        return true;
      }
      else {
        $level++;
        return $this->cayhethongmember($strParentId, $tang, $level);
      }
    }
    else {
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
  
  function IncomeIndirectMember_F2_F8($id,$rowgetInfo,$packages_id,$arrayPackeges,$valuePackeges,$level)
	{
        if((int)$id > 0)
        {
            $infoMB = $this->getInfoColum("member",$id); 
            if($infoMB["status"]==1)
			{	
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

  function getTotalMemberdownline($parentid, $arrayDownline,$packages,$member) {
    $result = $this->getDynamic("member", "parentid in(" . $parentid . ")", "");
    $total_level = $this->totalRows($result);

    if ($total_level > 0) {
      $strParentId = '';
      while ($row = $this->nextData($result)) {
        $strParentId .= $row['id'] . ",";
        if($row['status']==1 && ($row['datecreated'] >= $member["datecreated"]))
        {
            $arrayDownline['totalprice']+= $packages[$row['packages_id']]["price"];
        }

      }
      $strParentId .=-1;
      $arrayDownline['totalmember']+=$total_level;
      return $this->getTotalMemberdownline($strParentId, $arrayDownline,$packages,$member);
    }
    else {
      return $arrayDownline;
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

   function TotalMemberF1($id){
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
  
  
  function TotalPriceWidthdraw(){
     $result = $this->getDynamic("btc_tranfer", "status=1", "id asc");
     $totalprice = 0;
	 $totalpricebtc = 0;
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
			
            $totalprice+=$row["price"];
			$totalpricebtc+=$row["btc"];
         }
     }
	 $arraywithdraw["price"]=$totalprice;
	 $arraywithdraw["btc"]=$totalpricebtc;	
     return $arraywithdraw;
  }
  
  function TotalPrice_investment2($dates = ""){
	 if($dates!="")
     {
		$result = $this->getDynamic("member_invest", "FROM_UNIXTIME(datecreated,'%Y-%m-%d') ='".date("Y-m-d",strtotime($dates))."'", "id asc");
	 }else
	 {
	    $result = $this->getDynamic("member_invest", "1=1", "id asc");	 
	 } 		 
     
	 $usd_all = 0;
	 $eth_all = 0;     
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {		 
		    $eth_all+=$row["quantity"];
			$usd_all+=$row["quantity_usd"];			
         }
     }
	 $array_investment["usd_all"]=$usd_all;
	 $array_investment["eth_all"]=$eth_all;
	
     return $array_investment;
  }
  
  
  function TotalPriceWidthdraw2($dates = ""){
	 if($dates!="")
     {
		$result = $this->getDynamic("btc_tranfer", "FROM_UNIXTIME(datecreated,'%Y-%m-%d') ='".date("Y-m-d",strtotime($dates))."'", "id asc");
	 }else
	 {
	    $result = $this->getDynamic("btc_tranfer", "1=1", "id asc");	 
	 } 		 
     
	 $totalprice_all = 0;
	 $totalpricebtc_all = 0;
     $totalprice = 0;
	 $totalpricebtc = 0;
     if ($this->totalRows($result) > 0)
     {
         while ($row = $this->nextData($result)) {
		 
		    $totalprice_all+=$row["price"];
			$totalpricebtc_all+=$row["btc"];
			
			if($row["status"]==1)
			{
            $totalprice+=$row["price"];
			$totalpricebtc+=$row["btc"];
			}
			
         }
     }
	 $arraywithdraw["price_all"]=$totalprice_all;
	 $arraywithdraw["btc_all"]=$totalpricebtc_all;
	 $arraywithdraw["price"]=$totalprice;
	 $arraywithdraw["btc"]=$totalpricebtc;
	 /* so tien phai chi tra */
	 if($dates!="")
     {
		$result2 = $this->getDynamic("all_withdrawals", "datecreated ='".strtotime($dates)."'", "id asc");
	 }else
	 {
	    $result2 = $this->getDynamic("all_withdrawals", "1=1", "id asc");	 
	 }

     $total_withdrawals = 0;
     if ($this->totalRows($result2) > 0)
     {
         while ($row2 = $this->nextData($result2)) {
		    $total_withdrawals+=$row2["price"];
         }
     }

     $arraywithdraw["price_all_withdrawals"]=$total_withdrawals;	 
	 
	 
     return $arraywithdraw;
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
            $your_date   = $row["datecreated"];
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
            $your_date   = date('d-m-Y',$row["datecreated"]);
            $your_date   = strtotime($your_date);
            $datediff    = $now - $your_date;
            $date_re     = floor($datediff/(60*60*24));

            $price_f = 0;
            //cap 2
            if($level==2)
            {

                if($date_re < $packages[$packages_id]["circle"] && $date_re >0 )
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
                if($date_re < $packages[$packages_id]["circle"] && $date_re >0 )
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
                if($date_re < $packages[$packages_id]["circle"] && $date_re > 0)
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
                if($date_re < $packages[$packages_id]["circle"] && $date_re > 0)
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
        date_default_timezone_set('TotalIncomeIndirectDailyByDate_ShowAsia/Bangkok');
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
            $your_date   = $row["datecreated"];
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
                            <td class="itemText">+'.$this->price($price_f).' <sup>đ</sup> from to '.$row["ma_id"].'- '.$row['hovaten'].'

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
                            <td class="itemText">+'.$this->price($price_f).' <sup>đ</sup> from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
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
                            <td class="itemText">+'.$this->price($price_f).' <sup>đ</sup> from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
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
                            <td class="itemText">+'.$this->price($price_f).' <sup>đ</sup> from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
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
                            <td class="itemText">+'.$this->price($price_f).' <sup>đ</sup> from to '.$row["ma_id"].'- '.$row['hovaten'].'</td>
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
	date_default_timezone_set('Asia/Bangkok');  
    $totalIncome = 0;
	//echo "ponser_id =". (int)$id ." and status=1 and datecreated ='".$date."'";
	//echo "<br>";
    $result = $this->getDynamic("incomedirect", "ponser_id =". (int)$id ." and status=1 and datecreated='".$date."'", "id asc");
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
  
  

  function TotalPriceIncomeInDirectByDate($id,$date){
	date_default_timezone_set('Asia/Bangkok');  
    $totalIncome = 0;	
    //$result = $this->getDynamic("Incomeindirect", "ponser_id =".(int)$id ." and status=1 and FROM_UNIXTIME(datecreated,'%Y-%m-%d') ='".date("Y-m-d",$date)."'", "id asc");
	$result = $this->getDynamic("Incomeindirect", "ponser_id =".(int)$id ." and status=1 and datecreated ='".$date."'", "id asc");
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
  
  
  
  function price_fivepecent($Param) {
    $row = $Param['row'];
	$five_pecent = (3*$row["price"])/100;	
    $str= "<span style='float:left; width: 70px;font-size:15px'>".$row["price"]."</span>";
	$str.="<span style='float:right; width: 70px;font-size:15px'>".($row["price"]-$five_pecent)."</span>";
   
    return $str;
  }
  
  function update_payment($Param)
  {   
     $row = $Param['row'];
	 $str="";
	 $str.='<span class="itemText"><input style="cursor:pointer" id="check_status_'.$row["id"].'" onClick="kickhoat_payment('.$row["id"].',\'check_status_'.$row["id"].'\')" type="checkbox" '.(($row['status']==1)?"checked='true'":"").' value="" name="status"></span>';
     return $str;
  }
  
  function blockchain($Param)
  {
   
    $row = $Param['row'];
    $member 			= $this->getInfoColum("member", $row['member_id']);
    $info_packages   = $this->getInfoColum("packages", $member['packages_id']);
   
    $totalInvestment = $this->TotalPriceMember_invest($member["id"]);
    $usd_profit = 0;	
	if($totalInvestment < $info_packages["price"]){
		$usd_profit = $info_packages["price"];
	}else
	{
		$usd_profit = $totalInvestment;
	}	
	
   $five_pecent = (5*$row["price"])/100;
   $price_real = $row["price"]-$five_pecent;
   $str="";
   $str.='<p><a target="_blank" href="https://www.diamondcapital.co/_system_admins_mn/mngMain.php?table_name=member&ma_id='.$member["ma_id"].'">'.$member["ma_id"].'-'.$member["hovaten"].'</a><strong style="color:red;">($'.number_format($usd_profit).')</strong></p>';
   $str.='<span class="showvi"><img style="display:none" src="https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl='.$member["eth_address"].'&usd='.$price_real.'" width="150" height="150"><br> <span class="showvidetail">(Show ví)</span></span>';
   $str.='<p>'.$member["eth_address"].'</p>';
   return $str;
  }
  
  function confirm_payment($Param)
  {
	$row = $Param['row'];
    $member 			= $this->getInfoColum("member", $row['member_id']);	
	$five_pecent = (3*$row["price"])/100;
    $price_real = $row["price"]-$five_pecent;
    $str="";
	if($row["confirm_payment"]==0)
	{
		$str = "<a href='javascript:void(0)' amount='".$price_real."' payment_id='".$row["id"]."' address='".$member["eth_address"]."' class='confirm_payment view_sytem' style='width:60px; float:left; color:#fff'>Confirm</a>";
	}else
	{
		$str = "<a href='javascript:void(0)' data_id='".$price_real."' style='width:100px; float:left; color:#fff'>Confirm Complete</a>";
	}
    
    return $str; 
  }

  function longin_member($Param) {
    $row = $Param['row'];
    $str = "";
    if($_SESSION["user_login"]["role_id"]==11)
    {
    $str = "<a target='_blank' href='".HOST."/member-login-admin.aspx?id=".base64_encode(1). "&admin-login-by=".base64_encode($row["id"])."' class='view_sytem' style='width:70px; float:left; color:#fff'>Login</a>";
    }
    return $str;
  }
  
  /*Thuy.TQ 2016-12-08 */
  
  
  function ArrayIDMember_F1($id){
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
  
  function checkLevel_cannhanh($id){
    date_default_timezone_set('Asia/Bangkok');
    $level = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ."", "id asc");
    if ($this->totalRows($result) >= 2) {
       $level = 1;
    }
    return $level;
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
           $images = "<a href=\'cayhethongmember2.php?member_id=" . $id . "\'> <img src=\'".$packages["picture"]."\' width=\'40\' height=\'40\' /></a>";
        }else
        {
           $images = "<a href=\'cayhethongmember2.php?member_id=" . $id . "\'> <img src=\'images/goi/1.png\' width=\'40\' height=\'40\' /></a>";
        }
        $parentid_text = $ponser['ma_id'] . "-" . $ponser['hovaten'];
        $_SESSION["str_member"] .= "[{v:'" . $ma_id . "-" . $row["hovaten"] . "',f:'" . $ma_id . "-" . $row["hovaten"] . "<br/>" . $images . "'}, '" . $parentid_text . "', ''],";
        $i++;
      }
      $strParentId .= '-1';
      /*goi lai get thanh vien*/
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
  
  

  function checkLevel_I($id){
    date_default_timezone_set('Asia/Bangkok');
    $level = 0;
    $result = $this->getDynamic("member", "parentid =". (int)$id ." and status=1 and packages_id<>1", "id asc");
    if ($this->totalRows($result) >= 5) {
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
       if($no>=3)
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
       if($no>=3)
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
       if($no>=3)
       {
           $level = 4;
       }
    }
    return $level;
  }


    function IncomeIndirectMember($id,$rowgetInfo,$info_packages,$level=0,$pecent=7){
        if((int)$id > 0)
        {
            $infoMB = $this->getInfoColum("member",$id);
            $levelsPoner = $this->checkLevel_I($id);
            if($levelsPoner==1)
            {
                //check level 2
                $arrayID_F1 = $this->ArrayIDMemberF1($id);
                $arrayID_F1.="0";
                $levelsPoner = $this->checkLevel_II($arrayID_F1);
                if($levelsPoner==2)
                {
                    $arrayID_F2 = $this->ArrayIDMemberF2($arrayID_F1);
                    $arrayID_F2.="0";
                    $levelsPoner = $this->checkLevel_III($arrayID_F2);
                    if($levelsPoner==3)
                    {
                        $arrayID_F3 = $this->ArrayIDMemberF3($arrayID_F2);
                        $arrayID_F3.="0";
                        $levelsPoner = $this->checkLevel_IV($arrayID_F3);
                    }
                }
            }
            $price_indirect=0;
            $array_col_indirect=array();
            switch ($levelsPoner) {
                      case 1 :
                        if($levelsPoner > $level && $pecent>0)
                        {
                            $price_indirect = ($info_packages["price"] * 2)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent= $pecent-2;
                        }
                        break;

                      case 2 :
                        if($levelsPoner > $level && $pecent>0)
                        {
                            if($level==0)
                            {
                               $price_indirect = ($info_packages["price"] * 4)/100;
                               $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                               $pecent = $pecent-4;
                            }else
                            {
                            $price_indirect = ($info_packages["price"] * 2)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-2;
                            }
                        }
                        break;
                      case 3 :
                        if($levelsPoner > $level && $pecent>0)
                        {
                            if($level==0)
                            {
                               $price_indirect = ($info_packages["price"] * 6)/100;
                               $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                               $pecent = $pecent-6;
                            }else if($level==1)
                            {
                            $price_indirect = ($info_packages["price"] * 4)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-4;
                            }
                            else
                            {
                            $price_indirect = ($info_packages["price"] * 2)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-2;
                            }
                        }
                        break;

                      case 4 :
                        if($levelsPoner >= $level && $pecent>0)
                        {
                            if($level==0)
                            {
                               $price_indirect = ($info_packages["price"] * 7)/100;
                               $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                               $pecent = $pecent-7;
                            }else if($level==1)
                            {
                            $price_indirect = ($info_packages["price"] * 5)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-5;
                            }
                            else if($level==2)
                            {
                            $price_indirect = ($info_packages["price"] * 3)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-3;
                            }
                            else
                            {
                            $price_indirect = ($info_packages["price"] * 1)/100;
                            $array_col_indirect = array("ponser_id"=>$id,"member_id"=>$rowgetInfo["id"],"price"=>$price_indirect,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);
                            $pecent = $pecent-1;
                            }
                        }
                        break;
                      default :
                        break;
            }
            if($price_indirect)
            {
              $this->insertTable_2("Incomeindirect", $array_col_indirect);
            }

            if($levelsPoner < $level)
            {
                $levelsPoner = $level ;
            }
            return $this->IncomeIndirectMember($infoMB["parentid"],$info_packages,$levelsPoner,$pecent);

        }else
        {
            return true;
        }
    }
    /*Thuy.TQ 2016-12-08 */
	
	function checkInsertIncomeInDirectMember($ponser_id,$member_id,$price_indirect){
		
		$result = $this->getDynamic("Incomeindirect", "ponser_id=".$ponser_id." and member_id=".$member_id." and price=".$price_indirect."", "");
		if ($this->totalRows($result) >0) {
			return true;
		}
		return false;
	}
	
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


}
?>