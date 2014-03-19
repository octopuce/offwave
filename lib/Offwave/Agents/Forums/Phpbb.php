<?php

class Offwave_Agents_Forums_Phpbb extends Offwave_Agents_Abstract {
    
        
    /**
     * 
     * Attempts to identify using the version.php
     * 
     * @param type $path
     * @param type $parameters
     * @return type
     */
    public function identifyVersion($path,$parameters){
        
        $version_file                   = $path."/database_update.php";
        if(is_readable($version_file)){
            $file_content               = file_get_contents($version_file);
            preg_match("/^\$updates_to_version = '(.*)';/",$file_content,$matches);
            if( isset($matches[1])){
                return array(
                "application"   => $this->getApplicationName(),
                "version"       => $matches[1]
            );
          }
      }
      return $parameters;
    }
}