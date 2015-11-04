<?php

namespace Passworks;

use Passworks\Exception\ConnectionErrorException;
use Passworks\Exception\UnprocessableEntityException;

class Request {

  private $response_headers = null;
  private $endpoint         = 'https://api.passworks.io';
  private $user_agent       = null;
  private $api_version      = 2;

    public function getResponseHeaders()
    {
        return $this->response_headers;
    }

    public function getApiVersion()
    {
      return $this->api_version;
    }

    public function setUserAgent($user_agent)
    {
      $this->user_agent = $user_agent;
    }

    public function getUserAgent()
    {
      return $this->user_agent;
    }

    public function getEndpoint()
    {
      return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
      $this->endpoint = $endpoint;
    }

    public function setDefaultUserAgent()
    {
        $php_version  = phpversion();
        $curl_version = curl_version();
        $php_os       = PHP_OS;
        $php_client   = Client::VERSION;
        $this->setUserAgent("Passworks PHP Client/{$php_client} PHP/{$php_version} CURL/{$curl_version['version']} OS/{$php_os}");
    }

    public function request($method, $url, $post_data=null, $headers=null)
    {

        $method = strtoupper($method);

        if( empty($this->getUserAgent()) )
        {
          $this->setDefaultUserAgent();
        }

        if( empty($headers) ){
            $headers = array('Content-Type: application/json');
        }

        if( in_array('Content-Type: application/json', $headers) ){
            $post_data = json_encode($post_data);
        }

        $request_url    = "{$this->getEndpoint()}/v{$this->api_version}{$url}";

        $curl           = curl_init($request_url);

        if( $this->getDebug() ){
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            print "\n===================== REQUEST =======================\n";
            print "URL: {$method} {$request_url}\n";
            print_r($post_data);
            print "\n=============================================\n";
        }

        if( $method == 'POST' ){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        } elseif ( $method == 'PATCH' ) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            $headers[] = 'Content-Length: ' . strlen($post_data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER   , true);
        curl_setopt($curl, CURLOPT_HTTPHEADER       , $headers);
        curl_setopt($curl, CURLOPT_HEADER           , 1);
        curl_setopt($curl, CURLOPT_BUFFERSIZE       , 4096);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT   , 10);
        curl_setopt($curl, CURLOPT_TIMEOUT          , 60);
        curl_setopt($curl, CURLOPT_USERAGENT        , $this->getUserAgent());
        curl_setopt($curl, CURLOPT_HTTPAUTH         , CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD          , $this->getApiUsername() . ':' . $this->getApiKey());

        $result = curl_exec($curl);

        $error  = curl_error($curl);

        if ($result === false) {
            if ($curlErrorNumber = curl_errno($curl)) {
                $curlErrorString = curl_error($curl);
                throw new ConnectionErrorException('Unable to connect: ' . $curlErrorNumber . ' ' . $curlErrorString);
            }
            throw new ConnectionErrorException('Unable to connect.');
        }

        $info       = curl_getinfo($curl);

        $response   = explode("\r\n\r\n", $result, 2 + $info['redirect_count']);

        if( $this->getDebug() ){
            print "\n===================== RESPONSE =======================\n";
            print_r($response);
            print "\n=============================================\n";
        }

        $body                    = array_pop($response);
        $this->response_headers  = $this->_parseHeaders( array_pop($response) );

        curl_close($curl);

        $this->_handle_http_code($info, $body);

        return json_decode($body);
    }

    private function _handle_http_code($headers, $raw_body)
    {

        $http_message = json_decode($raw_body);

        $http_code = intval($headers['http_code']);
        switch( $http_code ){
            case 401:
                throw new UnauthorizedException('Unauthorized');
            break;

            case 403:
                throw new ForbiddenException('Forbidden');
            break;

            case 404:
                throw new ResourceNotFoundException('Resource Not Found');
            break;

            case 422:
                throw new UnprocessableEntityException($http_message->message, $http_message->error_code);
            break;

            case 400:
                throw new UnprocessableEntityException($http_message->message, $http_message->error_code);
            break;


            case 500:
                throw new ServerErrorException('Server Error');
            break;

            case 502:
                throw new BadGatewayException('Bad Gateway Error');
            break;

            case 503:
                throw new ServiceUnavailable('Service Unavailable');
            break;
        }
    }

    private function _parseHeaders($headers)
    {
        $headers = preg_split("/(\r|\n)+/", $headers, -1, \PREG_SPLIT_NO_EMPTY);
        $parse_headers = array();
        for ($i = 1; $i < count($headers); $i++) {
            list($key, $raw_value) = explode(':', $headers[$i], 2);
            $key = trim($key);
            $value = trim($raw_value);
            if (array_key_exists($key, $parse_headers)) {
                // See HTTP RFC Sec 4.2 Paragraph 5
                // http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
                // If a header appears more than once, it must also be able to
                // be represented as a single header with a comma-separated
                // list of values.  We transform accordingly.
                $parse_headers[$key] .= ',' . $value;
            } else {
                $parse_headers[$key] = $value;
            }
        }
        return $parse_headers;
    }

}
