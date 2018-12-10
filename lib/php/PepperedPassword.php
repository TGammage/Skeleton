<?php

namespace SystemCore;

class PepperedPassword
{

	/** The algorithm to use for calculating the HMac of the password */
	const HMAC_ALGORITHM = 'sha256';
	
	/** @var string The pepper value */
	private $pepper;
	
	/**
	*
	*		Constructor
	*
	*/
	public function __construct()
	{
		$this->pepper = $this->pepper_to_bin( $GLOBALS['conf']->password['pepper'] );
	}
	
	/**
	*		hash()
	*
	*		@purpose
	*			Calculate the peppered hash of a password
	*
	*		@param string $password The password to calculate the hash for
	*
	*		@return string The peppered hash of the password supplied
	*/
	public function hash( $password )
	{
		return password_hash(
			$this->hmac( $password ),
			PASSWORD_DEFAULT,
			array(
				'cost'	=> $GLOBALS['conf']->password['cost']
			)
		);
	}
	
	/**
	*		verify()
	*
	*		@purpose
	* 			Verify a password against its peppered hash
	*
	*		@param string $password			The password to verify
	*		@param string $passwordHash		The password hash to verify the password against
	*
	*		@return bool True if the password is correct, false otherwise
	*/
	public function verify( $password, $passwordHash )
	{
		return password_verify( $this->hmac( $password ), $passwordHash );
	}
	
	/**
	*		hmac()
	*
	*		@purpose
	*			Compute the HMac for the password
	*
	*		@param string $password		Raw password
	*
	*		@return string the HMac for the supplied password
	*/
	private function hmac( $password )
	{
		return hash_hmac( self::HMAC_ALGORITHM, $password, $this->pepper, true );
	}

	/**
	*		pepper_to_bin()
	*
	*		@purpose
	*			Convert pepper string to binary
	*
	*		@param string $pepper		Random characters
	*
	*		@return string Binary version of pepper
	*/
	private function pepper_to_bin( $pepper )
	{
		$hex = '';

		for ($i=0; $i < strlen( $pepper ); $i++ )
		{
			$hex .= dechex( ord( $pepper[$i] ) );
		}

		$bin = hex2bin( $hex );
		
		return $bin;
	}
}