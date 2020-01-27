<?php

namespace App\Services\HumanityAPI;

use Illuminate\Support\Facades\Artisan;

class HumanityAPICore
{
        private $clientId;
        private $clientSecret;
        private $grantType;
        private $username;
        private $password;
        private $accessToken;
        private $refreshToken;
        private $expiryTimestamp;

    /**
     * HumanityAPICore constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @param string $username
     * @param string $password
     * @param string $accessToken
     * @param string $refreshToken
     * @param string $expiryTimestamp
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $grantType,
        string $username,
        string $password,
        string $accessToken,
        string $refreshToken,
        string $expiryTimestamp
    )
    {
        $this->clientId        = $clientId;
        $this->clientSecret     = $clientSecret;
        $this->grantType        = $grantType;
        $this->username         = $username;
        $this->password         = $password;
        $this->accessToken      = $accessToken;
        $this->refreshToken     = $refreshToken;
        $this->expiryTimestamp  = $expiryTimestamp;
    }

    /**
     * @param bool $reauthenticate
     * @return array
     */
    private function authenticate($reauthenticate = false) {
        $result = [
            'success'       => false,
            'errorMessage' => ''
        ];

        // User data to send using HTTP POST method in curl
        $data = [
            'client_id' 	=>	$this->clientId,
            'client_secret'	=>	$this->clientSecret,
        ];

        if ($reauthenticate) {
            $data['grant_type']     = 'refresh_token';
            $data['refresh_token']  = $this->refreshToken;
        } else {
            $data['grant_type']	= $this->grantType;
            $data['username']   = $this->username;
            $data['password']	= $this->password;
        }

        // Data should be passed as json format
        $data_json = json_encode($data);

        // API URL to send data
                $url = 'https://www.humanity.com/oauth2/token.php';

        // curl initiate
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // SET Method as a POST
        curl_setopt($ch, CURLOPT_POST, 1);

        // Pass user data in POST command
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute curl and assign returned data
        $response = json_decode(curl_exec($ch));

        // Close curl
        curl_close($ch);

        if (isset($response->error)) {
            $result['errorMessage'] = $response->error;
        } else {
            $this->accessToken = $response->access_token;
            $this->refreshToken = $response->refresh_token;
            $this->expiryTimestamp = time() + $response->expires_in;
        }
        return $result;
    }

    /**
     * @return bool
     */
    private function isAuthenticated() {
        return !!$this->accessToken;
    }

    /**
     * @return bool
     */
    private function isExpired() {
        return time() <= $this->expiryTimestamp;
    }

    /**
     * @return array
     */
    public function checkAuth() {

        switch (true) {
            case !$this->isAuthenticated():
                return $this->authenticate();
        break;
            case $this->isAuthenticated() && $this->isExpired():
                return $this->authenticate(true);
        break;
            default:
                return [
                    'errorMessage' => ''
                ];
        }
    }

    /**
     * @param string $searchForItem
     * @return array|bool
     */
    public function getAll($searchForItem = '') {
        $checkAuth = $this->checkAuth();

        if (!empty($checkAuth['errorMessage'])) {
            return $checkAuth;
        }

        if (!$searchForItem) {
            return [
                'errorMessage' => 'Resource not found.'
            ];
        }

        $url = "https://www.humanity.com/api/v2/" . $searchForItem . "?access_token=" . $this->accessToken;

        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);

        return json_decode($curl_data)->data;
    }
}
