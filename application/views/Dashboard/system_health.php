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
                                            <h3 class="font-weight-bold">System Health Status</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- 1. Server Resource utilization -->
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-primary"><i class="ti-server"></i> Server Load & CPU</h4>
                                            <div class="mt-4">
                                                <h6>Load Average (1m, 5m, 15m)</h6>
                                                <p class="fs-30"><?= implode(', ', $cpu_load) ?></p>
                                            </div>
                                            <div class="mt-4">
                                                <h6>Platform / OS</h6>
                                                <p><?= php_uname() ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-primary"><i class="ti-pie-chart"></i> Disk Space Utilisation</h4>
                                            <div class="mt-4">
                                                <p><strong>Total Space:</strong> <?= $disk_total ?> GB</p>
                                                <p><strong>Free Space:</strong> <?= $disk_free ?> GB</p>
                                                <p><strong>Used Space:</strong> <?= $disk_used ?> GB (<?= $disk_percentage ?>%)</p>
                                            </div>
                                            <div class="progress mt-3" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $disk_percentage ?>%" aria-valuenow="<?= $disk_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- 2. Services & Database status -->
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-primary"><i class="ti-check-box"></i> Services & Database Status</h4>
                                            <div class="table-responsive mt-3">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Service</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>MongoDB Connection</td>
                                                            <td>
                                                                <?php if($mongo_status): ?>
                                                                    <span class="badge bg-success text-white">Connected</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger text-white">Disconnected</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>PHP Version</td>
                                                            <td><span class="badge bg-info text-white"><?= phpversion() ?></span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>System Timezone</td>
                                                            <td><code><?= date_default_timezone_get() ?></code></td>
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
