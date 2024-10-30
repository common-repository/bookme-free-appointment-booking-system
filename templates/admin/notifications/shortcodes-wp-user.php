<?php defined('ABSPATH') or die('No script kiddies please!'); // No direct access
$codes = array(
    array('code' => 'customer_name', 'description' => __('full name of customer', 'bookme')),
    array('code' => 'customer_first_name', 'description' => __('first name of customer', 'bookme')),
    array('code' => 'customer_last_name', 'description' => __('last name of customer', 'bookme')),
    array('code' => 'customer_email', 'description' => __('email of customer', 'bookme')),
    array('code' => 'customer_phone', 'description' => __('phone of customer', 'bookme')),
    array('code' => 'company_name', 'description' => __('name of your company', 'bookme')),
    array('code' => 'company_logo', 'description' => __('your company logo', 'bookme')),
    array('code' => 'company_phone', 'description' => __('your company phone', 'bookme')),
    array('code' => 'company_website', 'description' => __('this web-site address', 'bookme')),
    array('code' => 'company_address', 'description' => __('address of your company', 'bookme')),
    array('code' => 'new_username', 'description' => __('customer new username', 'bookme')),
    array('code' => 'new_password', 'description' => __('customer new password', 'bookme')),
    array('code' => 'site_address', 'description' => __('site address', 'bookme')),
);
\Bookme\Inc\Mains\Functions\System::shortcodes($codes);