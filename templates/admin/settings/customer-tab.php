<?php defined('ABSPATH') or die('No script kiddies please!'); ?>
<form class="theme-form bm-ajax-form" data-tab="customers">
    <div class="bookme-card card">
        <div class="card-header">
            <h5><?php esc_html_e('Customers', 'bookme') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_customer_create_account">
                            <?php esc_html_e('Create WP user account for customers', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('Create WP user account for new customers.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <div class="form-toggle-option" style="max-width: 100%">
                            <div>
                                <label for="bookme_customer_create_account"><?php esc_html_e('Enable', 'bookme') ?></label>
                            </div>
                            <div>
                                <input type="hidden" name="bookme_customer_create_account" value="0">
                                <label class="switch switch-sm">
                                    <input name="bookme_customer_create_account" type="checkbox"
                                           id="bookme_customer_create_account"
                                           value="1" <?php checked(get_option('bookme_customer_create_account'), 1) ?>>
                                    <span class="switch-state"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_customer_new_account_role">
                            <?php esc_html_e('New user account role', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('Select role for newly created WP users for customer.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <select class="form-control" name="bookme_customer_new_account_role"
                                id="bookme_customer_new_account_role">
                            <?php foreach ($new_user_roles as $role) {
                                print_r($role)?>
                                <option value="<?php echo esc_attr($role[0]) ?>"
                                    <?php selected(get_option('bookme_customer_new_account_role'), $role[0]) ?>>
                                    <?php echo esc_html($role[1]) ?>
                                </option>
                            <?php } ?>
                        </select>
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