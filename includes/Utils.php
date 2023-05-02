<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Exception;
use FormatJson;
use MediaWiki\MediaWikiServices;

class Utils {
	public static function checkDomain( string $url ) {
		global $wgExtImgWhiteList;
		$domain = parse_url( $url, PHP_URL_HOST );
		if ( in_array( $domain, $wgExtImgWhiteList ) ) {
			return true;
		}
		$valid = false;
		foreach ( $wgExtImgWhiteList as $whitelistDomain ) {
			$whitelistDomain = '.' . $whitelistDomain;
			if ( strpos( $domain, $whitelistDomain ) === ( strlen( $domain ) - strlen( $whitelistDomain ) ) ) {
				$valid = true;
				break;
			}
		}
		return $valid;
	}

	public static function getBBSUserJson( $uid ) {
		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$userCacheKey = $cache->makeKey( 'bbsuser', $uid );
		$userJson = $cache->get( $userCacheKey );
		if ( $userJson === false ) {
			wfDebugLog( 'bbsuser', "Fetch user $uid from API" );
			$userJson = self::getBBSUserFromAPI( $uid );
			if ( $userJson === false ) {
				$cache->set( $userCacheKey, "NOTFOUND", 10800 );
				return false;
			}
			$cache->set( $userCacheKey, $userJson, 10800 );
		} else {
			wfDebugLog( 'bbsuser', "Fetch user $uid from cache" );
		}
		if ( $userJson === 'NOTFOUND' ) {
			return false;
		}
		return $userJson;
	}

	public static function getBBSUserValue( $userJson, $data = 'username' ) {
		if ( $userJson === false ) {
			return false;
		}
		$user = FormatJson::decode( $userJson, true );
		if ( !$user ) {
			wfDebugLog( 'bbsuser', 'Failed to parse user' );
			return false;
		}
		switch ( $data ) {
			case 'uid':
				return $user['uid'];
			case 'nickname':
				return $user['nickname'];
			case 'post':
				return $user['activites']['post'];
			case 'thread':
				return $user['activites']['thread'];
			case 'groupid':
				return $user['activites']['currentGroupID'];
			case 'grouptext':
				return $user['activites']['currentGroupText'];
			case 'digiest':
				return $user['activites']['digiest'];
			case 'diamond':
				return $user['credits']['diamond'];
			case 'popularity':
				return $user['credits']['popularity'];
			case 'heart':
				return $user['credits']['heart'];
			case 'contribute':
				return $user['credits']['contribute'];
			case 'gem':
				return $user['credits']['gem'];
			case 'star':
				return $user['credits']['star'];
			case 'nugget':
				return $user['credits']['nugget'];
			case 'ingot':
				return $user['credits']['ingot'];
			case 'credit':
				return $user['credits']['credit'];
			default:
				return false;
		}
	}

	public static function getBBSUserFromAPI( $uid ) {
		global $wgMBWAPIURL;
		$request = MediaWikiServices::getInstance()->getHttpRequestFactory()->create( $wgMBWAPIURL . $uid );
		try{
			$status = $request->execute();
		} catch ( Exception $e ) {
			wfDebugLog( 'bbsuser', 'Failed to fetch user: ' . $e->getMessage() );
			return false;
		}
		if ( !$status->isOK() ) {
			wfDebugLog( 'bbsuser', 'Failed to fetch user: ' . $status->getErrorsArray()[0][0] );
			return false;
		}
		if ( $request->getStatus() === 500 && $request->getStatus() === 404 ) {
			return false;
		}
		return $request->getContent();
	}
}
