<?php

class db
{
	/** @var mysql Connection to database */
	public	$connection;

	/** @var string Host */
	private $host;

	/** @var string User */
	private $user;

	/** @var string Password */
	private $access;

	/** @var string Database */
	private $database;

	/** @var PDOStatment Current query to execute  */
	public $query = null;

	/** @var string Last query executed */
	public	$last_query  = 'Connect';

	/** @var int Number of queries */
	public	$query_count = 0;

	/** @var int Total time waiting for database */
	public	$up_time  = 0;

	/** @var float Timestamp used to record time */
	private $timestamp;

	/** @var array Errors found  */
	private $error	= array();

	/**
	*		Construct
	*
	*		@purpose
	*			Establish connection to database server
	*
	*		@param
	*			string $db		Supplied configuration variable of specified database
	*			string $user	Supplied configuration variable of specified user
	*
	*		@return void
	*/
	public function __construct( $db = false, $user = 'system' )
	{
		$this->host		= $GLOBALS['conf']->db['host'][$user];
		$this->user		= $GLOBALS['conf']->db['user'][$user];
		$this->access	= $GLOBALS['conf']->db['access'][$user];
		$this->database	= $db ? $GLOBALS['conf']->db[$db] : $GLOBALS['conf']->db['main'];

		$dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

		$options = array(
			PDO::MYSQL_ATTR_FOUND_ROWS	=> true,
			PDO::ATTR_ERRMODE			=> PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES	=> false
		);

		self::start_timer();

		$this->connection = new PDO( $dsn, $this->user, $this->access, $options );

		self::stop_timer();
	}

	/**
	*		query()
	*
	*		@purpose
	*			Sending queries to the database and returning results.
	*			Uses PDO to escape query
	*
	*		@param
	*			string	$statement		Query statement
	*			array	$params			Query parameters
	*			int		$fetch			Return results method. On false, return row count. Send PDO::FETCH_ parameter
	*			int		$single_return	Return with fetch() instead of fetchAll()
	*
	*		@return void
	*/
	public function query( $statement, $params = array(), $fetch = false, $single_return = false )
	{
		$this->last_query = $statement;
		$this->query_count ++;

		self::start_timer();

		try
		{
			$this->query = $this->connection->prepare( $statement );

			$this->query->execute( $params );

			self::stop_timer();

			if( !$fetch )
			{
				return $this->query->rowCount();
			} else {
				if( $single_return )
				{
					return $this->query->fetch( $fetch );
				} else {
					return $this->query->fetchAll( $fetch );
				}
			}
		}
		catch( PDOException $e )
		{
			if( DEBUG )
			{
				print $e->getMessage() . "<br>";
				var_dump( $e->getTrace() );
			}

			exit();
		}

	}

	/**
	*		mysql_up_time()
	*
	*		@purpose
	*			Total time for queries
	*
	*		@return void
	*/
	private function mysql_up_time( $decimals = 5)
	{
		return round( $this->up_time, $decimals );
	}

	/**
	*		start_timer()
	*
	*		@purpose
	*			Grab a timestamp for calculating query time
	*
	*		@return void
	*/
	private function start_timer()
	{
		$this->timestamp = microtime( true );
	}

	/**
	*		start_timer()
	*
	*		@purpose
	*			Add to up_time to see how long mysql takes
	*
	*		@return void
	*/
	private function stop_timer()
	{
		$this->up_time += microtime( true ) - $this->timestamp;
	}
}

?>