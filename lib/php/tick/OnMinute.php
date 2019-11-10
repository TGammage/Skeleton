<?php
namespace Tick;

class OnMinute extends TickModel
{
    /** @var string Used for logging */
    public  $self   = __CLASS__;

    public function test( $nameless = 1 )
    {
        return $nameless;
    }
    
    public function test2( $nameless = 1 )
    {
        return $nameless;
    }
}
?>