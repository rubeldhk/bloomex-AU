<?php

class RestClient
{
    const RC_TOKEN_LIFE_TIME = 3600;
    const RC_REFRESH_LIFE_TIME = 64000;

    const RC_SERVER_URL = "https://platform.ringcentral.com";
    const EV_SERVER_URL = "https://engage.ringcentral.com";
    const EV_SERVER_AND_PATH = "https://engage.ringcentral.com/voice/api/v1/";
    const LEGACY_SERVER_AND_PATH = "https://portal.vacd.biz/api/v1/";

    private $mode = "";
    private $server = "";
    private $clientId = "";
    private $clientSecret = "";

    private $accessToken = null;
    private $accountId = null;
    private $accountInfo = null;

    public function __construct($clientId = null, $clientSecret = null, $accountId = null)
    {
        if ($clientId == null || $clientSecret == null) {
            $this->server = self::LEGACY_SERVER_AND_PATH;
            $this->mode = "Legacy";
        } else {
            $this->server = self::EV_SERVER_AND_PATH;
            $this->clientId = $clientId;
            $this->clientSecret = $clientSecret;
            $this->accountId = $accountId;
            $this->mode = "Engage";
        }
    }

    public function getAccountInfo()
    {
        return $this->accountInfo;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @throws Exception
     */
    public function setAccessToken($accessToken, $callback = null)
    {
        $this->accessToken = $accessToken;
        $this->readAccount();
        return ($callback == null) ? $this->accountInfo : $callback($this->accountInfo);
    }

    /**
     * @throws Exception
     */
    public function login($options, $callback = null)
    {
        if ($this->mode == "Engage") {
            if (is_string($options)) {
                $username = func_get_arg(0);
                $password = func_get_arg(1);
                $extension = func_get_arg(2) ?: "";
                $rcAccessToken = $this->restApiOAuthToken($username, $password, $extension);
            } else if (!empty($options['jwt'])) {
                $rcAccessToken = $this->restApiOAuthTokenJWT($options['jwt']);
            } else {
                $rcAccessToken = $this->restApiOAuthToken($options['username'], $options['password'], $options['extension']);
            }
            $resp = $this->exchangeAccessTokens($rcAccessToken);
            if ($resp) {
                return ($callback == null) ? json_decode($resp['body'], false) : $callback($resp);
            }
            return ($callback == null) ? $resp : $callback($resp);
        }

        if ($this->accessToken != null) {
            return ($callback == null) ? $this->accessToken : $callback($this->accessToken);
        }

        if (is_string($options)) {
            $username = func_get_arg(0);
            $password = func_get_arg(1);
            $this->generateAuthToken($username, $password);
        } else {
            $this->generateAuthToken($options["username"], $options["password"]);
        }
        return ($callback == null) ? $this->accessToken : $callback($this->accessToken);
    }

    /**
     * @throws Exception
     */
    public function get($endpoint, $params = null, $callback = "")
    {
        if ($this->accessToken == null) {
            return $callback == "" ? "Login required!" : $callback("Login required!");
        }
        $apiEndpoint = $endpoint;
        if (strpos($endpoint, '~') !== false) {
            $apiEndpoint = preg_replace('/~/', $this->getAccountId(), $endpoint);
        }
        $url = $this->server . $apiEndpoint;
        if ($params !== null) {
            $url .= "?" . http_build_query($params);
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ];

        if ($this->mode == "Legacy") {
            $headers = [
                'Accept: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];
        }

        $resp = $this->sendRequest('GET', $url, $headers);
        if ($resp != null) {
            return $callback == "" ? $resp['body'] : $callback($resp);
        }

        return $callback == "" ? "AcccessToken expired. Login required!" : $callback("AcccessToken expired. Login required!");
    }

    /**
     * @throws Exception
     */
    public function post($endpoint, $params = null, $callback = "")
    {
        if ($this->accessToken == null) {
            return $callback == "" ? "Login required!" : $callback("Login required!");
        }

        if (strpos($endpoint, '~') !== false) {
            $endpoint = preg_replace('/~/', $this->getAccountId(), $endpoint);
        }
        $url = $this->server . $endpoint;

        $body = [];
        if ($params != null) {
            $body = json_encode($params);
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken,
        ];
        if ($this->mode == "Legacy") {
            $headers = [
                'Content-Type: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];
        }

        $resp = $this->sendRequest('POST', $url, $headers, $body);
        if ($resp != null) {
            return $callback == "" ? $resp['body'] : $callback($resp);
        }

        return $callback == "" ? "AcccessToken expired. Login required!" : $callback("AcccessToken expired. Login required!");
    }

    /**
     * @throws Exception
     */
    public function put($endpoint, $params = null, $callback = "")
    {
        if ($this->accessToken == null) {
            return $callback == "" ? "Login required!" : $callback("Login required!");
        }
        if (strpos($endpoint, '~') !== false) {
            $endpoint = preg_replace('/~/', $this->getAccountId(), $endpoint);
        }
        $url = $this->server . $endpoint;

        $body = [];
        if ($params != null) {
            $body = json_encode($params);
        }
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ];
        if ($this->mode == "Legacy") {
            $headers = [
                'Content-Type: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];
        }

        $resp = $this->sendRequest('PUT', $url, $headers, $body);
        if ($resp != null) {
            return $callback == "" ? $resp['body'] : $callback($resp);
        }

        return $callback == "" ? "AcccessToken expired. Login required!" : $callback("AcccessToken expired. Login required!");
    }

    /**
     * @throws Exception
     */
    public function patch($endpoint, $params = null, $callback = "")
    {
        if ($this->accessToken == null) {
            return $callback == "" ? "Login required!" : $callback("Login required!");
        }
        if (strpos($endpoint, '~') !== false) {
            $endpoint = preg_replace('/~/', $this->getAccountId(), $endpoint);
        }
        $url = $this->server . $endpoint;

        $body = [];
        if ($params != null) {
            $body = json_encode($params);
        }
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ];
        if ($this->mode == "Legacy") {
            $headers = [
                'Content-Type: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];
        }

        $resp = $this->sendRequest('PATCH', $url, $headers, $body);
        if ($resp != null) {
            return $callback == "" ? $resp['body'] : $callback($resp);
        }

        return $callback == "" ? "AcccessToken expired. Login required!" : $callback("AcccessToken expired. Login required!");
    }

    /**
     * @throws Exception
     */
    public function delete($endpoint, $params = null, $callback = "")
    {
        if ($this->accessToken == null) {
            return $callback == "" ? "Login required!" : $callback("Login required!");
        }
        if (strpos($endpoint, '~') !== false) {
            $endpoint = preg_replace('/~/', $this->getAccountId(), $endpoint);
        }
        $url = $this->server . $endpoint;
        $body = [];
        if ($params != null) {
            $body = json_encode($params);
        }
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ];
        if ($this->mode == "Legacy") {
            $headers = [
                'Content-Type: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];
        }

        $resp = $this->sendRequest('DELETE', $url, $headers, $body);
        if ($resp != null) {
            return $callback == "" ? $resp['body'] : $callback($resp);
        }

        return $callback == "" ? "AcccessToken expired. Login required!" : $callback("AcccessToken expired. Login required!");
    }

    private function sendRequest($method, $url, $headers, $body = "")
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            if ($body != "") {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            $strResponse = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                throw new \Exception($curlErrno);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $array = [
                'status' => $httpCode,
                'headers' => curl_getinfo($ch),
                'body' => $strResponse
            ];
            curl_close($ch);
            if ($httpCode == 200 || $httpCode == 201) {
                return $array;
            }

            if ($httpCode == 401) {
                print_r("EV access token expired\r\n");
                return null;
            }

            throw new \Exception($strResponse);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function restApiOAuthToken($username, $password, $extension)
    {
        $url = self::RC_SERVER_URL . "/restapi/oauth/token";
        $basic = $this->clientId . ":" . $this->clientSecret;
        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($basic),
        ];
        if ($extension == null) {
            $body = http_build_query([
                'grant_type' => 'password',
                'username' => urlencode($username),
                'password' => $password,
                'access_token_ttl' => self::RC_TOKEN_LIFE_TIME
            ]);
        } else {
            $body = http_build_query([
                'grant_type' => 'password',
                'username' => urlencode($username),
                'password' => $password,
                'extension' => $extension
            ]);
        }

        try {
            $resp = $this->sendRequest('POST', $url, $headers, $body);
            if ($resp) {
                return json_decode($resp['body'], false)->access_token;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function vcLoginJWT(string $username, string $password)
    {
        // login RC_Vc
        $url = $this->server . "auth/login";
        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken,
        ];
        $body = http_build_query([
            'username' => $username,
            'password' => $password,
            'stayLoggedIn' => true,
        ]);

        try {
            $resp = $this->sendRequest('POST', $url, $headers, $body);
            if ($resp) {
                return json_decode($resp['body'], false)->access_token;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function restApiOAuthTokenJWT($jwt)
    {
        $url = self::RC_SERVER_URL . "/restapi/oauth/token";
        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ":" . $this->clientSecret)
        ];
        $body = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
            'access_token_ttl' => self::RC_TOKEN_LIFE_TIME,
            'refresh_token_ttl' => self::RC_REFRESH_LIFE_TIME,
        ]);

        try {
            $resp = $this->sendRequest('POST', $url, $headers, $body);
            if ($resp) {
                return json_decode($resp['body'], false)->access_token;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function exchangeAccessTokens($rcAccessToken)
    {
        $url = self::EV_SERVER_URL . "/api/auth/login/rc/accesstoken?";
        $body = 'rcAccessToken=' . $rcAccessToken . "&rcTokenType=Bearer";
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        try {
            $resp = $this->sendRequest('POST', $url, $headers, $body);
            if ($resp) {
                $tokensObj = json_decode($resp['body'], false);
                $this->accessToken = $tokensObj->accessToken;
                $this->accountInfo = $tokensObj->agentDetails;
                $this->accountId = $tokensObj->agentDetails[0]->accountId ?: $this->accountId;
                return $resp;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function generateAuthToken($username, $password)
    {
        $url = $this->server . "auth/login";
        $body = "username=" . $username . "&password=" . $password;
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        try {
            $resp = $this->sendRequest('POST', $url, $headers, $body);
            if ($resp) {
                $jsonObj = json_decode($resp['body'], false);
                $this->accountId = $jsonObj->accounts[0]->accountId ?: $this->accountId;
                $this->readPermanentsToken($jsonObj->authToken);
                return $jsonObj;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function readPermanentsToken($authToken)
    {
        $url = $this->server . "admin/token";
        $headers = ['X-Auth-Token: ' . $authToken];
        try {
            $resp = $this->sendRequest('GET', $url, $headers);
            if ($resp) {
                $jsonObj = json_decode($resp['body'], false);
                if (count($jsonObj)) {
                    $this->accessToken = $jsonObj[0];
                    return $jsonObj;
                }

                return $this->generatePermanentToken($authToken);
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function generatePermanentToken($authToken)
    {
        $url = $this->server . "admin/token";
        $headers = ['X-Auth-Token: ' . $authToken];
        try {
            $resp = $this->sendRequest('POST', $url, $headers);
            if ($resp) {
                $this->accessToken = $resp['body'];
                return $resp['body'];
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function readAccount()
    {
        $url = $this->server . "admin/accounts";
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ];

        if ($this->mode == "Legacy")
            $headers = [
                'Content-Type: application/json',
                'X-Auth-Token: ' . $this->accessToken
            ];

        $resp = $this->sendRequest('GET', $url, $headers);
        if ($resp) {
            $this->accountInfo = json_decode($resp['body'], false);
            if (count($this->accountInfo)) {
                $this->accountId = $this->accountInfo[0]->accountId ?: $this->accountId;
            }
        }
        return $resp;
    }
}