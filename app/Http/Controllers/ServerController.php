<?php

namespace VPSController\Http\Controllers;

use Illuminate\Http\Request,
    VPSController\Utilities\SolusVM,
    VPSController\Utilities\Virtualizor,
    VPSController\Models\Server,
    Auth;

class ServerController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdd($type) {
        if($type == "solus") {
            return view('server.add_solus');
        }
        if($type == "virtualizor") {
            return view('server.add_virtualizor', ['servers' => []]);
        }
    }
  
    public function postAdd(Request $request, $type) {
        $validationArray = [
          'label' => 'required'
        ];
        if($type == 'solus') {
          $validationArray['host'] = 'required';
          $validationArray['api_key'] = 'required';
          $validationArray['api_hash'] = 'required';
        }
        if($type == 'virtualizor') {
          $validationArray['ip'] = 'required';
          $validationArray['port'] = 'required|numeric';
          $validationArray['serverId'] = 'required|numeric';
          $validationArray['api_key'] = 'required';
          $validationArray['api_pass'] = 'required';
        }
        $this->validate($request, $validationArray);
      
        if($type == 'solus') {
          $solus = new SolusVM($request->get('host'), $request->get('api_key'), $request->get('api_hash'));
            if(isset($solus->getStatus()['status']) && $solus->getStatus()['status'] == 'success') {
                if(Server::create([
                  'userId' => Auth::id(),
                  'panel' => 'solusvm',
                  'options' => $request->only(['host', 'api_key', 'api_hash', 'label'])
                ])) {
                    return redirect()->route('home')->with(['success' => 'Your VPS has been added succesfully.']);
                } else {
                    return redirect()->back()->with(['error' => 'Tests failed. Please fix your input']);
                }
            } else {
                return redirect()->back()->with(['error' => 'Tests failed. Please fix your input']);
            }
        } else if($type == 'virtualizor') {
            $virtualizor = new Virtualizor($request->get('ip'), $request->get('api_key'), $request->get('api_pass'), $request->get('port'));
            $servers = $virtualizor->listvs();
            if(array_key_exists($request->get('serverId'), $servers)) {
              if(Server::create([
                  'userId' => Auth::id(),
                  'panel' => 'virtualizor',
                  'options' => $request->only(['ip', 'port', 'serverId', 'api_key', 'api_pass', 'label'])
                ])) {
                    return redirect()->route('home')->with(['success' => 'Your VPS has been added succesfully.']);
                } else {
                    return redirect()->back()->with(['error' => 'Tests failed. Please fix your input']);
                }
            } else {
              return redirect()->back()->with(['error' => 'Tests failed. Please fix your input']);
            }
              
        } else {
            return redirect()->back()->with(['error' => 'Please use a supported panel.']);
        }
    }
  
    public function action($id, $action) {
      $server = Server::findOrFail($id);
      switch($action){
        case "poweron":
          return redirect()->back()->with($server->powerOn());
        break;
        case "reboot":
          return redirect()->back()->with($server->reboot());
        break;
        case "shutdown":
          return redirect()->back()->with($server->shutDown());
        break;
      }
    }
  
    public function loadServers($serverId) {
      $server = Server::where('id', $serverId)->where('userId', Auth::id())->first();
      return view('server.load', ['server' => $server]);
    }
  
    public function delete($serverId) {
      $server = Server::where('id', $serverId)->where('userId', Auth::id());
      if($server->count() == 1) {
        if($server->delete()) {
          return redirect()->back()->with(['success', 'The VPS has been deleted from VPSController']);
        } else {
          return redirect()->back()->with(['error', 'The VPS could not be deleted from VPSController']);
        }
      } else {
        return redirect()->back()->with(['error', 'The VPS could not be deleted from VPSController']);
      }      
    }
  
    public function getVirtualizorServers(Request $request) {
        $validationArray = [];
        $validationArray['ip'] = 'required';
        $validationArray['port'] = 'required|numeric';
        $validationArray['api_key'] = 'required';
        $validationArray['api_pass'] = 'required';
        $this->validate($request, $validationArray);
        $virtualizor = new Virtualizor($request->get('ip'), $request->get('api_key'), $request->get('api_pass'), $request->get('port'));
        $servers = [];
        foreach($virtualizor->listvs() as $id => $server) {
            $servers[$id] = $server['vps_name'] . ' (' . $server['hostname'] . ')';
        }
        echo json_encode($servers);
    }
}
