<?php

class Calendar extends DateTime
{
	/** Time Constants */
	const MINUTE		= 60;
	const MINUTE_15		= 60 * 15;
	const HOUR			= 60 * 60;
	const HOUR_6		= 60 * 60 * 6;
	const HOUR_12		= 60 * 60 * 12;
	const DAY			= 60 * 60 * 24;
	const WEEK			= 60 * 60 * 24 * 7;
	const WEEK_2		= 60 * 60 * 24 * 7 * 2;
	const WEEK_4		= 60 * 60 * 24 * 7 * 4;

	public	$today		= 'today';
	public	$tomorrow	= 'tomorrow';
	public	$yesterday	= 'yesterday';
	public	$dayofweek;

	/** Static Holidays */
	const NEW_YEARS_EVE			= "December 31";
	const NEW_YEARS_DAY			= "January 1";
	const GROUNDHOGS_DAY		= "February 1";
	const VALENTINES_DAY		= "February 14";
	const TX_INDEPENDENCE_DAY	= "March 2";
	const ST_PATRICKS_DAY		= "March 17";
	const USA_INDEPENDENCE_EVE	= "July 3";
	const USA_INDEPENDENCE_DAY	= "July 4";
	const HALLOWEEN				= "31 October";
	const CHRISTMAS_EVE			= "December 24";
	const CHRISTMAS_DAY			= "December 25";

	/** Dynamic Holidays */
	const MLK_DAY				= "Third Monday of January";
	const MOTHERS_DAY			= "Second Sunday of May";
	const MEMORIAL_DAY			= "Last Monday of May";
	const FATHERS_DAY			= "Third Sunday of June";
	const INDIGENOUS_DAY		= "Second Monday of October";
	const THANKSGIVING_DAY		= "Fourth Thursday of November";

	/** Return Patterns */
	const MYSQL_DATETIME	= "Y-m-d H:i:s";
	const MYSQL_DATE		= "Y-m-d";
	const MYSQL_TIME		= "H:i:s";
	const LONG_DATE			= "F n, Y";
	const LONG_24_DATETIME	= "F n, Y H:i:s";
	const LONG_12_DATETIME	= "F n, Y g:i:sa";
	const DAY_OF_WEEK_LONG	= "l";
	const DAY_OF_WEEK_SHORT	= "D";

	/*
	*
	*/
	public static function getHoliday( $holiday, $year = 'This Year', $return_format = self::LONG_12_DATETIME )
	{
		$string = "$holiday $year";
		var_dump( $string );

		$totime = strtotime( $string );
		var_dump( $totime );

		print date( $return_format, $totime ) . "<br>";
	}

	/**
	*
	*/
	public static function examples()
	{
		echo "11/12/10 = " . date("jS F, Y", strtotime("11/12/10")) . "<br>"; 
		// outputs 12th November, 2010 

		echo "11-12-10 = " . date("jS F, Y", strtotime("11-12-10")) . "<br>"; 
		// outputs 10th December, 2011  
	}

}
?>
