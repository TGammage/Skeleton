<?php
namespace Tick;

abstract class TickModel
{
    /** @var string Used for log purposes, typically overwritten in extending class  */
    public $self = __CLASS__;

    /** @var array Methods to run from extending class */
    public $registrar = array();

    /** @var array Methods to skip (TickModel Methods) */
    public $skip      = array(
        '__construct',
        'run',
        'logErrors',
        'report'
    );

    /** @var array Methods from the extending class */
    public $method = array();

    /** @var array Methods that cause errors */
    public $error = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        // Set how we handle tick errors
        set_error_handler( function( $s, $m, $f, $l )
        {
            // Trace Error
            $trace = debug_backtrace( false );

            // Grab method causing error
            $this->error[] = $trace[1]['function'];
        });
        
        // Run Methods in extending class
        self::run();

        // Report errors
        if( count( $this->error ) )
            self::logErrors();
    }
    
    /**
     * run()
     *
     * @purpose
     *      Runs methods in extending tick classes
     */
    public function run()
    {
        $this->method = get_class_methods( $this );

        foreach( $this->method as $fn )
        {
            // Skip methods in model class
            if( !in_array( $fn, $this->skip ) )
                $this->$fn();
        }
    }

    /**
     * logErrors()
     *
     * @purpose
     *      Reports which methods are producing errors.
     *      Will not report error itself, only the method.
     *      Appends to a file titled by date
     */
    private function logErrors()
    {
        /** TODO : Log Errors */
        $year    = date( 'Y', $GLOBALS['tick_time'] );
        $month   = date( 'm', $GLOBALS['tick_time'] );
        $day     = date( 'd', $GLOBALS['tick_time'] );

        $path   = implode( DIRECTORY_SEPARATOR, array( $GLOBALS['conf']->dir['log'], 'tick', $year, $month ) );

        // Determine if directory this month's directory exists
        if( !is_dir( $path ) )
            mkdir( $path, 0770, true );

        $time = date( 'H:i:s', $GLOBALS['tick_time'] );

        $text = "[$time] {$this->self} generated error(s) in function(s) : " . implode( '(), ', $this->error ) . "()\r\n";

        // Write to file
        $f = fopen( $path . DIRECTORY_SEPARATOR . $day . '.log', 'a' );

        fwrite( $f, $text );

        fclose( $f );
    }

    /**
     * report()
     *
     * @purpose
     *      Quick report for lasttick.log to tell if there were errors
     */
    public function report()
    {
        $ran = count( $this->method ) - count( $this->skip );

        return "\t{$this->self} ran $ran function(s) finishing with " . count( $this->error ) . " error(s)...\r\n";
    }
}
?>