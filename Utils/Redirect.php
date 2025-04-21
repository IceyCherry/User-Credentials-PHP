<?php

/**
 * Assignment 2 Instructions:
 * 
 * - No changes are needed.
 */

namespace Utils;
/**
 * Utility class for handling HTTP redirects.
 */
class Redirect {
	/**
	 * Redirects to a specified URL with optional query parameters.
	 *
	 * @param string $fileName The target file or URL to redirect to.
	 * @param array $getAttributes Optional associative array of query parameters to append to the URL.
	 * 
	 * @return void
	 */
	public static function to( $fileName, $getAttributes = [] ) {
		$queryString = http_build_query( $getAttributes );
		$url         = $fileName . ( $queryString ? '?' . $queryString : '' );
		header( "Location: $url" );
		exit();
	}
}