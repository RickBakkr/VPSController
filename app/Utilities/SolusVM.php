<?php

namespace VPSController\Utilities;

class SolusVM extends BasePanel {
  public function __construct($host, $key, $hash) {
     $this->host = $host;
     $this->key = $key;
     $this->hash = $hash;
  }
  
  public function getStatus() {
    $result = $this->_curl('status');
    if (isset($result['status'] ) && $result['status'] == 'error') {
        return ['error' => $result['statusmsg'], 'status' => 'error'];
    } elseif (isset($result['status'] ) && $result['status'] == 'success') {
      
      return ['status' => 'success', 'ip_address' => $result['ipaddress'], 'vps_status' => $result['vmstat'], 'hostname' => $result['hostname']];
    } else {
      return [];
    }
  }
  
  public function getInfo() {
    $result = $this->_curl('info');
    if (isset($result['status'] ) && $result['status'] == 'error') {
        return ['error' => $result['statusmsg'], 'status' => 'error'];
    } elseif (isset($result['status'] ) && $result['status'] == 'success') {
      $hddParts = explode(',', $result['hdd']);
      $memParts = explode(',', $result['mem']);
      $bwParts =  explode(',', $result['bw']);
      return [
        'status' => 'success', 
        'ip_address' => $result['ipaddress'],
        'diskspace' => $this->formatBytes($hddParts[0]),
        'diskspace_used' => $this->formatBytes($hddParts[1]),
        'diskspace_free' => $this->formatBytes($hddParts[2]),
        'diskspace_percent' => $hddParts[3],
        'memory' => $this->formatBytes($memParts[0]),
        'memory_used' => $this->formatBytes($memParts[1]),
        'memory_free' => $this->formatBytes($memParts[2]),
        'memory_percent' => $memParts[3],
        'bandwidth' => $this->formatBytes($bwParts[0]),
        'bandwidth_used' => $this->formatBytes($bwParts[1]),
        'bandwidth_free' => $this->formatBytes($bwParts[2]),
        'bandwidth_percent' => $bwParts[3],
      ];
    } else {
      return [];
    }
  }
  
  public function power($action) {
    $result = $this->_curl($action);
    if ($result["status"] == "success") {
        if ($result["statusmsg"] == "online") {
            return ['success' => "The virtual server is online!"];
        } elseif ($result["statusmsg"] == "offline") {
            return ['success' => "The virtual server is offline!"];
        } elseif ($result["statusmsg"] == "rebooted") {
            return ['success' => "The virtual server has been rebooted!"];
        } elseif ($result["statusmsg"] == "shutdown") {
            return ['success' => "The virtual server has been shutdown!"];
        } elseif ($result["statusmsg"] == "booted") {
            return ['success' => "The virtual server has been booted!"];
        } else {
            return ['error' => 'The action failed.'];
        }
    } else {
      return ['error' => 'The action failed.'];
    }
  }
  
  private function _curl($action) {
    $url = $this->host . '/api/client';
    $postfields = [
      'key' => $this->key,
      'hash' => $this->hash,
      'action' => $action,
      'ipaddr' => "true",
      'hdd' => "true",
      'mem' => "true",
      'bw' => "true",
      'status' => 'true'
    ];
    $url = $url . "/command.php?" . http_build_query($postfields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $match);
    $result = array();
    foreach ($match[1] as $x => $y) {
        $result[$y] = $match[2][$x];
    }
    return $result;
  }
}