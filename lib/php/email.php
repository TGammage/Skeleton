<?php

namespace SystemCore;

class email
{
	/** @const string Mime Version */
	const MIME_VERSION	= "MIME-Version: 1.0 \r\n";

	/** @const string Mime Codes */
	const MIME_TEXT		= 1;
	const MIME_HTML		= 2;
	const MIME_MULTI	= 3;
	const MIME_FILE		= 4;

	/** @const string Mime Types */
	const MIME_STRING_TEXT	= "Content-Type: text/plain; charset=UTF-8\r\n";
	const MIME_STRING_HTML	= "Content-Type: text/html; charset=UTF-8\r\n";
	const MIME_STRING_MULTI	= "Content-Type: multipart/mixed; boundary=";
	const MIME_STRING_FILE	= "Content-Type: application/octet-stream; name=";

	/** @const string Transfer Encodings */
	const ENCODE_7BIT	= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	const ENCODE_BASE64	= "Content-Transfer-Encoding: base64\r\n\r\n";

	/** @const string Description */
	const DESCRIPTION_ATTACHMENT = "Content-Description: ";

	/** @const string Disposition */
	const DISPOSITION_ATTACHMENT = "Content-Disposition: attachment; filename=";

	/** @const string Boundary divider */
	const BOUNDARY_DIVIDER = "--";

	/** @const int Priority information for header */
	const PRIORITY_HIGHEST	= 1;
	const PRIORITY_HIGH		= 2;
	const PRIORITY_NORMAL	= 3;
	const PRIORITY_LOW		= 4;
	const PRIORITY_LOWEST	= 5;


	/** @var array Recipient(s) */
	protected $to = array();

	/** @var mixed Sender, defaults to "donotreply@<domain>" */
	protected $from = false;

	/** @var array Carbon copy recipient(s) */
	protected $cc = array();

	/** @var array Blind carbon copy recipient(s) */
	protected $bcc = array();

	/** @var string Subject of email */
	protected $subject = '';

	/** @var string Email message */
	protected $message = '';

	/** @var string Headers */
	protected $headers = '';

	/** @var string Reply to */
	protected $reply_to = '';

	/** @var string Priority Level, defaults to normal */
	protected $priority = "X_Priority: 3\r\n";

	/** @var array Content blocks for message */
	protected $content_block = array();

	/** @var array File paths and names for attachments */
	protected $files = array();

	/** @var string boundary */
	protected $boundary = '';

	/** @var bool Error trigger that prevents mail()  */
	protected $error = false;

	/** @var bool Email was sending successful? */
	public $sent = false;
	

	/**
	 * send()
	 *
	 * @purpose
	 *  Sends off email with given parameters.
	 *	Will check for attachments and adjust headers
	 *	and message for proper mail() format.
	 *
	 * @param string	$email	Email address of recipient
	 * @param string	$name	Optional. Name of recipient
	 *
	 * @return void
	 */
	public function send()
	{
		self::set_headers();
		self::set_content();

		if( !$this->error )
		{
			$this->sent = mail(
				null,
				$this->subject,
				$this->message,
				$this->headers
			);
		}
	}

	/**
	 * set_headers()
	 *
	 * @purpose
	 * Sets up headers for sending email.
	 *
	 * @return void
	 */
	private function set_headers()
	{
		/** To : */
		$recipients = "To: " . implode( ',', $this->to ) . "\r\n";

		/** From : */
		$from = !$this->from ? "donotreply@" . $GLOBALS['conf']->base_domain : $this->from;
		$sender = "From: $from\r\n";

		/** Cc : */
		$cc = "Cc: " . implode( ',', $this->cc ) . "\r\n";

		/** Bcc : */
		$bcc = "Bcc: " . implode( ',', $this->cc ) . "\r\n";

		$this->headers = $recipients . $sender . $cc . $bcc . $this->reply_to . $this->priority . self::MIME_VERSION;
		
	}

	/**
	 * set_content()
	 *
	 * @purpose
	 *  Sets up content for sending email.
	 *
	 * @return void
	 */
	private function set_content()
	{
		// Multipart/mixed
		if( count( $this->content_block ) > 1 )
		{
			$this->headers .= self::MIME_STRING( self::MIME_MULTI );

			foreach( $this->content_block as $array )
			{
				// Boundary Division
				$this->message .= self::BOUNDARY_DIVIDER . $this->boundary . "\r\n";

				// Content-Type (text or html)
				if( $array['code'] < 3 )
				{
					$this->message .= self::MIME_STRING( $array['code'] );
					$this->message .= self::ENCODE_7BIT;
				}

				// Content Block (Message or File)
				$this->message .= $array['content'] . "\r\n\r\n";
			}

			// End Boundary
			$this->message .= self::BOUNDARY_DIVIDER . $this->boundary . self::BOUNDARY_DIVIDER;
		} else {
			// Single Message Only
			$this->headers .= self::MIME_STRING( $this->content_block[0]['code'] );

			$this->message = $this->content_block[0]['content'];
		}
		
	}

	/**
	 * to()
	 *
	 * @purpose
	 *	Adds to collection of recipients to send to
	 *
	 * @param string	$email	Email address of recipient
	 * @param string	$name	Optional. Name of recipient
	 *
	 * @return void
	 */
	public function to( $email, $name = false )
	{
		$prepend = $name ? "$name " : '';

		$this->to[] = "$prepend<$email>";
	}

