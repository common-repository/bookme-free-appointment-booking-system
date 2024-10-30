<?php

namespace Bookme\Inc\Mains\Tables;

use Bookme\Inc;

/**
 * Class Booking
 */
class Booking extends Inc\Core\Table
{
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $staff_any = 0;
    /** @var  int */
    protected $service_id;
    /** @var  string */
    protected $start_date;
    /** @var  string */
    protected $end_date;
    /** @var  string */
    protected $google_event_id;
    /** @var  string */
    protected $internal_note;

    protected static $table = 'bm_bookings';

    protected static $schema = array(
        'id' => array('format' => '%d'),
        'staff_id' => array('format' => '%d', 'reference' => array('table' => 'Employee')),
        'staff_any' => array('format' => '%d'),
        'service_id' => array('format' => '%d', 'reference' => array('table' => 'Service')),
        'start_date' => array('format' => '%s'),
        'end_date' => array('format' => '%s'),
        'google_event_id' => array('format' => '%s'),
        'internal_note' => array('format' => '%s'),
    );

    /**
     * Save booking to database and delete event in Google Calendar if staff changes
     *
     * @return false|int
     */
    public function save()
    {
        // Google Calendar.
        if ($this->is_loaded() && $this->has_google_calendar_event()) {
            $modified = $this->get_modified();
            if (array_key_exists('staff_id', $modified)) {
                // Delete event from the Google Calendar of the old staff if the staff was changed.
                $staff_id = $this->get_staff_id();
                $this->set_staff_id($modified['staff_id']);
                $this->delete_google_calendar_event();
                $this->set_staff_id($staff_id)
                    ->set_google_event_id(null);
            }
        }

        return parent::save();
    }

