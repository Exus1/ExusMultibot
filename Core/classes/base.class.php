<?php

class baseObject {

  //*******************************************************************************************
  //****************************************** Vars *******************************************
  //*******************************************************************************************

  /**
   * lang
   * 
   * <pre>
   * Array
   * (
   *  [section] => Array
   *   (
   *    [var] => value
   *   )
   *  [section2] => Array
   *   (
   *    [var] => value
   *   )
   *  )
   * )
   */
  protected $lang;
  /**
   * paths
   * 
   * <pre>
   * Array
   * (
   *  [folders] => Array
   *   (
   *    [var] => value
   *   )
   *  [files] => Array
   *   (
   *    [var] => value
   *   )
   * )
   * 
   * @var Array $paths
   */
  protected $paths;
  /**
   * ts3admin object
   * 
   * @var resource $tsAdmin
   */
  protected $tsAdmin;

  /**
   * internal socket resource
   * 
   * @var resource $socket
   */
  protected $socket;

  /**
   * Array
   * (
   *  [server_config] => Array
   *   (
   *    [adress] => ip adress
   *    [query_port] => port query (10011)
   *    [server_port] => server port (9987)
   *    [login] => query user login
   *    [password] => query user password
   *   )
   *  [multibot_config] => Array
   *   (
   *    [command_bot_name] => command user name
   *    [instance_name] => instance user name
   *    [instances] => the number of processes to run
   *    [protect_primary_instance] => In the primary instance will only have been function with the option "primary_insnce = true"
   *   )
   * )
   * 
   * @var Array $general_config
   */
  protected $general_config;
  /**
   * Array
   * (
   *  [command_name] => command file
   * )
   * 
   * @var Array $command_list
   */
  protected $command_list;
  /**
   * multibot_config
   * 
   * <pre>
   * Array 
   * (
   *   [function_name] => Array
   *    (
   *     [general_config] => Array
   *      (
   *       [enable] => bool true/false
   *       [weight] => int function_weight
   * 	   [refresh] => int refreh_time
   * 	   [primary_instance] => bool insert_in_primary_instance
   * 	  )
   * 	 [section1] => Array
   *      (
   * 	   [var1] => value
   * 	    .
   * 	    .
   * 	    .
   * 	  )
   * 	)
   * )
   * </pre>
   * 
   * @var Array $multibot_config
   */
  protected $multibot_config;
  /**
   * Array
   * (
   *  [group_id] => Array
   *   (
   *    [permission] => permission 
   *   )
   * )
   * 
   * @var Array $permission_list
   */
  protected $permission_list;

  //*******************************************************************************************
  //************************************ Public Functions *************************************
  //******************************************************************************************







  /**
   * 
   * @return Array
   */
  function getLang()  {
    return $this->lang;
  }

  /**
   * 
   * @param string $type
   * @return Array|Array[]
   */
  function getConfig($type = true) {
    if($type == "general")  {
      return $this->general_config;
    }elseif($type == "multibot")  {
      return $this->multibot_config;
    }else {
      return Array("general_config" => $this->general_config, "multibot_config" => $this->multibot_config, "permission_list" => $this->permission_list, "command_list" => $this->command_list);
    }
  }
  
  /**
   * 
   * @return Array
   */
  function getCommandList() {
    return $this->command_list;
  }

  /**
   * 
   * @return Array
   */
  function getPermissionList()  {
    return $this->permission_list;
  }

  /**
   * 
   * @return resource
   */
  function getInternalSocket()  {
    return $this->socket;
  }

  /**
   * 
   * @return Array
   */
  function getPaths() {
    return $this->paths;
  }

  /**
   * 
   * @return resource
   */
  function getTsAdmin() {
    return $this->tsAdmin;
  }

  /**
   * @return resource ts3admin socket
   */
  function getTsAdminSocket() {
    return $this->tsAdmin->runtime['socket'];
  }




