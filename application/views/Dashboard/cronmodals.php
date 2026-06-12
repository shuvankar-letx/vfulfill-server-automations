<div class="modal fade" id="add_cron">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel">Add New Cron</h5>
				<!-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button> -->
			</div>
			<div class="modal-body">
				<form class="forms-sample" method="post" action="<?= base_url('crons/add_cron') ?>">
                    <div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Cron Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="cron_name" name="cron_name" placeholder="Cron Name">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Cron Log File Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="cron_log_file_name" name="cron_log_file_name" placeholder="Cron Log File Name">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Select Controller</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="add_cron_controller" name="add_cron_controller" style="padding: 1.125rem 1.375rem;">
							<option value="#" data-select2-id="Select_Controller">----- Select Controller ----- </option>
							<?php foreach($controllers as $controller){?>
								<option value="<?= $controller?>" data-select2-id="<?= $controller?>"><?= ucwords(strtolower(str_replace("_"," ",$controller)))?></option>
							<?php }
							?>
						
						</select>
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Select Function</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="add_cron_function_name" name="add_cron_function_name" style="padding: 1.125rem 1.375rem;">
						<option value="#" data-select2-id="Select_Controller">----- Select Function ----- </option>
						</select>
                      </div>
                    </div>

					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Schedule</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="add_cron_schedule" name="add_cron_schedule" style="padding: 1.125rem 1.375rem;">
						<option value="#" data-select2-id="Select_Controller">----- Select Schedule ----- </option>
						<option value="every_minute" data-select2-id="Select_Controller">Every Minute</option>
						<option value="hourly" data-select2-id="Select_Controller">Hourly</option>
						<option value="daily" data-select2-id="Select_Controller">Daily</option>
						<option value="weekly" data-select2-id="Select_Controller">Weekly</option>
						<option value="monthly" data-select2-id="Select_Controller">Monthly</option>
						
						</select>
                      </div>
                    </div>
					<div class="form-group row display-decision" id="minute_gap_div" style="display:none">
                      <label class="col-sm-4 col-form-label">Minute Gap</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="minute_gap" name="minute_gap" style="padding: 1.125rem 1.375rem;">
						<option value="1">Every Minute</option>
						<option value="10">Every 10 Minutes</option>
						<option value="15">Every 15 Minutes</option>
						<option value="20">Every 20 Minutes</option>
						<option value="30">Every 30 Minutes</option>
						<option value="40">Every 40 Minutes</option>
						<option value="45">Every 45 Minutes</option>
						<option value="50">Every 50 Minutes</option>
						</select>
                      </div>
                    </div>

					<div class="form-group row display-decision" id="hour_gap_div" style="display:none">
                      <label class="col-sm-4 col-form-label">Hour Gap</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="hour_gap" name="hour_gap" style="padding: 1.125rem 1.375rem;">
						<option value="1">Every 1 Hour</option>
						<option value="3">Every 3 Hours</option>
						<option value="6">Every 6 Hours</option>
						<option value="12">Every 12 Hours</option>
						<option value="18">Every 18 Hours</option>
						</select>
                      </div>
                    </div>


					<div class="form-group row display-decision" id="time" style="display:none">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Time</label>
                      <div class="col-sm-8 col-form-label">
                        <div class="input-group date">
                        <div class="input-group">
                          <input type="text" class="form-control form-control-sm" id="cron_time" name="cron_time">
                          <div class="input-group-addon input-group-append"><i class="ti-time input-group-text"></i>
                          </div>
                        </div>
						
						<script>

							flatpickr("#cron_time", {

								enableTime: true,

								noCalendar: true,

								dateFormat: "h:i K",
								time_24hr: false
							});

							</script>
                      </div>
                      </div>
                    </div>

					<div class="form-group row  display-decision" id="day" style="display:none">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Day</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="cron_day" name="cron_day" style="padding: 1.125rem 1.375rem;">
						<option value="#" data-select2-id="Select_Controller">----- Select Day ----- </option>
						<option value="sunday" data-select2-id="Select_Controller">Sunday</option>
						<option value="monday" data-select2-id="Select_Controller">Monday</option>
						<option value="tuesday" data-select2-id="Select_Controller">Tuesday</option>
						<option value="wednesday" data-select2-id="Select_Controller">Wednesday</option>
						<option value="thursday" data-select2-id="Select_Controller">Thursday</option>
						<option value="friday" data-select2-id="Select_Controller">Friday</option>
						<option value="saturday" data-select2-id="Select_Controller">Saturday</option>
						
						</select>
                      </div>
                    </div>
					<div class="form-group row  display-decision" id="day_of_the_month" style="display:none">
                      <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Day of the Month</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="cron_day_of_the_month" name="cron_day_of_the_month" style="padding: 1.125rem 1.375rem;">
						<option value="#" data-select2-id="Select_Controller">----- Select Day of the Month ----- </option>
						<?php 
							for($i = 1;$i<32;$i++){
								?>
								<option value="<?= $i?>" data-select2-id="Select_Controller"<?php if($i > 28) {echo "disabled";}?>><?= $i?> </option>
								<?php 
							}
						?>
						
						</select>
                      </div>
                    </div>
                    
                    
                    
                    <div class="modal-footer">
					<button type="submit" class="btn btn-outline-primary btn-icon-text">Submit</button>
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
					</div>
                  </form>
				</div>
				
			</div>
		</div>
	</div>

  <div class="modal fade" id="edit_cron">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel">Edit Cron</h5>
			</div>
			<div class="modal-body">
				<form class="forms-sample" method="post" action="<?= base_url('crons/edit_cron') ?>">
					<input type="hidden" id="edit_cron_id" name="edit_cron_id">
                    <div class="form-group row">
                      <label for="edit_cron_name" class="col-sm-4 col-form-label">Cron Name</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_cron_name" readonly style="background-color: #e9ecef;">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Cron Log File Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="edit_cron_log_file_name" name="edit_cron_log_file_name" placeholder="Cron Log File Name">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="edit_cron_command" class="col-sm-4 col-form-label">Command</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_cron_command" readonly style="background-color: #e9ecef;">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="edit_cron_schedule" class="col-sm-4 col-form-label">Schedule</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="edit_cron_schedule" name="edit_cron_schedule" style="padding: 1.125rem 1.375rem;">
						<option value="#" data-select2-id="Select_Controller">----- Select Schedule ----- </option>
						<option value="every_minute">Every Minute</option>
						<option value="hourly">Hourly</option>
						<option value="daily">Daily</option>
						<option value="weekly">Weekly</option>
						<option value="monthly">Monthly</option>
						</select>
                      </div>
					<div class="form-group row display-decision" id="edit_minute_gap_div" style="display:none">
                      <label class="col-sm-4 col-form-label">Minute Gap</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="edit_minute_gap" name="edit_minute_gap" style="padding: 1.125rem 1.375rem;">
						<option value="1">Every Minute</option>
						<option value="10">Every 10 Minutes</option>
						<option value="15">Every 15 Minutes</option>
						<option value="20">Every 20 Minutes</option>
						<option value="30">Every 30 Minutes</option>
						<option value="40">Every 40 Minutes</option>
						<option value="45">Every 45 Minutes</option>
						<option value="50">Every 50 Minutes</option>
						</select>
                      </div>
                    </div>

					<div class="form-group row display-decision" id="edit_hour_gap_div" style="display:none">
                      <label class="col-sm-4 col-form-label">Hour Gap</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="edit_hour_gap" name="edit_hour_gap" style="padding: 1.125rem 1.375rem;">
						<option value="1">Every 1 Hour</option>
						<option value="3">Every 3 Hours</option>
						<option value="6">Every 6 Hours</option>
						<option value="12">Every 12 Hours</option>
						<option value="18">Every 18 Hours</option>
						</select>
                      </div>
                    </div>

					<div class="form-group row display-decision" id="edit_time" style="display:none">
                      <label for="edit_cron_time" class="col-sm-4 col-form-label">Time</label>
                      <div class="col-sm-8 col-form-label">
                        <div class="input-group">
                          <input type="text" class="form-control form-control-sm" id="edit_cron_time" name="edit_cron_time">
                          <div class="input-group-addon input-group-append"><i class="ti-time input-group-text"></i>
                          </div>
                        </div>
                      </div>
                    </div>

					<div class="form-group row display-decision" id="edit_day" style="display:none">
                      <label for="edit_cron_day" class="col-sm-4 col-form-label">Day</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="edit_cron_day" name="edit_cron_day" style="padding: 1.125rem 1.375rem;">
						<option value="#">----- Select Day ----- </option>
						<option value="sunday">Sunday</option>
						<option value="monday">Monday</option>
						<option value="tuesday">Tuesday</option>
						<option value="wednesday">Wednesday</option>
						<option value="thursday">Thursday</option>
						<option value="friday">Friday</option>
						<option value="saturday">Saturday</option>
						</select>
                      </div>
                    </div>

					<div class="form-group row display-decision" id="edit_day_of_the_month" style="display:none">
                      <label for="edit_cron_day_of_the_month" class="col-sm-4 col-form-label">Day of the Month</label>
                      <div class="col-sm-8 col-form-label">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible" id="edit_cron_day_of_the_month" name="edit_cron_day_of_the_month" style="padding: 1.125rem 1.375rem;">
						<option value="#">----- Select Day of the Month ----- </option>
						<?php 
							for($i = 1;$i<32;$i++){
								?>
								<option value="<?= $i?>" <?php if($i > 28) {echo "disabled";}?>><?= $i?> </option>
								<?php 
							}
						?>
						</select>
                      </div>
                    </div>
                    
                    <div class="modal-footer">
					<button type="submit" class="btn btn-outline-primary btn-icon-text">Submit</button>
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
					</div>
                  </form>
				</div>
			</div>
		</div>
	</div>

	

	
