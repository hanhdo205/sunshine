<table border="1" cellpadding="0" cellspacing="0" style="border:solid #e7e8ef 3.0pt;font-size:10pt;font-family:Calibri" width="600">
			<tbody><tr style="border:#e7e8ef;padding:0 0 0 0">
				<td style="background-color:#465770;padding-left:15pt" colspan="2">
					<br>
					<img alt="Vietquoc" src="{{ logo }}" class="CToWUd"><br>
					<br>
				</td>
			</tr>
			<tr>
				<td width="25" style="border:white">
					&nbsp;
				</td>
				<td style="border:white">
					<br>
					<h1><span style="font-size:19.0pt;font-family:Verdana;color:black">{{ thankyou }}</span></h1>
					<br>
				</td>
			</tr>
			<tr>
				<td width="25" style="border:white">
					&nbsp;
				</td>
				<td style="border:white">
					<div style="color:#818181;font-size:10.5pt;font-family:Verdana"><span class="im">
						{{ body_message }}	

						<br>
						<br>
						{{ email_text }}: {{ email }}
						<br>						
						{{ password_text }}: {{ password }}
					   	<br>
						
					   <p style="text-align:center">
							<a href="{{ url }}" style="background-color:#41A3E4;color:#ffffff;padding:15px 30px;border-radius:3px;text-decoration:none" target="_blank">{{ active_text }}</a>
						</p>
						
						<br>
					   
					{{ signature_text }}	
					</span></div>
				</td>
			</tr>
			<tr>
				<td width="25" style="border:white">
					&nbsp;
				</td>
				<td style="border:white">
					<div style="color:#818181;font-size:9pt;font-family:Verdana">
						<br>
						<br>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="height:30pt;background-color:#e7e8ef;border:none">
					<center>{{ mail_footer_text }} <a href="{{ url }}" style="color:#5b9bd5" target="_blank">{{ url }}</a><br></center>
				</td>
			</tr>
		</tbody></table>