  /**
   * 
   * @param string $name
   * @return boolean
   */
  function setName($name) {
    if(!$this->tsAdmin->getElement('success', $this->tsAdmin->setName($name)))  {
      $this->addError($this->lang['base']['change_name_error'], false, true);
      return false;
    }else {
      $this->addInfo($this->lang['base']['change_name_success'] . " " . "(" . $name . ")");
      return true;
    }
  }




  /**
   * 
   * @param string $object_type | multibot or commands
   * @return boolean
   */
  function setConfig($object_type)  {
    if($object_type = "commands") {
      $this->setCommandList();
      $this->setPermissionList();
      $this->setMultibotConfig();
      return true;
    }elseif($object_type = "multibot")  {
      $this->setMultibotConfig();
      return true;
    }else {
      return false;
    }
  }






  /**
   * 
   * @param string $name
   * @param boolean $critical
   * @param boolean $tsAdmin
   */
  function addError($name, $critical = false, $tsAdmin = false) {
    // critical == true - Błąd krytyczny (kończy wykonywanie)
    // critical == false - Błąd umożliwiający dalsze wykoananie
    if(empty($name))  {
      print red . 'UNKNOWN ERROR'. resetColor ."\n";
    }

    if(is_bool($critical))  {
      if($critical)  {

        //$this->killAllInstances();

        if($tsAdmin)  {
          $error = $this->tsAdmin->getDebugLog();
          print red . "CRITICAL ERROR: ". resetColor .$name . "\n";
          print_r($error);
          die();
        }else {
          die(red . "CRITICAL ERROR: ". resetColor . $name . "\n");
        }
      }

      if(!$critical)  {
        if($tsAdmin)  {
          $error = $this->tsAdmin->getDebugLog();
          print red . "ERROR: ". resetColor .$name . "\n";
          print_r($error);
          print "\n";
        } else {
          print red . 'ERROR: '. resetColor . $name. "\n";
        }
      }
    }
  }


  /**
   * 
   * @param string $name
   */
  function addInfo($name) {
    print green . 'INFO: '. resetColor . $name . "\n";
  }



  //*******************************************************************************************
  //*********************************** Internal Functions ************************************
  //*******************************************************************************************


  /**
   * 
   * @param string $object_type | commands or multibot
   */
  function __construct($object_type)  {
    global $lang;
    global $paths;

    $this->paths = $paths;
    $this->lang = $lang;

    // General Config load
    $general_config = parse_ini_file($paths['files']['general-config'], true);
    $this->general_config = $general_config;

    if(empty($general_config))  {
      $this->addError($lang['base']['general_config_load_error'], true);
    }else {
      $this->addInfo($lang['base']['general_config_load_success']);
    }

    require($paths['files']['ts3admin']);
    $this->tsAdmin = new ts3Admin($general_config['server_config']['adress'], $general_config['server_config']['query_port']);

    if(!is_object($this->tsAdmin))  {
      $this->addError($lang['base']['ts3admin_object_create_error'], true);
    }else{
      $this->addInfo($lang['base']['ts3admin_object_create_success']);
    }

    if(!$this->tsAdmin->getElement('success', $this->tsAdmin->connect()))  {
      $this->addError($lang['base']['server_connect_error'], true, true);
    }else{
      $this->addInfo($lang['base']['server_connect_success']);
    }


    if(!$this->tsAdmin->getElement('success', $this->tsAdmin->login($general_config['server_config']['login'], $general_config['server_config']['password'])))  {
      $this->addError($lang['base']['server_login_error'], true, true);
    }else {
      $this->addInfo($lang['base']['server_login_success']);
    }

    if(!$this->tsAdmin->getElement('success', $this->tsAdmin->selectServer($general_config['server_config']['server_port'])))  {
      $this->addError($lang['base']['server_select_error'], true, true);
    }else {
      $this->addInfo($lang['base']['server_select_success']);
    }

    $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
    if(!$this->socket)  {
      $this->addError("/n". $lang['base']['socket_create_error'], true);
    }else {
      $this->addInfo($lang['base']['socket_create_success']);
    }



    if(isset($object_type) && ($object_type == "commands"))  {

      if(!$this->tsAdmin->getElement('success', $this->tsAdmin->setName($general_config['multibot_config']['command_bot_name'])))  {
        $this->addError($lang['base']['change_name_error'], false, true);
      }else {
        $this->addInfo($lang['base']['change_name_success']);
      }

      if(file_exists("socket.sock")) {
	unlink("socket.sock");
      }

      if(!socket_bind($this->socket, "socket.sock")) {
        $this->addError($lang['base']['socket_bind_error'], true);
      }else {
        $this->addInfo($lang['base']['socket_bind_success']);
      }

      $this->setConfig($object_type);
    }elseif(isset($object_type) && ($object_type == "multibot")) {

      if(!socket_connect($this->socket, "socket.sock")) {
        $this->addError($lang['base']['instance_connect_error'], false);
      }else {
        $this->addInfo($lang['base']['instance_connect_success']);
      }

      if(!socket_set_nonblock($this->socket)) {
        $this->addError($lang['base']['socket_noblock_error']);
      }else {
        $this->addInfo($lang['base']['socket_noblock_success']);
      }

      $this->setConfig($object_type);
    }else {
      $this->addError($lang['base']['unknown_instance_type'] ." " . $object_type, true);
    }


  }


