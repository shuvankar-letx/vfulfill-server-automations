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
                                            <h3 class="font-weight-bold">Worker <?= htmlspecialchars($type) ?> Logs</h3>
                                            <h6 class="font-weight-normal mb-0">Viewing logs for worker: <?= htmlspecialchars($worker_id) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4 class="card-title mb-0">Log Output (Last 100 lines)</h4>
                                                <a href="javascript:window.location.reload();" class="btn btn-primary btn-sm"><i class="ti-reload"></i> Refresh Logs</a>
                                            </div>
                                            
                                            <div class="log-container bg-dark text-light p-3 rounded" style="max-height: 600px; overflow-y: auto;">
                                                <pre class="text-light m-0" style="white-space: pre-wrap; font-family: monospace; font-size: 14px;"><?= htmlspecialchars($log_content) ?></pre>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <a href="<?= base_url('workers') ?>" class="btn btn-secondary">Back to Workers</a>
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
