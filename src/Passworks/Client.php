<?php

namespace Passworks;

use Passworks\Exception;
use Passworks\Request;
use Passworks\Iterator\ResourceIterator;
use Passworks\Exception\FileNotFoundException;

class Client extends Request {

    const VERSION = '0.0.2';

    private $api_app_id     = null;
    private $api_app_key    = null;
    private $debug          = true;
    private $http           = null;

    public function __construct($api_username, $api_key, $debug = false) {
        $this->api_username = $api_username;
        $this->api_key = $api_key;
        $this->debug = $debug;
    }

    public function getApiUsername() {
        return $this->api_username;
    }

    public function setApiUsername($username) {
        $this->api_username = $username;
    }

    public function getApiKey() {
        return $this->api_key;
    }

    public function setApiKey($key) {
        $this->api_key = $key;
    }

    public function getDebug() {
        return $this->debug;
    }

    public function setDebug($debug) {
        $this->debug = $debug;
    }

    // =================
    // Coupons
    //

    public function createCouponCampaign($params) {
        return $this->request('post', '/coupons', array(
                    'coupon' => $params
                ));
    }

    public function getCouponCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/coupons', 'coupons', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function createCoupon($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/coupons/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function getCoupon($campaign_id, $pass_id) {
        return $this->request('get', "/coupons/{$campaign_id}/passes/{$pass_id}");
    }

    public function updateCoupon($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/coupons/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function updateCouponCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/coupons/{$campaign_id}", array_merge(
                                array('coupon' => $params), $extra
                ));
    }

    public function pushCoupon($campaign_id, $pass_id) {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushCouponCampaign($campaign_id, $pass_id) {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }


    // =================
    // Assets
    //

    public function getAssets($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/assets', 'assets', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function getAsset($asset_id) {
        return $this->request('get', "/assets/{$asset_id}");
    }

    public function createAsset($asset_type, $file) {

        $file_with_path = realpath($file);

        if (!file_exists($file)) {
            throw new FileNotFoundException("Can't find file {$file_with_path}");
        }

        $filename   = pathinfo($file_with_path, (PATHINFO_BASENAME | PATHINFO_EXTENSION));
        $mimetype   = mime_content_type($file_with_path);

        // For PHP 5.5 or later use  CURLFile or curl_file_create
        // for PHP 5.4 or earlier build the request by hand
        if( function_exists('curl_file_create') ){
            $cfile =  curl_file_create($file_with_path, $mimetype, $filename);
        }else{
            $cfile =  "@{$file_with_path};filename=" . $filename .';type=' . $mimetype;
        }

        $payload = array(
            'asset[file]'       => $cfile,
            'asset[asset_type]' => $asset_type
        );

        $headers = array(
            'Content-Type: multipart/form-data',
            'Accept: application/json'
        );

        $response = $this->request('post', '/assets', $payload, $headers);


        return $response;
    }

    // =================
    // Store Cards
    //
    public function createStoreCardCampaign($params) {
        return $this->request('post', '/store_cards', array(
                    'store_card' => $params
                ));
    }

    public function getStoreCardCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/store_cards', 'store_cards', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function createStoreCard($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/store_cards/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function getStoreCard($campaign_id, $pass_id) {
        return $this->request('get', "/store_cards/{$campaign_id}/passes/{$pass_id}");
    }

    public function updateStoreCard($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/store_cards/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function updateStoreCardCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/store_cards/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function pushStoreCard($campaign_id, $pass_id) {
        $this->request('post', "/store_cards/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushStoreCardCampaign($campaign_id, $pass_id) {
        $this->request('post', "/store_cards/{$campaign_id}/passes/{$pass_id}/push");
    }

    // =================
    // Event Tickets
    //
    public function createEventTicketCampaign($params) {
        return $this->request('post', '/event_tickets', array(
                    'event_ticket' => $params
                ));
    }

    public function getEventTicketCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/event_tickets', 'event_tickets', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function createEventTicket($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/event_tickets/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function getEventTicket($campaign_id, $pass_id) {
        return $this->request('get', "/event_tickets/{$campaign_id}/passes/{$pass_id}");
    }

    public function updateEventTicket($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/event_tickets/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function updateEventTicketCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/event_tickets/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function pushEventTicket($campaign_id, $pass_id) {
        $this->request('post', "/event_tickets/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushEventTicketCampaign($campaign_id, $pass_id) {
        $this->request('post', "/event_tickets/{$campaign_id}/passes/{$pass_id}/push");
    }

    // =================
    // Boarding Pass
    //
    public function createBoardingCampaign($params) {
        return $this->request('post', '/boarding_passes', array(
                    'boarding_pass' => $params
                ));
    }

    public function getBoardingCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/boarding_passes', 'boarding_passes', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function createBoardingPass($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/boarding_passes/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function getBoardingPass($campaign_id, $pass_id) {
        return $this->request('get', "/boarding_passes/{$campaign_id}/passes/{$pass_id}");
    }

    public function updateBoardingPass($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/boarding_passes/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function updateEventBoardingCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/boarding_passes/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function pushBoardingPass($campaign_id, $pass_id) {
        $this->request('post', "/boarding_passes/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushEventBoardingCampaign($campaign_id, $pass_id) {
        $this->request('post', "/boarding_passes/{$campaign_id}/passes/{$pass_id}/push");
    }

    // =================
    // Generic
    //
    public function createGenericCampaign($params) {
        return $this->request('post', '/generics', array(
                    'generic' => $params
                ));
    }

    public function getGenericCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/generics', 'generic_passes', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function createGenericPass($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/generics/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function getGenericPass($campaign_id, $pass_id) {
        return $this->request('get', "/generics/{$campaign_id}/passes/{$pass_id}");
    }

    public function updateGenericPass($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/generics/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function updateGenericCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/generics/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ));
    }

    public function pushGenericPass($campaign_id, $pass_id) {
        $this->request('post', "/generics/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushgenericCampaign($campaign_id, $pass_id) {
        $this->request('post', "/generics/{$campaign_id}/passes/{$pass_id}/push");
    }

}
