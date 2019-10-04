<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
function checksubmit(){
  if($("#keyword").val()=="")
  {
    alert("Vui lòng nhập từ khóa tìm kiếm !");
    return false;
  }
  return true;
}
/*]]>*/
</script>
<form name="frmSearch" action="search.html" method="post" onsubmit="return checksubmit();">
<input name="keyword" id="keyword" type="text" placeholder="MãID hoặc Họ và tên" value="<?php echo $_SESSION["search"];?>" />
<input type="submit" name="cmdSearch" value="Tìm kiếm" />
</form>