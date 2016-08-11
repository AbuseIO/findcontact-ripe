<?php

namespace AbuseIO\FindContact;

use AbuseIO\Models\Account;
use AbuseIO\Models\Contact;
use RipeDB\Client;
use Log;

/**
 * Class Ripe
 * @package AbuseIO\FindContact
 */
class Ripe
{
    /**
     * Get the abuse email address registered for this ip.
     * @param  string $ip   IPv4 Address
     * @return object       Returns contact object or false.
     */
    public function getContactByIp($ip)
    {
        $result = false;

        try {
            $data = $this->_getContactData($ip);

            if (!empty($data)) {

                // construct new contact
                $result = new Contact();
                $result->name        = $data['name'];
                $result->reference   = $data['name'];
                $result->email       = $data['abusemailbox'];
                $result->enabled     = true;
                $result->auto_notify = config("Findcontact.findcontact-ripe.auto_notify");
                $result->account_id  = Account::getSystemAccount()->id;
                $result->api_host    = '';
            }

        }
        catch (\Exception $e)
        {
            Log::debug("Error while talking to the Ripe DB : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this domain.
     * @param  string $ip   Domain name
     * @return object       Returns contact object or false.
     */
    public function getContactByDomain($domain)
    {
        return false;
    }

    /**
     * Get the email address registered for this ip.
     * @param  string $id   ID/Contact reference
     * @return object       Returns contact object or false.
     */
    public function getContactById($id)
    {
        return false;
    }

    /**
     * search the ip in the Ripe DB and if found, return the abusemailbox and name of the netowner
     *
     * @param $ip
     * @return array
     */
    private function _getContactData($ip)
    {
        $data = [];

        // test or production environment
        $env = config("Findcontact.findcontact-ripe.environment");
        $ripe = new Client($env);

        $responses = $ripe->search([$ip]);
        if (!empty($responses))
        {
            $xml = $responses[0];

            // get the net description as name
            $name = (string) $xml->xpath("(//attribute[@name='descr'])[1]/@value")[0];

            // try if there is an 'abuse-mailbox' attribute on the top level
            $abusemailbox_xml = $xml->xpath("(//attribute[@name='abuse-mailbox'])[1]/@value");

            if (empty($abusemailbox_xml))
            {
                // loop over the tech and admin contacts to see if there is an abuse-mailbox
                foreach( ['admin-c', 'tech-c'] as $contact )
                {
                    // get the contacts
                    $contacts = $xml->xpath("(//attribute[@name='$contact'])[1]/@value");

                    foreach ($contacts as $c)
                    {
                        $contact_responses = $ripe->search([$c]);
                        $contact_xml = $contact_responses[0];

                        $contact_abusemailbox_xml =
                            $contact_xml->xpath("(//attribute[@name='abuse-mailbox'])[1]/@value");
                        if (!empty($contact_abusemailbox_xml))
                        {
                            $abusemailbox_xml = $contact_abusemailbox_xml;
                            break 2;
                        }
                    }
                }
            }

            if (!empty($abusemailbox_xml)) {
                // only fill the array if we have both attributes
                $data['abusemailbox'] = (string) $abusemailbox_xml[0];
                $data['name'] = $name;
            }
        }

        return $data;
    }
}
