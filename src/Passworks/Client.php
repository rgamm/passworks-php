<?php

namespace Passworks;

use Passworks\Exception;
use Passworks\Request;
use Passworks\Iterator\ResourceIterator;
use Passworks\Exception\FileNotFoundException;

class Client extends Request {

    const VERSION = '0.0.1';

    private $api_app_id = null;
    private $api_app_key = null;
    private $debug = true;
    private $http = null;

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



    public function getEventTickets($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/event_tickets', 'event_tickets', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function getBoardingPasses($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/boarding_passes', 'boarding_passes', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function getGenerics($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/generics', 'generics', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    // =================
    // Coupons
    //

    public function createCouponCampaign($params) {
        return $this->request('post', '/coupons', array(
                    'coupon' => $params
                ))->coupon;
    }

    public function getCouponCampaigns($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/coupons', 'coupons', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function createCoupon($campaign_id, $params, $extra = array()) {
        return $this->request('post', "/coupons/{$campaign_id}/passes", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function getCoupon($campaign_id, $pass_id) {
        return $this->request('get', "/coupons/{$campaign_id}/passes/{$pass_id}")->pass;
    }

    public function updateCoupon($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/coupons/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function updateCouponCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/coupons/{$campaign_id}", array_merge(
                                array('coupon' => $params), $extra
                ))->coupon;
    }

    public function pushCoupon($campaign_id, $pass_id) {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushCouponCampaign($campaign_id, $pass_id) {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }


    // =================
    // Store Cards
    //

    public function getAssets($page = 1, $per_page = null) {
        return new ResourceIterator($this, 'get', '/assets', 'assets', array(
            'page'     => $page,
            'per_page' => $per_page
        ));
    }

    public function getAsset($asset_id) {
        return $this->request('get', "/assets/{$asset_id}")->asset;
    }

    public function createAsset($assetType, $file) {

        if (!file_exists($file)) {
            throw new FileNotFoundException("Can't find file {$file}");
        }

        $filename = pathinfo($file, (PATHINFO_BASENAME | PATHINFO_EXTENSION));
        $data = base64_encode(file_get_contents($file));
        $mimetype = mime_content_type($file);

        $payload = array(
            'filename' => $filename,
            'asset_type' => $assetType,
            'base64' => $data,
            'content_type' => $mimetype
        );

        $response = $this->request('post', '/assets', array('asset' => $payload));

        if (isset($response->asset)) {
            return $response->asset;
        }

        return null;
    }

    // =================
    // Store Cards
    //
    public function createStoreCardCampaign($params) {
        return $this->request('post', '/store_cards', array(
                    'store_card' => $params
                ))->store_card;
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
                ))->pass;
    }

    public function getStoreCard($campaign_id, $pass_id) {
        return $this->request('get', "/store_cards/{$campaign_id}/passes/{$pass_id}")->pass;
    }

    public function updateStoreCard($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/store_cards/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function updateStoreCardCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/store_cards/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
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
                ))->event_ticket;
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
                ))->pass;
    }

    public function getEventTicket($campaign_id, $pass_id) {
        return $this->request('get', "/event_tickets/{$campaign_id}/passes/{$pass_id}")->pass;
    }

    public function updateEventTicket($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/event_tickets/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function updateEventTicketCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/event_tickets/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
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
                ))->boarding_pass;
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
                ))->pass;
    }

    public function getBoardingPass($campaign_id, $pass_id) {
        return $this->request('get', "/boarding_passes/{$campaign_id}/passes/{$pass_id}")->pass;
    }

    public function updateBoardingPass($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/boarding_passes/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function updateEventBoardingCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/boarding_passes/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
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
                ))->generic;
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
                ))->pass;
    }

    public function getGenericPass($campaign_id, $pass_id) {
        return $this->request('get', "/generics/{$campaign_id}/passes/{$pass_id}")->pass;
    }

    public function updateGenericPass($campaign_id, $pass_id, $params, $extra = array()) {
        return $this->request('patch', "/generics/{$campaign_id}/passes/{$pass_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function updateGenericCampaign($campaign_id, $params, $extra = array()) {
        return $this->request('patch', "/generics/{$campaign_id}", array_merge(
                                array('pass' => $params), $extra
                ))->pass;
    }

    public function pushGenericPass($campaign_id, $pass_id) {
        $this->request('post', "/generics/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushgenericCampaign($campaign_id, $pass_id) {
        $this->request('post', "/generics/{$campaign_id}/passes/{$pass_id}/push");
    }

}
