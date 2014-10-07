<?php

namespace Passworks;

use Passworks\Exception;
use Passworks\Request;
use Passworks\Iterator\ResourceIterator;
use Passworks\Exception\FileNotFoundException;

class Client extends Request 
{

    const VERSION  = '0.0.1';

    private $api_app_id     = null;
    private $api_app_key    = null;
    private $debug          = true;
    private $http           = null;

    public function __construct($api_username, $api_key, $debug = false)
    {
        $this->api_username = $api_username;
        $this->api_key      = $api_key;
        $this->debug        = $debug;
    }

    public function getApiUsername(){
      return $this->api_username;
    }

    public function setApiUsername($username)
    {
      $this->api_username = $username;
    }

    public function getApiKey()
    {
      return $this->api_key;
    }

    public function setApiKey($key)
    {
      $this->api_key = $key;
    }

    public function getDebug()
    {
      return $this->debug;
    }

    public function setDebug($debug)
    {
      $this->debug = $debug;
    }

    public function getStoreCards($page = 1, $per_page = null){
      return new ResourceIterator($this, 'get', '/store_cards', 'store_cards', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }


    public function getEventTickets($page = 1, $per_page = null){
      return new ResourceIterator($this, 'get', '/event_tickets', 'event_tickets', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }


    public function getBoardingPasses($page = 1, $per_page = null){
      return new ResourceIterator($this, 'get', '/boarding_passes', 'boarding_passes', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }

    public function getGenerics($page = 1, $per_page = null){
      return new ResourceIterator($this, 'get', '/generics', 'generics', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }

    // Coupons

    public function createCouponCampaign($params)
    {
      return $this->request('post', '/coupons', array(
        'coupon' => $params
      ))->coupon; 
    }

    public function getCoupons($page = 1, $per_page = null){
      return new ResourceIterator($this, 'get', '/coupons', 'coupons', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }
    
    public function createCoupon($campaign_id, $params, $extra=array())
    {
      return $this->request('post', "/coupons/{$campaign_id}/passes", merge_array(
        array('pass' => $params),
        $extra
      ))->pass; 
    }
    
    public function getCoupon($campaign_id, $pass_id)
    {
      return $this->request('get', "/coupons/{$campaign_id}/{$pass_id}")->coupon;
    }
    

    public function updateCoupon($campaign_id, $pass_id, $params, $extra=array())
    {
      return $this->request('patch', "/coupons/{$campaign_id}/passes/{$pass_id}", array_merge(
        array('pass' => $params),
        $extra
      ))->pass; 
    }
    
    public function updateCouponCampaign($campaign_id, $pass_id, $params, $extra=array())
    {
      return $this->request('patch', "/coupons/{$campaign_id}", array_merge(
        array( 'pass' => $params ),
        $extra  
      ))->pass; 
    }

    public function pushCoupon($campaign_id, $pass_id)
    {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }

    public function pushCouponCampaign($campaign_id, $pass_id)
    {
        $this->request('post', "/coupons/{$campaign_id}/passes/{$pass_id}/push");
    }

    // --- assets ---

    public function getAssets($page = 1, $perPage = null){
      return new ResourceIterator($this, 'get', '/assets', 'assets', array(
        'page'     => $page,
        'per_page' => $per_page 
      ));
    }
    public function getAsset($asset_id){
      return $this->request('get', "/assets/{$asset_id}")->asset;
    }

    public function createAsset($assetType, $file){

        if( !file_exists($file) ){
            throw new FileNotFoundException("Can't find file {filename}");
        }

        $filename   = pathinfo($file, (PATHINFO_BASENAME | PATHINFO_EXTENSION));
        $data       = base64_encode(file_get_contents($file));
        $mimetype   = mime_content_type($file);

        $payload = array(
            'filename'      => $filename,
            'asset_type'    => $assetType,
            'base64'        => $data,
            'content_type'  => $mimetype
        );

        $response = $this->request('post', '/assets', array('asset' => $payload));

        if( isset($response->asset) )
        {
            return $response->asset;
        }

        return null;
    }

}
