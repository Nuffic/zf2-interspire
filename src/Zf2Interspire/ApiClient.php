<?php

namespace Zf2Interspire;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class ApiClient extends Configurable {

    public $apiToken;
    public $apiUser;
    public $host;
    public $endPoint;
    public $format = 'html';
    public $confirmed = 'yes';

    private $client;
    private $xml;

    public function getClient() {
        if(!isset($this->_lient)) {
            $this->client = new Client();
        }
        return $this->client;
    }

    public function getXml() {
        if(!isset($this->xml)) {
            $this->xml = new Xml();
        }
        return $this->xml;
    }

    public function addSubcscriber($email, $listId, array $customFields = [], $format = 'html', $confirmed = 'yes') {
        $details = [
            'format'       => $format,
            'confirmed'    => $confirmed,
            'emailaddress' => $email,
            'mailinglist'  => $listId,
            'customfields' => $customFields,
        ];

        $result = $this->getXml()->toArray($this->sendRequest('AddSubscriberToList', $details));
        if(!count($customFields)) {
            return;
        }
        if (strpos($result['errormessage'], 'already exists in the given list') !== false) {
            $this->updateCustomFields($email, $listId, $customFields);
        }
    }

    public function updateCustomFields($email, $listId, array $customFields = []) {
        $details = [
            'emailaddress' => $email,
            'mailinglist'  => $listId,
            'customfields' => $customFields,
        ];

        $this->sendRequest('updatesubscriber', $details);
    }

    private function sendRequest($requestMethod, array $details) {

        $payload = [
            'username'      => $this->apiUser,
            'usertoken'     => $this->apiToken,
            'requesttype'   => 'subscribers',
            'requestmethod' => $requestMethod,
            'details'       => $details,
        ];

        try {
            return $this->getClient()->post(
                $this->endPoint,
                [
                    'headers' => ['Host' => $this->host],
                    'body'    => $this->getXml()->fromArray($payload)
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }
}