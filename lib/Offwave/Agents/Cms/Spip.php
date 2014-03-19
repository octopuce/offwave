<?php

class Offwave_Agents_Cms_Spip extends Offwave_Agents_Abstract {

    
  public function identifyVersion($path,$parameters) {      
      
      $svn_revision_file            = $path."/svn.revision";
      if(is_file($svn_revision_file)){
          $file_content = file_get_contents($svn_revision_file);
          preg_match("/spip\-([\d\.\w]*)/", $file_content, $matches);
          if( isset($matches[1]) && !is_null($matches[1])){
              $version = $matches[1];
              return array(
                "application"   => $this->getApplicationName(),
                "version"       => "SPIP-".$version
                );
          }
      }
      
      return $parameters;
    }
    
    
}
