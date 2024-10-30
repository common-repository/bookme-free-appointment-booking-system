<?php defined('ABSPATH') or die('No script kiddies please!'); ?>
<form class="theme-form bm-ajax-form" data-tab="payments">
    <div class="bookme-card card">
        <div class="card-header">
            <h5><?php esc_html_e('Payments', 'bookme') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="bookme_currency"><?php esc_html_e('Currency', 'bookme') ?></label>
                        <select id="bookme_currency" class="form-control" name="bookme_currency">
                            <?php foreach (\Bookme\Inc\Mains\Functions\Price::get_currencies() as $code => $currency) { ?>
                                <option value="<?php echo esc_attr($code) ?>"
                                        data-symbol="<?php echo esc_attr($currency['symbol']) ?>" <?php selected(get_option('bookme_currency'), $code) ?> ><?php echo esc_html($code) ?>
                                    (<?php echo esc_html($currency['symbol']) ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="bookme_price_format"><?php esc_html_e('Price format', 'bookme') ?></label>
                        <select id="bookme_price_format" class="form-control"
                                name="bookme_price_format">
                            <?php foreach (\Bookme\Inc\Mains\Functions\Price::get_formats() as $format) { ?>
                                <option value="<?php echo esc_attr($format) ?>" <?php selected(get_option('bookme_price_format'), $format) ?> ></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="bookme_local_enabled">
                            <?php esc_html_e('Service paid locally', 'bookme') ?>
                        </label>
                        <div class="form-toggle-option">
                            <div>
                                <label for="bookme_local_enabled"><?php esc_html_e('Enable', 'bookme') ?></label>
                            </div>
                            <div>
                                <input type="hidden" name="bookme_local_enabled" value="0">
                                <label class="switch switch-sm">
                                    <input name="bookme_local_enabled" type="checkbox" id="bookme_local_enabled"
                                           value="1" <?php checked(get_option('bookme_local_enabled'), 1) ?>>
                                    <span class="switch-state"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <?php \Bookme\Inc\Mains\Functions\System::csrf() ?>
            <button type="submit" class="btn btn-primary"><?php esc_html_e('Save', 'bookme') ?></button>
        </div>
    </div>
</form>