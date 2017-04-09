<?php
namespace VPSController\Models;

use Illuminate\Database\Eloquent\Model,
    VPSController\Utilities\Virtualizor,
    VPSController\Utilities\SolusVM;

class Server extends Model {
    protected $table = 'servers';
    protected $fillable = ['userId', 'panel', 'options'];
    protected $casts = ['options' => 'array'];
  
    public function getStatus() {
      if($this->panel == 'solusvm') {
         $solus = new SolusVM($this->options['host'], $this->options['api_key'], $this->options['api_hash']);
         return $solus->getStatus();
      }
      if($this->panel == 'virtualizor') {
        $virtualizor = new Virtualizor($this->options['ip'], $this->options['api_key'], $this->options['api_pass'], $this->options['port']);
        if($virtualizor->status($this->options['serverId'])) {
          return ['status' => 'online'];
        } else {
          return ['status' => 'offline'];
        }
          
      }
      return [];
    }
  
    public function loadVirtualizorClass() {
      $this->virtualizor = new Virtualizor($this->options['ip'], $this->options['api_key'], $this->options['api_pass'], $this->options['port']);
    }
  
    public function getInfo() {
      if($this->panel == 'solusvm') {
         $solus = new SolusVM($this->options['host'], $this->options['api_key'], $this->options['api_hash']);
         return $solus->getInfo();
      }
      return [];
    }
  
    /* Power options */
    public function powerOn() {
      if($this->panel == 'solusvm') {
         $solus = new SolusVM($this->options['host'], $this->options['api_key'], $this->options['api_hash']);
         return $solus->power('boot');
      }
      if($this->panel == 'virtualizor') {
         $virtualizor = new Virtualizor($this->options['ip'], $this->options['api_key'], $this->options['api_pass'], $this->options['port']);
         if($virtualizor->start($this->options['serverId'])) {
            return ['success' => 'The VPS has been booted'];
         } else {
           return ['error' => 'The VPS could not be booted'];
         }
      }
      return ['error' => 'Apparently, you tried to shut down this server with an unsupported panel.'];
    }
    public function reboot() {
      if($this->panel == 'solusvm') {
         $solus = new SolusVM($this->options['host'], $this->options['api_key'], $this->options['api_hash']);
         return $solus->power('reboot');
      }
      if($this->panel == 'virtualizor') {
         $virtualizor = new Virtualizor($this->options['ip'], $this->options['api_key'], $this->options['api_pass'], $this->options['port']);
         if($virtualizor->reboot($this->options['serverId'])) {
            return ['success' => 'The VPS has been rebooted'];
         } else {
           return ['error' => 'The VPS could not be rebooted'];
         }
      }
      return ['error' => 'Aparently, you tried to shut down this server with an unsupported panel.'];
    }
    public function shutDown() {
      if($this->panel == 'solusvm') {
         $solus = new SolusVM($this->options['host'], $this->options['api_key'], $this->options['api_hash']);
         return $solus->power('shutdown');
      }
      if($this->panel == 'virtualizor') {
         $virtualizor = new Virtualizor($this->options['ip'], $this->options['api_key'], $this->options['api_pass'], $this->options['port']);
         if($virtualizor->poweroff($this->options['serverId'])) {
            return ['success' => 'The VPS has been powered off'];
         } else {
           return ['error' => 'The VPS could not be powered off'];
         }
      }
      return ['error' => 'Aparently, you tried to shut down this server with an unsupported panel.'];
    }
}