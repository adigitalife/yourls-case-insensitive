<?php
/*
Plugin Name: Case insensitive YOURLS
Plugin URI: https://github.com/adigitalife/yourls-case-insensitive
Description: Makes YOURLS case insensitive
Version: 1.0
Author: Aylwin
Author URI: http://adigitalife.net/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Hook our custom function into the 'add_new_link' filter
yourls_add_filter( 'shunt_keyword_is_taken', 'insensitive_keyword_is_taken' );
yourls_add_filter( 'shunt_get_keyword_info', 'insensitive_get_keyword_info' );

// If the keyword exists, display the long URL in the error message
function insensitive_keyword_is_taken( $return, $keyword ) {

	global $ydb;
	$keyword = yourls_sanitize_keyword( $keyword );
	$taken = false;
	$table = YOURLS_DB_TABLE_URL;
	$already_exists = $ydb->get_var( "SELECT COUNT(`keyword`) FROM `$table` WHERE LOWER(`keyword`) = LOWER('$keyword');" );
	if ( $already_exists )
		$taken = true;

	return yourls_apply_filter( 'keyword_is_taken', $taken, $keyword );
}

function insensitive_get_keyword_info( $return, $keyword, $field, $notfound ) {

	$keyword = yourls_sanitize_string( $keyword );
	$infos = insensitive_get_keyword_infos( $keyword );

	$return = $notfound;
	if ( isset( $infos[ $field ] ) && $infos[ $field ] !== false )
		$return = $infos[ $field ];

	return yourls_apply_filter( 'get_keyword_info', $return, $keyword, $field, $notfound );
}

function insensitive_get_keyword_infos( $keyword, $use_cache = true ) {

	global $ydb;
	$keyword = yourls_sanitize_string( $keyword );

	yourls_do_action( 'pre_get_keyword', $keyword, $use_cache );

	if( isset( $ydb->infos[$keyword] ) && $use_cache == true ) {
		return yourls_apply_filter( 'get_keyword_infos', $ydb->infos[$keyword], $keyword );
	}

	yourls_do_action( 'get_keyword_not_cached', $keyword );

	$table = YOURLS_DB_TABLE_URL;
	$infos = $ydb->get_row( "SELECT * FROM `$table` WHERE LOWER(`keyword`) = LOWER('$keyword')" );

	if( $infos ) {
		$infos = (array)$infos;
		$ydb->infos[ $keyword ] = $infos;
	} else {
		$ydb->infos[ $keyword ] = false;
	}

	return yourls_apply_filter( 'get_keyword_infos', $ydb->infos[$keyword], $keyword );
}
