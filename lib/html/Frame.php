<?php

namespace HTML;

class Frame
{
	/** @var array CSS Files to upload */
	public	$css	= array(
		"bootstrap.min",
		"font-awesome.min",
		"core"
	);

	/** @var array Javascript Files to upload */
	public	$js		= array(
		"jquery-3.3.1.min",
		"core"
	);

	/** @var string Title of Page */
	public	$title = "Skeleton";

	/** @var bool Output a blank page */
	public	$blank_page	= false;

	/**
	*		begin()
	*
	*		@purpose
	*			Commences self::head() and self::body_start()
	*
	*		@return void
	*/
	public function begin()
	{
		self::head();

		self::body_start();
	}

	/**
	*		end()
	*
	*		@purpose
	*			Alias of self::body_close()
	*
	*		@return void
	*/
	public function end()
	{
		self::body_close();
	}

	/**
	*		head()
	*
	*		@purpose
	*			Outputs the <head> portion of the html page
	*
	*		@param string $prepend_title	Page title override
	*
	*		@return void
	*/
	public function head( $prepend_title = true )
	{
		// Page Title
		$env		= IS_DEV ? "DEV" : "LIVE";
		$prefix		= IS_LOCAL ? "(Localhost|$env) " : '';
		$prepend	= $prepend_title ? $prefix : '';
		$title		= $prepend . $this->title ;

		echo "<!DOCTYPE html>
<html>
<head>

<meta charset=\"UTF-8\">
<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\r\n\r\n";

		self::css();

		self::js();

		echo "\r\n<title>$title</title>\r\n\r\n</head>\r\n\r\n";
	}

	/**
	*		body_start()
	*
	*		@purpose
	*			Outputs starting body tag and navbars by default
	*
	*		@return void
	*/
	public function body_start()
	{
		// Blank Page
		if( !$this->blank_page )
		{

			echo "<body>\r\n\r\n";
		
		} else {

			echo "<body>\r\n\r\n";

			//Navbar::heading();
		}
	}

	/**
	*		css()
	*
	*		@purpose
	*			Write out HTML to load CSS stylesheets
	*
	*		@return void
	*/
	protected function css()
	{
		foreach( $this->css as $css )
		{
			$file = $GLOBALS['conf']->url['css'] . "$css";

			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$file.css\">\r\n";
		}
	}

	/**
	*		js()
	*
	*		@purpose
	*			Write out HTML to load CSS stylesheets
	*
	*		@return void
	*/
	protected function js()
	{
		foreach( $this->js as $js )
		{
			$file = $GLOBALS['conf']->url['js'] . "$js";

			echo "<script type=\"text/javascript\" src=\"$file.js\"></script>\r\n";
		}
	}

	/**
	*		body_close()
	*
	*		@purpose
	*			Outputs closing body and html tags
	*
	*		@return void
	*/
	public function body_close()
	{
		echo "\r\n\r\n</body>\r\n</html>";
	}
}
?>