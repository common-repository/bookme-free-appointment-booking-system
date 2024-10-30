<?php
defined('ABSPATH') or die('No script kiddies please!'); // No direct access ?>
<!-- Page Header Start-->
<div class="page-main-header">
    <div class="main-header-right">
        <div class="main-header-left text-center">
            <div class="logo-wrapper"><a href="#"><img src="<?php echo BOOKME_URL.'assets/admin/images/logo.png'; ?>" alt=""></a></div>
        </div>
        <div class="mobile-sidebar">
            <div class="media-body text-right switch-sm">
                <label class="switch ml-3"><i class="font-primary icon-feather-align-center" id="sidebar-toggle"></i></label>
            </div>
        </div>
        <div class="nav-right col pull-right right-menu">
            <ul class="nav-menus">
                <li><a class="text-dark" href="#" onclick="toggleFullScreen()" title="<?php esc_html_e('Full Screen','bookme') ?>" data-tippy-placement="top"><i class="icon-feather-maximize"></i></a></li>
                <li><button class="btn btn-default" type="button" style="cursor: default"><?php echo esc_html__('Version','bookme').' '.BOOKME_VERSION ?></button></li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Header Ends -->