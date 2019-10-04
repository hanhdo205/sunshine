if($('.passenger_count').val()==""){
	$('.passenger_count').val('1');
}

$(document).on('click', '.accordion-icon, .custom-accordion', function() {
		$(this).find('.rotate').toggleClass('down')  ; 
	});
$(document).on('click', '#addMore', function() {
			var count = $('.patient_row').length + 1;
			$('.patient_count').val(count);
			if(show_price==true) {
				var clone = `<fieldset class="new_patient"><hr/>
							<a href="javascript:void(0);" class="remove"><i class="icon-close" aria-hidden="true"></i></a>
							<div class="form-group row patient_row">
							
							<label class="col-md-2 col-form-label">`+translate.patient_name+` <span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<select class="form-control select2-single patient_select select2" name="patient_name[]" data-number="`+ (count-1) +`" required oninvalid="this.setCustomValidity('`+translate.invalid+`')" onchange="setCustomValidity('')">`+ options +`</select>
								<input class="form-control patient_name_`+ (count-1) +`" name="name[]" type="hidden" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.gender+`</label>
							<div class="col-md-5 col-form-label">
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="male`+ (count-1) +`" type="radio" value="Male" name="sex[`+ (count-1) +`][]" checked>
								<label class="form-check-label" for="male`+ (count-1) +`">`+translate.male+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="female`+ (count-1) +`" type="radio" value="Female" name="sex[`+ (count-1) +`][]">
								<label class="form-check-label" for="female`+ (count-1) +`">`+translate.female+`</label>
								</div>
								
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.age+`</label>						  
							<div class="col-md-5">
								<input type="text" class="form-control patient_age_`+ (count-1) +`" name="age[]" required oninvalid="this.setCustomValidity('`+translate.invalid+`')" oninput="setCustomValidity('')">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.production+`</label>
							<div class="col-md-10 col-form-label">
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="CAM`+ (count-1) +`" type="radio" value="cad" name="work_tool[`+ (count-1) +`][]" checked>
									<label class="form-check-label" for="CAM`+ (count-1) +`">`+translate.cad_cam+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="zirconia`+ (count-1) +`" type="radio" value="zirconia" name="work_tool[`+ (count-1) +`][]" >
									<label class="form-check-label" for="zirconia`+ (count-1) +`">`+translate.zirconia+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="model`+ (count-1) +`" type="radio" value="3d" name="work_tool[`+ (count-1) +`][]" >
									<label class="form-check-label" for="model`+ (count-1) +`">`+translate.model+`</label>
								</div>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.shade+`</label>
							<div class="col-md-5 col-form-label">
								<select class="form-control" name="shade[`+ (count-1) +`][]" required oninvalid="this.setCustomValidity('`+translate.please_select_option+`')" oninput="setCustomValidity('')">
									<option value="None">`+translate.none+`</option>
									<option value="A1">A1</option>
									<option value="A2">A2</option>
									<option value="A3">A3</option>
									<option value="A3.5">A3.5</option>
									<option value="A4">A4</option>
									<option value="B1">B1</option>
									<option value="B2">B2</option>
									<option value="B3">B3</option>
									<option value="B4">B4</option>
									<option value="C1">C1</option>
									<option value="C2">C2</option>
									<option value="C3">C3</option>
									<option value="C4">C4</option>
									<option value="D2">D2</option>
									<option value="D3">D3</option>
									<option value="D4">D4</option>
									<option value="Other">`+translate.other+`</option>
								 </select>
								 </div>
							</div>
								 <div class="row custom-txt">
								<div class="col-sm-2"></div>
								<div class="col-sm-10">
										<div class="help-block"> `+translate.note_multi+`</div>

										</div>
								</div>
						<div class="form-group row">
								<label class="col-md-2 col-form-label">`+translate.position+` <span class="text-danger">*</span></label>						  
								<div class="col-md-10 position_check">
								<div class="input-group">
									<div class="col-form-label row top_position">
										<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt8" type="checkbox" value="18" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt8">18</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt7" type="checkbox"  value="17"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt7">17</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt6" type="checkbox"  value="16" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt6">16</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt5" type="checkbox"  value="15" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt5">15</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt4" type="checkbox"  value="14" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt4">14</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt3" type="checkbox"  value="13" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt3">13</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt2" type="checkbox"  value="12" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt2">12</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt1" type="checkbox"  value="11" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt1">11</label>
											</div>
										</div>
										
										<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt8" type="checkbox" value="21" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt8">21</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt7" type="checkbox"  value="22"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt7">22</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt6" type="checkbox"  value="23" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt6">23</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt5" type="checkbox"  value="24" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt5">24</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt4" type="checkbox"  value="25" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt4">25</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt3" type="checkbox"  value="26" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt3">26</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt2" type="checkbox"  value="27" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt2">27</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt1" type="checkbox"  value="28" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt1">28</label>
											</div>
										</div>
									
										
									
								<div class="col-sm-12 col-md-12 col-lg-12 border-shade">
									<div class=" border-shade-top" ></div>
								</div>
								</div>					
								
								 
								<div class="col-form-label row bottom_position">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb8" type="checkbox" value="48" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb8">48</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb7" type="checkbox"  value="47"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb7">47</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb6" type="checkbox"  value="46" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb6">46</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb5" type="checkbox"  value="45" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb5">45</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb4" type="checkbox"  value="44" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb4">44</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb3" type="checkbox"  value="43" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb3">43</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb2" type="checkbox"  value="42" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb2">42</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb1" type="checkbox"  value="41" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb1">41</label>
											</div>
									</div>
										
									<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb8" type="checkbox" value="31" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb8">31</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb7" type="checkbox"  value="32"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb7">32</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb6" type="checkbox"  value="33" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb6">33</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb5" type="checkbox"  value="34" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb5">34</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb4" type="checkbox"  value="35" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb4">35</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb3" type="checkbox"  value="36" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb3">36</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb2" type="checkbox"  value="37" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb2">37</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb1" type="checkbox"  value="38" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb1">38</label>
											</div>
									</div>
									<div class="col-sm-12 col-md-12 col-lg-12"> </div>
								</div>
							</div>
						</div>
						</div>
						

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.quantity+`</label>						  
							<div class="col-md-5">
								<label class="quantity_`+ count +`">0 `+translate.teeth+`</label>
								<input type="hidden" class="count_check_`+ count +`" name="quantity[]">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.total+`</label>						  
							<div class="col-md-5">
								<label class="subtotal_`+ count +`">0</label> `+translate.jpy+`
								<input type="hidden" class="input_subtotal_`+ count +`" name="subtotal[]">
								<input type="hidden" class="input_price_novat_`+ count +`" name="price_novat[]">
								<input type="hidden" class="input_vat_`+ count +`" name="singlevat[]">

								<input type="hidden" class="urgent_subtotal_`+ count +`" name="urgentsubtotal[]">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.desired_date+`</label>						  
							<div class="col-md-5 col-form-label">
								<div class="form-check mb-2">
									<input class="form-check-input delivery_time`+ count +`" id="normal`+ count +`" type="radio" value="normal" name="delivery_time[`+ (count-1) +`][]" checked>
									<label class="form-check-label" for="normal`+ count +`">`+translate.common+`</label>
								</div>
								<div class="calendar-wrap"><div id="desireddate`+ count +`" class="normal`+ count +` box`+ count +`"></div></div>
								<div class="input-group mt-2 mb-2 normal`+ count +` box`+ count +`">
										<span class="input-group-prepend">
										  <span class="input-group-text">
											<i class="fa fa-calendar"></i>
										  </span>
										</span>
										<input id="input_desireddate`+ count +`" class="form-control date" type="text" name="desireddate[]" autocomplete="off"/>
									</div>
								<div class="form-check">
									<div class="row">
										<div class="col-sm-4">
									<input class="form-check-input delivery_time`+ count +`" id="urgent`+ count +`" type="radio" value="urgent" name="delivery_time[`+ (count-1) +`][]">
									</div>
									<div class="help-block urgent_block customer-help urgent`+ count +` box`+ count +`">`+translate.extra_charge+`</div>
									
								</div>
								<label class="form-check-label customcheck" for="urgent`+ count +`">`+translate.urgent+`</label>
							
							</div>
																
						</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.remarks+`</label>						  
							<div class="col-md-5">
								<textarea class="form-control" rows="7" name="note[]"></textarea>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.attachement+` <span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<span class="input-group div-select-stlinput_<?php echo $i;?>"><input type="text" name="stlfile[`+ count +`]" class="stl_input_`+ count +` input full upload form-control" placeholder="`+translate.no_file+`" autocomplete="off" style="padding: 3px !important;background: #fff;" >
									<span class="input-group-append">
										<label for="stlinput_`+ count +`" class="btn btn-primary">`+translate.choose_file+`</label></span>
									</span>
									<em class="help-block"> `+translate.filetype+`</em>
									<input id="stlinput_`+ count +`" type="file" name="fileinput[]" style="visibility:hidden;">
							</div>
						</div>
						</fieldset>`;
			} else {
				var clone = `<fieldset class="new_patient"><hr/>
							<a href="javascript:void(0);" class="remove"><i class="icon-close" aria-hidden="true"></i></a>
							<div class="form-group row patient_row">
							
							<label class="col-md-2 col-form-label">`+translate.patient_name+` <span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<select class="form-control select2-single patient_select select2" name="patient_name[]" data-number="`+ (count-1) +`" required oninvalid="this.setCustomValidity('`+translate.invalid+`')" onchange="setCustomValidity('')">`+ options +`</select>
								<input class="form-control patient_name_`+ (count-1) +`" name="name[]" type="hidden" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.gender+`</label>
							<div class="col-md-5 col-form-label">
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="male`+ (count-1) +`" type="radio" value="Male" name="sex[`+ (count-1) +`][]" checked>
								<label class="form-check-label" for="male`+ (count-1) +`">`+translate.male+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="female`+ (count-1) +`" type="radio" value="Female" name="sex[`+ (count-1) +`][]">
								<label class="form-check-label" for="female`+ (count-1) +`">`+translate.female+`</label>
								</div>
								
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.age+`</label>						  
							<div class="col-md-5">
								<input type="text" class="form-control patient_age_`+ (count-1) +`" name="age[]" required oninvalid="this.setCustomValidity('`+translate.invalid+`')" oninput="setCustomValidity('')">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.production+`</label>
							<div class="col-md-10 col-form-label">
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="CAM`+ (count-1) +`" type="radio" value="cad" name="work_tool[`+ (count-1) +`][]" checked>
									<label class="form-check-label" for="CAM`+ (count-1) +`">`+translate.cad_cam+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="zirconia`+ (count-1) +`" type="radio" value="zirconia" name="work_tool[`+ (count-1) +`][]" >
									<label class="form-check-label" for="zirconia`+ (count-1) +`">`+translate.zirconia+`</label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="model`+ (count-1) +`" type="radio" value="3d" name="work_tool[`+ (count-1) +`][]" >
									<label class="form-check-label" for="model`+ (count-1) +`">`+translate.model+`</label>
								</div>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.shade+`</label>
							<div class="col-md-5 col-form-label">
								<select class="form-control" name="shade[`+ (count-1) +`][]" required oninvalid="this.setCustomValidity('`+translate.please_select_option+`')" oninput="setCustomValidity('')">
									<option value="None">`+translate.none+`</option>
									<option value="A1">A1</option>
									<option value="A2">A2</option>
									<option value="A3">A3</option>
									<option value="A3.5">A3.5</option>
									<option value="A4">A4</option>
									<option value="B1">B1</option>
									<option value="B2">B2</option>
									<option value="B3">B3</option>
									<option value="B4">B4</option>
									<option value="C1">C1</option>
									<option value="C2">C2</option>
									<option value="C3">C3</option>
									<option value="C4">C4</option>
									<option value="D2">D2</option>
									<option value="D3">D3</option>
									<option value="D4">D4</option>
									<option value="Other">`+translate.other+`</option>
								 </select>
								<div class="help-block"> `+translate.note_multi+`</div>
								</div>
						</div>
						<div class="form-group row">
								<label class="col-md-2 col-form-label">`+translate.position+` <span class="text-danger">*</span></label>						  
								<div class="col-md-10 position_check">
								<div class="input-group">
									<div class="col-form-label row top_position">
										<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt8" type="checkbox" value="18" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt8">18</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt7" type="checkbox"  value="17"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt7">17</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt6" type="checkbox"  value="16" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt6">16</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt5" type="checkbox"  value="15" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt5">15</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt4" type="checkbox"  value="14" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt4">14</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt3" type="checkbox"  value="13" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt3">13</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt2" type="checkbox"  value="12" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt2">12</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrt1" type="checkbox"  value="11" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrt1">11</label>
											</div>
										</div>
										
										<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt8" type="checkbox" value="21" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt8">21</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt7" type="checkbox"  value="22"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt7">22</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt6" type="checkbox"  value="23" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt6">23</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt5" type="checkbox"  value="24" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt5">24</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt4" type="checkbox"  value="25" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt4">25</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt3" type="checkbox"  value="26" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt3">26</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt2" type="checkbox"  value="27" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt2">27</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlt1" type="checkbox"  value="28" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlt1">28</label>
											</div>
										</div>
									
										
									
								<div class="col-sm-12 col-md-12 col-lg-12 border-shade">
									<div class=" border-shade-top" ></div>
								</div>
								</div>					
								
								 
								<div class="col-form-label row bottom_position">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb8" type="checkbox" value="48" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb8">48</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb7" type="checkbox"  value="47"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb7">47</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb6" type="checkbox"  value="46" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb6">46</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb5" type="checkbox"  value="45" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb5">45</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb4" type="checkbox"  value="44" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb4">44</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb3" type="checkbox"  value="43" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb3">43</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb2" type="checkbox"  value="42" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb2">42</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxrb1" type="checkbox"  value="41" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxrb1">41</label>
											</div>
									</div>
										
									<div class="col-6 col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb8" type="checkbox" value="31" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb8">31</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb7" type="checkbox"  value="32"  name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb7">32</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb6" type="checkbox"  value="33" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb6">33</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb5" type="checkbox"  value="34" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb5">34</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb4" type="checkbox"  value="35" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb4">35</label>
											</div>
										
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb3" type="checkbox"  value="36" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb3">36</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb2" type="checkbox"  value="37" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb2">37</label>
											</div>
											<div class="form-check form-check-inline mr-1">
												<input class="form-check-input check_position_1" id="inline-`+ count +`-checkboxlb1" type="checkbox"  value="38" name="check[`+ (count-1) +`][]">
												<label class="form-check-label" for="inline-`+ count +`-checkboxlb1">38</label>
											</div>
									</div>
									<div class="col-sm-12 col-md-12 col-lg-12"> </div>
								</div>
							</div>
						</div>
						</div>

								<input type="hidden" class="count_check_`+ count +`" name="quantity[]">
							
								<input type="hidden" class="input_subtotal_`+ count +`" name="subtotal[]">
								<input type="hidden" class="input_price_novat_`+ count +`" name="price_novat[]">
								<input type="hidden" class="input_vat_`+ count +`" name="singlevat[]">

								<input type="hidden" class="urgent_subtotal_`+ count +`" name="urgentsubtotal[]">
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.desired_date+`</label>						  
							<div class="col-md-5 col-form-label">
								<div class="form-check mb-2">
									<input class="form-check-input delivery_time`+ count +`" id="normal`+ count +`" type="radio" value="normal" name="delivery_time[`+ (count-1) +`][]" checked>
									<label class="form-check-label" for="normal`+ count +`">`+translate.common+`</label>
								</div>
								<div class="calendar-wrap"><div id="desireddate`+ count +`" class="normal`+ count +` box`+ count +`"></div></div>
								<div class="input-group mt-2 mb-2 normal`+ count +` box`+ count +`">
										<span class="input-group-prepend">
										  <span class="input-group-text">
											<i class="fa fa-calendar"></i>
										  </span>
										</span>
										<input id="input_desireddate`+ count +`" class="form-control date" type="text" name="desireddate[]" autocomplete="off"/>
									</div>
								<div class="form-check">
									<div class="row">
										<div class="col-sm-4">
									<input class="form-check-input delivery_time`+ count +`" id="urgent`+ count +`" type="radio" value="urgent" name="delivery_time[`+ (count-1) +`][]">
									</div>
									<div class="help-block urgent_block customer-help urgent`+ count +` box`+ count +`">`+translate.extra_charge+`</div>
									
								</div>
								<label class="form-check-label customcheck" for="urgent`+ count +`">`+translate.urgent+`</label>
							
							</div>
																
						</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.remarks+`</label>						  
							<div class="col-md-5">
								<textarea class="form-control" rows="7" name="note[]"></textarea>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label">`+translate.attachement+` <span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<span class="input-group div-select-stlinput_<?php echo $i;?>"><input type="text" name="stlfile[`+ count +`]" class="stl_input_`+ count +` input full upload form-control" placeholder="`+translate.no_file+`" autocomplete="off" style="padding: 3px !important;background: #fff;" >
									<span class="input-group-append">
										<label for="stlinput_`+ count +`" class="btn btn-primary">`+translate.choose_file+`</label></span>
									</span>
									<em class="help-block"> `+translate.filetype+`</em>
									<input id="stlinput_`+ count +`" type="file" name="fileinput[]" style="visibility:hidden;">
							</div>
						</div>
						</fieldset>`;
			}			
			$(clone).appendTo('.card-body');
			if(allow_tag==true) {
			$('.patient_select').select2({
				  theme: 'bootstrap',
				  placeholder: translate.enter_patient,
				  tags: true,
				  createTag: function (params) {
					return {
					  id: params.term,
					  text: params.term,
					  newOption: true
					}
				  },
				  language: {
					   noResults: function(){
						   return translate.enter_patient;
					   }
				   },
					escapeMarkup: function (markup) {
						return markup;
					}
				});
			} else {
				$('.patient_select').select2({
				  theme: 'bootstrap',
				  placeholder: translate.choose_patient,
				  language: {
					   noResults: function(){
						   return translate.no_patient_found + "&nbsp;<a href='new-patient.aspx' class='btn btn-sm btn-danger'>"+translate.add_new+"</a>";
					   }
				   },
					escapeMarkup: function (markup) {
						return markup;
					}
				});
			}
				var d = new Date();
				$('#desireddate' + count).datepicker({
					inline: true,
					altField: '#input_desireddate'  + count,
					dateFormat: "yy/mm/dd",
					minDate: d.getHours() >= 15 ? 1 : 0
				});
				$('#input_desireddate'  + count).change(function(){
					$('#desireddate'  + count).datepicker('setDate', $(this).val());
				});
				//$('#desireddate' + count).datepicker().datepicker("setDate", d);			
				var fileSelectEle = document.getElementById('stlinput_' + count);
						fileSelectEle.onchange = function ()
						{
							if(fileSelectEle.value.length == 0) {
								$('.stl_input_' + count).val('');
							} else {
								$('.stl_input_' + count).val(fileSelectEle.files[0].name);
								$('.file_field_error_' + count).remove();
							}
						}
			if(d.getHours() < 15) {
							$('#urgent' + count).attr('disabled',true);
						} else {
							$('#urgent' + count).attr('disabled',false);
						}
						
			$('.delivery_time' + count).click(function(){
				var inputValue = $(this).attr("value");
				var targetBox = $("." + inputValue + '' + count);
				$('.box' + count).not(targetBox).hide();
				$(targetBox).show();
				if(inputValue=='urgent') {
					var numberOfChecked = $('.count_check_' + count).val();
					console.log(numberOfChecked);
					$('.urgent_subtotal_' + count).val(numberOfChecked*urgent_price_inc_vat);
					$('.urgent_price_' + count).val(numberOfChecked*urgent_price_inc_vat);
				} else {
					$('.urgent_subtotal_' + count).val(0);
					$('.urgent_price_' + count).val(0);
				}
			});
			
			$(document).on('change', '[id^=inline-'+ count +'-checkbox]', function() {
				var numberOfChecked = $('[id^=inline-'+ count +'-checkbox]').filter(':checked').length
				var totalCheckboxes = $('input:checkbox').length;
				var numberNotChecked = $('input:checkbox:not(":checked")').length;
				var price = h(numberOfChecked*price_inc_vat);
				$('.subtotal_'+ count).text(price);
				$('.input_price_novat_'+ count).val(numberOfChecked*get_price);
				$('.input_vat_'+ count).val(numberOfChecked*get_vat*get_price/100);
				$('.input_subtotal_'+ count).val(numberOfChecked*price_inc_vat);
				$('.quantity_'+ count).text(numberOfChecked + ' ' + translate.teeth);
				$('.count_check_'+ count).val(numberOfChecked);
			});
	});
	
