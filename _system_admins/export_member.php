<?php
include("index_table.php");
?>

<table width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="boxRedInside" colspan="5"><div class="boxRedInside">Export member</div></td></tr></tbody></table>
<div class="panelAction" id="panelAction" style="height: 400px;">

   <div style="float: left; text-align: left; margin-top: 10px;">
       <fieldset style="width: 600px">
       <legend style="color: #000080; padding-bottom: 10px"><b>Export member</b></legend>

    <span style="float: left; width:100px">Member</span>
    <select name="member_id" id="member_id" style="width:180px;" required>
  	<option value="">-- All -- </option>
      <?php
            $rst_member=$dbf->getDynamic("member","status=1","id asc");
            $toal_member = $dbf->totalRows($rst_member);
            if($toal_member >0)
            {

              while($rows_member= $dbf->nextdata($rst_member))
               {
                 $id_member            = $rows_member['id'];
                 $ma_id                = stripcslashes($rows_member['ma_id']);
                 $hovaten              = stripcslashes($rows_member['hovaten']);


                 echo '<option value="'.$id_member.'">'.$ma_id.'-'.$hovaten.'</option>';

               }
            }
        ?>
  </select>
        <div class="clear"></div>

       <br /><span style="float: left; width:100px">From date</span><input type="text" onfocus="fo(this)" onblur="lo(this)" maxlength="12" value="" id="tungay" name="tungay" >
      	  <script type="text/javascript">
      		$(function() {
      			$('#tungay').datepicker({
      				changeMonth: true,
      				changeYear: true,
      				dateFormat: 'dd-mm-yy'
      			});
      		});
      	  </script>

          To date<input type="text" onfocus="fo(this)" onblur="lo(this)" maxlength="12" value="<?php echo date("d-m-Y",time())?>" id="denngay" name="denngay">
      	  <script type="text/javascript">
      		$(function() {
      			$('#denngay').datepicker({
      				changeMonth: true,
      				changeYear: true,
      				dateFormat: 'dd-mm-yy'
      			});
      		});
      	  </script>

       <br/><br/><input type="button" name="cmdExport" id="cmdExport" value="Export" />
        </fieldset>


   </div>
    <div id="clear" style="clear: both"></div>
</div>

<script>
jQuery("#cmdExport").click(function() {
    if(jQuery("#member_id").val()=="")
    {
        alert("Please choose member !!");
    }else
    {
        jQuery('#frm').attr('action', 'export.php');
       jQuery( "#frm" ).submit();
    }
})

</script>


