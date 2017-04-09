@php
  $server->loadVirtualizorClass();
  $serverInfo = $server->virtualizor->listvs()[$server->options['serverId']];
  $diskInfo = $server->virtualizor->disk($server->options['serverId'])['disk'];
  $memoryInfo = $server->virtualizor->ram($server->options['serverId']);
  $bwInfo = $server->virtualizor->bandwidth($server->options['serverId']);
@endphp
<div class="row">
    <div class="col-md-6">
      
      <table class="table table-bordered table-server">
          <tr>
              <td>Status:</td>
              <td>
                @if($server->getStatus()['status'] == 'online')
                    <span class="label label-success">{{ ucfirst($server->getStatus()['status']) }}</span>
                @else
                    <span class="label label-danger">{{ ucfirst($server->getStatus()['status']) }}</span>
                @endif
              </td>
          </tr>
          <tr>
              <td>IP Address(es):</td>
              <td>
                <ul>
                  @foreach($serverInfo['ips'] as $ip)
                      <li>{{ $ip }}</li>
                  @endforeach
                </ul>
              </td>
          </tr>
          <tr>
              <td>Hostname:</td>
              <td>
                  <div class="btn-group btn-group-xs closed">
                      <a class='btn btn-xs btn-default' href="http://{{ $serverInfo['hostname'] }}" target="_blank">{{ $serverInfo['hostname'] }}</a>
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
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $diskInfo['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $diskInfo['percent'] }}%">
                      <span>{{ $diskInfo['percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
          <tr>
              <td>Memory:</td>
              <td>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $memoryInfo['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $memoryInfo['percent'] }}%">
                      <span>{{ $memoryInfo['percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
          <tr>
              <td>Bandwidth:</td>
              <td>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $bwInfo['percent'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $bwInfo['percent'] }}%">
                      <span>{{ $bwInfo['percent'] }}%</span>
                    </div>
                  </div>
              </td>
          </tr>
      </table>
      
      
    </div>
  
  
  
</div>