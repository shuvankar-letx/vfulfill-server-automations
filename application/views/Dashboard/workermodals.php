<div class="modal fade" id="add_worker">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel">Add New Worker</h5>
			</div>
			<div class="modal-body">
				<form class="forms-sample" method="post" action="<?= base_url('workers/add_worker') ?>">
                    <div class="form-group row mb-3">
                      <label for="worker_name" class="col-sm-4 col-form-label">Worker Name</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="worker_name" name="worker_name" placeholder="Worker Name">
                      </div>
                    </div>
					<div class="form-group row mb-3">
                      <label for="add_worker_controller" class="col-sm-4 col-form-label">Select Controller</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="add_worker_controller" name="controller">
							<option value="#">----- Select Controller ----- </option>
							<?php foreach($controllers as $controller){?>
								<option value="<?= $controller?>"><?= ucwords(strtolower(str_replace("_"," ",$controller)))?></option>
							<?php } ?>
						</select>
                      </div>
                    </div>
					<div class="form-group row mb-3">
                      <label for="add_worker_function" class="col-sm-4 col-form-label">Select Function</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="add_worker_function" name="controller_function">
							<option value="#">----- Select Function ----- </option>
						</select>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <label for="error_logfile_path" class="col-sm-4 col-form-label">Error Log Path</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="error_logfile_path" name="error_logfile_path" placeholder="/var/log/worker.err.log">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <label for="stdout_logfile_path" class="col-sm-4 col-form-label">Stdout Log Path</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="stdout_logfile_path" name="stdout_logfile_path" placeholder="/var/log/worker.out.log">
                      </div>
                    </div>
                    <div class="form-group row mb-3 align-items-center">
                      <label class="col-sm-4 col-form-label">Autostart</label>
                      <div class="col-sm-8">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="autostart" id="autostart" value="1">
                        </div>
                      </div>
                    </div>
                    <div class="form-group row mb-3 align-items-center">
                      <label class="col-sm-4 col-form-label">Autorestart</label>
                      <div class="col-sm-8">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="autorestart" id="autorestart" value="1" checked>
                        </div>
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

  <div class="modal fade" id="edit_worker">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel">Edit Worker</h5>
			</div>
			<div class="modal-body">
				<form class="forms-sample" method="post" action="<?= base_url('workers/edit_worker') ?>">
					<input type="hidden" id="edit_worker_id" name="worker_id">
                    <div class="form-group row mb-3">
                      <label for="edit_worker_name" class="col-sm-4 col-form-label">Worker Name</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_worker_name" readonly style="background-color: #e9ecef;">
                      </div>
                    </div>
					<div class="form-group row mb-3">
                      <label for="edit_worker_controller" class="col-sm-4 col-form-label">Controller</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_worker_controller" readonly style="background-color: #e9ecef;">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <label for="edit_worker_function" class="col-sm-4 col-form-label">Function</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_worker_function" readonly style="background-color: #e9ecef;">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <label for="edit_error_logfile_path" class="col-sm-4 col-form-label">Error Log Path</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_error_logfile_path" name="error_logfile_path">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <label for="edit_stdout_logfile_path" class="col-sm-4 col-form-label">Stdout Log Path</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="edit_stdout_logfile_path" name="stdout_logfile_path">
                      </div>
                    </div>
                    <div class="form-group row mb-3 align-items-center">
                      <label class="col-sm-4 col-form-label">Autostart</label>
                      <div class="col-sm-8">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="autostart" id="edit_autostart" value="1">
                        </div>
                      </div>
                    </div>
                    <div class="form-group row mb-3 align-items-center">
                      <label class="col-sm-4 col-form-label">Autorestart</label>
                      <div class="col-sm-8">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="autorestart" id="edit_autorestart" value="1">
                        </div>
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
