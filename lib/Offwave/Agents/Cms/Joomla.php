<?php

class Offwave_Agents_Cms_Joomla extends Offwave_Agents_Abstract {
    
    
//    public function identifyApplication($path){
//		Alors joomla a un arbre comme ça en 1.0+
//		Mais joomla a un arbre comme ça en 1.5+
//    }
    
  public function identifyVersion($path,$parameters) {      
    return $parameters;
    }
    
    
  public function identifyModules($path,$parameters) {
    return $parameters;
  }
    
}
