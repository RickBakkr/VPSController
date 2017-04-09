@extends('adminlte::page')

@section('content_header')
    <h1>Add a SolusVM VPS</h1>
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
            {!! BootForm::text('host', 'Host of your SolusVM panel. Eg. https://vpspanel.yourhost.com', old('host')) !!}
            {!! BootForm::text('api_key', 'API key', old('api_key')) !!}
            {!! BootForm::text('api_hash', 'API hash', old('api_hash')) !!}
            <em>Please note: API key and hash are found within your SolusVM panel. Select your VPS and then click API.</em>
            {!! BootForm::submit('Add this VPS') !!}
            {!! BootForm::close() !!}
          </div>
          <!-- /.box-body -->
      </div>
      <!-- /.box -->
@stop