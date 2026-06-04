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
                                            <h3 class="font-weight-bold">System Automations Dashboard</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 1. Active & Fatal Counts Row -->
                            <div class="row">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card card-tale">
                                        <div class="card-body">
                                            <p class="mb-4">Active Crons & Workers</p>
                                            <p class="fs-30 mb-2">
                                                <?= $active_crons ?> <span class="fs-15">Crons</span> 
                                                / 
                                                <?= $active_workers ?> <span class="fs-15">Workers</span>
                                            </p>
                                            <p>Currently configured and running</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card card-light-danger">
                                        <div class="card-body">
                                            <p class="mb-4">Fatal Errors (Last 24h)</p>
                                            <p class="fs-30 mb-2 text-danger">
                                                <?= $failed_crons_24h ?> <span class="fs-15 text-dark">Cron Failures</span>
                                                /
                                                <?= $failed_workers_24h ?> <span class="fs-15 text-dark">Worker Failures</span>
                                            </p>
                                            <p>Failed runs or execution timeouts</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Performance: Slowest & Fastest Comparison Row -->
                            <div class="row">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-danger"><i class="ti-alarm-clock"></i> Slowest Automations (Max Time)</h4>
                                            <div class="list-group list-group-flush">
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-0">Slowest Cron Job</h6>
                                                        <small class="text-muted"><?= !empty($slowest_cron) ? ucwords(strtolower(str_replace("_"," ",$slowest_cron->cron_name))) : 'N/A' ?></small>
                                                    </div>
                                                    <span class="badge bg-danger text-white"><?= !empty($slowest_cron) ? $slowest_cron->duration . 's' : '-' ?></span>
                                                </div>
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-0">Slowest Worker Process</h6>
                                                        <small class="text-muted"><?= !empty($slowest_worker) ? ucwords(strtolower(str_replace("_"," ",$slowest_worker->worker_name))) : 'N/A' ?></small>
                                                    </div>
                                                    <span class="badge bg-danger text-white"><?= !empty($slowest_worker) ? $slowest_worker->duration . 's' : '-' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-success"><i class="ti-bolt"></i> Fastest Automations (Min Time)</h4>
                                            <div class="list-group list-group-flush">
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-0">Fastest Cron Job</h6>
                                                        <small class="text-muted"><?= !empty($fastest_cron) ? ucwords(strtolower(str_replace("_"," ",$fastest_cron->cron_name))) : 'N/A' ?></small>
                                                    </div>
                                                    <span class="badge bg-success text-white"><?= !empty($fastest_cron) ? $fastest_cron->duration . 's' : '-' ?></span>
                                                </div>
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-0">Fastest Worker Process</h6>
                                                        <small class="text-muted"><?= !empty($fastest_worker) ? ucwords(strtolower(str_replace("_"," ",$fastest_worker->worker_name))) : 'N/A' ?></small>
                                                    </div>
                                                    <span class="badge bg-success text-white"><?= !empty($fastest_worker) ? $fastest_worker->duration . 's' : '-' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Memory Consumption Comparison Row -->
                            <div class="row">
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-primary"><i class="ti-bar-graph"></i> Memory Consumption Comparison</h4>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Module</th>
                                                            <th>Highest Memory Consumer</th>
                                                            <th>Lowest Memory Consumer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Cron Jobs</strong></td>
                                                            <td><span class="text-danger"><?= htmlspecialchars($cron_mem['highest']) ?></span></td>
                                                            <td><span class="text-success"><?= htmlspecialchars($cron_mem['lowest']) ?></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Workers</strong></td>
                                                            <td><span class="text-danger"><?= htmlspecialchars($worker_mem['highest']) ?></span></td>
                                                            <td><span class="text-success"><?= htmlspecialchars($worker_mem['lowest']) ?></span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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