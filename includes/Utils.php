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
				break;
			case 'nickname':
				return $user['nickname'];
				break;
			case 'post':
				return $user['activities']['post'];
				break;
			case 'thread':
				return $user['activities']['thread'];
				break;
			case 'groupid':
				return $user['activities']['currentGroupID'];
				break;
			case 'grouptext':
				return $user['activities']['currentGroupText'];
				break;
			case 'digiest':
				return $user['activities']['digiest'];
				break;
			case 'notfound':
				return $user['notfound'];
				break;
			case 'diamond':
				return $user['credits']['diamond'];
				break;
			case 'popularity':
				return $user['credits']['popularity'];
				break;
			case 'heart':
				return $user['credits']['heart'];
				break;
			case 'contribute':
				return $user['credits']['contribute'];
				break;
			case 'gem':
				return $user['credits']['gem'];
				break;
			case 'star':
				return $user['credits']['star'];
				break;
			case 'nugget':
				return $user['credits']['nugget'];
				break;
			case 'ingot':
				return $user['credits']['ingot'];
				break;
			case 'credit':
				return $user['credits']['credit'];
				break;
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
