<?php

namespace Boxzilla\DI;

class ContainerWithPropertyAccess extends Container {
    
   /**
    * @param string $name
    * @return mixed
    */
   public function __get( $name ) {
       return $this[ $name ];
   }

   /**
    * @param string $name
    * @param mixed $value
    */
   public function __set( $name, $value ) {
       $this[ $name ] = $value;
   }

}