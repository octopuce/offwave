<?php

class Offwave_Agents_Cms_Wordpress extends Offwave_Agents_Abstract {
    
    
    /**
     * 
     * Attempts to identify using the version.php
     * 
     * @param type $path
     * @param type $parameters
     * @return type
     */
    public function identityVersion($path,$parameters){
        
        $version_file                   = $path."/wp-includes/version.php";
        if(is_readable($version_file)){
            $file_content               = file_get_contents($version_file);
            preg_match("/wp_version = '(.*)';/", $file_content,$matches);
            if( isset($matches[1])){
                return array(
                "application"   => $this->getApplicationName(),
                "version"       => $matches[1]
            );
          }
      }
      return $parameters;
    }
    
    
  public function identifyModules($path,$parameters) {
    return $parameters;
  }
}
