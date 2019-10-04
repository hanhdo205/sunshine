<div class="row">

<div class="card">
	<div class="card-header">
		<strong class="card-title">Two-Factor Authentication</strong>
	</div>
	<div class="card-body">
        
		
       
            <p>Greatly increase security by requiring both your password and another form of authentication.</p>

            <div class="alert alert-danger alert-dismissible text-center" style="text-transform: uppercase">
                <h4>STATUS: <b>False</b></h4>
            </div>
            <div class="box-2fa">
                <div class="form-group">
                        <h4>
                            How to Enable Two-Factor Authentication
                        </h4>
                        <div class="break"></div>
                        <div class="form-group">
                            <h4>1. Download Google Authenticator on your mobile device</h4>
                            <div class="text-center">
                                <img src="<?php echo HOST?>style/images/google-authenticator.png">
                                <p>Google Authenticator</p>
                                <p>
                                    <a target="_blank" href="http://www.apple.com/">
                                        <img src="<?php echo HOST?>style/images/download-apple-store.png">
                                    </a>
                                    <a target="_blank" href="https://play.google.com/store">
                                        <img src="<?php echo HOST?>style/images/download-google-store.png">
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="break"></div>
                        <div class="form-group">
                            <h4>2. Use Google Authenticator to scan the QRCode</h4>
                            <div class="text-center">
                                <img src="http://chart.googleapis.com/chart?cht=qr&amp;chs=300x300&amp;chl=otpauth%3A%2F%2Ftotp%2Fannavu%3Fsecret%3DMFXG4YLWOVIFESKWIFKEK23FPFADA%26issuer%3Dtruemining.world">
                            </div>
                        </div>
                        <div class="form-group">
                            <h4>3. Back up your Secret Key.</h4>
                            <div class="text-center">
                                <p>Reseting your two - factor authentication requires opening a support ticket and may take up to 48 hours to address.</p>
                                <div class="input-group">
                                    <input class="form-control" id="txt_SetupCode" value="MFXG4YLWOVIFESKWIFKEK23FPFADA" readonly="" type="text">
                                    <a class="input-group-addon" onclick="Copy('txt_SetupCode')"><i class="fa fa-copy"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="break"></div>
						
						<div class="form-group">
								<form action="/2fa" method="post" novalidate="novalidate">
								<input name="__RequestVerificationToken" value="7eoJgC_K-x-zcJ0B_rAusg_791Z-3JDG1TXRz26gdZEE90bMkReqBKUTYHOaUFTY4OsEOzKS8kDjaAGlEt3T0b-CSd01tObyB_HGXYBfbSk1" type="hidden">                                <h4>4. Enter the 6 digit authentication code provided by Google Authenticator</h4>
								<label>Authentication Code</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="AuthenticatorCode" data-valmsg-replace="true"></span></span>
								<div class="text-center">
									<p>
										<input autocomplete="off" class="form-control" data-val="true" data-val-regex="Your authenticator code is a 6 digit number" data-val-regex-pattern="^[0-9]{6}$" data-val-required="Please complete Authentication Code" id="AuthenticatorCode" name="AuthenticatorCode" placeholder="Input your 6-digit authenticator code" value="" type="text">
									</p>
								</div>
								<br>
								<div class="form-group text-center">
									<button type="submit" class="btn btn-primary btn-lg btn-flat">Enable 2FA</button>
								</div>
								<br>
								<br>
							</form>                    
						</div>
                </div>
            </div>
</div>
</div>
</div>
    