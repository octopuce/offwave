<?php

class Offwave_Agents_Cms_Typo3 extends Offwave_Agents_Abstract {
    
    
    public function identifyVersion($path,$parameters) {      
        $matchList                  = array(
            $path."/t3lib/config_default.php"   => "TYPO_VERSION = (.*)",
            $path."/ChangeLog"                  => "Release of TYPO3 (.*)"
        );
        foreach($matchList as $file => $pattern){
            if(is_file($file)){
                $file_content                 = file_get_contents($file);
                preg_match($pattern, $file_content, $matches);
                if( isset($matches[1]) && !is_null($matches[1])){
                    return array(
                      "application"   => $this->getApplicationName(),
                      "version"       => $matches[1]
                      );
                }
            }
        }
      return $parameters;
    }
}
