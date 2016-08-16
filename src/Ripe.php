<?php

namespace AbuseIO\FindContact;

use AbuseIO\Models\Account;
use AbuseIO\Models\Contact;
use RipeStat\AbuseContactFinder;
use Log;

/**
 * Class Ripe
 * @package AbuseIO\FindContact
 */
class Ripe
{
    /**
     * Get the abuse email address registered for this ip.
     * @param  string $ip IPv4 Address
     * @return mixed Returns contact object or false.
     */
    public function getContactByIp($ip)
    {
        $result = false;

        try {
            $data = $this->_getContactData($ip);

            if (!empty($data)) {

                // construct new contact
                $result = new Contact();
                $result->name = $data['name'];
                $result->reference = $data['name'];
                $result->email = $data['email'];
                $result->enabled = true;
                $result->auto_notify = config("Findcontact.findcontact-ripe.auto_notify");
                $result->account_id = Account::getSystemAccount()->id;
                $result->api_host = '';
            }

        } catch (\Exception $e) {
            Log::debug("Error while talking to the Ripe Stat API : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this domain.
     * @param  string $domain Domain name
     * @return mixed Returns contact object or false.
     */
    public function getContactByDomain($domain)
    {
        return false;
    }

    /**
     * Get the email address registered for this ip.
     * @param  string $id ID/Contact reference
     * @return mixed Returns contact object or false.
     */
    public function getContactById($id)
    {
        return false;
    }

    /**
     * search the ip using the ripe stat api and if found, return the abuse mailbox and network name
     *
     * @param $ip
     * @return array
     */
    private function _getContactData($ip)
    {
        $data = [];
        $name = null;
        $email = null;

        // create a new AbuseContactFinder with the configged appid
        $appid = config("Findcontact.findcontact-ripe.appid");
        $finder = new AbuseContactFinder($appid);

        $response = $finder->get($ip);

        // check if the nessecary properties exist
        if (isset($response->holder_info) && isset($response->holder_info->name)) {
            $name = $response->holder_info->name;
        }

        if (isset($response->anti_abuse_contacts) && isset($response->anti_abuse_contacts->abuse_c)) {
            foreach ($response->anti_abuse_contacts->abuse_c as $abuse_c) {
                if (isset($abuse_c->email)) {
                    $email = $abuse_c->email;
                    break;
                }
            }
        }

        // only create a result data if both email and name are set
        if (!is_null($name) && !is_null($email)) {
            $data['name'] = $name;
            $data['email'] = $email;
        }

        return $data;
    }
}