/*$(document).on('change', '[id^=inline-1-checkbox]', function() {
	var numberOfChecked = $('[id^=inline-1-checkbox]').filter(':checked').length
	var totalCheckboxes = $('input:checkbox').length;
	var numberNotChecked = $('input:checkbox:not(":checked")').length;
	var get_price = parseInt($('.get_price').val());
	var price = h(numberOfChecked*get_price);
	$('.subtotal_1').text(price + '円');
	$('.input_subtotal_1').val(numberOfChecked*get_price);
	$('.quantity_1').text(numberOfChecked);
	$('.count_check_1').val(numberOfChecked);
});*/

$(document).on('click', '.remove', function() {
         $(this).closest('fieldset').remove();
		 var count = $('.new_patient').length;
			$('.patient_count').val(count - 1);
	});

function h(a, n, t, e) {
	a = (a + "").replace(/[^0-9+\-Ee.]/g, "");
	var o, i, h, v = isFinite(+a) ? +a : 0,
		l = isFinite(+n) ? Math.abs(n) : 0,
		r = void 0 === e ? "," : e,
		d = void 0 === t ? "." : t,
		c = "";
	return 3 < (c = (l ? (o = v, i = l, h = Math.pow(10, i), "" + Math.round(o * h) / h) : "" + Math.round(v)).split("."))[0].length && (c[0] = c[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, r)), (c[1] || "").length < l && (c[1] = c[1] || "", c[1] += new Array(l - c[1].length + 1).join("0")), c.join(d)
}

