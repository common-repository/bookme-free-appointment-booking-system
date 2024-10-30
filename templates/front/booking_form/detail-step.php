<?php defined('ABSPATH') or die('No script kiddies please!'); // No direct access

use Bookme\Inc\Mains\Functions\System;
use Bookme\Inc\Mains\Tables\Payment;

echo wp_kses_post($progress_bar); ?>
<div class="bookme-booking-detail-step">
    <p><?php esc_html_e('Please provide your details in the form below.', 'bookme') ?></p>

    <?php if (get_option('bookme_customer_show_login_button') && get_current_user_id() == 0) { ?>
        <div class="bookme-form-group">
            <button type="button"
                    class="bookme-button bookme-login-dialog-show"><?php esc_html_e('Login', 'bookme') ?></button>
        </div>
    <?php } ?>

    <div class="bookme-row<?php echo (int)get_option('bookme_form_layout') == 1 ? ' bookme-layout-1': ''; ?>">
        <div class="<?php echo !$disabled ? 'bookme-col-md-6' : 'bookme-col-12' ?>">
            <?php if (System::show_first_last_name()) { ?>
                <div class="bookme-form-group">
                    <label><?php esc_html_e('First Name', 'bookme') ?></label>
                    <input class="bookme-first-name" type="text"
                           value="<?php echo esc_attr($user_data->get('first_name')) ?>"/>
                    <div class="bookme-first-name-error bookme-form-error"></div>
                </div>
                <div class="bookme-form-group">
                    <label><?php esc_html_e('Last Name', 'bookme') ?></label>
                    <input class="bookme-last-name" type="text"
                           value="<?php echo esc_attr($user_data->get('last_name')) ?>"/>
                    <div class="bookme-last-name-error bookme-form-error"></div>
                </div>
            <?php } else { ?>
                <div class="bookme-form-group">
                    <label><?php esc_html_e('Name', 'bookme') ?></label>
                    <input class="bookme-full-name" type="text"
                           value="<?php echo esc_attr($user_data->get('full_name')) ?>"/>
                    <div class="bookme-full-name-error bookme-form-error"></div>
                </div>
            <?php } ?>
            <div class="bookme-form-group">
                <label><?php esc_html_e('Phone', 'bookme') ?></label>
                <div>
                    <input class="bookme-phone<?php if (get_option('bookme_phone_default_country') != 'disabled') { ?> bookme-phone-input<?php } ?>"
                           value="<?php echo esc_attr($user_data->get('phone')) ?>" type="text"/>
                </div>
                <div class="bookme-phone-error bookme-form-error"></div>
            </div>
            <div class="bookme-form-group">
                <label><?php esc_html_e('Email', 'bookme') ?></label>
                <div>
                    <input class="bookme-email" type="text" value="<?php echo esc_attr($user_data->get('email')) ?>"/>
                </div>
                <div class="bookme-email-error bookme-form-error"></div>
            </div>
            <?php foreach ($cf_data as $key => $cf_item) { ?>
                <div class="bookme-custom-fields-wrapper" data-key="<?php echo esc_attr($key) ?>">
                    <?php if ($show_service_title && !empty ($cf_item['custom_fields'])) { ?>
                        <div class="bookme-form-group"><strong><?php echo esc_html($cf_item['service_title']) ?></strong></div>
                    <?php } ?>
                    <?php foreach ($cf_item['custom_fields'] as $custom_field) { ?>
                        <div class="bookme-form-group" data-id="<?php echo esc_attr($custom_field->id) ?>"
                             data-type="<?php echo esc_attr($custom_field->type) ?>">
                            <?php if ($custom_field->type != 'text-content') { ?>
                                <label><?php echo esc_html($custom_field->label); ?></label>
                            <?php } ?>
                            <?php if ($custom_field->type == 'text-field') { ?>
                                <input type="text" class="bookme-custom-field"
                                       value="<?php echo esc_attr(@$cf_item['data'][$custom_field->id]) ?>"/>
                            <?php } elseif ($custom_field->type == 'textarea') { ?>
                                <textarea rows="3"
                                          class="bookme-custom-field"><?php echo esc_textarea(@$cf_item['data'][$custom_field->id]) ?></textarea>
                            <?php } elseif ($custom_field->type == 'text-content') { ?>
                                <?php echo nl2br($custom_field->label) ?>
                            <?php } elseif ($custom_field->type == 'checkboxes') { ?>
                                <div>
                                    <?php foreach ($custom_field->items as $item) { ?>
                                        <label>
                                            <input type="checkbox" class="bookme-custom-field"
                                                   value="<?php echo esc_attr($item['value']) ?>" <?php checked(@in_array($item['value'], @$cf_item['data'][$custom_field->id]), true, true) ?> />
                                            <?php echo esc_html($item['label']) ?>
                                        </label><br/>
                                    <?php } ?>
                                </div>
                            <?php } elseif ($custom_field->type == 'radio-buttons') { ?>
                                <div>
                                    <?php foreach ($custom_field->items as $item) { ?>
                                        <label>
                                            <input type="radio" class="bookme-custom-field"
                                                   name="bookme-custom-field-<?php echo esc_attr($custom_field->id) ?>"
                                                   value="<?php echo esc_attr($item['value']) ?>" <?php checked($item['value'], @$cf_item['data'][$custom_field->id], true) ?> />
                                            <?php echo esc_html($item['label']) ?>
                                        </label><br/>
                                    <?php } ?>
                                </div>
                            <?php } elseif ($custom_field->type == 'drop-down') { ?>
                                <select class="bookme-custom-field">
                                    <option value=""></option>
                                    <?php foreach ($custom_field->items as $item) { ?>
                                        <option value="<?php echo esc_attr($item['value']) ?>" <?php selected($item['value'], @$cf_item['data'][$custom_field->id], true) ?>><?php echo esc_html($item['label']) ?></option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($custom_field->type == 'captcha') { ?>
                                <div class="bookme-g-recaptcha"></div>
                            <?php } ?>
                            <?php if ($custom_field->type != 'text-content') { ?>
                                <div class="bookme-form-error bookme-custom-field-error"></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if (!$disabled) { ?>
            <div class="bookme-col-md-6">
                <?php foreach ($booking_data as $data) { ?>
                    <div class="bookme-detail-appointments">
                        <div class="bookme-d-flex">
                            <div>
                                <?php esc_html_e('Time', 'bookme') ?>
                            </div>
                            <div>
                                <?php echo esc_html($data['service_date']) . ' ' . esc_html($data['service_time']) ?>
                            </div>
                        </div>
                        <div class="bookme-d-flex">
                            <div>
                                <?php
                                $text = '<strong>' . $data['service_name'] . '</strong> ' .
                                    esc_html__('by', 'bookme') .
                                    ' <strong>' . $data['staff_name'] . '</strong>';
                                $text2 = '';
                                if ($data['number_of_persons'] > 1) {
                                    $text2 .= ' <strong>&times; ' . ' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> ' . esc_html($data['number_of_persons']) . '</strong>';
                                }
                                echo wp_kses_post($text) . $text2;
                                ?>
                            </div>
                            <div>
                                <strong><?php echo esc_html($data['service_price']); ?></strong>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="bookme-detail-appointments">
                    <?php if (get_option('bookme_coupons_enabled')) { ?>
                        <div class="bookme-d-flex">
                            <div>
                                <?php esc_html_e('Sub Total', 'bookme') ?>
                            </div>
                            <div>
                                <strong><?php echo esc_html($sub_total) ?></strong>
                            </div>
                        </div>
                        <div class="bookme-d-flex">
                            <div>
                                <?php esc_html_e('Discount', 'bookme') ?>
                            </div>
                            <div>
                                <strong class="bookme-discount-price"><?php echo esc_html($discount_price) ?></strong>
                            </div>
                        </div>
                        <div class="bookme-d-flex">
                            <div>
                                <?php esc_html_e('Coupon', 'bookme') ?>
                                <div class="bookme-form-error bookme-coupon-error"></div>
                            </div>
                            <div>
                                <?php if ($coupon_code) { ?>
                                    <?php echo esc_attr($coupon_code) . ' <strong>✓</strong>' ?>
                                <?php } else { ?>
                                    <div class="bookme-d-flex">
                                        <input class="bookme-coupon-field" name="bookme-coupon-field" type="text"
                                               value="<?php echo esc_attr($coupon_code) ?>"/>
                                        <button class="bookme-button bookme-icon-button bookme-icon-button-sm bookme-apply-coupon"
                                                style="margin-left: 5px;">
                                            <strong>✓</strong>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="bookme-d-flex">
                        <div>
                            <strong><?php esc_html_e('Total', 'bookme') ?></strong>
                        </div>
                        <div>
                            <strong class="bookme-total-price"><?php echo esc_html($total) ?></strong>
                        </div>
                    </div>
                </div>
                <?php if (!System::woo_commerce_enabled()){ ?>
                <div class="bookme-payment-wrapper">
                    <div class="bookme-form-group">
                        <strong><?php esc_html_e('Choose your payment method', 'bookme') ?></strong>
                    </div>
                    <div class="bookme-payment-methods">
                        <?php if ($pay_local) { ?>
                            <div class="bookme-form-group">
                                <label>
                                    <input type="radio" class="bookme-payment" name="bookme-payment-<?php echo esc_attr($form_id) ?>" value="local" data-tab="local"/>
                                    <span><?php esc_html_e('Locally', 'bookme') ?></span>
                                </label>
                                <p class="bookme-pay-tab bookme-local" style="display: none;"><?php esc_html_e('I will pay locally on meeting place.', 'bookme') ?></p>
                            </div>
                        <?php } ?>


                        <div class="bookme-form-group" style="display: none">
                            <input type="radio" class="bookme-payment bookme-coupon-free" name="bookme-payment-<?php echo esc_attr($form_id) ?>" value="coupon"/>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="bookme-step-buttons">
        <div class="bookme-step-buttons-left">
            <button type="button" class="bookme-button bookme-back"><?php esc_html_e('Back', 'bookme') ?></button>
        </div>
        <button type="button" class="bookme-button bookme-next"><?php esc_html_e('Next', 'bookme') ?></button>
    </div>
</div>
