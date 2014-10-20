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
  protected $initialized     = false;

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
    $this->initialized = false;
  }

  public function load()
  {
    $fetch_url = "{$this->url}?page={$this->page}";

    if( $this->per_page ){ $fetch_url .= "&per_page={$this->per_page}";  }
    if( $this->offset   ){ $fetch_url .= "&offset={$this->offset}";  }

      $this->results = $this->api->request($this->method, $fetch_url);
      $this->headers = $this->api->getResponseHeaders();

    return $this->results;
  }

  public function current()
  {
      return $this->results[$this->current_index];
  }

  public function next(){
    return $this->current_index+=1;
  }

  public function key(){
    $offset       = isset($this->headers['X-Offset']) ? intval($this->headers['X-Offset'])     : 0;
    $current_page = isset($this->headers['X-Page'])   ? (intval($this->headers['X-Page']) - 1) : 0;
    return $offset + ($current_page * $this->current_index);
  }

  public function rewind(){
    $this->initialized = true;
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
    return isset($this->headers['X-Next-Page']);
  }

  public function getNextPage()
  {
    if( $this->hasNextPage() )
    {
      return intval($this->headers['X-Next-Page']);
    }
    return -1;
  }

  public function count(){
    if( !$this->initialized )
    {
      $this->rewind();
    }

    if( !isset($this->headers['X-Total']) )
    {
      return -1;
    }

    return intval($this->headers['X-Total']);
  }

  public function size(){
    return $this->count();
  }

  public function toArray(){

    if( !$this->initialized ){ $this->rewind(); }

    $retArray = array();
    while( $this->valid() )
    {
      $retArray[] = $this->current();
      $this->next();
    }

    return $retArray;
  }



}
