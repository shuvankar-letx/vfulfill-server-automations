<nav class="d-flex col-12 col-lg-12 fixed-top flex-row navbar p-0">
    <div class="d-flex align-items-center justify-content-start navbar-brand-wrapper text-center"><a class="navbar-brand brand-logo me-5" href=index.html><img alt=logo src="<?= base_url('assets/images/logo.png') ?>"class=me-2></a><a class="navbar-brand brand-logo-mini" href=index.html><img alt=logo src="<?= base_url('assets/images/favicon.png') ?>"></a></div>
    <div
    class="d-flex align-items-center justify-content-end navbar-menu-wrapper">
        <button class="navbar-toggler navbar-toggler align-self-center" data-toggle=minimize type=button><span class=icon-menu></span></button>
        <ul class="navbar-nav navbar-nav-right">
            <li class="dropdown nav-item"><a class="dropdown-toggle nav-link count-indicator" href=# data-bs-toggle=dropdown id=notificationDropdown><i class="mx-0 icon-bell"></i> <span class=count></span></a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                aria-labelledby=notificationDropdown>
                    <p class="font-weight-normal dropdown-header float-left mb-0">Notifications</p>
                    <a class="dropdown-item preview-item">
                        <div class=preview-thumbnail>
                            <div class="bg-success preview-icon"><i class="mx-0 ti-info-alt"></i></div>
                        </div>
                        <div class=preview-item-content>
                            <h6 class="font-weight-normal preview-subject">Application Error</h6>
                            <p class="mb-0 font-weight-light small-text text-muted">Just now</div>
                    </a>
                </div>
                <li class="dropdown nav-item nav-profile"><a class="dropdown-toggle nav-link" href=# data-bs-toggle=dropdown id=profileDropdown><img alt=profile src="<?= base_url('assets/images/faces/face28.jpg') ?>"></a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby=profileDropdown><a class=dropdown-item href="<?= base_url('login/logout');?>"><i class="text-primary ti-power-off"></i> Logout</a></div>
        </ul>
        <button class="navbar-toggler align-self-center d-lg-none navbar-toggler-right" data-toggle=offcanvas type=button><span class=icon-menu></span></button>
        </div>
</nav>