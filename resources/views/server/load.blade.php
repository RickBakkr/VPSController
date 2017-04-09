@if($server->panel == 'solusvm')
  @include('snippets.solus', ['server' => $server])
@elseif($server->panel == 'virtualizor')
  @include('snippets.virtualizor', ['server' => $server])
@endif