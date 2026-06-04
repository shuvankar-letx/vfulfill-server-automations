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
                                            <h3 class=font-weight-bold>Execution Details</h3>
                                            <h6 class="text-muted"><a href="<?= base_url('crons/logs') ?>" class="text-decoration-none"><i class="ti-arrow-left"></i> Back to Logs</a></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Main execution details -->
                                <div class="col-md-8 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="card-title">Run ID: <?= $execution->run_id ?></h4>
                                                <div>
                                                    <?php if($execution->status === 'success'): ?>
                                                        <span class="badge bg-success text-white">Success</span>
                                                    <?php elseif($execution->status === 'failed'): ?>
                                                        <span class="badge bg-danger text-white">Failed</span>
                                                    <?php elseif($execution->status === 'running'): ?>
                                                        <span class="badge bg-info text-white">Running</span>
                                                    <?php elseif($execution->status === 'timeout'): ?>
                                                        <span class="badge bg-warning text-dark">Timeout</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h5>Cron Information</h5>
                                                    <p><strong>Name:</strong> <?= ucwords(strtolower(str_replace("_"," ",$execution->cron_name))) ?></p>
                                                    <p><strong>Controller:</strong> <?= $execution->controller ?></p>
                                                    <p><strong>Method:</strong> <?= $execution->method ?></p>
                                                    <p><strong>PID:</strong> <?= $execution->pid ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Execution Timeline</h5>
                                                    <p><strong>Start Time:</strong> <?= date('d M, Y h:i A', strtotime($execution->start_time)) ?></p>
                                                    <p><strong>End Time:</strong> <?= !empty($execution->end_time) ? date('d M, Y h:i A', strtotime($execution->end_time)) : '-' ?></p>
                                                    <p><strong>Duration:</strong> <?= $execution->status === 'running' ? '-' : $execution->duration . 's' ?></p>
                                                    <p><strong>Server:</strong> <?= $execution->server ?></p>
                                                    <p><strong>Memory Peak:</strong> <?= $execution->memory_usage ?></p>
                                                </div>
                                            </div>

                                            <?php if(!empty($execution->error_message)): ?>
                                                <div class="alert alert-danger" role="alert">
                                                    <h5 class="alert-heading"><i class="ti-alert"></i> Error Message</h5>
                                                    <p class="mb-0"><?= htmlspecialchars($execution->error_message) ?></p>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h5>Console Output / Raw Log</h5>
                                                    <a href="<?= base_url('crons/view_raw_log/'.$execution->run_id) ?>" target="_blank" class="btn btn-outline-info btn-xs"><i class="ti-new-window"></i> View Raw Text</a>
                                                </div>
                                                <pre style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; font-family: monospace;"><?= htmlspecialchars($execution->raw_log) ?></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Side details (History & Comparison) -->
                                <div class="col-md-4 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Run History</h4>
                                            
                                            <!-- Last successful and failed run summaries -->
                                            <div class="mb-4">
                                                <h6>Last Successful Run</h6>
                                                <?php if($last_success): ?>
                                                    <p class="mb-1"><a href="<?= base_url('crons/log_details/'.$last_success->run_id) ?>"><?= $last_success->run_id ?></a></p>
                                                    <small class="text-muted"><?= date('d M, Y h:i A', strtotime($last_success->start_time)) ?> (<?= $last_success->duration ?>s)</small>
                                                <?php else: ?>
                                                    <p class="text-muted"><small>None found</small></p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mb-4">
                                                <h6>Last Failed Run</h6>
                                                <?php if($last_failed): ?>
                                                    <p class="mb-1"><a href="<?= base_url('crons/log_details/'.$last_failed->run_id) ?>"><?= $last_failed->run_id ?></a></p>
                                                    <small class="text-muted"><?= date('d M, Y h:i A', strtotime($last_failed->start_time)) ?> (<?= $last_failed->duration ?>s)</small>
                                                <?php else: ?>
                                                    <p class="text-muted"><small>None found</small></p>
                                                <?php endif; ?>
                                            </div>

                                            <hr>

                                            <!-- Previous 5 Runs list -->
                                            <h5 class="mt-4">Previous Runs</h5>
                                            <div class="list-group list-group-flush">
                                                <?php if(empty($previous_runs)): ?>
                                                    <p class="text-muted"><small>No other runs recorded</small></p>
                                                <?php else: ?>
                                                    <?php foreach($previous_runs as $prev): ?>
                                                        <a href="<?= base_url('crons/log_details/'.$prev->run_id) ?>" class="list-group-item list-group-item-action flex-column align-items-start px-0 border-bottom">
                                                            <div class="d-flex w-100 justify-content-between">
                                                                <h6 class="mb-1"><?= $prev->run_id ?></h6>
                                                                <small>
                                                                    <?php if($prev->status === 'success'): ?>
                                                                        <span class="badge bg-success text-white p-1">Success</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-danger text-white p-1">Failed</span>
                                                                    <?php endif; ?>
                                                                </small>
                                                            </div>
                                                            <p class="mb-1"><small class="text-muted"><?= date('d M, Y h:i A', strtotime($prev->start_time)) ?></small></p>
                                                            <small class="text-muted">Duration: <?= $prev->duration ?>s | Mem: <?= $prev->memory_usage ?></small>
                                                        </a>
                                                    <?php endforeach; ?>
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
