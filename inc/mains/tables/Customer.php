<?php

namespace Bookme\Inc\Mains\Tables;

use Bookme\Inc;

/**
 * Class Customer
 */
class Customer extends Inc\Core\Table
{
    /** @var  string */
    protected $full_name = '';
    /** @var  string */
    protected $first_name = '';
    /** @var  string */
    protected $last_name = '';
    /** @var  string */
    protected $phone = '';
    /** @var  string */
    protected $email = '';
    /** @var  int */
    protected $wp_user_id;
    /** @var  string */
    protected $notes = '';

    protected static $table = 'bm_customers';

    protected static $schema = array(
        'id' => array('format' => '%d'),
        'full_name' => array('format' => '%s'),
        'first_name' => array('format' => '%s'),
        'last_name' => array('format' => '%s'),
        'phone' => array('format' => '%s'),
        'email' => array('format' => '%s'),
        'wp_user_id' => array('format' => '%d'),
        'notes' => array('format' => '%s'),
    );

    /**
     * Save data to database.
     * Fill name, first_name, last_name before save
     *
     * @return int|false
     */
    public function save()
    {
        if ((!Inc\Mains\Functions\System::show_first_last_name() && $this->get_full_name() != '') || ($this->get_full_name() != '' && $this->get_first_name() == '' && $this->get_last_name() == '')) {
            $full_name = explode(' ', $this->get_full_name(), 2);
            $this->set_first_name($full_name[0]);
            $this->set_last_name(isset ($full_name[1]) ? trim($full_name[1]) : '');
        } else {
            $this->set_full_name(trim(rtrim($this->get_first_name()) . ' ' . ltrim($this->get_last_name())));
        }

        return parent::save();
    }

    /**
     * Get wp_user_id
     *
     * @return int
     */
    public function get_wp_user_id()
    {
        return $this->wp_user_id;
    }

    /**
     * Associate WP user with customer.
     *
     * @param int $wp_user_id
     * @return $this
     */
    public function set_wp_user_id($wp_user_id = 0)
    {
        if ($wp_user_id == 0) {
            $wp_user_id = $this->create_wp_user();
        }

        if ($wp_user_id) {
            $this->wp_user_id = $wp_user_id;
        }

        return $this;
    }

    /**
     * Delete customer
     *
     * @param bool $with_wp_user
     */
    public function delete_with_wp_user($with_wp_user)
    {
        if ($with_wp_user && $this->get_wp_user_id()
            // Can't delete your WP account
            && ($this->get_wp_user_id() != get_current_user_id())) {
            wp_delete_user($this->get_wp_user_id());
        }

        /** @var Booking[] $bookings */
        global $wpdb;
        $data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `" . Booking::get_table_name() . "` a 
                LEFT JOIN `" . CustomerBooking::get_table_name() . "` AS `ca` ON ca.booking_id = a.id
                WHERE ca.customer_id = %d 
                GROUP BY a.id",
                $this->get_id()
            ),
            ARRAY_A
        );
        $bookings = Inc\Mains\Functions\System::bind_data_with_table(Booking::class, $data);

        $this->delete();

        foreach ($bookings as $booking) {
            // Google Calendar.
            $booking->handle_google_calendar();
        }
    }

    /**
     * Create new WP user and send email notification.
     *
     * @return int|false
     */
    private function create_wp_user()
    {
        // Generate unique username.
        $base = Inc\Mains\Functions\System::show_first_last_name() ? sanitize_user(sprintf('%s %s', $this->get_first_name(), $this->get_last_name()), true) : sanitize_user($this->get_full_name(), true);
        $base = $base != '' ? $base : 'client';
        $username = $base;
        $i = 1;
        while (username_exists($username)) {
            $username = $base . $i;
            ++$i;
        }
        // Generate password.
        $password = wp_generate_password(6, true);
        // Create user.
        $user_id = wp_create_user($username, $password, $this->get_email());
        if (!$user_id instanceof \WP_Error) {
            // Set the role
            $user = new \WP_User($user_id);
            $user->set_role(get_option('bookme_customer_new_account_role', 'subscriber'));

            // Send email/sms notification.
            Inc\Mains\Notification\Sender::send_new_user_credentials($this, $username, $password);

            return $user_id;
        }

        return false;
    }

    /**
     * Gets full_name
     *
     * @return string
     */
    public function get_full_name()
    {
        return $this->full_name;
    }

    /**
     * Sets full_name
     *
     * @param string $full_name
     * @return $this
     */
    public function set_full_name($full_name)
    {
        $this->full_name = $full_name;

        return $this;
    }

    /**
     * Gets first_name
     *
     * @return string
     */
    public function get_first_name()
    {
        return $this->first_name;
    }

    /**
     * Sets first_name
     *
     * @param string $first_name
     * @return $this
     */
    public function set_first_name($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Gets last_name
     *
     * @return string
     */
    public function get_last_name()
    {
        return $this->last_name;
    }

    /**
     * Sets last_name
     *
     * @param string $last_name
     * @return $this
     */
    public function set_last_name($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Gets phone
     *
     * @return string
     */
    public function get_phone()
    {
        return $this->phone;
    }

    /**
     * Sets phone
     *
     * @param string $phone
     * @return $this
     */
    public function set_phone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * Sets email
     *
     * @param string $email
     * @return $this
     */
    public function set_email($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets notes
     *
     * @return string
     */
    public function get_notes()
    {
        return $this->notes;
    }

    /**
     * Sets notes
     *
     * @param string $notes
     * @return $this
     */
    public function set_notes($notes)
    {
        $this->notes = $notes;

        return $this;
    }
}