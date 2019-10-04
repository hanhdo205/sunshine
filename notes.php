<?php
date_default_timezone_set('Asia/Bangkok');
include ('class/class.BUSINESSLOGIC.php');
$dbf = new BUSINESSLOGIC();
$info2 = $dbf->getConfig();
?>
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet"  type="text/css" href="/_system_admins/themes/theme_default/style/style.pack.css"/>
<style type="text/css">
<!--
body,html {
    background: none;
    font-size: 12px;

}
#mainTable{
    width: 99%;
}

.block {
    background: #fff;
    text-align: left; 
}

-->
</style>
 <div class="block">
    <table id="mainTable" cellpadding="1" cellspacing="1">
       <thead>
          <tr role="row">
             <th class="titleBottom" colspan="3" align="left"><span style="color:red"><img src="icon-loudspeaker.png" alt="" width="50" height="45" align="absmiddle">Thông báo :</span></th>
          </tr>
       </thead>

       <tbody>

       <tr class="cell2">
            <td colspan="3" class="itemText" style="text-align: justify"><?php echo $info2["announcement"]?></td>
       </tr>
       </tbody>
    </table>
</div>

