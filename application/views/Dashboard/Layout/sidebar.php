<?php
$current_controller = $this->router->fetch_class();
$current_method = $this->router->fetch_method();
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item <?= ($current_controller == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?php echo base_url('dashboard')?>">
                <i class="icon-grid menu-icon"></i> 
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item <?= ($current_controller == 'crons') ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#cron-submenu" aria-expanded="<?= ($current_controller == 'crons') ? 'true' : 'false' ?>" aria-controls="cron-submenu">
                <i class="icon-watch menu-icon"></i>
                <span class="menu-title">Crons</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= ($current_controller == 'crons') ? 'show' : '' ?>" id="cron-submenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'crons' && $current_method == 'index') ? 'active' : '' ?>" href="<?php echo base_url('crons')?>">Cron List</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'crons' && $current_method == 'logs') ? 'active' : '' ?>" href="<?php echo base_url('crons/logs')?>">Execution Logs</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'crons' && $current_method == 'analytics') ? 'active' : '' ?>" href="<?php echo base_url('crons/analytics')?>">Analytics</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'crons' && $current_method == 'health') ? 'active' : '' ?>" href="<?php echo base_url('crons/health')?>">Health</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item <?= ($current_controller == 'workers') ? 'active' : '' ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#worker-submenu" aria-expanded="<?= ($current_controller == 'workers') ? 'true' : 'false' ?>" aria-controls="worker-submenu">
                <i class="icon-cog menu-icon"></i>
                <span class="menu-title">Workers</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse <?= ($current_controller == 'workers') ? 'show' : '' ?>" id="worker-submenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'workers' && $current_method == 'index') ? 'active' : '' ?>" href="<?php echo base_url('workers')?>">Worker List</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'workers' && $current_method == 'logs') ? 'active' : '' ?>" href="<?php echo base_url('workers/logs')?>">Execution Logs</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'workers' && $current_method == 'analytics') ? 'active' : '' ?>" href="<?php echo base_url('workers/analytics')?>">Analytics</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link <?= ($current_controller == 'workers' && $current_method == 'health') ? 'active' : '' ?>" href="<?php echo base_url('workers/health')?>">Health</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item <?= ($current_controller == 'system_health') ? 'active' : '' ?>">
            <a class="nav-link" href="<?php echo base_url('system_health')?>">
                <i class="icon-bar-graph menu-icon"></i> 
                <span class="menu-title">System Health</span>
            </a>
        </li>
    </ul>
</nav>