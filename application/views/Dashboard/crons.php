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
                    <div class="row">
                        <div class="col-12">
                            <p class="card-title">Cron Jobs <button type="button" class="btn btn-primary" style="float:right" data-bs-toggle="modal" data-bs-target="#add_cron">Add Cron</button></p>
                        </div>
                        <?php $this->load->view('dashboard/cronmodals');?>
                    </div>
                  
                    <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <div id="example_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-12"></div>
                                
                                <!-- <div class="col-sm-12 col-md-6"></div>
                                <div class="col-sm-12 col-md-6"></div> -->
                            </div>
                            <div class="row dt-row">
                                <div class="col-sm-12">
                                    <table id="example" class="display expandable-table dataTable no-footer" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="" rowspan="1" colspan="1" aria-label="Cron ID#" style="width: 134px;">ID#</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Name" aria-sort="ascending" style="width: 158px;">Name</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Time" style="width: 183px;">Time</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Frequency" style="width: 126px;">Frequency</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Settings" style="width: 171px;">Settings</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Status" style="width: 132px;">Status</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Updated at" style="width: 151px;">Updated at</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Actions" style="width: 151px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $i = 1;
                                                foreach($crons as $cron){
                                                    $class = ($i % 2 == 0) ? "even" : "odd";
                                                ?>
                                                <tr class="<?= $class?>">
                                                    <td><?= $cron->cron_id?></td>
                                                    <td><?= ucwords(strtolower(str_replace("_"," ",$cron->name)))?></td>
                                                    <td><?= $cron->cronToDisplay['time']?></td>
                                                    <td><?= $cron->cronToDisplay['frequency']?></td>
                                                    <td><?= $cron->command?></td>
                                                    <td><?= ucwords($cron->status)?></td>
                                                    <td><?= date('d M, Y h:i A', strtotime(utcToIst($cron->updated_at)));?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-fw btn-inverse-primary" data-bs-toggle="dropdown" aria-expanded="false" style="padding : 6px 12px;"> Action </button>
                                                        <div class="dropdown-menu" style="">
                                                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit_cron"><i class="ti-pencil"></i> Edit</a>
                                                            <a class="dropdown-item" href="<?= base_url('crons/run_now/'.$cron->cron_id) ?>" onclick="return confirm('Are you sure to run this cron ?')"><i class="ti-control-play"></i> Sync Again</a>
                                                            
                                                        </div>  
                                                        <!-- <a href="#" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                        <br/>
                                                        <a href="<?= base_url('crons/run_now/'.$cron->cron_id) ?>"

                                                            class="btn btn-inverse-info btn-fw"

                                                            onclick="return confirm('Run this cron now?')">

                                                                Sync <br/>
                                                                Again

                                                            </a> -->
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
<script type="text/javascript">
    $(document).on('change', '#add_cron_controller', function() {

            var controller = $(this).val();

            $('#add_cron_function_name').html(

                '<option value="">Loading...</option>'

            );

            $.ajax({

                url: '<?= base_url("crons/get_controller_functions") ?>',

                type: 'POST',

                dataType: 'json',

                data: {

                    controller: controller

                },

                success: function(response) {

                    var html =

                        '<option value="#" data-select2-id="Select_Controller">----- Select Functions ----- </option>';

                    $.each(response, function(index, function_name) {
                        //<option value="#" data-select2-id="Select_Controller">----- Select Controller ----- </option>
                        html += '<option value="' +

                                function_name +

                                '">' +

                                function_name +

                                '</option>';

                    });

                    $('#add_cron_function_name').html(html);

                }

            });

        });
</script>