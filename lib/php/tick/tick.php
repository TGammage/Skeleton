<?php

namespace Tick;

class Tock
{
    protected   $timer = array(
        'start'     => null,
        'finish'    => null
    );

    protected   $timestamp = null;


    protected   $reports        = '';
    protected   $error_count    = 0;


    public function __construct( $timestamp = null )
    {
        // Start Timer
        $this->timer['start'] = new \DateTime();

        // Create Tick Timestamp
        if( $timestamp )
        {
            // Override Tick Time
            $GLOBALS['tick_time'] = \DateTime::createFromFormat( 'Y-m-d H:i', $timestamp );
        } else {
            $GLOBALS['tick_time'] = new \DateTime();
        }

        // Dir = ...Skeleton/lib/php/tick/
        $dir = explode( DIRECTORY_SEPARATOR, __DIR__ );
        array_pop( $dir );
        array_pop( $dir );
        array_pop( $dir );

        chdir( implode( DIRECTORY_SEPARATOR, $dir ) );

        require "start.php";

        // ---------------------------------
        // Global DB Connection
        // ---------------------------------
        $GLOBALS['__mysql__'] = new \db( 'member', 'tick' );

        // ---------------------------------
        // Determine which ticks to fire
        // ---------------------------------

        // Disect Tick
        $min  = (int) $GLOBALS['tick_time']->format('i');
        $hour = (int) $GLOBALS['tick_time']->format('H');

        // Every minute
        $tick = new OnMinute;
        $this->error_count  += count( $tick->error );
        $this->reports      .= $tick->report();
        
        // Even/Odd Minutes
        if( $min % 2 === 0 )
        {
            // Even Minutes
            $tick = new EvenMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        } else {
            // Odd Minutes
            $tick = new OddMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        
        // Every 5th min (:00,:05,:10...)
        if( $min % 5 === 0 )
        {
            $tick = new FiveMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        
        // Every 10th min (:00,:10,:20...)
        if( $min % 10 === 0 )
        {
            $tick = new TenMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        
        // Every 15th min (:00,:15,:30,:45)
        if( $min % 15 === 0 )
        {
            $tick = new FifteenMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        
        // Every 30th min (:00,:30)
        if( $min % 30 === 0 )
        {
            $tick = new ThirtyMinute;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        
        // Every Hour (:00)
        if( $min % 60 === 0 )
        {
            $tick = new OnHour;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }

        // Half Day (00:00,12:00)
        if( $hour % 12 === 0 && $min %60 === 0 )
        {
            $tick = new HalfDay;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }

        // Midnight (00:00)
        if( $hour % 24 === 0 && $min %60 === 0 )
        {
            $tick = new Midnight;
            $this->error_count  += count( $tick->error );
            $this->reports      .= $tick->report();            
        }
        // ---------------------------------
        
        // Finish Timer
        $this->timer['finish'] = new \DateTime();

        $this->log();
    }

    private function log()
    {
        $_ =  "Tick commenced at : " . $this->timer['start']->format( 'H:i:s.u' ) . "\r\n";
        $_ .=  "\r\nTick executed : " . $GLOBALS['tick_time']->format( 'Y-m-d H:i:s' ) . "\r\n";
        $_ .= $this->reports;
        $_ .= "\r\nTick finished at : " . $this->timer['finish']->format( 'H:i:s.u' ) . "\r\n";
        
        $f = fopen( ROOT_DIR . '/log/tick/lasttick.log', 'w' );
        
        fwrite( $f, $_ );
        fclose( $f );
    }
}

// Make sure key is supplied
if( !isset( $_SERVER['argv'][1] ) )
{
    echo "Tick Error : Missing key\r\n";
    exit;
}

// Make sure key is correct
if( $_SERVER['argv'][1] !== 'Zero' )
{
    echo "Tick Error : Incorrect key\r\n";
    exit;
}


// Override the time for the tick
$time = null;

if( isset( $_SERVER['argv'][2] ) && preg_match( '/\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}/', $_SERVER['argv'][2] ) )
{
    $time = $_SERVER['argv'][2];
}

new Tock( $time );
// var_dump($_SERVER);
?>