<?php

class Offwave_Agents_Cms_Drupal extends Offwave_Agents_Abstract {
    
    
    public function identifyVersion($path,$parameters) {      

        // Searches through one of two changelog files the version in the top 3 lines
        $fileList                       = array(
            $path."/CHANGELOG",
            $path."/CHANGELOG.txt"
        );
        $pattern                        = "/^Drupal ([\d\.\w]*)/";
        foreach ($fileList as $changelog_file){
            if(is_file($changelog_file)){
                $fileContent            = file($changelog_file);
                for ($index = 0; $index < 3; $index++) {
                    $line               = $fileContent[$index];
                    preg_match($pattern, $line, $matches);
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
    
}
