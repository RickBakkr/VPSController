@php
  $info = $server->getInfo();
  $status = $server->getStatus();
@endphp

<div class="row">
    <div class="col-md-6">
      
      <table class="table table-bordered table-server">
          <tr>
              <td>Status:</td>
              <td>
                  @if($status['vps_status'] == 'online')
                      <span class="label label-success">{{ ucfirst($status['vps_status']) }}</span>
                  @else
                      <span class="label label-danger">{{ ucfirst($status['vps_status']) }}</span>
                  @endif
              </td>
          </tr>
          <tr>
              <td>IP Address(es):</td>
              <td><ul><li>{{ $status['ip_address'] }}</li></ul></td>
          </tr>
          <tr>
              <td>Hostname:</td>
              <td>
                  <div class="btn-group btn-group-xs closed">
                      <a class='btn btn-xs btn-default' href="http://{{ $status['hostname'] }}" target="_blank">{{ $status['hostname'] }}</a>
                  </div>
              </td>
          </tr>
      </table>
      
    </div>
    <div class="col-md-6">

      <table class="table table-bordered table-server">
          <tr>
              <td>Disk:</td>
              <td>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $info['diskspace_percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $info['diskspace_percent'] }}%">
                      <span>{{ $info['diskspace_percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
          <tr>
              <td>Memory:</td>
              <td>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $info['memory_percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $info['memory_percent'] }}%">
                      <span>{{ $info['memory_percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
          <tr>
              <td>Bandwidth:</td>
              <td>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $info['bandwidth_percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $info['bandwidth_percent'] }}%">
                      <span>{{ $info['bandwidth_percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
      </table>
      
    </div>
</div>