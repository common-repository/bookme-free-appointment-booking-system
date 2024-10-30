<?php defined('ABSPATH') or die('No script kiddies please!'); // No direct access
$codes = array(
    array('code' => 'employee_name', 'description' => __('name of employee', 'bookme')),
    array('code' => 'next_day_agenda', 'description' => __('employee agenda for next day', 'bookme')),
    array('code' => 'tomorrow_date', 'description' => __('date of next day', 'bookme')),
);
\Bookme\Inc\Mains\Functions\System::shortcodes($codes);