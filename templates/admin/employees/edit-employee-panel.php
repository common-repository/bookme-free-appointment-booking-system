<?php defined('ABSPATH') or die('No script kiddies please!');// No direct access
use \Bookme\Inc\Mains\Functions\System; ?>

<div class="bookme-page-wrapper">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo esc_html($employee['full_name']) ?></h2>
            </div>
            <div class="slidePanel-actions">
                <button class="btn-icon btn-primary ajax-update-employee" title="<?php esc_attr_e('Save', 'bookme') ?>">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn-icon btn-danger ajax-delete-employee"
                        title="<?php esc_attr_e('Delete', 'bookme') ?>">
                    <i class="icon-feather-trash-2"></i>
                </button>
                <button class="btn-icon slidePanel-close" title="<?php esc_attr_e('Close', 'bookme') ?>">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form method="post" class="theme-form">
            <div class="bm-accordion" id="accordion">
                <!-- Details -->
                <div class="card bookme-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link pl-0" data-toggle="collapse" data-target="#details"
                                    aria-expanded="true" aria-controls="details"
                                    type="button"><?php esc_html_e('Details', 'bookme') ?></button>
                        </h5>
                    </div>
                    <div class="collapse show" id="details" aria-labelledby="details" data-parent="#accordion">
                        <div class="card-body">
                            <div class="employee-image">
                                <div class="image-box employee-image-selector">
                                    <?php
                                    $img = wp_get_attachment_image_src($employee['attachment_id'], 'thumbnail');
                                    $img_url = $img ? $img[0] : BOOKME_URL . '/assets/admin/images/user-default.png';
                                    ?>
                                    <img class="img-round"
                                         src="<?php echo esc_url($img_url); ?>"
                                         alt="<?php echo esc_html($employee['full_name']); ?>">
                                    <i class="icon-feather-camera"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-required">
                                        <label for="bookme-full-name"><?php esc_html_e('Full name', 'bookme') ?></label>
                                        <input type="text" class="form-control" id="bookme-full-name" name="full_name"
                                               value="<?php echo esc_attr($employee['full_name']) ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="bookme-wp-user"><?php esc_html_e(' WP User', 'bookme') ?>
                                            <i class="dashicons dashicons-editor-help"
                                               title="<?php esc_attr_e('Here you can assign a WordPress user to the staff member, if you want to give the admin access to the staff member. User with "Administrator" role will have access to all the pages and settings, user with another role will have access to only their personal settings.', 'bookme') ?>"
                                               data-tippy-placement="top"></i></label>
                                        <select class="form-control" name="wp_user_id" id="bookme-wp-user">
                                            <option value=""><?php esc_attr_e('Select WP user', 'bookme') ?></option>
                                            <?php foreach ($wp_users as $user) { ?>
                                                <option value="<?php echo esc_attr($user['ID']) ?>"
                                                        data-email="<?php echo esc_attr($user['user_email']) ?>" <?php selected($user['ID'], $employee['wp_user_id']) ?>><?php echo esc_html($user['display_name']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="bookme-email"><?php esc_html_e('Email', 'bookme') ?></label>
                                        <input class="form-control" id="bookme-email" name="email"
                                               value="<?php echo esc_attr($employee['email']) ?>"
                                               type="text"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="bookme-phone"><?php esc_html_e('Phone', 'bookme') ?></label>
                                        <input class="form-control" id="bookme-phone"
                                               value="<?php echo esc_attr($employee['phone']) ?>"
                                               type="text"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bookme-visibility"><?php esc_html_e('Visibility', 'bookme') ?></label>
                                <select name="visibility" class="form-control" id="bookme-visibility">
                                    <option value="public" <?php selected($employee['visibility'], 'public') ?>><?php esc_attr_e('Public', 'bookme') ?></option>
                                    <option value="private" <?php selected($employee['visibility'], 'private') ?>><?php esc_attr_e('Private', 'bookme') ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bookme-info"><?php esc_html_e('Info', 'bookme') ?></label>
                                <textarea id="bookme-info" name="info" rows="3"
                                          class="form-control"><?php echo esc_textarea($employee['info']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Services -->
                <div class="card bookme-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link pl-0" data-toggle="collapse" data-target="#services"
                                    aria-expanded="false" aria-controls="services"
                                    type="button"><?php esc_html_e('Services', 'bookme') ?></button>
                        </h5>
                    </div>
                    <div class="collapse" id="services" aria-labelledby="services" data-parent="#accordion">
                        <div class="card-body">
                            <div class="m-b-20">
                                <select name="services[]" id="bookme-services" class="form-control"
                                        multiple
                                        data-placeholder="<?php esc_attr_e('Select Services', 'bookme') ?>"
                                        data-nothing="<?php esc_attr_e('No service selected', 'bookme') ?>"
                                        data-selected="<?php esc_attr_e('selected', 'bookme') ?>"
                                        data-selectall="<?php esc_attr_e('Select All', 'bookme') ?>"
                                        data-unselectall="<?php esc_attr_e('Unselect All', 'bookme') ?>"
                                        data-allselected="<?php esc_attr_e('All Services Selected', 'bookme') ?>">
                                    <?php foreach ($all_services as $category => $services) { ?>
                                        <optgroup label="<?php echo esc_attr($category) ?>">
                                            <?php foreach ($services as $service) { ?>
                                                <option value="<?php echo esc_attr($service['id']) ?>" <?php selected(array_key_exists($service['id'], $employee_services)) ?>><?php echo esc_html($service['title']) ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php foreach ($all_services as $services) {
                                foreach ($services as $service) {
                                    ?>
                                    <div class="row row-sm m-b-20 align-items-center d-none bm-service-group bm-service-group-<?php echo esc_attr($service['id']) ?>"
                                         data-service-id="<?php echo esc_attr($service['id']) ?>"
                                        <?php echo !array_key_exists($service['id'], $employee_services) ? 'style="display:none"' : '' ?>>
                                        <div class="col-4">
                                            <div class="bm-three-dots"><?php echo esc_html($service['title']) ?></div>
                                        </div>
                                        <div class="col-8">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <input class="form-control" type="text"
                                                           name="price[<?php echo esc_attr($service['id']) ?>]"
                                                           value="<?php echo esc_attr(array_key_exists($service['id'], $employee_services) ? $employee_services[$service['id']]['price'] : $service['price']) ?>" <?php disabled(!array_key_exists($service['id'], $employee_services)) ?>>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <input class="form-control bookme-capacity-min"
                                                                   type="number" min="1"
                                                                   name="capacity_min[<?php echo esc_attr($service['id']) ?>]"
                                                                   value="<?php echo esc_attr(array_key_exists($service['id'], $employee_services) ? $employee_services[$service['id']]['capacity_min'] : $service['capacity_min']) ?>" <?php disabled(!array_key_exists($service['id'], $employee_services)) ?>>
                                                        </div>
                                                        <div class="col-6">
                                                            <input class="form-control bookme-capacity-max"
                                                                   type="number" min="1"
                                                                   name="capacity_max[<?php echo esc_attr($service['id']) ?>]"
                                                                   value="<?php echo esc_attr(array_key_exists($service['id'], $employee_services) ? $employee_services[$service['id']]['capacity_max'] : $service['capacity_max']) ?>" <?php disabled(!array_key_exists($service['id'], $employee_services)) ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
                <!-- Schedule -->
                <div class="card bookme-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link pl-0" data-toggle="collapse" data-target="#schedule"
                                    aria-expanded="false" aria-controls="schedule"
                                    type="button"><?php esc_html_e('Schedule', 'bookme') ?></button>
                        </h5>
                    </div>
                    <div class="collapse" id="schedule" aria-labelledby="schedule" data-parent="#accordion">
                        <div class="card-body">
                            <?php
                            foreach ($employee_schedule as $schedule) { ?>
                                <div data-id="<?php echo esc_attr($schedule['day_index']) ?>"
                                     data-staff-schedule-id="<?php echo esc_attr($schedule['id']) ?>">
                                    <div class="row row-sm m-b-15 py-1 bg-light">
                                        <div class="col-sm-3">
                                            <strong><?php esc_html_e(\Bookme\Inc\Mains\Functions\DateTime::get_week_day_by_number($schedule['day_index'] - 1, true) /* take translation from WP catalog */) ?></strong>
                                        </div>
                                        <div class="col-sm-9 d-none d-sm-block">
                                            <strong><?php esc_html_e('Time (Start - End)', 'bookme') ?></strong>
                                        </div>
                                    </div>
                                    <div class="row row-sm m-b-20 <?php echo empty($schedule['breaks'])?'align-items-center':'' ?>">
                                        <div class="col-sm-3 m-b-5">
                                            <label class="switch" data-tippy-placement="top"
                                                   title="<?php esc_attr_e('Day Off', 'bookme') ?>">
                                                <input class="schedule-day-off"
                                                       type="checkbox" <?php checked(!$schedule['start_time']) ?>><span
                                                        class="switch-state"></span>
                                            </label>
                                            <input type="hidden"
                                                   name="days[<?php echo esc_attr($schedule['id']) ?>]"
                                                   value="<?php echo esc_attr($schedule['day_index']) ?>">
                                        </div>
                                        <div class="col-sm-9 m-b-5 schedule-day-off-hide">
                                            <div class="input-group">
                                                <select name="start_time[<?php echo esc_attr($schedule['day_index']); ?>]" class="form-control schedule-start" id="schedule-start-<?php echo esc_attr($schedule['id']) ?>" data-last-value="<?php echo esc_attr($schedule['start_time']); ?>">
                                                    <?php echo System::schedule_options($schedule['start_time'], 'from'); ?>
                                                </select>
                                                <span class="input-group-text">-</span>
                                                <select name="end_time[<?php echo esc_attr($schedule['day_index']); ?>]" class="form-control schedule-end" id="schedule-end-<?php echo esc_attr($schedule['id']) ?>" data-last-value="<?php echo esc_attr($schedule['end_time']); ?>">
                                                    <?php echo System::schedule_options($schedule['end_time']); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="attachment_id" value="<?php echo esc_attr($employee['attachment_id']) ?>">
            <input type="hidden" name="id" value="<?php echo esc_attr($employee['id']) ?>">
            <?php System::csrf() ?>
        </form>
    </div>
</div>