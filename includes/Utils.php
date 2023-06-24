<?php

namespace MediaWiki\Extension\MCBBSWiki;

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

	public static function getBBSUserValue( $user, $data = 'username' ) {
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

	public static function checkNumber( mixed $str, int $defaultValue, int $min, int $max ) {
		$num = (int)$str;
		$pass = $num === 0 || $num < $min || $num > $max;
		return !$pass ? $num : $defaultValue;
	}

}
