<?php

namespace HTML;

class Frame
{
	/** @var array CSS Files to upload */
	public $css = array(
		"bootstrap.min.css",
		"font-awesome.min.css",
		"core.css"
	);

	/** @var array Javascript Files to upload */
	public $js = array(
		"jquery-3.3.1.min.js",
		"core.js"
	);

	/** @var string Raw Javascript to add to heading */
	protected $raw_js = '';

	/** @var string Raw CSS to add to heading */
	protected $raw_css = '';

	/** @var string Raw HTML to add to body */
	protected $content = '';

	/** @var string Title of Page */
	public $title = "Skeleton";

	/** @var string Hexidecimal theme color of page */
	public $theme = '#FFF';

	/** @var string HTML template to use */
	public $template = false;



	/**
	 * @var bool Output a blank page
	 * 
	 * NOTE : This will prevent the loading of modules
	 */
	public $blank = false;


	// ----------------------
	// 		Methods
	// ----------------------

	/**
	 * title()
	 *
	 * @purpose
	 * 	Get the title of the page, and add local and dev designations
	 *
	 * @return string HTML title of the page
	 */
	public function title()
	{
		// Page Title
		$env		= IS_DEV ? "DEV" : "LIVE";
		$prefix		= IS_LOCAL ? "(Localhost|$env) " : '';
		$prepend	= IS_DEV ? $prefix : '';

		return $prepend . $this->title ;
	}

	/**
	 * js()
	 *
	 * @purpose
	 *	Write out HTML to load CSS stylesheets
	 *
	 * @return void
	 */
	protected function js()
	{
		$load = '';

		foreach( $this->js as $js )
		{
			$file = $GLOBALS['conf']->url['js'] . $js;

			$load .= "\t<script type='text/javascript' src='$file'></script>\r\n";
		}

		return $load;
	}

	/**
	 * css()
	 *
	 * @purpose
	 *	Write out HTML to load CSS stylesheets
	 *
	 * @return string HTML output for linking css files
	 */
	protected function css()
	{
		$load = '';

		foreach( $this->css as $css )
		{
			$file = $GLOBALS['conf']->url['css'] . $css;

			$load .= "\t<link rel='stylesheet' type='text/css' href='$file'>\r\n";
		}

		return $load;
	}

	/**
	 * addJs()
	 *
	 * @purpose
	 * 	Add raw javascript to a page
	 *
	 * @var string $js javascript code to add
	 *
	 * @return void
	 */
	public function addJs( $js )
	{
		$this->raw_js .= $js;
	}

	/**
	 * addCss()
	 *
	 * @purpose
	 * 	Add raw styles to a page
	 *
	 * @var string $css stylesheet info to add to page
	 *
	 * @return void
	 */
	public function addCss( $css )
	{
		$this->raw_css .= $css;
	}

	/**
	 * addContent()
	 *
	 * @purpose
	 * 	Add raw HTML to a page
	 *
	 * @var string $html HTML to add to page
	 *
	 * @return void
	 */
	public function addContent( $html )
	{
		$this->content .= $html;
	}

	/**
	 * output()
	 * 
	 * @purpose
	 * 	Create and print the HTML page
	 */
	public function output()
	{
		$vars = array(
			'theme_color'	=> $this->theme,
			'title'			=> '',
			'modules_css'	=> '',
			'modules_js'	=> '',
			'raw_js'		=> '',
			'raw_css'		=> '',
			'header'		=> '',
			'content'		=> '',
			'footer'		=> '',
			'post_js'		=> ''
		);

		// Get title of the page
		$vars['title'] = $this->title();

		// Get js modules
		$vars['modules_js'] = $this->js();

		// Get css modules
		$vars['modules_css'] = $this->css();

		// Get raw js
		$vars['raw_js'] = strlen( $this->raw_js ) > 0 ? "\r\n<script type='text/javascript' language='javascript'>{$this->raw_js}</script>\r\n" : '';

		// Get raw css
		$vars['raw_css'] = strlen( $this->raw_css ) > 0 ? "\r\n<style rel='stylesheet'>{$this->raw_css}</style>\r\n" : '';

		// Get Page Content
		$vars['content'] = "\r\n{$this->content}\r\n";

		// Determine what template to use
		$template = $this->template ? $this->template : $GLOBALS['conf']->dir['html'] . DIRECTORY_SEPARATOR . 'default.tpl';

		$HTML = file_get_contents( $template );

		foreach( $vars as $key => $value )
		{
			$HTML = preg_replace( "/\{$key\}/", $value, $HTML );
		}

		print $HTML;
		
		// Determine if a blank page
		// if( true ){}
	}
}
?>