<?php
namespace Bookme\Inc\Mains;

/**
 * Class SMS
 * TODO: Check for Twilio curl based api
 */
class SMS
{
    private $acc_sid,
        $auth_token,
        $twilio_no,
        $errors = array();

    public function __construct()
    {
        $this->acc_sid = get_option('bookme_twillio_account_sid');
        $this->auth_token = get_option('bookme_twillio_auth_token');
        $this->twilio_no = get_option('bookme_twillio_phone_number');
    }

    /**
     * Send SMS
     *
     * @param string $phone_number
     * @param string $message
     * @return bool
     */
    public function send_sms($phone_number, $message)
    {
        return true;
    }

    /**
     * Return phone_number in international format
     *
     * @param $phone_number
     * @return string
     */
    public function normalize_phone_number($phone_number)
    {
        // Remove everything except numbers and "+".
        $phone_number = preg_replace('/[^\d\+]/', '', $phone_number);

        if (strpos($phone_number, '+') === 0) {
            // ok.
        } elseif (strpos($phone_number, '00') === 0) {
            $phone_number = ltrim($phone_number, '0');
        } else {
            // Default country code can contain not permitted characters. Remove everything except numbers.
            $phone_number = ltrim(preg_replace('/\D/', '', get_option('bookme_default_country_code', '')), '0') . ltrim($phone_number, '0');
        }

        return $phone_number;
    }


    /**
     * @return array
     */
    public function get_errors()
    {
        return $this->errors;
    }

    public function clear_errors()
    {
        $this->errors = array();
    }

}