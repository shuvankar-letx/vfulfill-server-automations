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
                            <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Cron Jobs</p>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <div id="example_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-6"></div>
                                <div class="col-sm-12 col-md-6"></div>
                            </div>
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table id="example" class="display expandable-table dataTable no-footer" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="" rowspan="1" colspan="1" aria-label="Cron ID#" style="width: 134px;">ID#</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Name" aria-sort="ascending" style="width: 158px;">Name</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Time" style="width: 183px;">Time</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Settings" style="width: 171px;">Settings</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Frequency" style="width: 126px;">Frequency</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Status" style="width: 132px;">Status</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Updated at" style="width: 151px;">Updated at</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Actions" style="width: 151px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="odd">
                                                <td class="">CRONJOB1</td>
                                                <td class="">Inventory Analytics</td>
                                                <td>Morning 7AM</td>
                                                <td>php index.php inventory_analytics cron_init</td>
                                                <td>Everyday</td>
                                                <td>Active</td>
                                                <td>25/05/2026</td>
                                                <td>
                                                    <a href="#">Edit</a>
                                                    <br/>
                                                    <a href="#">Sync Again</a>
                                                </td>
                                                
                                            </tr>
                                            <tr class="even">
                                                <td class="">CRONJOB2</td>
                                                <td class="">Snapshot Analytics</td>
                                                <td>Morning 6AM</td>
                                                <td>php index.php inventory_analytics snapshot_cron</td>
                                                <td>Every Monday</td>
                                                <td>Active</td>
                                                <td>29/05/2026</td>
                                                <td>
                                                    <a href="#">Edit</a>
                                                    <br/>
                                                    <a href="#">Sync Again</a>
                                                </td>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-5"></div>
                                <div class="col-sm-12 col-md-7"></div>
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