<?php

class Offwave_Agents_Abstract{

    const ERR_MISSING_PARAMETER = 1;
    const ERR_INVALID_PARAMETER = 2;
    
    /**
     * Defines the name of the application ie. Wordpress, Drupal, etc.
     * 
     * @var string
     */
    protected $application_name;
    /**
     * Defines the type of application ie. CMS, Framework
     * 
     * @var string
     */
    protected $application_type;
    
    /**
     * agent configuration, local files like trees 
     *
     * @var array
     */
    protected $config;

    /**
     * Stores an optional array of root db connections, passed as reference by scanner
     * 
     * @see Offwave_Scanner.__construct
     * @var array
     */
    protected $dbAccounts;
    /**
     * Stores an associative array of path => type
     * 
     * @var array
     */
    protected $pathCache;

    /**
     * 
     * @param array $options an associative array
     */
    public function __construct($options) {
        
        // Setting default parameters an extended class can replace at will
        $this->config = array(
            "application_tree_file"  => "tree.ini"
        );
        
    }
    
    /**
     * Virtual method
     */
    public function identifyApplication($path){
        return $this->_identifyApplicationTree($path);
    }
    
    /**
     * Virtual method
     */
    public function identityVersion($path){
        throw new Offwave_Exception("This method requires to be overriden.");
    }
    
    /**
     * Virtual method
     */
    public function identifyPlugins($path){
        throw new Offwave_Exception("This method requires to be overriden.");
    }
    
    /**
     * 
     * @param array $options
     * @return array
     */
    private function _identifyApplicationTree($path){
        
        $appplicationTrees = $this->_loadConfigurationFile($this->config["application_tree_file"]);
        foreach($appplicationTrees as $application_version => $applicationTree){
            if($this->_matchTree(array("tree" => $applicationTree,"path" => $path))){
                return array(
                    "application"   => $this->getApplicationName(),
                    "version"       => $application_version
                );
            }
        }
        
        return array();
        
    }
    
    /**
     * Getter for the application_name property
     * 
     * @return string
     * @throws Offwave_Exception
     */
    public function getApplicationName(){
        
        if( empty($this->application_name)){
            throw new Offwave_Exception("Missing application name",1);
        }
        return $this->application_name;
    }
    
    /**
     * Basic getter for application type
     * 
     * @return string
     * @throws Offwave_Exception
     */
    public function getApplicationType(){
        
        if( empty($this->application_type)){
            throw new Offwave_Exception("Missing application type",1);
        }
        return $this->application_type;
    }
        
    /**
     * Attempts to parse a file, actually only ini files are accepted
     * 
     * @param string $filename
     * @return array
     * @throws Offwave_Exception
     */
    protected function _loadConfigurationFile( $filename = NULL ){
        $filename                   = __DIR__."/".$this->getApplicationType()."/".$this->getApplicationName()."/".$filename;
        if( !is_file($filename)){
            throw new Offwave_Exception("File $filename doesn't exist",1);
        }
        return parse_ini_file($filename, TRUE);
    }

     /**
     * 
     * @param type $options
     * @return boolean
     * @throws Offwave_Exception
     */
    protected function _matchTree( $options = NULL ){
       
        if( ! empty($options["tree"]) ) {
            $tree = $options["tree"];
        } else {
            throw new Offwave_Exception(_("Missing tree."), self::ERR_MISSING_PARAMETER);
        }
        if( ! is_array($tree)){
            throw new Offwave_Exception(_("Invalid tree structure, array required."), self::ERR_INVALID_PARAMETER);
        }
        if( ! empty($options["path"])) {
            $path = $options["path"];
        } else {
            throw new Exception(_("Missing path."), self::ERR_MISSING_PARAMETER);
        }
        foreach( $tree as $file_path => $file_type){
            $assertion              = FALSE;
            $file_path              = "{$path}/{$file_path}";
            if(array_key_exists($file_path, $this->pathCache) && array_key_exists($file_type, $this->pathCache[$file_path])){
                $assertion          = $this->pathCache[$file_path][$file_type];
            }
            else switch ($file_type){
                case "dir":
                    if(is_dir($file_path)){
                        $assertion  = TRUE;
                    }
                    break;
                case "file":
                    if(is_file($file_path)){
                        $assertion  = TRUE;
                    }
                    break;
                default:
                    throw new Offwave_Exception("Invalid file type : $file_type");
            }
            $this->pathCache[$file_path][$file_type]    = $assertion;
            if( ! $assertion ){
                return FALSE;
            }
        }
        
        return TRUE;

    }
        
    /**
     * Basic setter for application_name
     * 
     * @param string $name
     * @return \Offwave_Agents_Abstract
     * @throws Offwave_Exception
     */
    public function setAgentName( $name = NULL ){
        if (NULL == $name ) {
            throw new Offwave_Exception(_("Missing name."));
        }
        $this->application_name = $name;
        return $this;
    }
    
    /**
     * Basic setter for application_type
     * 
     * @param type $type
     * @return \Offwave_Agents_Abstract
     * @throws Offwave_Exception
     */
    public function setAgentType( $type = NULL ){
        if (NULL == $type ) {
            throw new Offwave_Exception(_("Missing type."));
        }
        $this->application_type = $type;
        return $this;
    }
    
    /**
     * Sets a persistent cache located on Offwave_Scanner
     * 
     * @param array $cache
     * @return \Offwave_Agents_Abstract
     * @throws Offwave_Exception
     */
    public function setPathCache( array &$cache = array()){

        $this->pathCache = &$cache;
        return $this;
    }
    
}