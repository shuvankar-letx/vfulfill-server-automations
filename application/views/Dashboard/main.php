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
                                            <h3 class=font-weight-bold>Welcome Shuvankar</h3></div>
                                    </div>
                                </div>
                            </div>
                            <div class=row>
                                <div class="transparent col-md-12 grid-margin">
                                    <div class=row>
                                        <div class="mb-4 col-md-6 stretch-card transparent">
                                            <div class="card card-tale">
                                                <div class=card-body>
                                                    <p class=mb-4>Active Cron Jobs
                                                        <p class="fs-30 mb-2">48
                                                            <p>3 paused</div>
                                            </div>
                                        </div>
                                        <div class="mb-4 col-md-6 stretch-card transparent">
                                            <div class="card card-dark-blue">
                                                <div class=card-body>
                                                    <p class=mb-4>Running Workers
                                                        <p class="fs-30 mb-2">22
                                                            <p>2 unhealthy</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=row>
                                        <div class="mb-4 col-md-6 stretch-card transparent mb-lg-0">
                                            <div class="card card-light-blue">
                                                <div class=card-body>
                                                    <p class=mb-4>Critical Alerts
                                                        <p class="fs-30 mb-2">7
                                                            <p>Last 24 Hours</div>
                                            </div>
                                        </div>
                                        <div class="transparent col-md-6 stretch-card">
                                            <div class="card card-light-danger">
                                                <div class=card-body>
                                                    <p class=mb-4>Server Health
                                                        <p class="fs-30 mb-2">4 / 5 Healthy
                                                            <p>1 Warning</div>
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