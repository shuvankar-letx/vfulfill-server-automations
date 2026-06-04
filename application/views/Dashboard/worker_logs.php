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
                                            <h3 class="font-weight-bold">Worker Execution Logs</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Filters Card -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="GET" action="<?= base_url('workers/logs') ?>" class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">Worker</label>
                                                    <select class="form-control" name="worker">
                                                        <option value="">All Workers</option>
                                                        <?php foreach($active_workers as $w): ?>
                                                            <option value="<?= $w->worker_id ?>" <?= $filters['worker'] == $w->worker_id ? 'selected' : '' ?>>
                                                                <?= ucwords(strtolower(str_replace("_"," ",$w->worker_name))) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-control" name="status">
                                                        <option value="">All Statuses</option>
                                                        <option value="success" <?= $filters['status'] == 'success' ? 'selected' : '' ?>>Success</option>
                                                        <option value="failed" <?= $filters['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                                                        <option value="running" <?= $filters['status'] == 'running' ? 'selected' : '' ?>>Running</option>
                                                        <option value="timeout" <?= $filters['status'] == 'timeout' ? 'selected' : '' ?>>Timeout</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Date Range</label>
                                                    <input type="text" class="form-control" id="date_range" name="date_range" value="<?= htmlspecialchars($filters['date_range'] ?? '') ?>" placeholder="Select range">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Run ID</label>
                                                    <input type="text" class="form-control" name="run_id" value="<?= htmlspecialchars($filters['run_id'] ?? '') ?>" placeholder="WRUN_XXX">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="submit" class="btn btn-primary me-2 w-50">Filter</button>
                                                    <a href="<?= base_url('workers/logs') ?>" class="btn btn-light w-50">Reset</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Logs Table -->
                            <div class="row">
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover display expandable-table" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Run ID</th>
                                                            <th>Worker Name</th>
                                                            <th>Controller</th>
                                                            <th>Method</th>
                                                            <th>Status</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>Duration</th>
                                                            <th>Server</th>
                                                            <th>Memory</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($logs)): ?>
                                                            <tr>
                                                                <td colspan="11" class="text-center">No execution logs found.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($logs as $log): ?>
                                                                <tr>
                                                                    <td><strong><?= $log->run_id ?></strong></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$log->worker_name))) ?></td>
                                                                    <td><?= $log->controller ?></td>
                                                                    <td><?= $log->method ?></td>
                                                                    <td>
                                                                        <?php if($log->status === 'success'): ?>
                                                                            <span class="badge bg-success text-white">Success</span>
                                                                        <?php elseif($log->status === 'failed'): ?>
                                                                            <span class="badge bg-danger text-white">Failed</span>
                                                                        <?php elseif($log->status === 'running'): ?>
                                                                            <span class="badge bg-info text-white">Running</span>
                                                                        <?php elseif($log->status === 'timeout'): ?>
                                                                            <span class="badge bg-warning text-dark">Timeout</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-secondary text-white"><?= $log->status ?></span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= date('d M, Y h:i A', strtotime($log->start_time)) ?></td>
                                                                    <td><?= !empty($log->end_time) ? date('d M, Y h:i A', strtotime($log->end_time)) : '-' ?></td>
                                                                    <td><?= $log->status === 'running' ? '-' : $log->duration . 's' ?></td>
                                                                    <td><?= $log->server ?></td>
                                                                    <td><?= $log->memory_usage ?></td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-fw btn-inverse-primary" data-bs-toggle="dropdown" aria-expanded="false" style="padding : 6px 12px;"> Action </button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item" href="<?= base_url('workers/log_details/'.$log->run_id) ?>"><i class="ti-info-alt"></i> View Details</a>
                                                                            <a class="dropdown-item" href="<?= base_url('workers/sync_worker/'.$log->worker_id) ?>" onclick="return confirm('Are you sure you want to sync this worker?')"><i class="ti-reload"></i> Sync Worker</a>
                                                                            <a class="dropdown-item" target="_blank" href="<?= base_url('workers/view_error_log/'.$log->worker_id) ?>"><i class="ti-file"></i> View Error Log</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
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
                        <?php $this->load->view('dashboard/layout/footer');?>
                    </div>
            </div>
    </div>
    <?php $this->load->view('dashboard/layout/added_js');?>
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d"
        });
    </script>
