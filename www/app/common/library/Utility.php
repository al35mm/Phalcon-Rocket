<?php
/**
 * Created by PhpStorm.
 * User: Al
 * Date: 10/07/14
 * Time: 11:13
 */

namespace Baseapp\Library;

/**
 * Class Utility
 * This is a utility class with various useful functions
 * These functions are also available in views simply by
 * calling the function()
 *
 * @package Baseapp\Library
 */

class Utility
{

	/*
	 * make text (e.g. a title with disallowed characters in) a safe site url - for
	 * converting title's to URLs with characters converted to words
	 */

	public static function textToUrl($string = FALSE)
	{
		$clean = self::stripHtml($string);
		$clean = str_replace('&', 'and', $clean);
		$clean = str_replace('&amp;', 'and', $clean);
		$clean = str_replace('$', 'dollars', $clean);
		$clean = str_replace('£', 'pounds', $clean);
		$clean = str_replace('&pound;', 'pounds', $clean);
		$clean = str_replace('€', 'euros', $clean);
		$clean = str_replace('&euro;', 'euros', $clean);
		$clean = str_replace('-', '', $clean);
		$clean = preg_replace('!\s+!', ' ', $clean); // replace multiple white space with just one space - may also remove multople linebreaks
		$clean = str_replace(' ', '-', $clean);
		$clean = strtolower($clean);
		$clean = preg_replace('/[^a-z0-9-_]/', '', $clean);
		$clean = trim($clean, '-');

		return $clean;
	}


    /**
     * Convert a URL to a presentable title (unfiltered!)
     * @param $string
     * @return mixed|string
     */
    public static function urlToTitle($string){
        $clean = str_replace('-', ' ', $string);
        $clean = ucwords($clean);

        return $clean;
    }



	/*
	 * SAFE TITLE
	 * Make safe title - for forum threads etc
	 */
	public static function safeTitle($string)
	{
		$clean = self::stripHtml($string);
		$clean = str_replace('@', ' at ', $clean);
		$clean = str_replace('&', ' and ', $clean);
		$clean = str_replace('&amp;', ' and ', $clean);
		$clean = str_replace('$', ' dollars ', $clean);
		$clean = str_replace('£', ' pounds ', $clean);
		$clean = str_replace('&pound;', ' pounds ', $clean);
		$clean = str_replace('€', ' euros ', $clean);
		$clean = str_replace('&euro;', ' euros ', $clean);
		$clean = preg_replace('!\s+!', ' ', $clean); // replace multiple white space with just one space - may also remove multople linebreaks
		$clean = preg_replace('/[^A-Za-z0-9 -_]/', '', $clean);
		$clean = trim($clean);

		return $clean;
	}


	/*
	 * SAFE MESSAGE IN
	 * message submitted to database
	 * $string = message string
	 * $options = array()
	 */
	public static function safeMessageIn($string, $options = array('url_block' => FALSE, 'email_block' => FALSE, 'html_block' => FALSE))
	{
		// convert html
		$clean = html_entity_decode($string); // convert entities to html

		if ($options['url_block'] == TRUE) {
			// filter out URLs
			$pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
			$replacement = '<span class="muted">**</span>';
			$clean = preg_replace($pattern, $replacement, $clean);
		}

		if ($options['email_block'] == TRUE) {
			// fiter out email addresses
			$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
			$replacement = '<span class="muted">*</span>';
			$clean = preg_replace($pattern, $replacement, $clean);
		}

		if ($options['html_block'] == TRUE) {
			// Strip HTML
			$clean = self::stripHtml($clean);
		}

		/*  if($options['embed_block'] == FALSE){
			  // string filter
			 // $clean = $this->stringFilter($clean);
		  }*/

		// html purify
		/*include_once('HTMLPurifier.standalone.php');
		$confi = \HTMLPurifier_Config::createDefault();
		$purifier = new \HTMLPurifier($confi);
		$clean = $purifier->purify($clean, $confi);*/

		return $clean;
	}



	/*
	 * SAFE MESSAGE OUT
	 * message retrieved from database
	 */
	public static function safeMessageOut($string, $options = array())
	{
		// convert html
		$clean = html_entity_decode($string); // convert entities to html
        $clean = nl2br($clean);

		// decode string filter
		// $clean = $this->urlPasser($clean);

        // html purify
        include_once('HTMLPurifier.standalone.php');
        $confi = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($confi);
        $clean = $purifier->purify($clean, $confi);

		return $clean;
	}

	/*
	 * remove html
	 */

	public static function stripHtml($string = FALSE)
	{
		// XSS CLEAN
		//$clear = $this->security->xss_clean($string);
		// Strip HTML Tags
		$clear = strip_tags($string);
		// Clean up things like &amp;
		$clear = html_entity_decode($clear);
		// Strip out any url-encoded stuff
		$clear = urldecode($clear);
		// Replace non-AlNum characters with space
		$clear = preg_replace('/[^A-Za-z0-9 -_\$\,\.\?\!\&\%\x{00A3}\x{20AC}\s\n\r]/u', '', $clear); // \x{00A3} = unicode of £  \x{20AC} = unicode of € /u at end tells it that it's utf8
		// Replace Multiple spaces with single space
		$clear = preg_replace('/ +/', ' ', $clear);
		// Trim the string of leading/trailing space
		$clear = trim($clear);

		return $clear;
	}



	/*
	 * OUTPUT FILTER
	 * Filter user submitted content as it is output
	 */

	public static function filter_output($string = FALSE, $options = FALSE)
	{
		// email address removal
		if ($options['email'] == FALSE) {
			$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
			$replacement = '<span class="muted">*</span>';
			$string = preg_replace($pattern, $replacement, $string);
		}
		// url - link removal
		if ($options['url'] == FALSE) {
			$pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
			$replacement = '<span class="muted">**</span>';
			$string = preg_replace($pattern, $replacement, $string);
		}
		// html purify
		include_once('HTMLPurifier.standalone.php');
		$confi = \HTMLPurifier_Config::createDefault();
		$purifier = new \HTMLPurifier($confi);
		$clean = $purifier->purify($string, $confi);

		return $string;
	}



	/**
	 * Character limiter
	 */
	public static function character_limiter($string, $limit = 20, $options = array())
	{
		$out = html_entity_decode($string);
		if (ISSET($options['allowed_tags'])) {
			$out = strip_tags($out, $options['allowed_tags']);
		} else {
			$out = strip_tags($out);
		}
		$out = trim($out);
		if (strlen($out) > $limit) {
			$outn = FALSE;
			$outl = substr($out, 0, $limit);
			// whole words option will not break words
			if(ISSET($options['words'])) {
				$outar = explode(' ', $outl);
				$num = 0;
				foreach ($outar as $ou) {
					if ($num <= count($outar) - 3) {
						$outn .= $ou . ' ';
					}else if($num == count($outar) - 2){
						$outn .= $ou . '';
						break;
					}
					$num++;
				}
			}
			if($outn) {
				$out = $outn . '...';
			}else{
				$out = $outl . '....';
			}
		}
		return $out;
	}

} 