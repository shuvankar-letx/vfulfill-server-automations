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
				<form class="forms-sample">
                    <div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Cron Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="cron_name" name="cron_name" placeholder="Cron Name">
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
                    
                    
                    
                    <div class="modal-footer">
					<button type="button" class="btn btn-outline-primary btn-icon-text">Submit</button>
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
				<!-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button> -->
			</div>
			<div class="modal-body">
				<form class="forms-sample">
                    <div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Cron Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="cron_name" name="cron_name" placeholder="Cron Name">
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Select Controller</label>
                      <div class="col-sm-9">
                        <select class="form-control js-example-basic-single w-100 select2-hidden-accessible">
						<option value="AL" data-select2-id="3">Alabama</option>
						<option value="WY" data-select2-id="18">Wyoming</option>
						<option value="AM" data-select2-id="19">America</option>
						<option value="CA" data-select2-id="20">Canada</option>
						<option value="RU" data-select2-id="21">Russia</option>
						</select>
                      </div>
                    </div>
					<div class="form-group row">
                      <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Select Controller</label>
                      <div class="col-sm-9">
                        <select class="form-control">
						<option value="AL" data-select2-id="3">Alabama</option>
						<option value="WY" data-select2-id="18">Wyoming</option>
						<option value="AM" data-select2-id="19">America</option>
						<option value="CA" data-select2-id="20">Canada</option>
						<option value="RU" data-select2-id="21">Russia</option>
						</select>
                      </div>
                    </div>
                    
                    
                    
                    <div class="modal-footer">
					<button type="button" class="btn btn-outline-primary btn-icon-text">Submit</button>
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
					</div>
                  </form>
				</div>
				
			</div>
		</div>
	</div>

	
