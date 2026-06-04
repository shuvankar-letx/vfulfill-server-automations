<?php $this->load->view('dashboard/layout/header') ;?>
    <div class="container-scroller">
        <?php $this->load->view('dashboard/layout/navbar');?>
            <div class="container-fluid page-body-wrapper">
                <?php $this->load->view("dashboard/layout/sidebar");?>
                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="row">
                                <div class="col-md-12 grid-margin">
                                    <div class="row">
                                        <div class="mb-4 col-12 col-xl-8 mb-xl-0">
                                            <h3 class="font-weight-bold">Welcome Shuvankar</h3></div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('success') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('info')): ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('info') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="card-title">Workers 
                                <a href="<?= base_url('workers/sync_workers') ?>" class="btn btn-secondary" style="float:right; margin-right:10px;">Sync Workers</a> 
                                <button type="button" class="btn btn-primary" style="float:right; margin-right:10px;" data-bs-toggle="modal" data-bs-target="#add_worker">Add Worker</button>
                            </p>
                        </div>
                        <?php $this->load->view('dashboard/workermodals');?>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="<?= base_url('workers') ?>" class="d-flex">
                                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search by Worker Name..." value="<?= htmlspecialchars($search ?? '') ?>">
                                <?php if(!empty($limit)): ?>
                                    <input type="hidden" name="limit" value="<?= $limit ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary btn-sm me-1">Search</button>
                                <?php if(!empty($search)): ?>
                                    <a href="<?= base_url('workers') ?>?limit=<?= $limit ?>" class="btn btn-light btn-sm">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                  
                    <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <div id="example_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table id="example" class="display expandable-table dataTable no-footer" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="" rowspan="1" colspan="1" style="width: 134px;">
                                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sort'=>'worker_id','dir'=> (isset($_GET['sort']) && $_GET['sort']=='worker_id' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">
                                                        ID#
                                                        <?php if(isset($_GET['sort']) && $_GET['sort'] == 'worker_id'): ?>
                                                            <i class="ti-arrow-<?php echo $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </th>
                                                <th class="" rowspan="1" colspan="1" style="width: 158px;">Name</th>
                                                <th class="" rowspan="1" colspan="1" style="width: 220px;">Settings / Command</th>
                                                <th class="" rowspan="1" colspan="1" style="width: 132px;">
                                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sort'=>'status','dir'=> (isset($_GET['sort']) && $_GET['sort']=='status' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">
                                                        Status
                                                        <?php if(isset($_GET['sort']) && $_GET['sort'] == 'status'): ?>
                                                            <i class="ti-arrow-<?php echo $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </th>
                                                <th class="" rowspan="1" colspan="1" style="width: 150px;">
                                                    <a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'added_on','dir'=> (isset($_GET['sort']) && $_GET['sort']=='added_on' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">
                                                        Added on 
                                                        <?php if(isset($_GET['sort']) && $_GET['sort'] == 'added_on'): ?>
                                                            <i class="ti-arrow-<?= $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </th>
                                                <th class="" rowspan="1" colspan="1" style="width: 151px;">
                                                    <a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'updated_on','dir'=> (isset($_GET['sort']) && $_GET['sort']=='updated_on' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">
                                                        Updated on 
                                                        <?php if(isset($_GET['sort']) && $_GET['sort'] == 'updated_on'): ?>
                                                            <i class="ti-arrow-<?= $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </th>
                                                <th class="" rowspan="1" colspan="1" style="width: 151px;">Actions</th>
                                            </tr>
                                        </thead>
                                        
                                        <style>
                                            /* Switch styling */
                                            .switch {
                                                position: relative;
                                                display: inline-block;
                                                width: 46px;
                                                height: 24px;
                                            }
                                            .switch input {
                                                opacity: 0;
                                                width: 0;
                                                height: 0;
                                            }
                                            .slider {
                                                position: absolute;
                                                cursor: pointer;
                                                top: 0; left: 0; right: 0; bottom: 0;
                                                background-color: #ccc;
                                                transition: .3s;
                                                border-radius: 24px;
                                            }
                                            .slider:before {
                                                position: absolute;
                                                content: "";
                                                height: 18px;
                                                width: 18px;
                                                left: 3px;
                                                bottom: 3px;
                                                background-color: white;
                                                transition: .3s;
                                                border-radius: 50%;
                                            }
                                            .switch input:checked + .slider {
                                                background-color: #28a745;
                                            }
                                            .switch input:checked + .slider:before {
                                                transform: translateX(22px);
                                            }
                                            /* Sort link styling */
                                            th a {
                                                color: inherit;
                                                text-decoration: none;
                                            }
                                            th a:hover {
                                                text-decoration: underline;
                                            }
                                        </style>
                                        
                                        <tbody>
                                            <?php 
                                                $i = 1;
                                                foreach($workers as $worker){
                                                    $class = ($i % 2 == 0) ? "even" : "odd";
                                                ?>
                                                <tr class="<?= $class?>">
                                                    <td><?= $worker->worker_id?></td>
                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$worker->worker_name)))?></td>
                                                    <td>
                                                        <code><?= htmlspecialchars($worker->controller) ?> <?= htmlspecialchars($worker->controller_function) ?></code>
                                                    </td>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" onchange="toggleWorker('<?= $worker->worker_id ?>', this.checked)" <?= ($worker->status == "running" || $worker->status == "active") ? "checked" : "" ?>>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </td>
                                                    <td><?= date('d M, Y h:i A', strtotime($worker->added_on));?></td>
                                                    <td><?= date('d M, Y h:i A', strtotime($worker->updated_on));?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-fw btn-inverse-primary" data-bs-toggle="dropdown" aria-expanded="false" style="padding : 6px 12px;"> Action </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item btn-edit-worker" data-bs-toggle="modal" data-bs-target="#edit_worker"
                                                                data-id="<?= $worker->worker_id ?>"
                                                                data-name="<?= htmlspecialchars($worker->worker_name) ?>"
                                                                data-controller="<?= htmlspecialchars($worker->controller) ?>"
                                                                data-function="<?= htmlspecialchars($worker->controller_function) ?>"
                                                                data-error="<?= htmlspecialchars($worker->error_logfile_path) ?>"
                                                                data-stdout="<?= htmlspecialchars($worker->stdout_logfile_path) ?>"
                                                                data-autostart="<?= $worker->autostart ? 1 : 0 ?>"
                                                                data-autorestart="<?= $worker->autorestart ? 1 : 0 ?>">
                                                                <i class="ti-pencil"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item" href="<?= base_url('workers/sync_worker/'.$worker->worker_id) ?>" onclick="return confirm('Are you sure you want to sync this worker?')">
                                                                <i class="ti-reload"></i> Sync Worker
                                                            </a>
                                                            <a class="dropdown-item" href="<?= base_url('workers/view_error_log/'.$worker->worker_id) ?>">
                                                                <i class="ti-file"></i> Show Error Log
                                                            </a>
                                                            <a class="dropdown-item" href="<?= base_url('workers/view_output_log/'.$worker->worker_id) ?>">
                                                                <i class="ti-file"></i> Show Out Log
                                                            </a>
                                                        </div>  
                                                    </td>
                                                </tr>
                                                <?php
                                                    $i++;
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                             <!-- Pagination -->
                             <div class="d-flex justify-content-between align-items-center mt-4">
                                 <div class="d-flex align-items-center">
                                     <label class="me-2 mb-0">Show</label>
                                     <select class="form-control form-control-sm" style="width:70px;" onchange="var params = new URLSearchParams(window.location.search); params.set('limit', this.value); params.set('page', '1'); window.location.href='?'+params.toString();">
                                         <?php foreach([5, 10, 25, 50, 100] as $opt): ?>
                                             <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <span class="ms-2 mb-0">entries</span>
                                 </div>
                                 <div>Showing <?= $count > 0 ? (($page - 1) * $limit) + 1 : 0 ?> to <?= min($page * $limit, $count) ?> of <?= $count ?> entries</div>
                                 <?php if($count > $limit): ?>
                                 <nav>
                                     <ul class="pagination mb-0">
                                         <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                             <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                         </li>
                                         <?php for($p = 1; $p <= ceil($count / $limit); $p++): ?>
                                             <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                                                 <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
                                             </li>
                                         <?php endfor; ?>
                                         <li class="page-item <?= $page >= ceil($count / $limit) ? 'disabled' : '' ?>">
                                             <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                                         </li>
                                     </ul>
                                 </nav>
                                 <?php endif; ?>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php $this->load->view('dashboard/layout/footer');?>
