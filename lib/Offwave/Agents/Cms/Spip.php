<?php

class Offwave_Agents_Cms_Spip extends Offwave_Agents_Abstract {
    
    
//    public function identifyApplication($path){
		
//		Alors spip a un arbre comme ça en 1.0+
//		
//		Mais spip a un arbre comme ça en 1.5+
//		
//		Oui et la DB est
//		
//    }
    
  public function identifyVersion($path,$parameters) {      
    return $parameters;
    }
    
    
  public function identifyModules($path,$parameters) {
    return $parameters;
  }
    
}
