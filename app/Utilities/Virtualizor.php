<?php

namespace VPSController\Utilities;

class Virtualizor extends BasePanel {
    public $key = '';
    public $pass = '';
    public $ip = '';
    public $port = 4083;
    public $protocol = 'https';
    public $error = [];

    /**
     * Contructor.
     *
     * @author       Pulkit Gupta
     *
     * @param string $ip   IP of the Control Panel
     * @param string $key  The API KEY of your account
     * @param string $pass The API Password of your account
     * @param int    $port (Optional) The port to connect to. Port 4083 is the default. 4082 is non-SSL
     *
     * @return null
     */
    public function __construct($ip = null, $key = null, $pass = null, $port = null)
    {
        $this->key = env('VIRTUALIZOR_KEY', $key);
        $this->pass = env('VIRTUALIZOR_PASS', $pass);
        $this->ip = env('VIRTUALIZOR_IP', $ip);
        $this->port = env('VIRTUALIZOR_PORT', $port);
        if (!($port == 4083 || $port == 443)) {
            $this->protocol = 'http';
        }
    }

    /**
     * Dumps a variable.
     *
     * @author       Pulkit Gupta
     *
     * @param array $re The Array or any other variable.
     *
     * @return null
     */
    public function r($re)
    {
        echo '<pre>';
        print_r($re);
        echo '</pre>';
    }

    /**
     * Unserializes a string.
     *
     * @author       Pulkit Gupta
     *
     * @param string $str The serialized string
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function _unserialize($str)
    {
        $var = @unserialize($str);

        if (empty($var)) {
            $str = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'._strlen('$2').':\"$2\";'", $str);

            $var = @unserialize($str);
        }

        //If it is still empty false
        if (empty($var)) {
            return false;
        } else {
            return $var;
        }
    }

    /**
     * Makes an API request to the server to do a particular task.
     *
     * @author       Pulkit Gupta
     *
     * @param string $path    The action you want to do
     * @param array  $post    An array of DATA that should be posted
     * @param array  $cookies An array FOR SENDING COOKIES
     *
     * @return array The unserialized array on success OR false on failure
     */
    public function call($path, $post = [], $cookies = [])
    {
        $url = ($this->protocol).'://'.$this->ip.':'.$this->port.'/'.$path;
        $url .= (strstr($url, '?') ? '' : '?');
        $url .= '&api=serialize&apikey='.rawurlencode($this->key).'&apipass='.rawurlencode($this->pass);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Time OUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // UserAgent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Virtualizor');

        // Cookies
        if (!empty($cookies)) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
        }

        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Get response from the server.
        $resp = curl_exec($ch);
        curl_close($ch);

        // The following line is a method to test
        //if(preg_match('/sync/is', $url)) echo $resp;

        if (empty($resp)) {
            return false;
        }

        $r = $this->_unserialize($resp);

        if (empty($r)) {
            return false;
        }

