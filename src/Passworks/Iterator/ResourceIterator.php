<?php

namespace Passworks\Iterator;

class ResourceIterator implements \Iterator
{

  protected $api             = null;
  protected $method          = null;
  protected $url             = null;
  protected $params          = null;
  protected $page            = null;
  protected $per_page        = null;
  protected $offset          = null;
  protected $currentIndex    = 0;
  protected $collection_name = null;
  protected $results         = array();

  public function __construct($api, $method, $url, $collection_name, $params=array())
  {
    $this->api      = $api;
    $this->method   = $method;
    $this->url      = $url;
    $this->page     = empty($params['page'])     ? 1    : $params['page'];
    $this->per_page = empty($params['per_page']) ? null : $params['per_page'];
    $this->offset   = empty($params['offset'])   ? null : $params['offset'];
    $this->current_index = 0;
    $this->collection_name = $collection_name;
  }

  public function load()
  {
    $fetch_url = "{$this->url}?page={$this->page}";

    if( $this->per_page ){ $fetch_url .= "&per_page={$this->per_page}";  }
    if( $this->offset   ){ $fetch_url .= "&offset={$this->offset}";  }

      $this->results = $this->api->request($this->method, $fetch_url);
      $this->headers = $this->api->getResponseHeaders(); 

    $results =  $this->results->{$this->collection_name}; 
    if( empty($results) ){
      $this->results = array();
    }

    $this->results = $results;
    return $this->results;
  }

  public function current()
  {
      return $this->results[$this->current_index];
  }

  public function next(){
    return $this->current_index+=1;
  }
  
  public function key(){}
  
    public function rewind(){
      $this->page = 1;
      $this->current_index = 0;
      $this->load();
    }

  public function valid(){ 
    if ( $this->current_index <  count($this->results) )
    {
      return true;
    }
    elseif( $this->hasNextPage() )
    {
      $this->current_index = 0;
      $this->page = $this->getNextPage();
      $this->load();
      return !empty($this->results);
    }
    else
    {
      return false;
    }
  }

  public function hasNextPage()
  {
    return !empty($this->headers['X-Next-Page']);
  }

  public function getNextPage()
  {
    if( $this->hasNextPage() )
    {
      return intval($this->headers['X-Next-Page']);
    }
    return -1;
  }


}