	/**
	 * from()
	 *
	 * @purpose
	 *	Adds to collection of recipients to send to
	 *
	 * @param string	$email	Email address of recipient
	 * @param string	$name	Optional. Name of recipient
	 *
	 * @return void
	 */
	public function from( $email, $name = false )
	{
		$prepend = $name ? "$name " : '';

		$this->from = "$prepend<$email>";
	}

	/**
	 * cc()
	 *
	 * @purpose
	 *	Adds to collection of copied recipients to add to headers when sending
	 *
	 * @param string	$email	Email address of recipient
	 * @param string	$name	Optional. Name of recipient
	 *
	 * @return void
	 */
	public function cc( $email, $name = false )
	{
		$prepend = $name ? "$name " : '';

		$this->cc[] = "$prepend<$email>";
	}

	/**
	 * bcc()
	 *
	 * @purpose
	 *	Adds to collection of blind copied recipients to add to headers when sending
	 *
	 * @param string	$email	Email address of recipient
	 * @param string	$name	Optional. Name of recipient
	 *
	 * @return void
	 */
	public function bcc( $email, $name = false )
	{
		$prepend = $name ? "$name " : '';

		$this->bcc[] = "$prepend<$email>";
	}

	/**
	 * reply_to()
	 *
	 * @purpose
	 *  Designates to whom the recipient should reply
	 *
	 * @param string	$email	Email address of reply-to recipient
	 * @param string	$name	Optional. Name of reply-to recipient
	 *
	 * @return void
	 */
	public function reply_to( $email, $name = false )
	{
		$prepend = $name ? "$name " : '';

		$this->reply_to = "Reply-To: $prepend<$email>\r\n";
	}

	/**
	 * priority()
	 *
	 * @purpose
	 *	Adds to collection of blind copied recipients to add to headers when sending
	 *
	 * @param string	$level	Number corresponding to importance level
	 *
	 * @return void
	 */
	public function priority( $level )
	{
		$this->priority = "X-Priority: " . $level . "\r\n";
	}

	/**
	 * subject()
	 *
	 * @purpose
	 *	Adds to collection of recipients to send to
	 *
	 * @param string	$_	Subject line of email
	 *
	 * @return void
	 */
	public function subject( $_ )
	{
		$this->subject = $_;
	}

	/**
	 * message()
	 *
	 * @purpose
	 *	Adds to collection of recipients to send to
	 *
	 * @param string	$message	Subject line of email
	 * @param int		$mime_type	MIME code
	 *
	 * @return void
	 */
	public function message( $message, $mime_type = 1 )
	{
		$formatted_message = wordwrap( $message, 70, "\r\n" );

		$this->content_block[] = array( 
			'content'	=> $formatted_message,
			'code'		=> $mime_type
		);
	}

	/**
	 * MIME()
	 *
	 * @purpose
	 * 	Changes MIME type of last content_block
	 *
	 * @param string	$code	MIME code constant
	 *
	 * @return void
	 */
	public function MIME( $code )
	{
		$pop = array_pop( $this->content_block );

		$pop['code'] = $code;

		$this->content_block[] = $pop;
	}

	/**
	 * MIME_STRING()
	 *
	 * @purpose
	 *	Adds to collection of recipients to send to
	 *
	 * @param string	$code	MIME code constant
	 *
	 * @return string MIME content for message
	 */
	public function MIME_STRING( $code )
	{
		switch( $code )
		{
			default:
				return self::MIME_STRING_TEXT;
			break;

			case self::MIME_HTML:
				return self::MIME_STRING_HTML;
			break;

			case self::MIME_FILE:
				return self::MIME_STRING_FILE;
			break;

			case self::MIME_MULTI:

				// Create boundary
				if( strlen( $this->boundary ) == 0 )
				{
					$this->boundary = md5( uniqid( time() ) );
				}

				// Set MIME type
				return self::MIME_STRING_MULTI . "\"" . $this->boundary . "\"\r\n";
			break;
		}
	}

	/**
	 * attach()
	 *
	 * @purpose
	 *	Checks if file exists and adds to atatchments array for sending
	 *
	 * @param string	$file	File path and name of attachment
	 *
	 * @return void
	 */
	public function attach( $file )
	{
		if( !is_file( $file ) )
		{
			$this->error = true;

			throw new ErrorHandler( 2, 'Could Not attach file : ' . $file, __file__, __line__  );

			return;
		}

		if( !in_array( $file, $this->files ) )
		{
			$this->files[] = $file;

			$base_name = basename( $file );
			$file_size = filesize( $file );

			// Read File Contents
			$file_handle	= fopen( $file, "rb" );
			$file_data		= fread( $file_handle, $file_size );

			fclose( $file_handle );

			$chunk = chunk_split( base64_encode( $file_data ) );

			// Attachment Message Content
			$content = self::MIME_STRING_FILE . "\"$base_name\"\r\n";
			$content .= self::DESCRIPTION_ATTACHMENT . "$base_name\r\n";
			$content .= self::DISPOSITION_ATTACHMENT . "\"$base_name\"; size=$file_size\r\n";
			$content .= self::ENCODE_BASE64 . "$chunk\r\n\r\n";

			// Add Contents to collection
			$this->content_block[] = array(
				'content'	=> $content,
				'code'		=> self::MIME_FILE
			);
		}
	}
}
?>