        return $r;
    }

    /**
     * List the Virtual Servers in your account.
     *
     * @author       Pulkit Gupta
     *
     * @return array The array containing a list of Virtual Servers one has in their account
     */
    public function listvs()
    {
        $resp = $this->call('index.php?act=listvs');

        return $resp['vs'];
    }

    /**
     * START a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function start($vid)
    {

        // Make the Request
        $res = $this->call('index.php?svs='.$vid.'&act=start&do=1');

        // Did it finish ?
        if (!empty($res['done'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * STOP a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function stop($vid)
    {

        // Make the Request
        $res = $this->call('index.php?svs='.$vid.'&act=stop&do=1');

        // Did it finish ?
        if (!empty($res['done'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * RESTART a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function restart($vid)
    {

        // Make the Request
        $res = $this->call('index.php?svs='.$vid.'&act=restart&do=1');

        // Did it finish ?
        if (!empty($res['done'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * POWER OFF a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return bool TRUE on success or FALSE on failure
     */
    public function poweroff($vid)
    {

        // Make the Request
        $res = $this->call('index.php?svs='.$vid.'&act=poweroff&do=1');

        // Did it finish ?
        if (!empty($res['done'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * STOP a Virtual Server.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return int 1 if the VM is ON, 0 if its OFF
     */
    public function status($vid)
    {

        // Make the Request
        $res = $this->call('index.php?svs='.$vid.'&act=start');

        return $res['status'];
    }

    /**
     * GET or SET the hostname of a VM. To get the current hostname dont pass the $newhostname parameter.
     *
     * @author       Pulkit Gupta
     *
     * @param int    $vid         The VMs ID
     * @param string $newhostname The new HOSTNAME of the virtual server.
     *
     * @return string The CURRENT hostname is returned if $newhostname is NULL.
     *                FALSE is returned if there was an error while setting the new hostname
     *                'onboot' is returned if the new hostname will be set when the VPS is STOPPED and STARTED
     *                'done' is returned if the new hostname has been set right now - Mainly OpenVZ
     */
    public function hostname($vid, $newhostname = null)
    {

        // Are we to change ?
        if (!empty($newhostname)) {
            $post = ['newhost'           => $newhostname,
                            'changehost' => 'Change Hostname', ];

            $resp = $this->call('index.php?svs='.$vid.'&act=hostname', $post);

            // Was there an error
            if (!empty($resp['error'])) {
                $this->error = $resp['error'];

                return false;

            // Will it be done when the VPS is STOPPED and STARTED ?
            } elseif (!empty($resp['onboot'])) {
                return 'onboot';

            // It was done successfully
            } elseif (!empty($resp['done'])) {
                return 'done';
            }

        // Just return the CURRENT HOSTNAME
        } else {
            $resp = $this->call('index.php?svs='.$vid.'&act=hostname');

            return $resp['current'];
        }
    }

    /**
     * GET the CPU details of a VM. Incase of Xen / KVM, only information is available as usage cannot be sensed.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing the details is returned. Usage details is available only in case of OpenVZ.
     */
    public function cpu($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=cpu');

        return $resp['cpu'];
    }

    /**
     * GET the RAM details of a VM. Incase of Xen / KVM, only information is available as usage cannot be sensed.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing the details is returned. Usage details is available only in case of OpenVZ.
     */
    public function ram($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=ram');

        return $resp['ram'];
    }

    /**
     * GET the Disk details of a VM. Incase of Xen / KVM, only information is available as usage cannot be sensed.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing the details is returned. Usage details is available only in case of OpenVZ.
     */
    public function disk($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=disk');

        $ret['disk'] = $resp['disk'];
        $ret['inodes'] = $resp['inodes'];

        return $ret;
    }

    /**
     * GET the Bandwidth Usage of a VM.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid   The VMs ID
     * @param int $month The month in the format YYYYMM e.g. 201205 is for the Month of May, 2012
     *
     * @return array Returns an array of Bandwidth Information for the Month GIVEN.
     *               By Default the CURRENT MONTH details are returned
     */
    public function bandwidth($vid, $month = 0)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=bandwidth'.(!empty($month) ? '&show='.$month : ''));

        return $resp['bandwidth'];
    }

    /**
     * List the Processes in a VPS - Only OpenVZ.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing all the processes is returned
     */
    public function processes($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=processes');

        return $resp['processes'];
    }

    /**
     * List the Services in a VPS - Only OpenVZ.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing all the services is returned
     */
    public function services($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=services');

        $ret['services'] = $resp['services'];
        $ret['autostart'] = $resp['autostart'];
        $ret['running'] = $resp['running'];

        return $ret;
    }

    /**
     * Changes the root password of a VPS.
     *
     * @author       Pulkit Gupta
     *
     * @param int    $vid  The VMs ID
     * @param string $pass The new password to set
     *
     * @return string FALSE is returned if there was an error while setting the new password
     *                'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
     *                'done' is returned if the new password has been set right now - Mainly OpenVZ
     */
    public function changepassword($vid, $pass)
    {
        $post = ['newpass'       => $pass,
                    'conf'       => $pass,
                    'changepass' => 'Change Password',
                    ];

        $resp = $this->call('index.php?svs='.$vid.'&act=changepassword', $post);

        // Was there an error
        if (!empty($resp['error'])) {
            $this->error = $resp['error'];

            return false;

        // Will it be done when the VPS is STOPPED and STARTED ?
        } elseif (!empty($resp['onboot'])) {
            return 'onboot';

        // It was done successfully
        } elseif (!empty($resp['done'])) {
            return 'done';
        }
    }

    /**
     * Get the VNC Details like PORT, IP, VNC Password. Only available in case of Xen and KVM VPS if VNC is enabled.
     *
     * @author       Pulkit Gupta
     *
     * @param int $vid The VMs ID
     *
     * @return array An array containing all the VNC Details
     */
    public function vnc($vid)
    {
        $resp = $this->call('index.php?svs='.$vid.'&act=vnc');

        return $resp['info'];
    }

    /**
     * Change the VNC Password. Only available in case of Xen and KVM VPS if VNC is enabled.
     *
     * @author       Pulkit Gupta
     *
     * @param int    $vid  The VMs ID
     * @param string $pass The new password to set
     *
     * @return string FALSE is returned if there was an error while setting the new password
     *                'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
     */
    public function vncpass($vid, $pass)
    {
        $post = ['newpass'    => $pass,
                    'conf'    => $pass,
                    'vncpass' => 'Change Password',
                    ];

        $resp = $this->call('index.php?svs='.$vid.'&act=vncpass', $post);

        // Was there an error
        if (!empty($resp['error'])) {
            $this->error = $resp['error'];

            return false;

        // Will it be done when the VPS is STOPPED and STARTED ?
        } elseif (!empty($resp['onboot']) || !empty($resp['done'])) {
            return 'onboot';
        }
    }

    /**
     * Re-installs a VPS if the $newosid is specified. If the $newosid is not passed,
     * then this function will return an array of available templates.
     *
     * @author       Pulkit Gupta
     *
     * @param int    $vid     The VMs ID
     * @param int    $newosid The Operating System ID (you got from the list) that will be installed on the VPS.
     * @param string $newpass The new root password to set
     *
     * @return string FALSE is returned if there was an error while setting the new password
     *                string 'onboot' is returned if the new password will be set when the VPS is STOPPED and STARTED
     *                string 'done' is returned if the new password has been set right now - Mainly OpenVZ
     *                array An array of the list of avvailable OS Templates is returned if $newosid is NULL
     */
    public function ostemplate($vid, $newosid = null, $newpass = null)
    {

        // Get the list of OS Templates
        $resp = $this->call('index.php?svs='.$vid.'&act=ostemplate');

        // Get a list of Virtual Servers
        $listvs = $this->listvs();

        // Is there such a VPS ?
        if (!empty($listvs[$vid])) {
            $resp = $resp['oslist'][$listvs[$vid]['virt']];

        // No such VPS. Return an EMPTY ARRAY
        } else {
            return [];
        }

        if (!empty($newosid)) {

            // The POST Vars
            $post = ['newos'      => $newosid,
                        'newpass' => $newpass,
                        'conf'    => $newpass,
                        'reinsos' => 'Reinstall', ];

            $resp = $this->call('index.php?svs='.$vid.'&act=ostemplate', $post);

            // Was there an error
            if (!empty($resp['error'])) {
                $this->error = $resp['error'];

                return false;

            // Will it be done when the VPS is STOPPED and STARTED ?
            } elseif (!empty($resp['onboot'])) {
                return 'onboot';

            // It was done successfully
            } elseif (!empty($resp['done'])) {
                return 'done';
            }

        // Just return the OS List
        } else {
            return $resp;
        }
    }

    /**
     * Install a Control Panel.
     *
     * @author       Pulkit Gupta
     *
     * @param int    $vid   The VMs ID
     * @param string $panel The Name of the Panel you want to install. Options - cpanel, plesk, webuzo, kloxo, webmin
     *
     * @return string FALSE is returned if there was an error while installing the control panel
     *                'onboot' is returned if the control panel will be installed when the VPS is STOPPED and STARTED
     *                'done' is returned if the control panel has been installed right now - Mainly OpenVZ
     */
    public function controlpanel($vid, $panel)
    {
        $post['ins'][$panel] = 1;

        $resp = $this->call('index.php?svs='.$vid.'&act=controlpanel', $post);

        // Was there an error
        if (!empty($resp['error'])) {
            $this->error = $resp['error'];

            return false;

        // Will it be done when the VPS is STOPPED and STARTED ?
        } elseif (!empty($resp['onboot'])) {
            return 'onboot';

        // It was done successfully
        } elseif (!empty($resp['done'])) {
            return 'done';
        }
    }
}

//////////////
// Examples
//////////////

//$v = new Virtualizor_Enduser_API('127.0.0.1', '16_BIT_API_KEY', '32_BIT_API_PASS');

// Get the list of the VPS
//$v->r($v->listvs());

// Start a VPS
//echo $v->start(3);

// Stop a VPS
//echo $v->stop(3);

// Restart a VPS
//echo $v->restart(3);

// Poweroff a VPS
//echo $v->poweroff(3);

// Get the Status of a VPS
//echo $v->status(3);

// Get the Hostname
//echo $v->hostname(4);

// Change the Hostname
//$v->r($v->hostname(4, 'NEWHOSTNAME'));

// CPU Details
//$v->r($v->cpu(4));

// Ram Details
//$v->r($v->ram(4));

// Disk Details
//$v->r($v->disk(4));

// Bandwidth Details for the Current Month
//$v->r($v->bandwidth(4));

// Bandwidth Details for the Month of May in 2012
//$v->r($v->bandwidth(4, 201205));

// List the processes - OpenVZ only
//$v->r($v->processes(4));

// List the services - OpenVZ only
//$v->r($v->services(4));

// Change the Root Password of a Virtual Server ?
//$v->r($v->changepassword(4, 'test'));

// Give the VNC Details - VNC must be enabled - Xen / KVM
//$v->r($v->vnc(4));

// Change the VNC Password - VNC must be enabled - Xen / KVM
//$v->r($v->vncpass(4, 'NEWpass'));

// List available OS Templates
//$v->r($v->ostemplate(2));

// Reinstall the OS
//$v->r($v->ostemplate(4, 1, 'test'));

// Install a Control Panel
//$v->r($v->controlpanel(4, 'cpanel'));;