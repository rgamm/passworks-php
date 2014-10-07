<?php

namespace Passworks;

use Passworks\Exception;
use Passworks\Request;

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

    public function getStoreCards($page = 1, $perPage = null){

        $url = "/store_cards.json?page={$page}";

        if( !empty($perPage) ){
            $url = "{$url}&per_page={$perPage}";
        }

        $response = $this->request('get', $url);
        if( isset($response->store_cards) ){
            return $response->store_cards;
        }

        return null;
    }

    public function getAssets($page = 1, $perPage = null){
        $url = "/assets.json?page={$page}";

        if( !empty($perPage) ){
            $url = "{$url}&per_page={$perPage}";
        }

        $response = $this->request('get', $url);
        if( isset($response->assets) ){
            return $response->assets;
        }

        return null;
    }

    public function getAsset($assetId){

        $url = "/assets/{$assetId}";

        $response = $this->request('get', $url);
        if( isset($response->asset) ){
            return $response->asset;
        }

        return null;
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
