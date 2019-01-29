<?php

require_once 'DNSUtilBase.php';

use PleskX\Api\Struct\Dns\Info;

class Modules_CloudflareDnsSync_ModulesCloudflareDnsSync_DNSListUtil extends Modules_CloudflareDnsSync_DNSUtilBase
{
  public function __construct($siteID, Modules_CloudflareDnsSync_Cloudflare $cloudflare, Modules_CloudflareDnsSync_PleskDNS $pleskDNS)
  {
    parent::__construct($siteID, $cloudflare, $pleskDNS);
  }

  public function getList() {
    $data = array();

    $cloudflareList = $this->getCloudflareRecords();

    foreach ($this->getPleskRecords() as $pleskRecord) {

      if (in_array($pleskRecord->type, Modules_CloudflareDnsSync_RecordsHelper::getAvailableRecords())) {

        $cloudflareRecord = $this->getCloudflareRecord($pleskRecord);

        $cloudflareValue = 'Record not found';
        $syncStatus = pm_Context::getBaseUrl() . 'images/error.png';

        if ($cloudflareRecord !== false) {
          $cloudflareValue = $cloudflareRecord->content;

          if ($this->doRecordsMatch($pleskRecord, $cloudflareRecord)) {
            $syncStatus = pm_Context::getBaseUrl() . 'images/success.png';
          } else {
            $syncStatus = pm_Context::getBaseUrl() . 'images/warning.png';
          }

          foreach ($cloudflareList as $key => $value) {
            if ($value->id == $cloudflareRecord->id) {
              unset($cloudflareList[$key]);
              break;
            }
          }
        }

        $data[] = array(
            'col-host' => $this->removeDotAfterTLD($pleskRecord->host),
            'col-type' => $pleskRecord->type.($pleskRecord->type == 'MX' ? ' ('.$pleskRecord->opt.')' : ''),
            'col-status' => '<img src="' . $syncStatus . '"/>',
            'col-plesk' => $this->minifyValue($pleskRecord->value),
            'col-cloudflare' => $this->minifyValue($cloudflareValue),
        );

      }

    }

    foreach ($cloudflareList as $cloudflareRecord) {
      $data[] = array(
          'col-host' => $this->removeDotAfterTLD($cloudflareRecord->name),
          'col-type' => $cloudflareRecord->type.($cloudflareRecord->type == 'MX' ? ' ('.$cloudflareRecord->priority.')' : ''),
          'col-status' => '<img src="' . pm_Context::getBaseUrl() . 'images/error2.png"/>',
          'col-plesk' => 'Record not found',
          'col-cloudflare' => $this->minifyValue($cloudflareRecord->content)
      );
    }

    return $data;
  }

  private function minifyValue($value, $length = 60) {
    if (strlen($value) > $length) {
      return substr($value, 0, $length).'...';
    }
    return $value;
  }

}