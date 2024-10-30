<?php defined('ABSPATH') or die('No script kiddies please!');// No direct access

use \Bookme\Inc\Mains\Functions\System;
use \Bookme\Inc\Mains\Functions\DateTime;
use \Bookme\Inc\Mains\Tables\Service;

$time_interval = get_option('bookme_time_slot_step');
$service_id = isset($service['id']) ? $service['id'] : null;
$assigned_staff_ids = isset($service['staff_ids']) ? explode(',', $service['staff_ids']) : array();
$all_staff_selected = count($assigned_staff_ids) == count($employees);
?>

<div class="bookme-page-wrapper">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2><?php echo !empty($service['title'])? esc_html($service['title']) : esc_html__('Add Service', 'bookme') ?></h2>
            </div>
            <div class="slidePanel-actions">
                <button class="btn-icon btn-primary ajax-save-service" title="<?php esc_attr_e('Save', 'bookme') ?>">
                    <i class="icon-feather-check"></i>
                </button>
                <?php if($service_id) { ?>
                    <button class="btn-icon btn-danger ajax-delete-service" title="<?php esc_attr_e('Delete', 'bookme') ?>">
                        <i class="icon-feather-trash-2"></i>
                    </button>
                    <?php
                }
                ?>
                <button class="btn-icon slidePanel-close" title="<?php esc_attr_e('Close', 'bookme') ?>">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form method="post" class="theme-form">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="title"><?php esc_html_e('Title', 'bookme') ?></label>
                        <div class="d-flex align-items-center">
                            <div class="bm-color-picker-wrapper">
                                <button class="bm-color-picker"></button>
                                <input type="hidden" class="color-input" name="color"
                                       value="<?php echo isset($service['color']) ? esc_attr($service['color']): null; ?>">
                            </div>
                            <div class="flex-grow-1 m-l-20 form-required">
                                <input name="title" value="<?php echo esc_attr($service['title']) ?>"
                                       id="title" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="duration">
                            <?php esc_html_e('Duration', 'bookme') ?>
                        </label>
                        <select id="duration" class="form-control"
                                name="duration">
                            <?php for ($j = $time_interval; $j <= 720; $j += $time_interval) { ?><?php if ($service['duration'] / 60 > $j - $time_interval && $service['duration'] / 60 < $j) { ?>
                                <option value="<?php echo esc_attr($service['duration']) ?>"
                                        selected><?php echo DateTime::seconds_to_interval($service['duration']) ?></option><?php } ?>
                                <option
                                value="<?php echo esc_attr($j * 60) ?>" <?php selected($service['duration'], $j * 60) ?>><?php echo DateTime::seconds_to_interval($j * 60) ?></option><?php } ?>
                            <?php for ($j = 86400; $j <= 604800; $j += 86400) { ?>
                                <option
                                value="<?php echo esc_attr($j) ?>" <?php selected($service['duration'], $j) ?>><?php echo DateTime::seconds_to_interval($j) ?></option><?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="price"><?php esc_html_e('Price', 'bookme') ?></label>
                        <input id="price" class="form-control bookme-question"
                               type="number" min="0" step="1" name="price"
                               value="<?php echo isset($service['price']) ? esc_attr($service['price']): null ?>">
                    </div>
                </div>
            </div>
            <div class="row" style="display: none">
                <div class="col-6">
                    <div class="form-group">
                        <label for="start_time_info"><?php esc_html_e('Start time of the appointment', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('Set the start time of the appointment with service duration longer than 1 day. This time will be displayed in notifications to customers.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <input id="start_time_info" class="form-control"
                               type="text" name="start_time_info"
                               value="<?php echo isset($service['start_time_info']) ? esc_attr($service['start_time_info']): null ?>">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="end_time_info"><?php esc_html_e('End time of the appointment', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('Set the end time of the appointment with service duration longer than 1 day. This time will be displayed in notifications to customers.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <input id="end_time_info" class="form-control" type="text" name="end_time_info"
                               value="<?php echo isset($service['end_time_info']) ? esc_attr($service['end_time_info']): null ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="capacity"><?php esc_html_e('Capacity (min and max)', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('Here you can set the minimum and maximum number of persons for one booking.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <input id="capacity_min"
                                       class="form-control bookme-question bookme-capacity-min"
                                       type="number" min="1"
                                       step="1" name="capacity_min"
                                       value="<?php echo !empty($service['capacity_min'])?esc_attr($service['capacity_min']):1 ?>">
                            </div>
                            <div class="col-6">
                                <input id="capacity_max"
                                       class="form-control bookme-question bookme-capacity-max"
                                       type="number" min="1"
                                       step="1" name="capacity_max"
                                       value="<?php echo !empty($service['capacity_max'])?esc_attr($service['capacity_max']):1 ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="visibility"><?php esc_html_e('Visibility', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('To hide the service from your customers set the visibility to "Private".', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <select name="visibility" class="form-control"
                                id="visibility">
                            <option value="public" <?php selected($service['visibility'], 'public') ?>><?php esc_attr_e('Public', 'bookme') ?></option>
                            <option value="private" <?php selected($service['visibility'], 'private') ?>><?php esc_attr_e('Private', 'bookme') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group form-required">
                        <label for="category"><?php esc_html_e('Category', 'bookme') ?></label>
                        <select id="category" class="form-control" name="category_id">
                            <option value=""><?php esc_attr_e('Select Category', 'bookme') ?></option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo esc_attr($category['id']) ?>" <?php selected($category['id'], $service['category_id']) ?>><?php echo esc_html($category['name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme-js-staff-selector"><?php esc_html_e('Staff Members', 'bookme') ?></label><br>
                        <select name="staff_ids[]" id="bookme-employees" class="form-control"
                                multiple
                                data-placeholder="<?php esc_attr_e('Select Staff Members', 'bookme') ?>"
                                data-nothing="<?php esc_attr_e('No staff selected', 'bookme') ?>"
                                data-selected="<?php esc_attr_e('selected', 'bookme') ?>"
                                data-selectall="<?php esc_attr_e('Select All', 'bookme') ?>"
                                data-unselectall="<?php esc_attr_e('Unselect All', 'bookme') ?>"
                                data-allselected="<?php esc_attr_e('All Staffs Selected', 'bookme') ?>">
                            <?php foreach ($employees as $i => $staff) { ?>
                                <option value="<?php echo esc_attr($staff['id']) ?>" <?php selected(in_array($staff['id'], $assigned_staff_ids)) ?>><?php echo esc_html($staff['full_name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="info"><?php esc_html_e('Info', 'bookme') ?></label>
                <textarea class="form-control" id="info" name="info" rows="2"
                          type="text"><?php echo isset($service['info']) ? esc_attr($service['info']): null ?></textarea>
            </div>
            <input type="hidden" name="action" value="bookme_update_service">
            <input type="hidden" name="id" value="<?php echo esc_attr($service_id) ?>">
            <input type="hidden" name="update_staff" value="0">
            <?php System::csrf() ?>
        </form>
    </div>
</div>