// On customer select
	$(document).on('change', '#select_customer', function () {
		var customer_id = this.value;
		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: 'select_customer',
				customer_id: customer_id,
			},
			success: function(response){
				if(response['status']==1) {
					$('.get_price').val(response['get_price']);
					$('.urgent_get_price').val(response['urgent_get_price']);
					$('.urgent_price_inc_vat').val(response['urgent_price_inc_vat']);
					$('.urgent_block').text(response['urgent_box']);
					translate.extra_charge = response['urgent_box'];
					
					get_price = parseInt(response['get_price']);
					price_inc_vat = get_price + (get_vat*get_price/100);
					
					urgent_get_price = parseInt(response['urgent_get_price']);
					urgent_price_inc_vat = (price_inc_vat*urgent_get_price)/100;
					
				}
			}
		});
	})
	
// On patient select
	$(document).on('change', '.patient_select', function () {
		var patient_id = this.value;
		var patient_no = parseInt($(this).attr('data-number'));
		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: 'select_patient',
				patient_id: patient_id
			},
			success: function(response){
				if(response['status']==1) {
					$('.patient_name_' + patient_no).val(response['hovaten']);
					$('.patient_age_' + patient_no).val(response['age']);
					$('#'+response['gender']+''+patient_no).prop('checked',true);
				} else if(response['status']==2) {
					$('.patient_name_' + patient_no).val(response['hovaten']);
				}
			}
		});
	})


