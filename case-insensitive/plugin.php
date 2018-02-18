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
yourls_add_filter( 'shunt_update_clicks', 'insensitive_update_clicks' );

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

	if( $ydb->has_infos($keyword) && $use_cache == true ) {
		return yourls_apply_filter( 'get_keyword_infos', $ydb->get_infos($keyword), $keyword );
	}

	yourls_do_action( 'get_keyword_not_cached', $keyword );

	$table = YOURLS_DB_TABLE_URL;
	$infos = $ydb->get_row( "SELECT * FROM `$table` WHERE LOWER(`keyword`) = LOWER('$keyword')" );

	if( $infos ) {
		$infos = (array)$infos;
		$ydb->set_infos($keyword, $infos);
	} else {
		$ydb->set_infos($keyword, false);
	}

	return yourls_apply_filter( 'get_keyword_infos', $ydb->get_infos($keyword), $keyword );
}

 function insensitive_update_clicks( $return, $keyword, $clicks = false ) {
	global $ydb;
	$keyword = yourls_sanitize_string( $keyword );
	$table = YOURLS_DB_TABLE_URL;
    error_log(var_export($keyword, true));
	if ( $clicks !== false && is_int( $clicks ) && $clicks >= 0 )
		$update = $ydb->fetchAffected( "UPDATE `$table` SET `clicks` = :clicks WHERE LOWER(`keyword`) = LOWER(:keyword)", array('clicks' => $clicks, 'keyword' => $keyword) );
	else
		$update = $ydb->fetchAffected( "UPDATE `$table` SET `clicks` = clicks + 1 WHERE LOWER(`keyword`) = LOWER(:keyword)", array('keyword' => $keyword) );

	yourls_do_action( 'update_clicks', $keyword, $update, $clicks );
	return $update;
}
