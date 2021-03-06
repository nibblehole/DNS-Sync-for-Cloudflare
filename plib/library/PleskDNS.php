<?php

use Cloudflare\API\Endpoints\DNS;
use PleskX\Api\Struct\Dns\Info;

class Modules_CloudflareDnsSync_PleskDNS
{
  private $client;

  /**
   * PleskDNS constructor.
   */
  public function __construct()
  {
    $this->client = new \PleskX\Api\InternalClient();
  }

  /**
   * @param $siteID
   * @return Info[]
   */
  public function getRecords($siteID) {
    return $this->client->dns()->getAll("site-id", $siteID);
  }

  /**
   * @param $recordID
   * @return Info
   */
  public function getRecord($recordID)
  {
    return $this->client->dns()->get('id', $recordID);
  }

}