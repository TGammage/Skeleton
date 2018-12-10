<?php

class random
{
	/** Numbers and all alphabetic characters */
	const	ALPHANUMERIC_ALL	= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	/** Numbers and capital alphabetic letters only */
	const	ALPHANUMERIC_CAP	= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/** Numbers and lower case alphabetic letters only */
	const	ALPHANUMERIC_LOW	= '0123456789abcdefghijklmnopqrstuvwxyz';

	/** Capital alphabetic letters only */
	const	ALPHA_CAP			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/** Lower case alphabetic letters only */
	const	ALPHA_LOW			= 'abcdefghijklmnopqrstuvwxyz';

	/** Easy to decipher characters */
	const	CAPTCHA				= '23456789ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnpqrstuvwxyz';

	/**
	*		string()
	*
	*		@purpose
	*			To return a random string of characters
	*
	*		@param
	*			int		$length		Number of characters in string
	*			string	$chars		Possible characters in return string
	*			bool	$repeat		Allows repetition of characters
	*
	*		@return string
	*/
	public static function string( $length = 8, $chars = self::ALPHANUMERIC_ALL, $repeat = true )
	{
		$collect = '';

		for( $i = 0; $i < $length; $i++ )
		{
			$strlen		= strlen( $chars ) - 1;
			$addendum	= substr( $chars, rand( 0, $strlen ), 1 );
			$collect	.= $addendum;

			if( !$repeat )
			{
				$chars	= str_replace( $addendum, '', $chars);
			}
		}

		return $collect;
	}

	/**
	*		integer()
	*
	*		@purpose
	*			To return a random number
	*
	*		@param
	*			int		$max	Highest possible value
	*			int		$min	Lowest possible value
	*
	*		@return int
	*/
	public static function integer( $max = 100, $min = 0 )
	{
		return rand( $min, $max );
	}
}
?>