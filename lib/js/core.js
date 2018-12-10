$(document).ready(function()
{
	console.log( "Initialized" );
});

var modules = [];

/**
*		addModule()
*
*		@purpose
*			Checks if module exists, loads if not
*			
*		@return void
*/
function addModule( title )
{
	if( !modules.includes( title ) )
	{
		var head = document.getElementsByTagName('head')[0],
			host = location.protocol + '//' + location.host + '/lib/js/';

		var script		= document.createElement('script');
			script.type = 'text/javascript';
			script.src	= host + title + ".js";

		head.appendChild( script );

		modules.push( title );
	} else {
		console.log( "Module Already Loaded : " + title + ".js" );
	}
}