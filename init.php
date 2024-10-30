<?php
/*
Plugin Name: Bookme - Free Appointment Booking System
Plugin URI: https://bookme.bylancer.com/
Description: Bookme is a responsive, multipurpose and fully customizable WordPress appointment booking and scheduling software for accepting online appointments bookings & scheduling.
Version: 4.2
Author: Bylancer
Author URI: https://bylancer.com/
Text Domain: bookme
Domain Path: /languages
License: Free
*/

namespace Bookme;

defined('ABSPATH') or die('No script kiddies please!'); // No direct access

define('BOOKME_VERSION', '4.2');
define('BOOKME_PLUGIN_SLUG', plugin_basename(__FILE__));
define('BOOKME_PATH', plugin_dir_path(__FILE__));
define('BOOKME_URL', plugin_dir_url(__FILE__));


if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50500) {
    function bookme_php_outdated()
    {
        echo '<div class="updated"><h3>Bookme</h3><p>To install the plugin - <strong>PHP 5.5</strong> or higher is required.</p></div>';
    }

    add_action(is_network_admin() ? 'network_admin_notices' : 'admin_notices', 'bookme_php_outdated');
} else {

    include_once __DIR__ . '/inc/autoload.php';

    /**
     * Plugin Class
     */
    class BookmePlugin
    {

        /**
         * Init.
         */
        public function init()
        {
            if (!session_id()) {
                // WP 4.9+ fix loopback request failure and WP Site Health
                if (!isset($_GET['wp_scrape_key']) &&
                    !(isset($_POST['action']) && strncmp($_POST['action'], 'health-check-', 13) === 0)) {
                    // Start session.
                    @session_start();
                }
            }

            if (!is_admin()) {
                // Payments ( PayPal Express Checkout, 2Checkout, Mollie )
                if (isset($_REQUEST['bookme_action'])) {
                    // Disable caching.
                    Inc\Mains\Functions\System::no_cache();

                    switch ($_REQUEST['bookme_action']) {
                        // PayPal Express Checkout.
                        case 'paypal-ec-init':
                            $this->paypalApp->ec_init();
                            break;
                        case 'paypal-ec-return':
                            $this->paypalApp->ec_return();
                            break;
                        case 'paypal-ec-cancel':
                            $this->paypalApp->ec_cancel();
                            break;
                        case 'paypal-ec-error':
                            $this->paypalApp->ec_error();
                            break;
                    }
                }
            }
        }

        /**
         * Admin menu.
         */
        public function add_admin_menu()
        {
            global $current_user, $submenu, $wpdb;

            if (
                $current_user->has_cap('administrator') ||
                $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . Inc\Mains\Tables\Employee::get_table_name() . "` WHERE `wp_user_id` = %d", $current_user->ID))
            ) {
                add_menu_page('Bookme Lite', 'Bookme Lite', 'read', 'bookme-menu', '',
                    BOOKME_URL . 'assets/admin/images/menu-logo.png', 40);

                // sub-menu pages.
                $services = esc_html__('Services', 'bookme');
                $staff_members = esc_html__('Staff Members', 'bookme');
                $appointments = esc_html__('All Bookings', 'bookme');
                $customers = esc_html__('Customers', 'bookme');
                $payments = esc_html__('Payments', 'bookme');
                $appearance = esc_html__('Appearance', 'bookme');
                $notifications = esc_html__('Notifications', 'bookme');
                $settings = esc_html__('Settings', 'bookme');

                add_submenu_page('bookme-menu', $staff_members, $staff_members, 'manage_options',
                        App\Admin\Employees::page_slug, array($this->employee_app, 'execute'));
                add_submenu_page('bookme-menu', $services, $services, 'manage_options',
                    App\Admin\Services::page_slug, array($this->service_app, 'execute'));
                add_submenu_page('bookme-menu', $appointments, $appointments, 'manage_options',
                    App\Admin\Bookings::page_slug, array($this->bookings_app, 'execute'));
                add_submenu_page('bookme-menu', $customers, $customers, 'manage_options',
                    App\Admin\Customers::page_slug, array($this->customer_app, 'execute'));
                add_submenu_page('bookme-menu', $payments, $payments, 'manage_options',
                    App\Admin\Payments::page_slug, array($this->payment_app, 'execute'));
                add_submenu_page('bookme-menu', $appearance, $appearance, 'manage_options',
                    App\Admin\Appearance::page_slug, array($this->apearance_app, 'execute'));
                add_submenu_page('bookme-menu', $notifications, $notifications, 'manage_options',
                    App\Admin\Notifications::page_slug, array($this->notifications_app, 'execute'));
                add_submenu_page('bookme-menu', $settings, $settings, 'manage_options',
                    App\Admin\Settings::page_slug, array($this->settings_app, 'execute'));

                unset($submenu['bookme-menu'][0]);
            }
        }

        /**
         * Add the Bookme Button for shortcode
         */
        public function shortcode_button()
        {
            new App\Admin\ShortcodeButton();
        }

        /**
         * Enqueue frontend assets
         */
        public function enqueue_assets()
        {
            $assets = BOOKME_URL . 'assets/front';
            $admin_assets = BOOKME_URL . 'assets/admin';

            // load assets for booking form
            if (get_option('bookme_phone_default_country') != 'disabled') {
                wp_enqueue_style('bookme-intlTelInput', $assets . '/css/intlTelInput.css', array(), BOOKME_VERSION);
            }
            wp_enqueue_style('bookme-scroll', $assets . '/css/trackpad-scroll.css', array(), BOOKME_VERSION);
            wp_enqueue_style('bookme', $assets . '/css/bookme.css', array(), BOOKME_VERSION);
            if (is_rtl()) {
                wp_enqueue_style('bookme-rtl', $assets . '/css/bookme-rtl.css', array(), BOOKME_VERSION);
            }

            wp_enqueue_script('bookme-scroll', $assets . '/js/jquery.scroll.min.js', array('jquery'), BOOKME_VERSION);
            if (get_option('bookme_phone_default_country') != 'disabled') {
                wp_enqueue_script('bookme-intlTelInput-js', $assets . '/js/intlTelInput.min.js', array('jquery'), BOOKME_VERSION);
            }
            //wp_enqueue_script('bookme-moment-js', $admin_assets . '/js/moment.min.js', array('jquery'), BOOKME_VERSION);
            wp_enqueue_script('bookme-clndr-js', $assets . '/js/clndr.js', array('jquery', 'underscore', 'moment'), BOOKME_VERSION);

            if (strpos(get_option('bookme_custom_fields'), '"captcha"') !== false) {
                wp_enqueue_script('bookme-google-recaptcha-js', 'https://www.google.com/recaptcha/api.js?render=explicit', array());
            }

            wp_enqueue_script('bookme-js', $assets . '/js/bookme.js', array('bookme-clndr-js', 'bookme-scroll'), BOOKME_VERSION);

            global $sitepress, $wp_locale;
            // AJAX url with WPML support
            $ajax_url = admin_url('admin-ajax.php');
            if ($sitepress instanceof \SitePress) {
                $ajax_url .= (strpos($ajax_url, '?') ? '&' : '?') . 'lang=' . $sitepress->get_current_language();
            }

            $woocommerce_enabled = (int)Inc\Mains\Functions\System::woo_commerce_enabled();
            $intlTelInput = array('enabled' => 0);
            if (get_option('bookme_phone_default_country') != 'disabled') {
                $intlTelInput['enabled'] = 1;
                $intlTelInput['utils'] = BOOKME_URL . 'assets/front/js/intlTelInput.utils.js';
                $intlTelInput['country'] = get_option('bookme_phone_default_country');
            }

            $required = array(
                'staff' => (int)get_option('bookme_required_employee')
            );

            wp_localize_script('bookme-js', 'Bookme', array(
                'csrf_token' => Inc\Mains\Functions\System::get_security_token(),
                'months' => array_values($wp_locale->month),
                'days' => array_values($wp_locale->weekday),
                'daysShort' => array_values($wp_locale->weekday_abbrev),
                'start_of_week' => (int)get_option('start_of_week'),
                'ajaxurl' => $ajax_url,
                'required' => $required,
                'final_step_url' => get_option('bookme_final_step_url'),
                'intlTelInput' => $intlTelInput,
                'woocommerce' => array('enabled' => $woocommerce_enabled, 'cart_url' => $woocommerce_enabled ? wc_get_cart_url() : ''),
                'cart' => array('enabled' => $woocommerce_enabled ? 0 : (int)Inc\Mains\Functions\System::show_step_cart()),
                'is_rtl' => (int)is_rtl(),
                'not_available' => esc_html__('Not Available', 'bookme'),
            ));
        }

        /**
         * Add a Go Pro link on the plugin page
         * @param $links
         * @return array
         */
        public function add_gopro_link($links)
        {
            $action = array('<a href="https://1.envato.market/c/1265721/275988/4415?subId1=appointment&subId2=Bookme&u=https%3A%2F%2Fcodecanyon.net%2Fitem%2Fbookme-wordpress-booking-plugin%2F20926116" target="_blank" style="font-weight: bold; color: #58ee42;">' .esc_html__('Go Pro', 'bookme').'</a>');
            return  array_merge( $links, $action );
        }

        /**
         * Constructor.
         */
        public function __construct()
        {
            // Call all the hooks
            add_action('wp_loaded', array($this, 'init'));

            // Front Apps
            $this->bookingFormApp = App\Front\BookingForm::get_instance();

            // Apps for payment gateways
            if (Inc\Mains\Functions\System::payment_type_enabled(Inc\Mains\Tables\Payment::TYPE_PAYPAL)) {
                $this->paypalApp = App\Front\PayPal::get_instance();
            }

            if (is_admin()) {
                add_action('admin_menu', array($this, 'add_admin_menu'));
                add_action('admin_init', array($this, 'shortcode_button'));

                // for Site Health
                add_filter('site_status_tests', function ($tests) {
                    // Close current session, for fixing loopback request
                    session_write_close();
                    return $tests;
                }, 10, 1);

                // Admin Apps
                $this->apearance_app = App\Admin\Appearance::get_instance();
                $this->bookings_app = App\Admin\Bookings::get_instance();
                $this->calendar_app = App\Admin\Calendar::get_instance();
                $this->customer_app = App\Admin\Customers::get_instance();
                $this->notifications_app = App\Admin\Notifications::get_instance();
                $this->payment_app = App\Admin\Payments::get_instance();
                $this->service_app = App\Admin\Services::get_instance();
                $this->settings_app = App\Admin\Settings::get_instance();
                $this->employee_app = App\Admin\Employees::get_instance();
            } else {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

                // Register shortcode
                add_shortcode('bookme', array($this->bookingFormApp, 'execute'));
            }

            // add go pro link on plugins page
            add_filter('plugin_action_links_'.BOOKME_PLUGIN_SLUG, array($this, 'add_gopro_link'));
        }
    }

    // run the plugin
    Inc\Mains\Plugin::run();
    new BookmePlugin();
}