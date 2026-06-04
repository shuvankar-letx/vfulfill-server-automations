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
                                            <h3 class=font-weight-bold>Cron Analytics</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Metric Cards (Matching Main Dashboard Design) -->
                            <div class=row>
                                <div class="transparent col-md-12 grid-margin">
                                    <div class=row>
                                        <div class="mb-4 col-md-3 stretch-card transparent">
                                            <div class="card card-tale">
                                                <div class=card-body>
                                                    <p class=mb-4>Total Runs Today</p>
                                                    <p class="fs-30 mb-2"><?= $today_runs ?></p>
                                                    <p>All active automations</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-3 stretch-card transparent">
                                            <div class="card card-dark-blue">
                                                <div class=card-body>
                                                    <p class=mb-4>Success Rate Today</p>
                                                    <p class="fs-30 mb-2"><?= $success_rate ?>%</p>
                                                    <p><?= $today_success ?> successful | <?= $today_failed ?> failed</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-3 stretch-card transparent">
                                            <div class="card card-light-blue">
                                                <div class=card-body>
                                                    <p class=mb-4>Average Duration</p>
                                                    <p class="fs-30 mb-2"><?= $avg_duration ?>s</p>
                                                    <p>Time per successful run</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-3 stretch-card transparent">
                                            <div class="card card-light-danger">
                                                <div class=card-body>
                                                    <p class=mb-4>Timeouts Today</p>
                                                    <p class="fs-30 mb-2"><?= $today_timeout ?></p>
                                                    <p>Runs exceeding limit</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-4 stretch-card transparent">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary"><i class="ti-crown"></i> Most Executed Cron</h5>
                                                    <h4><?= $most_executed ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4 stretch-card transparent">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-danger"><i class="ti-alarm-clock"></i> Slowest Cron</h5>
                                                    <h4><?= $slowest_cron ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Charts section -->
                            <div class="row">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Daily Execution Trend</h4>
                                            <canvas id="dailyExecutionChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Success vs Failure Trend</h4>
                                            <canvas id="successFailureChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Top 10 Most Executed Crons</h4>
                                            <canvas id="topExecutedChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Top 10 Slowest Crons (Average Duration)</h4>
                                            <canvas id="topSlowChart"></canvas>
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
        $(function() {
            // Chart 1: Daily Execution Trend
            var dailyExecutionCtx = document.getElementById('dailyExecutionChart').getContext('2d');
            new Chart(dailyExecutionCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($charts['labels']) ?>,
                    datasets: [{
                        label: 'Total Executions',
                        data: <?= json_encode($charts['total_runs']) ?>,
                        borderColor: '#4B49AC',
                        backgroundColor: 'rgba(75, 73, 172, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Chart 2: Success vs Failure Trend
            var successFailureCtx = document.getElementById('successFailureChart').getContext('2d');
            new Chart(successFailureCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($charts['labels']) ?>,
                    datasets: [
                        {
                            label: 'Success',
                            data: <?= json_encode($charts['success_runs']) ?>,
                            backgroundColor: '#28a745'
                        },
                        {
                            label: 'Failure',
                            data: <?= json_encode($charts['failed_runs']) ?>,
                            backgroundColor: '#dc3545'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true }
                    }
                }
            });

            // Chart 3: Top 10 Most Executed
            var topExecutedCtx = document.getElementById('topExecutedChart').getContext('2d');
            new Chart(topExecutedCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($charts['top_executed_labels']) ?>,
                    datasets: [{
                        label: 'Runs count',
                        data: <?= json_encode($charts['top_executed_values']) ?>,
                        backgroundColor: '#7DA0FA'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Chart 4: Top 10 Slowest
            var topSlowCtx = document.getElementById('topSlowChart').getContext('2d');
            new Chart(topSlowCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($charts['top_slow_labels']) ?>,
                    datasets: [{
                        label: 'Average Duration (seconds)',
                        data: <?= json_encode($charts['top_slow_values']) ?>,
                        backgroundColor: '#F3797E'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
