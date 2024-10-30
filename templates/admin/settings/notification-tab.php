<?php defined('ABSPATH') or die('No script kiddies please!'); ?>
<form class="theme-form bm-ajax-form" data-tab="notifications">
    <div class="bookme-card card">
        <div class="card-header">
            <h5><?php esc_html_e('Notifications', 'bookme') ?></h5>
        </div>
        <div class="card-body">
            <h5 class="m-b-20"><?php esc_html_e('Email Notifications', 'bookme') ?></h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_email_sender_name"><?php _e('Sender name', 'bookme') ?></label>
                        <input id="bookme_email_sender_name" name="bookme_email_sender_name"
                               class="form-control"
                               type="text"
                               value="<?php echo esc_attr(get_option('bookme_email_sender_name') == '' ?
                                   get_option('blogname') : get_option('bookme_email_sender_name')) ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_email_sender"><?php _e('Sender email', 'bookme') ?></label>
                        <input id="bookme_email_sender" name="bookme_email_sender"
                               class="form-control" type="text"
                               value="<?php echo esc_attr(get_option('bookme_email_sender') == '' ?
                                   get_option('admin_email') : get_option('bookme_email_sender')) ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_email_send_as">
                            <?php esc_html_e('Send emails as', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('HTML allows formatting, fonts, colors etc. With text you must use the text-mode of the editor below.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <select class="form-control" name="bookme_email_send_as" id="bookme_email_send_as">
                            <option value="html" <?php selected(get_option('bookme_email_send_as'),'html') ?>><?php esc_html_e('HTML', 'bookme') ?></option>
                            <option value="text" <?php selected(get_option('bookme_email_send_as'),'text') ?>><?php esc_html_e('Text', 'bookme') ?></option>
                        </select></div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="bookme_email_reply_to_customers">
                            <?php esc_html_e('Reply directly to customers', 'bookme') ?>
                            <i class="dashicons dashicons-editor-help"
                               title="<?php esc_attr_e('If this option is enabled, then staff members can directly reply to the email, to reply to the customer.', 'bookme') ?>"
                               data-tippy-placement="top"></i>
                        </label>
                        <div class="form-toggle-option" style="max-width: 100%">
                            <div>
                                <label for="bookme_email_reply_to_customers"><?php esc_html_e('Enable', 'bookme') ?></label>
                            </div>
                            <div>
                                <input type="hidden" name="bookme_email_reply_to_customers" value="0">
                                <label class="switch switch-sm">
                                    <input name="bookme_email_reply_to_customers" type="checkbox"
                                           id="bookme_email_reply_to_customers"
                                           value="1" <?php checked(get_option('bookme_email_reply_to_customers'), 1) ?>>
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