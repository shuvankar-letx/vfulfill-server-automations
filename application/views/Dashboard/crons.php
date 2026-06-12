<?php $this->load->view('dashboard/layout/header'); ?>
    
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
                            <p class="card-title">Cron Jobs <a href="<?= base_url('crons/sync_crontab') ?>" class="btn btn-secondary" style="float:right; margin-right:10px;">Sync Crontab</a> <button type="button" class="btn btn-primary" style="float:right; margin-right:10px;" data-bs-toggle="modal" data-bs-target="#add_cron">Add Cron</button></p>
                        </div>
                        <?php $this->load->view('dashboard/cronmodals');?>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="<?= base_url('crons') ?>" class="d-flex">
                                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search by Cron Name..." value="<?= htmlspecialchars($search ?? '') ?>">
                                <?php if(!empty($limit)): ?>
                                    <input type="hidden" name="limit" value="<?= $limit ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary btn-sm me-1">Search</button>
                                <?php if(!empty($search)): ?>
                                    <a href="<?= base_url('crons') ?>?limit=<?= $limit ?>" class="btn btn-light btn-sm">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
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
                                                                                                <th class="" rowspan="1" colspan="1" aria-label="Cron ID#" style="width: 134px;">
                                                <a href="?<?php echo http_build_query(array_merge($_GET, ['sort'=>'cron_id','dir'=> (isset($_GET['sort']) && $_GET['sort']=='cron_id' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">
                                                    ID#
                                                    <?php if(isset($_GET['sort']) && $_GET['sort'] == 'cron_id'): ?>
                                                        <i class="ti-arrow-<?php echo $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i>
                                                    <?php endif; ?>
                                                </a>
                                                </th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Name" aria-sort="ascending" style="width: 158px;">Name</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Time" style="width: 183px;">Time</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Frequency" style="width: 126px;">Frequency</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Settings" style="width: 171px;">Settings</th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Cron Status" style="width: 132px;">Status</th>
                                                <th class="" rowspan="1" colspan="1" aria-label="Created at" style="width: 150px;"><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'created_at','dir'=> (isset($_GET['sort']) && $_GET['sort']=='created_at' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">Created at <?php if(isset($_GET['sort']) && $_GET['sort'] == 'created_at'): ?><i class="ti-arrow-<?= $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a></th>
<th class="" rowspan="1" colspan="1" aria-label="Updated at" style="width: 151px;"><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'updated_at','dir'=> (isset($_GET['sort']) && $_GET['sort']=='updated_at' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">Updated at <?php if(isset($_GET['sort']) && $_GET['sort'] == 'updated_at'): ?><i class="ti-arrow-<?= $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a></th>
<th class="" rowspan="1" colspan="1" aria-label="Last Executed" style="width: 183px;"><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'last_executed','dir'=> (isset($_GET['sort']) && $_GET['sort']=='last_executed' && (isset($_GET['dir']) && $_GET['dir']=='asc') ? 'desc' : 'asc')])) ?>">Last Executed <?php if(isset($_GET['sort']) && $_GET['sort'] == 'last_executed'): ?><i class="ti-arrow-<?= $_GET['dir'] == 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a></th>
                                                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Actions" style="width: 151px;">Actions</th>
                                            </tr>
                                        </thead>