$(document).on('click', '.hide_error', function() {
       $('.has_error.field_message').toggle();
		$('.card-body').find('.has_error').removeClass('has_error');
});

$(document).ready(function() {
	
	// DataTable Booking History
	if($('#datatable').hasClass('datatable')) {
		fetch_data('no');
		function fetch_data(is_date_search, start_date='', end_date='', search='') {
			var dataTable = $('#datatable').DataTable( {
				"sPaginationType": "simple_numbers",
				"bFilter": false,
				"language":
					{
						 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/"+datatables_language+".json"
					},
				"columnDefs": [ {
					"targets": 'no-sort',
					"orderable": false,
				} ],
				"processing": true,
				"serverSide": true,
				"order" : [],
				"ajax":{
					url: ajax_url,
					type: 'post',
					data: {
						action: 'booking_history',
						is_date_search: is_date_search,
						start_date: start_date,
						end_date: end_date,
						search: search
					},
					
				}
			} );
		}

		$('#daterangeButton').on( 'keyup click', function () {
			var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
			var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
			$('#mySearchText').val('');
			var search_type = $("input[name='search_type']:checked").val();
			if(start_date != "" && end_date != ""){
			   $('#datatable').DataTable().destroy();
			   fetch_data(search_type, start_date, end_date);
			  }else{
			   alert("Both Date is Required");
			  }
		  } );
		  
		$('#mySearchButton').on( 'keyup click', function () {
			//dataTable.search($('#mySearchText').val()).draw();
			var search = $('#mySearchText').val();
			 $('#datatable').DataTable().destroy();
			   fetch_data('no', '', '', search);
		  } );

		// Extend dataTables search
		/*$('#daterange').on('apply.daterangepicker', function(ev, picker) {
			//console.log(datearr);
			$.fn.dataTable.ext.search.push(
			  function(settings, data, dataIndex) {
				var min = picker.startDate.format('YYYY/MM/DD 00:00');
				var max = picker.endDate.format('YYYY/MM/DD 23:59');
				//console.log(min);
				var createdAt = data[0] || 0; // Our date column in the table
				
				if (
				  (min == "" || max == "") ||
				  (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))
				) {
				  return true;
				}
				return false;
			  }
			);
			$(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
			table.draw();
		});*/
	}
});