</div>
</div>
</div>
<?php $this->load->view('dashboard/layout/added_js');?>
<script type="text/javascript">
    $(document).on('change', '#add_worker_controller', function() {
        var controller = $(this).val();

        $('#add_worker_function').html(
            '<option value="">Loading...</option>'
        );
        setTimeout(function() {
            $.ajax({
                url: '<?= base_url("crons/get_controller_functions") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    controller: controller
                },
                success: function(response) {
                    var html = '<option value="">----- Select Function -----</option>';
                    $.each(response, function(index, function_name) {
                        html += '<option value="' + function_name + '">' +
                                function_name +
                                '</option>';
                    });
                    $('#add_worker_function').html(html);
                }
            })
        }, 500);
    });

    $(document).on('click', '.btn-edit-worker', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var controller = $(this).data('controller');
        var func = $(this).data('function');
        var error = $(this).data('error');
        var stdout = $(this).data('stdout');
        var autostart = $(this).data('autostart');
        var autorestart = $(this).data('autorestart');

        $('#edit_worker_id').val(id);
        $('#edit_worker_name').val(name);
        $('#edit_worker_controller').val(controller);
        $('#edit_worker_function').val(func);
        $('#edit_error_logfile_path').val(error);
        $('#edit_stdout_logfile_path').val(stdout);
        $('#edit_autostart').prop('checked', autostart == 1);
        $('#edit_autorestart').prop('checked', autorestart == 1);
    });

    function toggleWorker(id, state) {
        fetch("<?= base_url('workers/toggle_status/') ?>" + id, {
            method: "GET"
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert("Failed to update status");
            }
        })
        .catch(() => {
            alert("Error updating worker status");
        });
    }
</script>