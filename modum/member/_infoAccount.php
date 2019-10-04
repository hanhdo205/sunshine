<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>
<?php
date_default_timezone_set('Asia/Bangkok');
if($_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{

  $currentDate = date("d-m-Y",time());
  $now = date("d-m-Y",time());
  $now = strtotime($now);
  $your_date = date("d-m-Y",$rowgetInfo["datecreated"]);
  
 $your_date = strtotime($your_date);
 $datediff = $now - $your_date;
 $date_re = floor($datediff/(60*60*24));
 $date_re2 = $date_re;
 $date_re++;
 $no = 1;
 $date_start = 0;
 $total_daily_wallet = 0;
 $total_income_direct = 0;
 $total_income_indirect = 0;
			  
?>	

<script>
function showTooltip(elem, msg) {
    elem.setAttribute('class', 'btn btn-default btn-copy tooltipped tooltipped-s');
    elem.setAttribute('aria-label', msg);
}

function fallbackMessage(action) {
    var actionMsg = '';
    var actionKey = (action === 'cut' ? 'X' : 'C');
    if (/iPhone|iPad/i.test(navigator.userAgent)) {
        actionMsg = 'No support :(';
    } else if (/Mac/i.test(navigator.userAgent)) {
        actionMsg = 'Press ⌘-' + actionKey + ' to ' + action;
    } else {
        actionMsg = 'Press Ctrl-' + actionKey + ' to ' + action;
    }
    return actionMsg;
}
var clipboardDemos = new Clipboard('.btn-copy');
clipboardDemos.on('success', function(e) {
    e.clearSelection();
    showTooltip(e.trigger, 'Copied!');
});
clipboardDemos.on('error', function(e) {
    showTooltip(e.trigger, fallbackMessage(e.action));
});

function clearTooltip(e) {
    e.currentTarget.setAttribute('class', 'btn btn-default btn-copy');
    e.currentTarget.removeAttribute('aria-label');
}
var btns = document.querySelectorAll('.btn-copy');
for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener('mouseleave', clearTooltip);
    btns[i].addEventListener('blur', clearTooltip);
}

</script>
    <?php include("_important_note.php");?>
	<div class="row">					
		<div class="card-body col-lg-12" style="padding:0px;">
			<div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
				<span class="badge badge-pill badge-primary">Your Personal Referral Link</span>
				<div class="row">
				<p class="url" id="url_ref" style="line-height: 120%; padding: 10px 15px 5px;float:left; border: 1px solid #ccc;"><?php echo HOST?>register.aspx?ref=<?php echo $rowgetInfo["tendangnhap"];?></p>	
                <button style="float:left; margin-left:10px;" class="btn btn-default btn-copy" type="button" data-clipboard-target="#url_ref"><i class="fa fa-clipboard" aria-hidden="true"></i></button></span>				
				</div>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		</div>
	</div>
	
    <div class="row panel-body">
	    <div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none;cursor: pointer" onClick="window.location.href='/history-investment.aspx'">
			  <div class="card-header" style="border-bottom:0px">
				Total Investment			
			  </div>
			  <div class="card-body">
				    <div class="half pull-left l-s-t-30">
						<!-- BOX -->
						<div class="box blue-1">
							</p><span style="font-size:26px;">$<?php echo number_format($totalInvestment,0);?></span></p>
							
						</div>
						<!-- /BOX -->
					</div>		
				
			  </div>
			</div>
	    </div> <!-- End item !-->
		
	    <div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none;cursor: pointer" onClick="window.location.href='/daily_income_history.aspx'">
			  <div class="card-header" style="border-bottom:0px">
				Principal & Interest
			  </div>
			  <div class="card-body">
				<p><span style="font-size:26px;">$<?php echo number_format($commission,0);?></span></p>				
			  </div>
			</div>
	    </div> <!-- End item !-->		
		
		<div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none;cursor: pointer" onClick="window.location.href='/withdraw.aspx'">
			  <div class="card-header" style="border-bottom:0px">
				Wallet		
			  </div>
			  <div class="card-body">
				<p><span style="font-size:26px;">$<?php echo number_format($total_price_have,0);?> </p>
			 <div class="au-card au-card--bg-blue au-card-top-countries m-b-30" style="display:none;">              
			 			  
			  </div>
			  </div>
			</div>
	    </div> <!-- End item !-->
		
		  <div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none;" >
			  <div class="card-header" style="border-bottom:0px">
				DMCC Bonus			
			  </div>
			  
			  <div class="card-body">				
				<div class="box blue-2 p-10-20">
					<p><span style="font-size:26px;"><?php echo number_format($dmcc_bonus,0);?></span></p>
			  </div>
			</div>
	    </div> <!-- End item !-->
	 </div>
	 
	 <div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none; cursor: pointer;min-height:176px" onClick="window.location.href='/bonus_history.aspx'">
			  <div class="card-header" style="border-bottom:0px;">
				Total Bonus			
			  </div>
			  <div class="card-body">
				    <div class="half pull-left l-s-t-30">
						<!-- BOX -->
						<div class="box blue-1">
							<p><span style="font-size:26px;">$<?php echo number_format(($total_income_direct+$total_income_indirect+$leadership_bonus),0);?></span></p>							
						</div>
						<!-- /BOX -->
					</div>		
				
			  </div>
			</div>
	    </div> <!-- End item !-->
		
		<div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none; min-height:176px">
			  <div class="card-header" style="border-bottom:0px">
				Leadership Bonus		
			  </div>
			  <div class="card-body">
				    <div class="half pull-left l-s-t-30">
						<!-- BOX -->
						<div class="box blue-1">
							<p><span style="font-size:26px;">$<?php echo number_format($leadership_bonus,0);?></span></p>							
						</div>
						<!-- /BOX -->
					</div>		
				
			  </div>
			</div>
	    </div> <!-- End item !-->
		
		<div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none; min-height:176px">
			  <div class="card-header" style="border-bottom:0px">
				Total Income			
			  </div>
			  <div class="card-body">
				    <div class="half pull-left l-s-t-30">
						<!-- BOX -->
						<div class="box blue-1">							
							<p><span style="font-size:26px;"><span> $<?php echo number_format(($total_income_direct+$total_income_indirect+$commission),0);?><br><span style="font-size:18px">Profit Statitics <?php echo round($percent_income,2) ?><sup>%</sup> No. <?php echo $number_invest;?> </span></p>							
						</div>
						<!-- /BOX -->
					</div>		
				
			  </div>
			</div>
	    </div> <!-- End item !-->
		
		<div class="col-lg-3 nopadding">
			<div class="card" style="margin-bottom:0px; background:none; min-height:176px">
			  <div class="card-header" style="border-bottom:0px">
					&nbsp;	
			  </div>
			  <div class="card-body">
				    <div class="half pull-left l-s-t-30">
						<!-- BOX -->
						<div class="box blue-1">
							</p><span style="font-size:26px;"><strong>coming soon</strong></span></p>							
						</div>
						<!-- /BOX -->
					</div>		
				
			  </div>
			</div>
	    </div> <!-- End item !-->
		
		</div>
		<div class="row">		
		</div>
	 
      <!-- /# card --> 
<?php include("inc/footer.php");?>
<?php
}
?>
