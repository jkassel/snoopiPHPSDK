<?php

class SnoopiClient
{
    private $api_key;

    public function __construct($api_key = null)
    {
        $this->api_key = $api_key;
    }

    private function call_api($api_url, $api_params = null, $retry_count = 1, $max_retries = 3, $sleep_time = 2, $req_type = 'get', $headers = array())
    {

        $base_url = "http://api.snoopi.io/v1/";
        $api_key_suffix = "?api_key={$this->api_key}";

        if ($api_params != null) {
            $url = $base_url . $api_url . $api_params . $api_key_suffix;

        } else {
            $url = $base_url . $api_url . $api_key_suffix;
        }

        $response = '';

        //print_r("calling api url: " . $url);

        if ($req_type == 'get') {
            $response = HTTPRequester::HTTPGet($url, array("headers"=> $headers));
        }

        $response->body = json_decode($response->body);

        //print_r("early_response: {$response}");

        if ($response->http_status_code == "200") {
            //print_r("response is ok");
            return $response->body;
        } else if ($response->body->{'Code'} == "429") {
            if ($retry_count == $max_retries) {
                print_r("tried too many times. exiting...");
                exit(0);
            }

            print_r("getting rate limited.  sleeping for {$sleep_time}s then retrying...[Retry_Count={$retry_count}]");
            sleep($sleep_time);
            $result = $this->call_api($api_url, $api_params, $retry_count + 1);
            return $result;
        } else {
            print_r($response->error);
        }

    }

    public function get_zip_code_radius($origin_zip_code, $radius = "5")
    {
        $api_url = "zipcoderange/";
        $params = $origin_zip_code . "-" . $radius;
        $result = $this->call_api($api_url, $params);
        return $result;
    }

    public function get_location_by_ip($ip_address)
    {
        $api_url = "ip/";
        $params = $ip_address;
        $result = $this->call_api($api_url, $params);
        return $result;
    }

    public function get_zip_code_distance($start_zip_code, $end_zip_code)
    {
        $api_url = "zipcodedistance/";
        $params = $start_zip_code . "-" . $end_zip_code;
        $result = $this->call_api($api_url, $params);
        return $result;
    }

    public function get_states()
    {
        $api_url = "getstates/";
        $result = $this->call_api($api_url);
        return $result;
    }

    public function get_state_abbreviation($state)
    {
        $api_url = "getstates/";
        $params = $state;
        $result = $this->call_api($api_url, $params);
        return $result;
    }

    public function get_cities($state_abbreviation = null)
    {
        $api_url = "getcities/";
        if ($state_abbreviation != null) {
            $params = $state_abbreviation;
            $result = $this->call_api($api_url, $params);
        } else {
            $result = $this->call_api($api_url);
            return $result;
        }
    }
}

class HTTPRequester {
    /**
     * @description Make HTTP-GET call
     * @param       $url
     * @param       array $params
     * @return      HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPGet($url, array $params) {
        $query = http_build_query($params);
        $ch = curl_init($url.'?'.$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $http_response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($http_response, 0, $header_size);
        $body = substr($http_response, $header_size);

        curl_close($ch);

        $headers_arr = explode("\r\n", $headers);
        $headers_arr = array_filter($headers_arr);

        $http_status_code = explode(" ", $headers_arr[0])[1];

        $response = (object) array('body' => json_encode($body), 'headers' => $headers_arr, 'http_status_code' => $http_status_code);

        return $response;
    }
    /**
     * @description Make HTTP-POST call
     * @param       $url
     * @param       array $params
     * @return      HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPPost($url, array $params) {
        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * @description Make HTTP-PUT call
     * @param       $url
     * @param       array $params
     * @return      HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPPut($url, array $params) {
        $query = \http_build_query($params);
        $ch    = \curl_init();
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_HEADER, false);
        \curl_setopt($ch, \CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'PUT');
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $query);
        $response = \curl_exec($ch);
        \curl_close($ch);
        return $response;
    }
    /**
     * @category Make HTTP-DELETE call
     * @param    $url
     * @param    array $params
     * @return   HTTP-Response body or an empty string if the request fails or is empty
     */
    public static function HTTPDelete($url, array $params) {
        $query = \http_build_query($params);
        $ch    = \curl_init();
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_HEADER, false);
        \curl_setopt($ch, \CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'DELETE');
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $query);
        $response = \curl_exec($ch);
        \curl_close($ch);
        return $response;
    }
    
}