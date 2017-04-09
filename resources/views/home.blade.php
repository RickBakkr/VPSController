@extends('adminlte::page')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('css')
    <style>
      .table-server td {
        width: 50%;
      }
    </style>
    <link href="https://cdn.jsdelivr.net/sweetalert2/6.6.0/sweetalert2.min.css" rel="stylesheet">
@stop

@section('content')
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row" style="margin-bottom: 25px;">
        <div class="col-md-12">
            <button id="refresh" class="btn btn-primary pull-right">Refresh</button>  
        </div>
    </div>
    <div id="serverList">
        @forelse($servers as $server)
          <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">{{ $server->options['label'] }}</h3>
                <!-- button -->
                <div class="pull-right">
                  <a class="btn btn-danger" href="{{ route('server.delete', ['id' => $server->id]) }}">Delete</a>
                  <a class="btn btn-success" href="{{ route('server.action', ['id' => $server->id, 'action' => 'poweron']) }}">Power on</a>
                  <a class="btn btn-warning" href="{{ route('server.action', ['id' => $server->id, 'action' => 'reboot']) }}">Reboot</a>
                  <a class="btn btn-danger" href="{{ route('server.action', ['id' => $server->id, 'action' => 'shutdown']) }}">Shutdown</a>
                </div>

              </div>
              <div class="box-body" id="serverbox-{{ $server->id }}">
                Loading ...
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.box -->
        @empty
          <div class="alert alert-danger"><strong>Notice:</strong> Please add a server to VPSController to continue.</div>
        @endforelse
    </div>
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/sweetalert2/6.6.0/sweetalert2.min.js"></script>
    <script type="text/javascript">
        function refresh() {
          $('.box-body').html("Loading ...");
          loadServers();
        }
        function loadServers() {
          @forelse($servers as $server)
            $.get( "{{ route('server.load', ['serverId' => $server->id]) }}", function( data ) {
                $("#serverbox-{{ $server->id }}").html( data );
            });
          @endforeach
        }
        $(document).ready(function () {
            loadServers();
            $('#refresh').click(refresh);
        });
    </script>

        @if (session('error'))
            <script type="text/javascript">
                swal(
                  'Oops...',
                  '{{ session('error') }}',
                  'error'
                );
            </script>
    @endif
    @if (session('success'))
            <script type="text/javascript">
                swal(
                  'Yay!',
                  '{{ session('success') }}',
                  'success'
                );
            </script>
    @endif
    
@stop