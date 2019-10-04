<?php
	include str_replace('\\','/',dirname(__FILE__)).'/config.DATABASE.php';
	class DBFUNCTION {
			private $connect=null;
			function __construct()
			{
               $this->connect=mysqli_connect(HOSTADDRESS,DBACCOUNT,DBPASSWORD);
			   mysqli_query($this->connect,"SET CHARACTER SET 'utf8'");
			   mysqli_query($this->connect,"SET SESSION collation_connection ='utf8_unicode_ci'");
               mysqli_select_db($this->connect,DBNAME);
               
			}
			function getConnect()
			{
				return $this->connect;
			}
			function __destruct()
			{
				$this->connect=null;
			}
			function dbConnect()
			{
				try {
						if(empty($this->connect))
						{
							$this->connect=mysqli_connect(HOSTADDRESS,DBACCOUNT,DBPASSWORD);
						    mysqli_query($this->connect,"SET CHARACTER SET 'utf8'");
						    mysqli_query($this->connect,"SET SESSION collation_connection ='utf8_unicode_ci'");
						    mysqli_select_db($this->connect,DBNAME);
						}
				}catch (Exception $ex) {
						return false;
				}
			}
			function dbClose()
			{
				if($this->connect!=null)
				{
					mysqli_close($this->connect);
					$this->connect=null;
				}
			}
            function getValueOfQuery($sql){
				$rst =  $this->doSQL($sql);
				$row = @mysqli_fetch_array($rst);
				return $row[0];
			}
			/* Remove SQLInjection
			-----------------------------------------------------------------*/
			function escapeStr($str)
			{
				return mysqli_real_escape_string($this->connect,$str);
			}
			function removeSQLInjection($str) {
				return mysqli_real_escape_string($this->connect,$str);
			}
			/*
			-----------------------------------------------------------------*/
			function getScale($tbName,$condition,$orderby,$arrayCol=null)
			{
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?" ORDER BY ".$orderby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql="SELECT ".$type."(".$col.") FROM ".$tbName."  ".$condition."  ".$orderby;
						$rst=$this->doSQL($sql);
						if($row=$this->nextAssoc($rst))$count=(int)$row[0];
						return $count;
				}catch (Exception $ex) {
						return 0;
				}
			}
			/* Get data dynamic
			-----------------------------------------------------------------*/
			function getDynamic($tbName,$condition,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$sql='SELECT * FROM '.$tbName.' '.$condition.' '.$orderby . $limit;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get subtracting two diff columns
			-----------------------------------------------------------------*/
			function getSubtracting($tb1,$tb2,$col1,$col2,$condition1,$condition2) {
				try {
						$this->dbConnect();
						$condition1=(!empty($condition1))?' WHERE '.$condition1:'';
						$condition2=(!empty($condition2))?' WHERE '.$condition2:'';
						$sql='SELECT (SELECT SUM('.$col1.') FROM '.$tb1.$condition1.') as amount, (SELECT SUM('.$col1.') FROM '.$tb1.$condition1.') - (SELECT SUM('.$col2.') FROM '.$tb2.$condition2.') AS difference';
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get specific month amount billed
			-----------------------------------------------------------------*/
			function getAmountBilled($tbName,$condition) {
				try {
						$this->dbConnect();
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT SUM(total) AS sum_total FROM '.$tbName.'  '.$condition;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			
			/* Get monthly data
			-----------------------------------------------------------------*/
			function getMonthlyRevenue($tbName,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$groupby=(!empty($groupby))?' GROUP BY '.$groupby:'';
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$sql='SELECT COUNT(id) AS value_count, MONTH(FROM_UNIXTIME(order_date)) AS month,YEAR(FROM_UNIXTIME(order_date)) AS year,  SUM(total) AS sum_total, SUM(paid) AS sum_paid, SUM(no_patient) AS sum_patient, SUM(quantity) AS sum_quantity FROM '.$tbName.'  '.$condition.'  '.$groupby.$orderby.$limit;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			
			/* Get last news
			-----------------------------------------------------------------*/
			function getLastNews($tbName,$condition,$orderby,$limit) {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$limit=(!empty($limit))?' LIMIT '.$limit:'';
						$sql='SELECT * FROM '.$tbName.$condition.$orderby.$limit ;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get related news
			-----------------------------------------------------------------*/
			function getRelatedNews($tbName,$condition,$orderby,$limit) {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$limit=(!empty($limit))?' LIMIT '.$limit:'';
						$sql='SELECT * FROM '.$tbName.$condition.$orderby.$limit ;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			
			/* Get join dynamic
			-----------------------------------------------------------------*/
			function getjoinDynamic($tbName1,$tbName2,$on,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY ' . $orderby : '';
						$groupby=(!empty($groupby))?' GROUP BY ' . $groupby : '';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$on=(!empty($on))?' ON '.$on:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT *,tb2.id AS detail_id FROM `'.$tbName1.'` AS tb1 JOIN `'.$tbName2 .'` AS tb2 '. $on . $condition.' ' . $groupby.$orderby.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get leftjoin dynamic
			-----------------------------------------------------------------*/
			function innerjoinDynamic($tbName1,$tbName2,$on,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY ' . $orderby : '';
						$groupby=(!empty($groupby))?' GROUP BY ' . $groupby : '';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$on=(!empty($on))?' ON '.$on:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT * FROM `'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $on . $condition.' ' . $groupby.$orderby.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			
			/* Get count leftjoin dynamic
			-----------------------------------------------------------------*/
			function countjoinDynamic($tbName1,$tbName2,$on,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY ' . $orderby : '';
						$groupby=(!empty($groupby))?' GROUP BY ' . $groupby : '';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$on=(!empty($on))?' ON '.$on:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT COUNT(tb1.id) AS OID FROM `'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $on . $condition.' ' . $groupby.$orderby.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get sum leftjoin dynamic
			-----------------------------------------------------------------*/
			function getjoinSum($tbName1,$tbName2,$on,$condition) {
				try {
						$this->dbConnect();
						$on=(!empty($on))?' ON '.$on:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT SUM(tb1.total) AS amount_sum FROM `'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $on . $condition;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get inner3join dynamic
			-----------------------------------------------------------------*/
			function inner3joinDynamic($select,$tbName1,$tbName2,$tbName3,$ontb1vstb2,$ontb2vstb3,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY ' . $orderby : '';
						$groupby=(!empty($groupby))?' GROUP BY ' . $groupby : '';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$ontb1vstb2=(!empty($ontb1vstb2))?' ON '.$ontb1vstb2:'';
						$ontb2vstb3=(!empty($ontb2vstb3))?' ON '.$ontb2vstb3:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT ' . $select . ' FROM (`'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $ontb1vstb2 . ') LEFT JOIN `'.$tbName3 .'` as tb3 ' . $ontb2vstb3 . $condition.' ' . $groupby.$orderby.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get count 3join dynamic
			-----------------------------------------------------------------*/
			function count3joinDynamic($tbName1,$tbName2,$tbName3,$ontb1vstb2,$ontb2vstb3,$condition,$groupby,$orderby,$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY ' . $orderby : '';
						$groupby=(!empty($groupby))?' GROUP BY ' . $groupby : '';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$ontb1vstb2=(!empty($ontb1vstb2))?' ON '.$ontb1vstb2:'';
						$ontb2vstb3=(!empty($ontb2vstb3))?' ON '.$ontb2vstb3:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT COUNT(tb1.id) AS OID FROM (`'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $ontb1vstb2 . ') LEFT JOIN `'.$tbName3 .'` as tb3 ' . $ontb2vstb3 . $condition . ' ' . $groupby.$orderby.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get sum 3join dynamic
			-----------------------------------------------------------------*/
			function get3joinSum($tbName1,$tbName2,$tbName3,$ontb1vstb2,$ontb2vstb3,$condition) {
				try {
						$this->dbConnect();
						$ontb1vstb2=(!empty($ontb1vstb2))?' ON '.$ontb1vstb2:'';
						$ontb2vstb3=(!empty($ontb2vstb3))?' ON '.$ontb2vstb3:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT SUM(tb1.total) AS amount_sum FROM (`'.$tbName1.'` as tb1 LEFT JOIN `'.$tbName2 .'` as tb2 '. $ontb1vstb2 . ') LEFT JOIN `'.$tbName3 .'` as tb3 ' . $ontb2vstb3 . $condition;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get data sum
			-----------------------------------------------------------------*/
			function getSum($tbName,$col,$sumcol,$condition,$orderby,$groupby='',$limit='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$groupby=(!empty($groupby))?' GROUP BY '.$groupby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$limit=(!empty($limit))?' LIMIT ' . $limit : '';
						$sql='SELECT '.$col.',SUM('.$sumcol.') AS value_sum FROM '.$tbName.'  '.$condition.'  '.$groupby.'  '.$orderby.'  '.$limit;
                        //echo $sql;die();
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get data count
			-----------------------------------------------------------------*/
			function getCount($tbName,$col,$condition,$orderby,$groupby='') {
				try {
						$this->dbConnect();
						$orderby=(!empty($orderby))?' ORDER BY '.$orderby:'';
						$groupby=(!empty($groupby))?' GROUP BY '.$groupby:'';
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql='SELECT COUNT('.$col.') AS value_count FROM '.$tbName.'  '.$condition.'  '.$groupby.'  '.$orderby;
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Get data ranking
			-----------------------------------------------------------------*/
			function getRanking($tbName,$condition=false) {
				try {
						$this->dbConnect();
						if($condition) {
							$condition=(!empty($condition))?' WHERE '.$condition:'';
							$sql='SELECT `member_id`,SUM(`quantity`) AS sum_value FROM `'.$tbName.'` AS S JOIN `member` AS M ON S.member_id = M.id '.$condition.' AND M.is_del = 0 GROUP BY `member_id`' ;
						} else {
							$sql='SELECT `member_id`, SUM(`quantity`) AS sum_value FROM `'.$tbName.'` AS S JOIN `member` AS M ON S.member_id = M.id WHERE M.is_del = 0 GROUP BY `member_id`';
						}
                        //echo $sql;
                           $rst=$this->doSQL($sql);
						return $rst;
				}catch (Exception $ex)
				{
						return null;
				}
			}
			/* Insert into table
			-----------------------------------------------------------------*/
			function insertTable($tbName,$arrayValue) {
				try {
						$array=array(2);
						$this->dbConnect();
                           if(count($arrayValue)>0)
                           {
                               foreach($arrayValue as &$value)$value=$this->removeSQLInjection($value);
   							$sql="INSERT INTO ".$tbName."(".implode(",",array_keys($arrayValue)).") VALUES('".implode("','",array_values($arrayValue))."')";
                             						  
							   $affect=$this->doNoSQL($sql);
                            $id=mysqli_insert_id($this->connect);
   							$array []=$affect;
   							$array []=$id;
                               unset($id);
                               unset($affect);
                               return $array;
                           }
				}catch (Exception $ex) {
						return null;
				}
			}


            function insertTable_2($tbName,$arrayValue) {
				try {
						$array=array(2);
						$this->dbConnect();
                           if(count($arrayValue)>0)
                           {
                               foreach($arrayValue as &$value)$value=$this->removeSQLInjection($value);
   							    $sql="INSERT INTO ".$tbName."(".implode(",",array_keys($arrayValue)).") VALUES('".implode("','",array_values($arrayValue))."')";                                
								
                                $this->dbConnect();
                                mysqli_query($this->connect,"SET AUTOCOMMIT=0");
                                mysqli_query($this->connect,"START TRANSACTION");								
        					    $affect=mysqli_query($this->connect,$sql);
                                if($affect)
                                {
                                    $id=mysqli_insert_id ($this->connect);
           							$array []=$affect;
           							$array []=$id;
                                    mysqli_query($this->connect,"COMMIT");
        						    return $array;
                                }else {
                                    mysqli_query($this->connect,"ROLLBACK");
                                    return null;
                                }
                           }
				}catch (Exception $ex) {
						return null;
				}
			}

			/* Update table
			-----------------------------------------------------------------*/
			function updateTable($tbName,$arrayValue,$condition,$type="UPDATE",$order_id=NULL) {
				$key_update = array(
					'hovaten' => 'Name',
					'age' => 'Age',
					'gender' => 'Sex',
					'fb' => 'Facebook',
					'email' => 'Email',
					'diachi' => 'Address',
					'didong' => 'Tel',
					'tax' => 'Tax (%)',
					'title' => 'Title',
					'content' => 'Content',
					'status' => 'Order status',
					'datecreated' => 'Date created',
					'is_del' => 'Delete',
					'password' => 'Password',
					'password2' => 'Password 2',
					'password3' => 'Password 3',
					'quantity' => 'Total Qualtity',
					'production' => 'Manufacturing',
					'shade' => 'VITA shade',
					'position' => 'Tooth Numbering',
					'detail_quantity' => 'Quantity',
					'desired_date' => 'Shipping Request Date',
					'delivery_file' => 'Shippable data',
					'remarks' => 'Remarks',
					'paid_date' => 'Paid Date',
					'payment_method' => 'Payment Method',
					'payment_status' => 'Payment Status',
					'paid' => 'Paid',
					'no_patient' => 'No. of Patient',
					'total' => 'Total Amount',
					'subtotal' => 'Subtotal',
					'tax' => 'Tax',
					'manager' => 'Manager name',
					'manager_id' => 'Manager ID',
					'operator' => 'Operator name',
					'operator_id' => 'Operator ID',
					'checker' => 'Checker',
					'checker_id' => 'Checker ID',
					'shipping_file' => 'Shippable data',
					'ship_file' => 'Shippable data',
					'shipping_date' => 'Shiping date',
					'read_list' => 'Member ID who view this order detail',
					'shipping_password' => 'Shipping Password',
					'show_price' => 'Show/hide price',
					'downloadable' => 'Allow to download shippable file',
					'dateupdated' => 'Updated on',
					'detail_updated' => 'Updated on',
				);
				$table_update = array(
					'member' => 'Member',
					'informations' => 'Informations',
					'orders' => 'Orders',
					'order_detail' => 'Order Details',
				);
				try {
						$str='';
                        $type=strtoupper($type);
						$where_old = $condition;
						$condition=(!empty($condition))?' WHERE '.$condition:'';						
						$this->dbConnect();						
						$infoColum_old = array();
						$result = $this->getDynamic($tbName,$where_old,"");
						if ($this->totalRows($result)>0) 
						{
						  $infoColum_old = $this->nextData($result);                          						  
						}												
						$str_change = array();
						foreach($arrayValue as $key => $value) {
							if(isset($infoColum_old[$key]) && $key != 'dateupdated')
							{
								if($infoColum_old[$key]!=$value)
								{
									if($infoColum_old[$key]=='') $infoColum_old[$key] = 'none';
									if($value=='') $value = 'none';
									if($key=='desired_date' || $key=='shipping_date' || $key=='dateupdated' || $key=='detail_updated' ) {
										$str_change[]= "field '" . $key_update[$key] ."' in table " . $table_update[$tbName] . " (" . $where_old . ")" . " from '".date("Y/m/d H:i",$infoColum_old[$key])."' to '". date("Y/m/d H:i",$value)."'";
									} else {
										$str_change[]= "field '" . $key_update[$key] ."' in table " . $table_update[$tbName] . " (where " . $where_old . ")" . " from '".$infoColum_old[$key]."' to '". $value."'";
									}
								}
							}
								if(strpos("$arrayValue[$key]","$key")===false)$str.="$key='".$this->removeSQLInjection($arrayValue[$key])."',";
								else $str.="$key='".$this->escapeStr($arrayValue[$key])."',";
						}
						$str=substr($str,0,strlen($str)-1);
						if($type=="INSERT")	$sql="INSERT INTO ".$tbName." SET ".$str;
						else $sql="UPDATE ".$tbName." SET ".$str." ".$condition;
                        //echo $sql."\n";die();
					    $affect=$this->doNoSQL($sql);
						
						/*write log Db>> */
						/*printf("<pre>%s</pre>",print_r($str_change,true));*/						
						if(count($str_change)>0)
						{
							$content_log = implode(";",$str_change);
							$array_log = array("member_id"=>$_SESSION["member_id"],"name_member"=>$_SESSION["member_hovaten"],"content_log"=>$content_log,"type_query"=>$type,"table_name"=>$tbName,"sqlquery"=>stripcslashes($sql),"datecreated"=>time(),"order_id"=>$order_id);

							if($type!="NOSAVELOG")
								$this->insertTable_2("history_logs", $array_log);
						}
						
						/*write log file>> */
						if($type!="NOSAVELOG") {
							$logfilename = "logs_".date("d-m-Y",time()).".txt";
							$myfile = fopen("logs/".$logfilename."", "a") or die("Unable to open file!");
							$txt = "".date('d-m-Y h:s:i',time())."=>'".$type."','".$tbName."','".stripcslashes($sql)."','".$_SESSION["member_hovaten"]."'\n";						
							fwrite($myfile, $txt);
							fclose($myfile);
						}
						/*write log << */
						
                           unset($str);
                           unset($sql);
						return (int)$affect;
				}catch (Exception $ex) {					
					    $logfilename = "logs_".date("d-m-Y",time()).".txt";
                        $myfile = fopen("logs/".$logfilename."", "a") or die("Unable to open file!");
                        $txt = "".date('d-m-Y h:s:i',time())."=>'".$type."','".$tbName."','".stripcslashes($ex)."','".$_SESSION["user_login"]["fullname"]."'\n";
                        fwrite($myfile, $txt);
                        fclose($myfile);
						return 0;
				}
			}
			/* Update notificatios read
			-----------------------------------------------------------------*/
			function updateNotofication($tbName,$condition) {
				$where = 'WHERE '.$condition;
				$sql="UPDATE ".$tbName." SET `is_read`='yes' ".$where;
				$affect=$this->doNoSQL($sql);
				unset($sql);
				return (int)$affect;
			}
			
			/* Update all comments read
			-----------------------------------------------------------------*/
			function updateComment($tbName,$condition,$read_list,$column) {
				$where = 'WHERE '.$condition;
				$sql="UPDATE ".$tbName." SET `".$column."`='".$read_list."' ".$where;
				//echo $sql; die();
				$affect=$this->doNoSQL($sql);
				unset($sql);
				return (int)$affect;
			}
			
			/* Update when operator download file
			-----------------------------------------------------------------*/
			function updateStatus($tbName,$condition,$read_list,$column) {
				$where = 'WHERE '.$condition;
				$sql="UPDATE ".$tbName." SET `".$column."`='".$read_list."' ".$where;
				//echo $sql; die();
				$affect=$this->doNoSQL($sql);
				unset($sql);
				return (int)$affect;
			}
			
			/* Update all banned
			-----------------------------------------------------------------*/
			function updateBanned($tbName,$condition) {
				$where = 'WHERE '.$condition;
				$sql="UPDATE ".$tbName." SET `is_read`='yes' ".$where;
				$affect=$this->doNoSQL($sql);
				unset($sql);
				return (int)$affect;
			}
			/* Update event
			-----------------------------------------------------------------*/
			function updateEvent($tbName,$set) {
				$sql="UPDATE ".$tbName." SET ".$set;
				$affect=$this->doNoSQL($sql);
				//echo $sql; die();
				unset($sql);
				return (int)$affect;
			}
			/* Delete dynamic
			-----------------------------------------------------------------*/
			function deleteDynamic($tbName,$condition) {
				try {
						$this->dbConnect();
						$condition=(!empty($condition))?' WHERE '.$condition:'';
						$sql="DELETE FROM ".$tbName." ".$condition;
                        //echo"delect". $sql."\n";
                           $affect=$this->doNoSQL($sql);
                           unset($condition);
                           unset($sql);
						return $affect;
				}catch (Exception $ex) {
						return 0;
				}
			}
			/* Return total rows
			-----------------------------------------------------------------*/
			function totalRows($result) {
				if(!$result) return 0;
				return mysqli_num_rows($result);
			}
			/* Convert resultset to array with key and value array
			-----------------------------------------------------------------*/
			function RSTToArray($rst,$col_key,$arrayCol=null){
				try {
						$array=array();
                           $array_tmp=array();
						while($row=$this->nextAssoc($rst)){
							foreach($arrayCol as $key)
                               {
                                   $array_tmp[$key]=$row[$key];
                                   $array_tmp[0]=$array_tmp[$key];
                               }
							$array [$row[$col_key]]=$array_tmp;
						}
                           unset($array_tmp);
                           unset($row);
						return $array;
				}catch (Exception $ex) {
						return null;
				}
			}
			/* Return total fields
			-----------------------------------------------------------------*/
			function totalFields($result) {
				try {
						return mysqli_num_fields($result);
				}catch (Exception $ex) {
						return 0;
				}
			}
			/* Next Record using Assoc
			-----------------------------------------------------------------*/
			function nextAssoc($result) {
				return mysqli_fetch_assoc($result);
			}
			/* Next record using array
			-----------------------------------------------------------------*/
			function nextData($result) {
				return mysqli_fetch_array($result);
			}
			/* Next record using index
			-----------------------------------------------------------------*/
			function nextRow($result) {
				return mysqli_fetch_row($result);
			}
			/* Next record using Object
			-----------------------------------------------------------------*/
			function nextObject($result) {
				return mysqli_fetch_object($result);
			}
			/* Next field
			-----------------------------------------------------------------*/
			function nextField($result,$offset=0) {
				return mysqli_fetch_field($result,$offset);
			}
			/* Execute sqlcommand with select query
			-----------------------------------------------------------------*/
			function doSQL($sql) {				
                /*echo $sql."<br>";*/
				try {
                        /*date_default_timezone_set('Asia/Bangkok');*/
                        $this->dbConnect();
                        mysqli_query($this->connect,"SET AUTOCOMMIT=0");
                        mysqli_query($this->connect,"START TRANSACTION");
						$rst=mysqli_query($this->connect,$sql);   
                        if($rst)
                        {
                           mysqli_query($this->connect,"COMMIT");
						   return $rst;
						   
                        }else {
							mysqli_query($this->connect,"ROLLBACK");
                        }

				}catch (Exception $ex) {
						return null;
				}
			}
			/* Execute sqlcommand with insert, update, delete query
			-----------------------------------------------------------------*/
			function doNoSQL($sql) {
				try {
						$this->dbConnect();						
                        mysqli_query($this->connect,"SET AUTOCOMMIT=0");
                        mysqli_query($this->connect,"START TRANSACTION");
					    $affect=mysqli_query($this->connect,$sql);
                        if($affect)
                        {
                            mysqli_query($this->connect,"COMMIT");
						    return $affect;
                        }else {
                                mysqli_query($this->connect,"ROLLBACK");
                        }


				}catch (Exception $ex) {
						return 0;
				}
			}


			function truncateTable($table) {
				try {
						if(!empty($table)) {
								$this->dbConnect();
								$sql="TRUNCATE TABLE ".$table;
								$affect=$this->doNoSQL($sql);
                                   unset($sql);
								return $affect;
						}
				}catch (Exception $ex) {
						return 0;
				}
			}
			/* Return array from resultset
			-----------------------------------------------------------------*/
			function getArray($tbName,$condition,$orderby,$mode="",&$array_col=null) {
				try {
							$str='';
							$array_row=array();
							$this->dbConnect();
							$rst=$this->getDynamic($tbName,$condition,$orderby);
							if(is_array($array_col))$array_col = $this->getColumns($rst);
							switch($mode)
							{
							 case "stdObject":
							   while($row=$this->nextObject($rst))$array_row[]=$row;
							   break;
                             case "Assoc":
							   while($row=$this->nextAssoc($rst))$array_row[]=$row;
							   break;
                             case "Row":
                                while($row=$this->nextRow($rst))$array_row[]=$row;
							    break;
							 default:
							   while($row=$this->nextData($rst))$array_row[]=$row;
							   break;
							}
                           unset($rst);unset($str);unset($row);
						return $array_row;
				}catch (Exception $ex) {
				}
			}
			/* Return all column informaiton
			-----------------------------------------------------------------*/
			function getColumns($rst=null) {
				try {
					while($field=$this->nextField($rst)) $array[]=$field->name;
     					unset($numfield);unset($field);
					return $array;
				}catch (Exception $ex) {
				}
			}
			/* Generate select tag and no recursive
			*******************************************************************/
			function generateSelect($tablename,$where,$orderby,$idName,$datatextfield,$datavaluefield,$matchSelected,$arrayOption=null) {
				try {
						$str="<select onchange=\"".$arrayOption["onchange"]."\" name='".$idName."' id='".$idName."' class=\"".$arrayOption["class"]."\">";
						if($arrayOption["firstText"]!='')
								$str.="<option value='0' > ".$arrayOption["firstText"]." </option>";
						$obj=$this->getArray($tablename,$where,$orderby);
						$array=$obj["arrayRow"];
						if(is_array($array))
								foreach($array as $rs)
										$str.="<option value='".$rs[$datavaluefield]."' ".(($rs[$datavaluefield]==$matchSelected)?"  selected='selected'":" ").'>&nbsp;'.$arrayOption["char"].'&nbsp;&nbsp;'.$rs[$datatextfield]."</option>";
						$str.="</select>";
                           unset($array);
                           unset($obj);
						return $str;
				}catch (Exception $ex) {
						return '';
				}
			}
			/* Generate select tag recursive
			******************************************************************/
			function generateSelectArray($array=null,$col=null,$parentid=0,$idName,$datatextfield,$datavaluefield,$matchSelected,$arrayOption=null)
			{
				try {
						$col[$datatextfield]=$datatextfield;
						$col[$datavaluefield]=$datavaluefield;
						$list=$this->recursiveArray($array,$parentid,$col);
						$str="<select onchange=\"".$arrayOption["onchange"]."\" name='".$idName."' id='".$idName."' style='".$arrayOption["style"]."'>";
						if($arrayOption["firstText"]!='')
								$str.="<option value='0' > ".$arrayOption["firstText"]." </option>";
						if(is_array($list))
								foreach($list as $key => $value) {
										$key_value=preg_replace("/[^0-9]/",'',$value[$datavaluefield]);
										$str.="<option ".(($matchSelected==$key_value)?"selected='selected'":'')." value=\"".$key_value."\">".$value[$datatextfield]."</option>";
						}
						$str.="</select>";
                           unset($list);
                           unset($key_value);
						return $str;
				}catch (Exception $ex) {
						return '';
				}
			}
			/* Generate Select tag
			******************************************************************/
			function arrayToSelect($array,$match='',$idName,$Options=null) {
				try {
						$str='<select name="'.$idName.'" id="'.$idName.'" size="'.$Options["size"].'"  class="'.$Options["class"].'" style="'.$Options["style"].'"  onchange="'.$Options["onchange"].'">';
						if(!empty($Options["firstText"])) $str.='<option value="">'.$Options["firstText"].'</option>';
						if(count($array)>0)foreach($array as $key => $value) $str.='<option value="'.$key.'"'.(($match==$key)?" selected ":"").'>'.$value.'</option>';
						$str.='</select>';
                           unset($key);unset($value);
						return $str;
				}catch (Exception $ex) {
						return '';
				}
			}
            function arrayToSelect_C($array,$match='',$idName,$Options=null) {
				try {
						$str='<select name="'.$idName.'" id="'.$idName.'" size="'.$Options["size"].'"  class="'.$Options["class"].'" style="'.$Options["style"].'"  onchange="'.$Options["onchange"].'">';
						if(!empty($Options["firstText"])) $str.='<option value="">'.$Options["firstText"].'</option>';
						if(count($array)>0)foreach($array as $key => $value) $str.='<option value="'.$value.'"'.(($match==$value)?" selected ":"").'>'.$value.'</option>';
						$str.='</select>';
                           unset($key);unset($value);
						return $str;
				}catch (Exception $ex) {
						return '';
				}
			}
			/*Recursive array
			******************************************************************/
			function recursiveArray($array=null,$parentid,$col=null,$space='',$trees=null) {
				if(is_array($array))
						foreach($array as $row) {
								if($row['parentid']==$parentid) {
										$tmp=array();
										if(is_array($col))
												foreach($col as $key => $value) {
														$tmp[$value]=$space.stripslashes($row[$value]);
										}
										$trees[]=$tmp;
										$trees=$this->recursiveArray($array,$row[0],$col,$space.'&nbsp;&nbsp;=>&nbsp;',$trees);
								}
				}
				return $trees;
			}
			/* Get metadata information
			-----------------------------------------------------------------*/
			function getMetaData($tableName,$type='') {
				try {
						$result=$this->doSQL("show full fields from ".$tableName);
						$flag=false;
						$i=1;
						while($field=$this->nextObject($result,0))
						{
							$fname=$field->Field;
							$fFullType=$field->Type;
							$fFullType=str_replace("unsigned",'',$fFullType);
							$fFullType=str_replace("zerofill",'',$fFullType);
							$fType=preg_replace("/[^a-z]/i",'',$fFullType);
							$fType=strtolower($fType);
							if(strstr($fType,"int")!='') $fType="int";
							elseif($fType=="double"||$fType=="float"||$fType=="decimal")$fType="real";
							elseif($fType=="varchar")$fType="string";
							elseif($fType=="text")$fType="blob";
							$fLength=preg_replace("/[^0-9]/",'',$fFullType);
							if(strtolower($fname)!="id") {
									$array1[]=$fname;
									$array2[$fname]=$fType;
									$array3[$fname]=$fLength;
							}
							$arrayComment[$fname]=$field->Comment;
							if(($field->Key)=="PRI"&&!$flag) {
									$array4["pk"]=$fname;
									$flag=true;
							}
							++$i;
						}
						$array4["table"]=$tableName;
						$array []=$array1;					//column name
						$array []=$array2; 					//data type
						$array []=$array3;        			//length field
						$array []=$array4;  				//extension(primary key, tablename)
						$array["Comment"]=$arrayComment; 	//get comment of field
                           unset($array1);unset($array2);unset($array3);unset($array4);
                           unset($flag);
                           unset($result);
                           unset($field);
                           unset($fname);
                           unset($fType);
                           unset($fFullType);
						return $array;
				}catch (Exception $ex) {
				}
			}
			
			//function creates page links
		function pagination($count, $href) {
			$output = '';
			if(!isset($_REQUEST["pageNumber"])) $_REQUEST["pageNumber"] = 1;
			if(PERPAGE_LIMIT != 0)
				$pages  = ceil($count/PERPAGE_LIMIT);

			//if pages exists after loop's lower limit
			if($pages>1) {
				if(($_REQUEST["pageNumber"]-3)>0) {
					$output = $output . '<li class="page-item"><a href="' . $href . 'pageNumber=1" class="page-link">1</a></li>';
				}
				if(($_REQUEST["pageNumber"]-3)>1) {
					$output = $output . '...';
				}

				//Loop for provides links for 2 pages before and after current page
				for($i=($_REQUEST["pageNumber"]-2); $i<=($_REQUEST["pageNumber"]+2); $i++)	{
					if($i<1) continue;
					if($i>$pages) break;
					if($_REQUEST["pageNumber"] == $i)
						$output = $output . '<li class="page-item active"><span id='.$i.' class="page-link">'.$i.'</span></li>';
					else				
						$output = $output . '<li class="page-item"><a href="' . $href . "pageNumber=".$i . '" class="page-link">'.$i.'</a></li>';
				}

				//if pages exists after loop's upper limit
				if(($pages-($_REQUEST["pageNumber"]+2))>1) {
					$output = $output . '...';
				}
				if(($pages-($_REQUEST["pageNumber"]+2))>0) {
					if($_REQUEST["pageNumber"] == $pages)
					$output = $output . '<li class="page-item active"><span id=' . ($pages) .' class="page-link">' . ($pages) .'</span></li>';
					else				
					$output = $output . '<li class="page-item"><a href="' . $href .  "pageNumber=" .($pages) .'" class="page-link">' . ($pages) .'</a></li>';
				}

			}
			return $output;
		}

		//function calculate total records count and trigger pagination function	
		function paginateResults($sql, $href) {
			$this->dbConnect();
			$result  = $this->doSQL($sql);
			$count   = mysqli_num_rows($result);
			$page_links = $this->pagination($count, $href);
			return $page_links;
		}
		
		// send mail
		  function sendmail($param,$mail) {
			global $arraySMTPSERVER;
			
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
			$mail->From = $param['EmailFrom'];
			$mail->FromName = $param['FromName'];
			$mail->Sender=$param['EmailFrom'];
			$mail->AddAddress($param['EmailTo'], $param['ToName']);
			$mail->AddReplyTo($param['ReplyTo'], $param['ReplyName']);
			$mail->SMTPDebug  = 1;
			$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
			
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
			/*printf("<pre>%s</pre>",print_r($mail,true));*/
			if ($mail->Send()) {
				$mail->ClearAllRecipients();
				$mail->clearReplyTos();
			  return true;
			}
			else {
			  return false;
			}
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
	}
?>