<style>

    th a {
        color : #ffff !important;
    }
                                        
    /* Icon colors */
    .ti-arrow-up, .ti-arrow-down {
        color: #fff;
    }
    
    
    /* Switch styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    .switch input:checked + .slider {
        background-color: #28a745;
    }
    .switch input:checked + .slider:before {
        transform: translateX(22px);
    }
</style>
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
                                                    <td><code><?= $cron->command?></code></td>
                                                    <td>

                                                        <label class="switch">

                                                            <input type="checkbox"

                                                                onchange="toggleCron('<?= $cron->cron_id ?>', this.checked)"

                                                                <?= ($cron->status == "active") ? "checked" : "" ?>>

                                                            <span class="slider round"></span>

                                                        </label>

                                                    </td>
                                                    <td><?= date('d M, Y h:i A', strtotime(utcToIst($cron->created_at)));?></td>
<td><?= date('d M, Y h:i A', strtotime(utcToIst($cron->updated_at)));?></td>
<td><?= !empty($cron->last_executed) ? date('d M, Y h:i A', strtotime(utcToIst($cron->last_executed))) : 'Never'; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-fw btn-inverse-primary" data-bs-toggle="dropdown" aria-expanded="false" style="padding : 6px 12px;"> Action </button>
                                                        <div class="dropdown-menu" style="">
                                                            <a class="dropdown-item btn-edit-cron" data-bs-toggle="modal" data-bs-target="#edit_cron"
                                                                data-id="<?= $cron->cron_id ?>"
                                                                data-name="<?= htmlspecialchars($cron->name) ?>"
                                                                data-command="<?= htmlspecialchars($cron->command) ?>"
                                                                data-schedule="<?= htmlspecialchars($cron->schedule) ?>">
                                                                <i class="ti-pencil"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item" href="<?= base_url('crons/run_now/'.$cron->cron_id) ?>" onclick="return confirm('Are you sure to run this cron ?')"><i class="ti-control-play"></i> Sync Again</a>
                                                            
                                                        </div>  
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
        setTimeout(function() {
            $.ajax({
                url: '<?= base_url("crons/get_controller_functions") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    controller: controller
                },
                success: function(response) {

                    var html = '<option value="">----- Select Function -----</option>';

                    $.each(response, function(index, function_name) {
                        html += '<option value="' + function_name + '">' +
                                function_name +
                                '</option>';
                    });

                    $('#add_cron_function_name').html(html);
                }
            })
        },1000);
    });
    $(document).on('change', '#add_cron_schedule', function() {
        var schedule = $(this).val();

        $('#cron_time').val('');
        $('#cron_day').val('');
        $('#cron_day_of_the_month').val('');

        $('#time, #day, #day_of_the_month, #minute_gap_div, #hour_gap_div').stop(true, true).slideUp(100);

        setTimeout(function() {
            switch (schedule) {
                case 'every_minute':
                    $('#minute_gap_div').slideDown(200);
                    break;

                case 'hourly':
                    $('#hour_gap_div').slideDown(200);
                    break;

                case 'daily':
                    $('#time').slideDown(200);
                    break;

                case 'weekly':
                    $('#time, #day').slideDown(200);
                    break;

                case 'monthly':
                    $('#time, #day_of_the_month').slideDown(200);
                    break;
            }
        }, 100);
    });

    // Flatpickr for edit cron time
    flatpickr("#edit_cron_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false
    });

    $(document).on('change', '#edit_cron_schedule', function() {
        var schedule = $(this).val();

        $('#edit_cron_time').val('');
        $('#edit_cron_day').val('');
        $('#edit_cron_day_of_the_month').val('');

        $('#edit_time, #edit_day, #edit_day_of_the_month, #edit_minute_gap_div, #edit_hour_gap_div').stop(true, true).slideUp(100);

        setTimeout(function() {
            switch (schedule) {
                case 'every_minute':
                    $('#edit_minute_gap_div').slideDown(200);
                    break;

                case 'hourly':
                    $('#edit_hour_gap_div').slideDown(200);
                    break;

                case 'daily':
                    $('#edit_time').slideDown(200);
                    break;

                case 'weekly':
                    $('#edit_time, #edit_day').slideDown(200);
                    break;

                case 'monthly':
                    $('#edit_time, #edit_day_of_the_month').slideDown(200);
                    break;
            }
        }, 100);
    });

    $(document).on('click', '.btn-edit-cron', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var command = $(this).data('command');
        var schedule = $(this).data('schedule');

        $('#edit_cron_id').val(id);
        $('#edit_cron_name').val(name);
        $('#edit_cron_command').val(command);

        // Hide schedule elements initially
        $('#edit_time, #edit_day, #edit_day_of_the_month, #edit_minute_gap_div, #edit_hour_gap_div').hide();
        $('#edit_cron_schedule').val('#');
        $('#edit_cron_time').val('');
        $('#edit_cron_day').val('#');
        $('#edit_cron_day_of_the_month').val('#');
        $('#edit_minute_gap').val('1');
        $('#edit_hour_gap').val('1');

        // Parse schedule expression to populate values
        var parts = schedule.split(/\s+/);
        if (parts.length === 5) {
            var min = parts[0];
            var hour = parts[1];
            var dom = parts[2];
            var mon = parts[3];
            var dow = parts[4];

            // Helper to format time
            var formatTime = function(h, m) {
                h = parseInt(h);
                m = parseInt(m);
                var ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12; // 0 should be 12
                var mStr = m < 10 ? '0' + m : m;
                var hStr = h < 10 ? '0' + h : h;
                return hStr + ':' + mStr + ' ' + ampm;
            };

            var daysMap = {
                '0': 'sunday', '1': 'monday', '2': 'tuesday', '3': 'wednesday',
                '4': 'thursday', '5': 'friday', '6': 'saturday', '7': 'sunday'
            };

            if (schedule === '* * * * *') {
                $('#edit_cron_schedule').val('every_minute');
                $('#edit_minute_gap').val('1');
                $('#edit_minute_gap_div').show();
            } else if (schedule.match(/^\*\/(\d+) \* \* \* \*$/)) {
                var m = schedule.match(/^\*\/(\d+) \* \* \* \*$/);
                $('#edit_cron_schedule').val('every_minute');
                $('#edit_minute_gap').val(m[1]);
                $('#edit_minute_gap_div').show();
            } else if (schedule === '0 * * * *') {
                $('#edit_cron_schedule').val('hourly');
                $('#edit_hour_gap').val('1');
                $('#edit_hour_gap_div').show();
            } else if (schedule.match(/^0 \*\/(\d+) \* \* \*$/)) {
                var m = schedule.match(/^0 \*\/(\d+) \* \* \*$/);
                $('#edit_cron_schedule').val('hourly');
                $('#edit_hour_gap').val(m[1]);
                $('#edit_hour_gap_div').show();
            } else if (dow !== '*' && !isNaN(hour) && !isNaN(min)) {
                $('#edit_cron_schedule').val('weekly');
                $('#edit_cron_time').val(formatTime(hour, min));
                $('#edit_cron_day').val(daysMap[dow] || 'sunday');
                $('#edit_time, #edit_day').show();
            } else if (dom !== '*' && !isNaN(hour) && !isNaN(min)) {
                $('#edit_cron_schedule').val('monthly');
                $('#edit_cron_time').val(formatTime(hour, min));
                $('#edit_cron_day_of_the_month').val(parseInt(dom));
                $('#edit_time, #edit_day_of_the_month').show();
            } else if (!isNaN(hour) && !isNaN(min)) {
                $('#edit_cron_schedule').val('daily');
                $('#edit_cron_time').val(formatTime(hour, min));
                $('#edit_time').show();
            }
        }
    });

    function toggleCron(id, state) {

            fetch("<?= base_url('crons/toggle_status/') ?>" + id, {

                method: "GET"

            })

            .then(res => res.json())

            .then(data => {

                if (!data.success) {

                    alert("Failed to update status");

                }

            })

            .catch(() => {

                alert("Error updating cron status");

            });

        }
    
</script>
