<?php

namespace Boxzilla\DI;

/**
 * Class ContainerWithPropertyAccess
 *
 * This modifies Pimple so it is PSR-11 compatible and can be accessed using property accessors.
 *
 * @package Boxzilla\DI
 */
class ContainerWithPropertyAccess extends Container {
    
   /**
    * @param string $name
    * @return mixed
    */
   public function __get( $name ) {
       return $this->offsetGet( $name );
   }

   /**
    * @param string $name
    * @param mixed $value
    */
   public function __set( $name, $value ) {
       $this[ $name ] = $value;
   }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset( $name ) {
        return $this->offsetExists( $name );
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has( $name ) {
        return $this->offsetExists( $name );
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get( $name ) {
        return $this->offsetGet( $name );
    }

}