  /**
   * @return boolean
   */
  private function setCommandList()  {
    if(is_dir($this->paths['folders']['commands']))  {
      if($dh = opendir($this->paths['folders']['commands'])) {
        while(($file = readdir($dh)) !== false) {
          if(strstr($file, ".php") !== false) {
            $command_name = substr($file, 0, strpos($file, ".php"));
            $command_list[mb_strtolower($command_name, "UTF-8")] = $file;
          }
        }
        if(empty($command_list))  {
          $this->addError($this->lang['base']['command_list_error']);
          $this->commandList  = false;
          return false;
        } else {
          $this->command_list = $command_list;
          $this->addInfo($this->lang['base']['command_list_success']);
          return true;
        }
        closedir($dh);
      }else {
        $this->addError($this->lang['base']['command_folder_open_error']);
        $this->commandList = false;
        return false;
      }
    }else {
      $this->addError($this->lang['base']['command_folder_open_error']);
      $this->commandList = false;
      return false;
    }
  }








  /**
   * @return boolean
   */
  private function setPermissionList() {
    $permissionsLoad = parse_ini_file($this->paths['files']['permissions']);

    if(empty($permissionsLoad)) {
      $this->addError($this->lang['base']['permission_file_open_error']);
    }else {
      $this->addInfo($this->lang['base']['permission_file_load_success']);
    }

    $permissions = Array();

    foreach($permissionsLoad as $dbid => $perms) {
      $perms = preg_replace('/\s+/', '', $perms);
      $perm = explode(",", $perms);
      foreach($perm as $permTemp) {
        $permTemp = mb_strtolower($permTemp, "UTF-8");
        if(isset($permissions[$permTemp]) && !empty($permissions[$permTemp])) {
          $permissions[$permTemp] .= ",".$dbid;
        }else {
          $permissions[$permTemp] = $dbid;
        }
      }
    }
    $this->permission_list = $permissions;
    return true;
  }



  /**
   * @return boolean
   */
  private function setMultibotConfig() {
    $config_files = getFilesList($this->paths['folders']['functions-configs']);

    if(empty($config_files))  {
      $this->addError($this->lang['base']['config_foler_open_error'], true);
    }

    foreach($config_files as $config_file) {
      if(substr($config_file, 0, strpos($config_file, ".conf")))
      $functions_configs[mb_strtolower(substr($config_file, 0, strpos($config_file, ".conf")), "UTF-8")] = parse_ini_file($this->paths['folders']['functions-configs']. $config_file, true);
    }

    if(!empty($functions_configs))  {
      $this->multibot_config = $functions_configs;
      $this->addInfo($this->lang['base']['config_load_success']);
      return true;
    }else {
      $this->addError($this->lang['base']['config_load_error'], true);
      return false;
    }
  }

}

?>
