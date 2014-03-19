<?php

class Offwave_Agents_Cms_Joomla extends Offwave_Agents_Abstract {
    
    public function identifyVersion($path,$parameters) {      
        $matchList                  = array(
            $path."/CHANGELOG.php"   => "(.*).*Released ",
        );
        foreach($matchList as $file => $pattern){
            if(is_file($file)){
                $file_content                 = file_get_contents($file);
                preg_match("%".preg_quote($pattern,"%")."%", $file_content, $matches);
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
