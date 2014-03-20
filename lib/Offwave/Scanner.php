<?php
class Offwave_Scanner{
    
    const AGRESSIVITY_LOW       = 1; // stop at the first found application
    const AGRESSIVITY_MEDIUM    = 5; // scan all applications
    const AGRESSIVITY_HIGH      = 10;
    const DEPTH_NONE            = 0; // don't scan subfolders recursively.
    const DEPTH_INFINITE        = -1; // scan folders recursively, with infinite depth.
    
    /**
     * Records configuration for multiple agents once 
     * 
     * @var array
     */
    private $agentConfiguration = array();
    /**
     * Stores the agents
     * 
     * @var array
     */
    private $agentsList         = array();
    /**
     * Defines the level of depth for the search
     * 
     * @var int
     */
    private $aggressivity       = self::AGRESSIVITY_HIGH;
    
    /**
     * Stores the optional root db connections used to investigate applications
     * 
     * @var array
     */
    private $dbAccounts         = array();
    
    /**
     * Flag for activating debug
     * 
     * @see Offwave_Scanner::debug
     * @var bool
     */
    static $do_debug            = FALSE;
    
    /**
     * Stores debug messages
     *
     * @see Offwave_Scanner::debug
     * @var array
     */
    private static $debugContainer      = array();
    
    /**
     * Stores path => type => bool information for a given root path
     * 
     * @var array
     */
    public $pathCache          = array();


    function __construct( array $options=array() ) {
        
        // Sets up optional db access instances
        if( ! empty($options["db_accounts"])){
            $this->_setupDbAccounts( $options["db_accounts"]);
        }
        // Loads agents and injects dependencies
        if( ! empty($options["agents_directories"])){
            $agentsDirectories = $options["agents_directories"];
        }else{
            $agentsDirectories = array(
              "Cms","Forums","Tools"
            );
        }
        
        $this->agentConfiguration = array(
            "dbAccounts"    => &$this->dbAccounts
        );
        
        $this->_setupAgentsList($agentsDirectories);
      
        
    }
    
    /**
     * Getter / Setter for debug container
     * 
     * @param type $msg
     * @return array
     */
    static function debug($msg = NULL ){
        if( NULL !== $msg && TRUE === self::$do_debug){
            self::$debugContainer[] = $msg;
            return;
        }
        return self::$debugContainer;
    }

    /**
     * Scan a folder (and maybe its subfolders) for CMSes
     * @param string $path the root path to search for
     * @param integer $depth the DEPTH to search recursively (or not) 
     * you can use DEPTH_NONE or DEPTH_INFINITE or any integer
     * @return array an associative array as follow: each key is a path name, 
     *  and its values are a list of arrays for each application found 
     *  the values in those arrays are "application" name, "version", "modules"
     */
    function scan($path, $depth=Offwave_Scanner::DEPTH_NONE){

        if( null == $path ){
            throw new Offwave_Exception("Scanner path cannot be null",1);
        }
	$depth                                      = intval($depth);
	$pathResult                                 = array();
     	$this->_scanDepth($path, $pathResult, $depth);
	return $pathResult;
    }


    private function _scanDepth($path, &$pathResult, $depth) {
        // 1. Attempts to identify application
        $webApplicationData                         = array();
        foreach( $this->agentsList as $agent_label => $agentInstance){
            Offwave_Scanner::debug("[1] in '$path' Attempting to identify {$agentInstance->getApplicationType()} {$agent_label}");
            $result = $agentInstance->identifyApplication($path);
            if(count($result)){
                $matchingAgents [$agent_label]      = $agentInstance;
                $webApplicationData[$agent_label]   = $result;
                // Skips if aggressivity is low
                if($this->aggressivity < self::AGRESSIVITY_MEDIUM){
                    break;
                }
            }
        }
        
        if( count($webApplicationData)){
	  // We found something quickly, let's search more deeper:

	  // 2. Attempts to get application versions
	  foreach($matchingAgents as $agent_label => $agentInstance){
	    $webApplicationData[$agent_label]       = $agentInstance->identifyVersion( $path, $webApplicationData[$agent_label]);
	  }
	  
	  // 3. Attempts to identify plugins and their versions
	  foreach($matchingAgents as $agent_label => $agentInstance){
	    $webApplicationData[$agent_label]       = $agentInstance->identifyModules( $path, $webApplicationData[$agent_label] );
	  }
	  
	  // 4. Add found applications to the $result array:
	  foreach($webApplicationData as $agent=>$agent_result) {
	    if (count($agent_result)) {
	      $pathResult[$path][$agent]=$agent_result;
	    }
	  }
	}

	// 5. if required, parse recursively on subfolders
	if ($depth!=0) {
	  foreach (new DirectoryIterator($path) as $fileInfo) {
	    if( ! $fileInfo->isDot() && $fileInfo->isDir() ){
	      $this->_scanDepth($fileInfo->getPathname(),$pathResult,$depth-1);
	    }
	  }
	}
	return;
    } /* _scanDepth */
    

    private function _setupDbAccounts( $options = NULL ){
        // Attempts to spawn a PDO connection        
        // Stores successful connection in self property
    }
    
    /**
     * Acts as a factory for the agents, instancing them with the same config container
     * 
     * @param type $agentsDirectories
     * @return \Offwave_Scanner
     * @throws Offwave_Exception
     */
    private function _setupAgentsList( $agentsDirectories = array()){
        
        $dir_path = __DIR__."/Agents";
        if( ! is_dir( $dir_path)){
            throw new Offwave_Exception("Path {$dir_path} is invalid.");
        }
        // Loops trough multiple known directories
        foreach( $agentsDirectories as $agent_type ){
            $dir_agent_path = "{$dir_path}/{$agent_type}";
            // Loops through multiple unknown files
            foreach (new DirectoryIterator($dir_agent_path) as $fileInfo) {
                if( ! $fileInfo->isDot() && ! $fileInfo->isDir() ){
                    if( preg_match("/([A-Za-z0-9]+)\.php/",$fileInfo->getFilename(),$matches)){
                        require_once $fileInfo->getPathname();
                        $agent_name                         = $matches[1];
                        $class_name                         = "Offwave_Agents_{$agent_type}_{$agent_name}";
                        $agentInstance                      = new $class_name($this->agentConfiguration);
                        $agentInstance->setAgentName( $agent_name )->setAgentType( $agent_type )->setPathCache( $this->pathCache );
                        $this->agentsList[$agent_name]      = $agentInstance;
                    }
                }
            }
        }
        return $this;
    }
}
