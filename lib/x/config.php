<?php


/**
*
*		Core Configuration
*
*/	

namespace SystemCore;


class Config
{


	// Version
	public	$version		= '0.0.1';

	// Timezone
	public	$timezone		= 'EST';

	// Follow Daylight Saving Patterns
	public	$daylightsaving	= false;

	// Collective Setup Data
	private	$data			= array();


	/**
	*
	*		Construct
	*
	*/
	public function __construct()
	{
		/**
		*		Time Zone
		*/
		ini_set( 'date.timezone', $this->timezone );

		date_default_timezone_set( $this->timezone );

		$this->Setup( IS_DEV );
	}


	/**
	*
	*		Set Up Server
	*
	*/
	private function Setup( $dev = false )
	{
		if( $dev )
		{
			/**
			*
			*		Dev Version
			*
			*/

			/**
			*		Site Title
			*/
			$this->data['site_title']	= 'Skeleton';

			/**
			*		Site Address
			*/
			$this->data['subdomain']	= 'dev';
			$this->data['base_domain']	= 'skeleton.localdev';
			$this->data['domain']		= 'dev.skeleton.localdev';
			$this->data['host_nice']	= $this->data['domain'];
			$this->data['host']			= 'http://' . $this->data['host_nice'] . '/';


			/**
			*		Site Directories
			*/
			$this->data['dir']['lib']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'lib';
			$this->data['dir']['dev']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'dev';
			$this->data['dir']['bin']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'bin';
			$this->data['dir']['log']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'log';
			$this->data['dir']['css']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'css';
			$this->data['dir']['js']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'js';
			$this->data['dir']['less']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'less';
			$this->data['dir']['php']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'php';
			$this->data['dir']['html']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'html';
			$this->data['dir']['scss']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'scss';
			$this->data['dir']['fonts']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'fonts';
			$this->data['dir']['img']		= $this->data['dir']['bin'] . DIRECTORY_SEPARATOR . 'img';
			$this->data['dir']['vid']		= $this->data['dir']['bin'] . DIRECTORY_SEPARATOR . 'vid';
			$this->data['dir']['session']	= $this->data['dir']['log'] . DIRECTORY_SEPARATOR . 'session';
			$this->data['dir']['sys']		= $this->data['dir']['log'] . DIRECTORY_SEPARATOR . 'sys';
			$this->data['dir']['tick']		= $this->data['dir']['php'] . DIRECTORY_SEPARATOR . 'tick';
			$this->data['dir']['access']	= $this->data['dir']['sys'] . DIRECTORY_SEPARATOR . 'access';
			$this->data['dir']['error']		= $this->data['dir']['sys'] . DIRECTORY_SEPARATOR . 'error';


			/**
			*		Site URLs
			*/
			$this->data['url']['lib']		= $this->data['host'] . 'lib/';
			$this->data['url']['dev']		= $this->data['host'] . 'dev/';
			$this->data['url']['bin']		= $this->data['host'] . 'bin/';
			$this->data['url']['css']		= $this->data['url']['lib'] . 'css/';
			$this->data['url']['js']		= $this->data['url']['lib'] . 'js/';
			$this->data['url']['less']		= $this->data['url']['lib'] . 'less/';
			$this->data['url']['scss']		= $this->data['url']['lib'] . 'scss/';
			$this->data['url']['fonts']		= $this->data['url']['lib'] . 'fonts/';
			$this->data['url']['img']		= $this->data['url']['bin'] . 'img/';
			$this->data['url']['vid']		= $this->data['url']['bin'] . 'vid/';


			/**
			*		Cookie Data
			*/
			$this->data['cookie']['session_name']	= 'Skeleton';
			$this->data['cookie']['life']			= 60 * 60 * 24 * 7;

			// Extend the cookie life after each request
			$this->data['cookie']['extend']	= true;


			/**
			*		Multiple Logged in Sessions
			*
			*		@purpose
			*			Max number of simultaneous live sessions
			*
			*		@options
			*			0 = Infinite
			*			1 = Solo Session
			*			2 = 2 Sessions
			*			etc...
			*/
			$this->data['session']['simultaneous_count'] = 2;


			/**
			*		Maximum Sessions Handler
			*
			*		@purpose
			*			When max number of sessions is reached and a new login occurs,
			*			this is how we choose to dispose of a prior session.
			*
			*		@options
			*			'session_created'	By oldest live session
			*			'last_active'		By longest dormant session
			*/
			$this->data['session']['max_bump_technique'] = 'last_active';

			/**
			*		Session Regeneration Timer
			*/
			$this->data['session']['regenerate_after'] = 60 * 60 * 3;


			/**
			*		Login Identify By
			*
			*		@purpose
			*			Method of checking credentials for a login
			*
			*		@options
			*			'email'		email supplied
			*			'username'	current alias
			*/
			$this->data['login']['identify_by']	= 'username';

			/**
			* 		Login On Signup
			*/
			$this->data['login']['signup'] = true;

			/**
			*		Password Pepper
			*/
			$this->data['password']['pepper']	= 'nm7OJe6TYEoXsxLn8nIyfFjHJFHSPG1i';
			$this->data['password']['cost']		= 10;


			/**
			*		Database User Prefix
			*/
			$this->data['db']['prefix']		= 'skeleton_dev_';

			/**
			*		Site Databases
			*/
			$this->data['db']['main']	= $this->data['db']['prefix'] . 'main';
			$this->data['db']['member']	= $this->data['db']['prefix'] . 'member';
			$this->data['db']['backup']	= $this->data['db']['prefix'] . 'backup';
			$this->data['db']['tmp']	= $this->data['db']['prefix'] . 'tmp';


				/**
				*		Portal Credentials
				*/
				$this->data['db']['host']['portal']		= 'localhost';
				$this->data['db']['user']['portal']		= $this->data['db']['prefix'] . 'portal';
				$this->data['db']['access']['portal']	= 'EJVFxvzYuVVyrSbm';

				/**
				*		System Credentials
				*/
				$this->data['db']['host']['system']		= 'localhost';
				$this->data['db']['user']['system']		= $this->data['db']['prefix'] . 'system';
				$this->data['db']['access']['system']	= 'NojeGpLokdSiWnQLI';

				/**
				*		Staff Credentials
				*/
				$this->data['db']['host']['staff']		= 'localhost';
				$this->data['db']['user']['staff']		= $this->data['db']['prefix'] . 'staff';
				$this->data['db']['access']['staff']	= 'iWZkMZZS8XiOKuua';

				/**
				*		Tick Credentials
				*/
				$this->data['db']['host']['tick']		= 'localhost';
				$this->data['db']['user']['tick']		= $this->data['db']['prefix'] . 'tick';
				$this->data['db']['access']['tick']		= 'QmjeBfLTJiGfEaLD';

		} else {
			/**
			*
			*		Live Version
			*
			*/

			/**
			*		Site Title
			*/
			$this->data['site_title']	= 'Skeleton';

			/**
			*		Site Address
			*/
			$this->data['subdomain']	= '';
			$this->data['base_domain']	= 'skeleton.localdev';
			$this->data['domain']		= 'skeleton.localdev';
			$this->data['host_nice']	= $this->data['domain'];
			$this->data['host']			= 'http://' . $this->data['host_nice'] . '/';


			/**
			*		Site Directories
			*/
			$this->data['dir']['lib']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'lib';
			$this->data['dir']['dev']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'dev';
			$this->data['dir']['bin']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'bin';
			$this->data['dir']['log']		= ROOT_DIR . DIRECTORY_SEPARATOR . 'log';
			$this->data['dir']['css']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'css';
			$this->data['dir']['js']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'js';
			$this->data['dir']['less']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'less';
			$this->data['dir']['php']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'php';
			$this->data['dir']['html']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'html';
			$this->data['dir']['scss']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'scss';
			$this->data['dir']['fonts']		= $this->data['dir']['lib'] . DIRECTORY_SEPARATOR . 'fonts';
			$this->data['dir']['img']		= $this->data['dir']['bin'] . DIRECTORY_SEPARATOR . 'img';
			$this->data['dir']['vid']		= $this->data['dir']['bin'] . DIRECTORY_SEPARATOR . 'vid';
			$this->data['dir']['session']	= $this->data['dir']['log'] . DIRECTORY_SEPARATOR . 'session';
			$this->data['dir']['sys']		= $this->data['dir']['log'] . DIRECTORY_SEPARATOR . 'sys';
			$this->data['dir']['tick']		= $this->data['dir']['php'] . DIRECTORY_SEPARATOR . 'tick';
			$this->data['dir']['access']	= $this->data['dir']['sys'] . DIRECTORY_SEPARATOR . 'access';
			$this->data['dir']['error']		= $this->data['dir']['sys'] . DIRECTORY_SEPARATOR . 'error';


			/**
			*		Site URLs
			*/
			$this->data['url']['lib']		= $this->data['host'] . 'lib/';
			$this->data['url']['dev']		= $this->data['host'] . 'dev/';
			$this->data['url']['bin']		= $this->data['host'] . 'bin/';
			$this->data['url']['css']		= $this->data['url']['lib'] . 'css/';
			$this->data['url']['js']		= $this->data['url']['lib'] . 'js/';
			$this->data['url']['less']		= $this->data['url']['lib'] . 'less/';
			$this->data['url']['scss']		= $this->data['url']['lib'] . 'scss/';
			$this->data['url']['fonts']		= $this->data['url']['lib'] . 'fonts/';
			$this->data['url']['img']		= $this->data['url']['bin'] . 'img/';
			$this->data['url']['vid']		= $this->data['url']['bin'] . 'vid/';


			/**
			*		Cookie Data
			*/
			$this->data['cookie']['session_name']	= 'Skeleton';
			$this->data['cookie']['life']			= 60 * 60 * 24 * 7;

			// Extend the cookie life after each request
			$this->data['cookie']['extend']	= true;


			/**
			*		Multiple Logged in Sessions
			*
			*		@purpose
			*			Max number of simultaneous live sessions
			*
			*		@options
			*			0 = Infinite
			*			1 = Solo Session
			*			2 = 2 Sessions
			*			etc...
			*/
			$this->data['session']['simultaneous_count'] = 2;


			/**
			*		Maximum Sessions Handler
			*
			*		@purpose
			*			When max number of sessions is reached and a new login occurs,
			*			this is how we choose to dispose of a prior session.
			*
			*		@options
			*			'session_created'	By oldest live session
			*			'last_active'		By longest dormant session
			*/
			$this->data['session']['max_bump_technique'] = 'last_active';

			/**
			*		Session Regeneration Timer
			*/
			$this->data['session']['regenerate_after'] = 60 * 60 * 3;


			/**
			*		Login Identify By
			*
			*		@purpose
			*			Method of checking credentials for a login
			*
			*		@options
			*			'email'		email supplied
			*			'username'	current alias
			*/
			$this->data['login']['identify_by']	= 'username';


			/**
			*		Password Pepper
			*/
			$this->data['password']['pepper']	= 'nm7OJe6TYEoXsxLn8nIyfFjHJFHSPG1i';
			$this->data['password']['cost']		= 10;

			
			/**
			*		Database User Prefix
			*/
			$this->data['db']['prefix']		= 'skeleton_';

			/**
			*		Site Databases
			*/
			$this->data['db']['main']	= $this->data['db']['prefix'] . 'main';
			$this->data['db']['member']	= $this->data['db']['prefix'] . 'member';
			$this->data['db']['backup']	= $this->data['db']['prefix'] . 'backup';
			$this->data['db']['tmp']	= $this->data['db']['prefix'] . 'tmp';


				/**
				*		Portal Credentials
				*/
				$this->data['db']['host']['portal']		= '';
				$this->data['db']['user']['portal']		= $this->data['db']['prefix'] . 'portal';
				$this->data['db']['access']['portal']	= '';

				/**
				*		System Credentials
				*/
				$this->data['db']['host']['system']		= '';
				$this->data['db']['user']['system']		= $this->data['db']['prefix'] . 'system';
				$this->data['db']['access']['system']	= '';

				/**
				*		Staff Credentials
				*/
				$this->data['db']['host']['staff']		= '';
				$this->data['db']['user']['staff']		= $this->data['db']['prefix'] . 'staff';
				$this->data['db']['access']['staff']	= '';

				/**
				*		Tick Credentials
				*/
				$this->data['db']['host']['tick']		= '';
				$this->data['db']['user']['tick']		= $this->data['db']['prefix'] . 'tick';
				$this->data['db']['access']['tick']		= '';

		}
	}


	public function __get( $key = false )
	{
		if( array_key_exists( $key, $this->data ) )
		{
			return $this->data[$key];

		} else {

			return null;
		}
	}

	public function __set( $k, $v )
	{
		return;
	}
}
?>