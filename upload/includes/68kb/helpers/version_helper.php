<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/license.html
 *
 * @package		iClassEngine
 * @author		ICE Dev Team
 * @copyright	Copyright (c) 2010, 68 Designs, LLC
 * @license		http://iclassengine.com/user_guide/license.html
 * @link		http://iclassengine.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Version Helpers
 *
 * @subpackage	Helpers
 * @link		http://iclassengine.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
 * Checks for the latest release
 *
 * @return 	string
 */
function version_check()
{
	$fp = fsockopen("www.iclassengine.com", 80, $errno, $errstr, 30);
	
	$return = '';
	
	if ( ! $fp) 
	{
	    echo "$errstr ($errno)<br />\n";
	} 
	else 
	{
	    $out = "GET /downloads/latest/version.txt HTTP/1.1\r\n";
	    $out .= "Host: www.iclassengine.com\r\n";
	    $out .= "Connection: Close\r\n\r\n";

	    fwrite($fp, $out);
	
	    while ( ! feof($fp)) 
	    {
	        $return .= fgets($fp, 128);
	    }
	
		// Get rid of HTTP headers
		$content = explode("\r\n\r\n", $return);
		$content = explode($content[0], $return);

		// Assign version to var
		$version = trim($content[1]);
		
	    fclose($fp);
	
		return $version;
	}
}
/* End of file version_helper.php */
/* Location: ./upload/includes/iclassengine/helpers/version_helper.php */ 