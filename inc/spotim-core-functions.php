<?php

/**
 * Get the URL to the SpotIM REST API
 *
 * @since 2.1
 * @param string $path an endpoint to include in the URL
 * @return string the URL
 */
function get_spotim_api_url( $path ) {

	$version = defined( 'SpotIM_API_REQUEST_VERSION' ) ? SpotIM_API_REQUEST_VERSION : SpotIM_API::VERSION;

	$url = get_home_url( null, "spotim-api/v{$version}/", is_ssl() ? 'https' : 'http' );

	if ( ! empty( $path ) && is_string( $path ) ) {
		$url .= ltrim( $path, '/' );
	}

	return $url;
}

/**
 * WooCommerce Timezone - helper to retrieve the timezone string for a site until
 * a WP core method exists (see http://core.trac.wordpress.org/ticket/24730)
 *
 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
 *
 * @since 2.1
 * @return string a valid PHP timezone string for the site
 */
function spotim_timezone_string() {

	// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set then return UTC
	if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
		return 'UTC';
	}

	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr( '', $utc_offset, 0 );

	// last try, guess timezone string manually
	if ( false === $timezone ) {
		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}

		// fallback to UTC
		return 'UTC';
	}

	return $timezone;
}