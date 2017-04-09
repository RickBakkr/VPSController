@extends('adminlte::page')

@section('content_header')
    <h1>Add a Virtualizor VPS</h1>
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
      <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Add a VPS</h3>
          </div>
          <div class="box-body">
            {!! BootForm::open() !!}
            {!! BootForm::text('label', 'Label', old('label')) !!}
            {!! BootForm::text('ip', 'Hostname or IP address of your Virtualizor panel. Eg. virtualizor.yourhost.com (Do not include a protocol like http or any backslash)', old('ip')) !!}
            {!! BootForm::text('port', 'Port. In case: virtualizor.yourhost.com:4082, enter 4082.', old('api_key')) !!}
            {!! BootForm::text('api_key', 'API key', old('api_key')) !!}
            {!! BootForm::text('api_pass', 'API password', old('api_pass')) !!}
            {!! BootForm::select('serverId', 'Server', $servers) !!}
            <em>Please note: API key and password are found within your Virtualizor panel. Click "API Credentials" in the left menu after login and create a key pair.</em>
            <div id="load">
              {!! BootForm::button('Load servers to select', ['id' => 'load_btn']) !!}
            </div>
            <div id="submit" style="display: none;">
              {!! BootForm::submit('Add this VPS') !!}
            </div>
            {!! BootForm::close() !!}
          </div>
          <!-- /.box-body -->
      </div>
      <!-- /.box -->
@stop

@section('js')
<script type="text/javascript">
  $(document).ready(function () {
    $('#load_btn').click(function () {
      $.getJSON( "{{ route('server.getVirtServers') }}", { ip: $('#ip').val(), port: $('#port').val(), api_key: $('#api_key').val(), api_pass: $('#api_pass').val() } )
        .done(function( json ) {
          $('#serverId').empty();
          $.each(json, function(i, value) {
              $('#serverId').append($('<option>').text(value).attr('value', i));
          });
          $('#load').hide();
          $('#submit').show();
        });
    });
  });
</script>
@stop