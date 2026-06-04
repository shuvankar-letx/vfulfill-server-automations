<?php $this->load->view('dashboard/layout/header') ;?>
    
    <div class=container-scroller>
        <?php $this->load->view('dashboard/layout/navbar');?>
            <div class="container-fluid page-body-wrapper">
                <?php $this->load->view("dashboard/layout/sidebar");?>
                    <div class=main-panel>
                        <div class=content-wrapper>
                            <div class=row>
                                <div class="col-md-12 grid-margin">
                                    <div class=row>
                                        <div class="mb-4 col-12 col-xl-8 mb-xl-0">
                                            <h3 class=font-weight-bold>Cron Health Monitor</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Display Flash messages -->
                            <?php if($this->session->flashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('success') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= $this->session->flashdata('error') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Locked Crons Panel -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <h4 class="card-title text-warning"><i class="ti-lock"></i> Active Locks</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Cron ID</th>
                                                            <th>Name</th>
                                                            <th>Command</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($locked_crons)): ?>
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">No active locks found. System runs are unlocked.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($locked_crons as $locked): ?>
                                                                <tr>
                                                                    <td><?= $locked->cron_id ?></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$locked->name))) ?></td>
                                                                    <td><?= $locked->command ?></td>
                                                                    <td><span class="badge bg-warning text-dark">Locked</span></td>
                                                                    <td>
                                                                        <a href="<?= base_url('crons/release_lock/'.$locked->cron_id) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Release lock for this Cron?')">Release Lock</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Running and Stuck Crons -->
                            <div class="row mb-4">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-info"><i class="ti-control-play"></i> Currently Running Crons</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Run ID</th>
                                                            <th>Cron Name</th>
                                                            <th>Start Time</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($running)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No currently running crons.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($running as $run): ?>
                                                                <tr>
                                                                    <td><strong><?= $run->run_id ?></strong></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$run->cron_name))) ?></td>
                                                                    <td><?= date('d M h:i A', strtotime($run->start_time)) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('crons/mark_timeout/'.$run->run_id) ?>" class="btn btn-inverse-warning btn-xs" onclick="return confirm('Mark this run as Timeout?')">Mark Timeout</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <h4 class="card-title text-danger"><i class="ti-time"></i> Stuck Crons (Running > 1 Hour)</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Run ID</th>
                                                            <th>Cron Name</th>
                                                            <th>Start Time</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($stuck)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No stuck crons detected.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($stuck as $stk): ?>
                                                                <tr>
                                                                    <td><strong><?= $stk->run_id ?></strong></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$stk->cron_name))) ?></td>
                                                                    <td><?= date('d M h:i A', strtotime($stk->start_time)) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('crons/mark_timeout/'.$stk->run_id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Mark this run as Timeout?')">Mark Timeout</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recently Failed and Timed Out Crons -->
                            <div class="row">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-danger"><i class="ti-face-sad"></i> Recently Failed Crons (Last 24h)</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Run ID</th>
                                                            <th>Cron Name</th>
                                                            <th>Failed At</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($failed)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No recently failed crons.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($failed as $fail): ?>
                                                                <tr>
                                                                    <td><a href="<?= base_url('crons/log_details/'.$fail->run_id) ?>"><?= $fail->run_id ?></a></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$fail->cron_name))) ?></td>
                                                                    <td><?= date('d M h:i A', strtotime($fail->start_time)) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('crons/retry_execution/'.$fail->run_id) ?>" class="btn btn-inverse-primary btn-xs" onclick="return confirm('Retry execution for this cron?')">Retry</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title text-warning"><i class="ti-alert"></i> Timed Out Crons</h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Run ID</th>
                                                            <th>Cron Name</th>
                                                            <th>Timed Out At</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(empty($timeouts)): ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">No timed out crons found.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach($timeouts as $t): ?>
                                                                <tr>
                                                                    <td><a href="<?= base_url('crons/log_details/'.$t->run_id) ?>"><?= $t->run_id ?></a></td>
                                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$t->cron_name))) ?></td>
                                                                    <td><?= date('d M h:i A', strtotime($t->start_time)) ?></td>
                                                                    <td>
                                                                        <a href="<?= base_url('crons/retry_execution/'.$t->run_id) ?>" class="btn btn-inverse-primary btn-xs" onclick="return confirm('Retry execution for this cron?')">Retry</a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
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
