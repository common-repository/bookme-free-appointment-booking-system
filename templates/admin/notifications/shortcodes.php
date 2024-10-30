<?php defined('ABSPATH') or die('No script kiddies please!'); // No direct access
$codes = array(
    array('code' => 'booking_number', 'description' => __('booking number', 'bookme')),
    array('code' => 'booking_date', 'description' => __('date of booking', 'bookme')),
    array('code' => 'booking_end_date', 'description' => __('end date of booking', 'bookme')),
    array('code' => 'booking_time', 'description' => __('time of booking', 'bookme')),
    array('code' => 'booking_end_time', 'description' => __('end time of booking', 'bookme')),
    array('code' => 'number_of_persons', 'description' => __('number of persons', 'bookme')),
    array('code' => 'total_price', 'description' => __('total price of booking (sum of all cart items after applying coupon)', 'bookme')),
    array('code' => 'approve_booking_url', 'description' => esc_html__('URL of approve booking link (to use inside <a> tag)', 'bookme')),
    array('code' => 'cancel_booking_url', 'description' => esc_html__('URL of cancel booking link (to use inside <a> tag)', 'bookme')),
    array('code' => 'cancellation_reason', 'description' => __('reason you mentioned while deleting booking', 'bookme')),
    array('code' => 'reject_booking_url', 'description' => esc_html__('URL of reject booking link (to use inside <a> tag)', 'bookme')),
    array('code' => 'payment_type', 'description' => __('payment type', 'bookme')),
    array('code' => 'customer_name', 'description' => __('full name of customer', 'bookme')),
    array('code' => 'customer_first_name', 'description' => __('first name of customer', 'bookme')),
    array('code' => 'customer_last_name', 'description' => __('last name of customer', 'bookme')),
    array('code' => 'customer_email', 'description' => __('email of customer', 'bookme')),
    array('code' => 'customer_phone', 'description' => __('phone of customer', 'bookme')),
    array('code' => 'category_name', 'description' => __('name of category', 'bookme')),
    array('code' => 'service_name', 'description' => __('name of service', 'bookme')),
    array('code' => 'service_price', 'description' => __('price of service', 'bookme')),
    array('code' => 'service_duration', 'description' => __('duration of service', 'bookme')),
    array('code' => 'service_info', 'description' => __('info of service', 'bookme')),
    array('code' => 'employee_name', 'description' => __('name of employee', 'bookme')),
    array('code' => 'employee_phone', 'description' => __('phone of employee', 'bookme')),
    array('code' => 'employee_email', 'description' => __('email of employee', 'bookme')),
    array('code' => 'employee_info', 'description' => __('info of employee', 'bookme')),
    array('code' => 'employee_photo', 'description' => __('photo of staff', 'bookme')),
    array('code' => 'company_name', 'description' => __('name of company', 'bookme')),
    array('code' => 'company_logo', 'description' => __('company logo', 'bookme')),
    array('code' => 'company_address', 'description' => __('address of company', 'bookme')),
    array('code' => 'company_phone', 'description' => __('company phone', 'bookme')),
    array('code' => 'company_website', 'description' => __('company web-site address', 'bookme')),
    array('code' => 'custom_fields', 'description' => __('combined values of all custom fields', 'bookme')),
    array('code' => 'custom_fields_2col', 'description' => __('combined values of all custom fields (formatted in 2 columns)', 'bookme')),
    array('code' => 'google_calendar_url', 'description' => esc_html__('URL for adding event to customer\'s Google Calendar (to use inside <a> tag)', 'bookme')),
);
\Bookme\Inc\Mains\Functions\System::shortcodes($codes);