<nav class="sidebar sidebar-offcanvas" id=sidebar>
    <ul class=nav>
        <li class=nav-item><a class=nav-link href="<?php echo base_url('dashboard')?>"><i class="icon-grid menu-icon"></i> <span class=menu-title>Dashboard</span></a></li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#cron-submenu" aria-expanded="false" aria-controls="cron-submenu">
                <i class="icon-watch menu-icon"></i>
                <span class="menu-title">Crons</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="cron-submenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('crons')?>">Cron List</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('crons/logs')?>">Execution Logs</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('crons/analytics')?>">Analytics</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?php echo base_url('crons/health')?>">Health</a></li>
                </ul>
            </div>
        </li>
        <li class=nav-item><a class=nav-link href="<?php echo base_url('workers')?>"><i class="icon-cog menu-icon"></i> <span class=menu-title>Workers</span></a></li>
        <li class=nav-item><a class=nav-link href="<?php echo base_url('system_health')?>"><i class="icon-bar-graph menu-icon"></i> <span class=menu-title>System Health</span></a></li>
    </ul>
</nav>