    /**
     * Delete data from database and delete event in Google Calendar if it exists
     *
     * @return bool|false|int
     */
    public function delete()
    {
        // Delete all CustomerBookings for current bookings
        global $wpdb;
        $cb_list = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `" . Inc\Mains\Tables\CustomerBooking::get_table_name() . "` 
                    WHERE booking_id = %d",
                $this->get_id()
            ),
            ARRAY_A);
        $cb_list = Inc\Mains\Functions\System::bind_data_with_table(Inc\Mains\Tables\CustomerBooking::class, $cb_list);

        /** @var CustomerBooking $cb */
        foreach ($cb_list as $cb) {
            $cb->delete();
        }

        $result = parent::delete();
        if ($result) {
            if ($this->has_google_calendar_event()) {
                $this->delete_google_calendar_event();
            }
        }

        return $result;
    }

    /**
     * Get service color
     *
     * @param string $default
     * @return string
     */
    public function get_color($default = '#333333')
    {
        if (!$this->is_loaded()) {
            return $default;
        }
        $service = new Service();
        if ($service->load($this->get_service_id())) {
            return $service->get_color();
        }
        return $default;
    }

    /**
     * Get CustomerBooking tables
     *
     * @param bool $with_cancelled
     * @return CustomerBooking[] Array of tables
     */
    public function get_customer_bookings($with_cancelled = false)
    {
        $result = array();

        if ($this->get_id()) {
            $query = "SELECT 
                            cb.*, c.full_name, c.first_name, c.last_name, c.phone, c.email
                        FROM `" . CustomerBooking::get_table_name() . "` AS cb 
                        LEFT JOIN `" . Customer::get_table_name() . "` AS `c` ON c.id = cb.customer_id 
                        WHERE cb.booking_id = %d";
            if (!$with_cancelled) {
                $query .= " AND `cb`.`status` IN ('" . CustomerBooking::STATUS_PENDING . "','" . CustomerBooking::STATUS_APPROVED . "') ";
            }
            global $wpdb;
            $array = $wpdb->get_results(
                $wpdb->prepare(
                    $query,
                    $this->get_id()
                ),
                ARRAY_A
            );

            foreach ($array as $data) {
                $cb = new CustomerBooking($data);

                // Insert Customer data
                $cb->customer = new Customer();
                $data['id'] = $data['customer_id'];
                $cb->customer->set_fields($data, true);

                $result[] = $cb;
            }
        }

        return $result;
    }

    /**
     * Set array of customers associated with this booking.
     *
     * @param array $cst_data Array of customer IDs, custom_fields, number_of_persons, extras and status
     * @return CustomerBooking[] Array of customer_booking with changed status
     */
    public function save_customer_bookings(array $cst_data)
    {
        global $wpdb;

        $cb_status_changed = array();
        $cb_data = array();
        foreach ($cst_data as $item) {
            if (array_key_exists('ca_id', $item)) {
                $cb_id = $item['ca_id'];
            } else do {
                // New CustomerBooking.
                $cb_id = 'new-' . mt_rand(1, 999);
            } while (array_key_exists($cb_id, $cb_data) === true);
            $cb_data[$cb_id] = $item;
        }

        // Retrieve customer bookings IDs currently associated with this booking.
        $current_ids = array_map(function (CustomerBooking $cb) {
            return $cb->get_id();
        }, $this->get_customer_bookings(true));
        $ids_to_delete = array_diff($current_ids, array_keys($cb_data));
        if (!empty ($ids_to_delete)) {
            // Remove redundant customer bookings
            $wpdb->query("DELETE FROM `" . Inc\Mains\Tables\CustomerBooking::get_table_name() . "` WHERE id IN (" . implode(',', $ids_to_delete) . ")");
        }
        // Add new customer bookings.
        foreach (array_diff(array_keys($cb_data), $current_ids) as $id) {
            $customer_booking = new CustomerBooking();
            $customer_booking
                ->set_booking_id($this->get_id())
                ->set_customer_id($cb_data[$id]['id'])
                ->set_custom_fields(json_encode($cb_data[$id]['custom_fields']))
                ->set_status($cb_data[$id]['status'])
                ->set_number_of_persons($cb_data[$id]['number_of_persons'])
                ->set_created_from($cb_data[$id]['created_from'])
                ->set_created(current_time('mysql'))
                ->save();
            $cb_status_changed[] = $customer_booking;
        }

        // Update existing customer bookings.
        foreach (array_intersect($current_ids, array_keys($cb_data)) as $id) {
            $customer_booking = new CustomerBooking();
            $customer_booking->load($id);

            if ($customer_booking->get_status() != $cb_data[$id]['status']) {
                $cb_status_changed[] = $customer_booking;
                $customer_booking->set_status($cb_data[$id]['status']);
            }
            $customer_booking
                ->set_number_of_persons($cb_data[$id]['number_of_persons'])
                ->set_custom_fields(json_encode($cb_data[$id]['custom_fields']))
                ->save();
        }

        return $cb_status_changed;
    }

    /**
     * Create or update event in Google Calendar.
     *
     * @return bool
     */
    public function handle_google_calendar()
    {
        if ($this->has_google_calendar_event()) {
            return $this->update_google_calendar_event();
        } else {
            $google_event_id = $this->create_google_calendar_event();
            if ($google_event_id) {
                $this->set_google_event_id($google_event_id);
                return (bool)$this->save();
            }
        }

        return false;
    }

    /**
     * Check whether this booking has an associated event in Google Calendar.
     *
     * @return bool
     */
    public function has_google_calendar_event()
    {
        return !empty($this->google_event_id);
    }

    /**
     * Create a new event in Google Calendar and associate it to this booking.
     *
     * @return string|false
     */
    public function create_google_calendar_event()
    {
        $google = new Inc\Mains\Google();
        if ($google->load_by_staff_id($this->get_staff_id())) {
            // Create new event in Google Calendar.
            return $google->create_event($this);
        }

        return false;
    }

    public function update_google_calendar_event()
    {
        $google = new Inc\Mains\Google();
        if ($google->load_by_staff_id($this->get_staff_id())) {
            // Update existing event in Google Calendar.
            return $google->update_event($this);
        }

        return false;
    }

    /**
     * Delete event from Google Calendar associated to this booking.
     *
     * @return bool
     */
    public function delete_google_calendar_event()
    {
        $google = new Inc\Mains\Google();
        if ($google->load_by_staff_id($this->get_staff_id())) {
            // Delete existing event in Google Calendar.
            return $google->delete($this->get_google_event_id());
        }

        return false;
    }

    /**
     * Gets staff_id
     *
     * @return int
     */
    public function get_staff_id()
    {
        return $this->staff_id;
    }

    /**
     * Sets staff
     *
     * @param Employee $staff
     * @return $this
     */
    public function set_staff(Employee $staff)
    {
        return $this->set_staff_id($staff->get_id());
    }

    /**
     * Sets staff_id
     *
     * @param int $staff_id
     * @return $this
     */
    public function set_staff_id($staff_id)
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Gets staff_any
     *
     * @return int
     */
    public function get_staff_any()
    {
        return $this->staff_any;
    }

    /**
     * Sets staff_any
     *
     * @param int $staff_any
     * @return $this
     */
    public function set_staff_any($staff_any)
    {
        $this->staff_any = $staff_any;

        return $this;
    }

    /**
     * Gets service_id
     *
     * @return int
     */
    public function get_service_id()
    {
        return $this->service_id;
    }

    /**
     * Sets service
     *
     * @param Service $service
     * @return $this
     */
    public function set_service(Service $service)
    {
        return $this->set_service_id($service->get_id());
    }

    /**
     * Sets service_id
     *
     * @param int $service_id
     * @return $this
     */
    public function set_service_id($service_id)
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Gets start_date
     *
     * @return string
     */
    public function get_start_date()
    {
        return $this->start_date;
    }

    /**
     * Sets start_date
     *
     * @param string $start_date
     * @return $this
     */
    public function set_start_date($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * Gets end_date
     *
     * @return string
     */
    public function get_end_date()
    {
        return $this->end_date;
    }

    /**
     * Sets end_date
     *
     * @param string $end_date
     * @return $this
     */
    public function set_end_date($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * Gets google_event_id
     *
     * @return string
     */
    public function get_google_event_id()
    {
        return $this->google_event_id;
    }

    /**
     * Sets google_event_id
     *
     * @param string $google_event_id
     * @return $this
     */
    public function set_google_event_id($google_event_id)
    {
        $this->google_event_id = $google_event_id;

        return $this;
    }

    /**
     * Gets internal_note
     *
     * @return string
     */
    public function get_internal_note()
    {
        return $this->internal_note;
    }

    /**
     * Sets internal_note
     *
     * @param string $internal_note
     * @return $this
     */
    public function set_internal_note($internal_note)
    {
        $this->internal_note = $internal_note;

        return $this;
    }
}