<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use Parser;
use PPFrame;

class TagsMCBBS {
	public static function renderTagSkinviewLite( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.skinview.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.skinview-lite' ] );
		$isURL = filter_var( $input, FILTER_VALIDATE_URL );
		$width = $args['width'] ?? 250;
		$height = $args['height'] ?? 350;
		$speed = $args['speed'] ?? 'slow';
		if ( $isURL !== false ) {
			if ( !Utils::checkDomain( $input ) ) {
				return Html::element( 'strong',
				[ 'class' => 'error' ],
				 wfMessage( 'extimg-invalidurl' )->text() );
			}
		}
		$output = $parser->recursiveTagParse( $input, $frame );
		$controller = Html::element( 'div', [ 'class' => 'skinview-controller-lite' ] );
		$fix = Html::element( 'div', [ 'class' => 'skinview-controller-fix' ] );
		$canvas = Html::rawElement( 'div', [ 'class' => 'skinview-canvas','style' => "height:{$height}px;" ], $output );
		return Html::rawElement( 'div', [
				'class' => 'skinview-lite skinview-loading',
				'data-speed' => $speed,
				'style' => "width:{$width}px;"
			], $canvas . $controller . $fix );
	}

	public static function renderTagSkinview( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.skinview.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.skinview' ] );
		$isURL = filter_var( $input, FILTER_VALIDATE_URL );
		$width = $args['width'] ?? 250;
		$height = $args['height'] ?? 350;
		$speed = $args['speed'] ?? 'slow';
		if ( $isURL !== false ) {
			if ( !Utils::checkDomain( $input ) ) {
				return Html::element( 'strong',
				[ 'class' => 'error' ],
				 wfMessage( 'extimg-invalidurl' )->text() );
			}
		}
		$output = $parser->recursiveTagParse( $input, $frame );
		$controller = Html::element( 'div', [ 'class' => 'skinview-controller' ] );
		$canvas = Html::rawElement( 'div', [ 'class' => 'skinview-canvas','style' => "height:{$height}px;" ], $output );
		return Html::rawElement( 'div', [
				'class' => 'skinview skinview-loading',
				'data-speed' => $speed,
				'style' => "width:{$width}px;"
			], $canvas . $controller );
	}

	public static function renderCreditValue( Parser $parser, $uid = '3038', $data = 'diamond' ) {
		$user = Utils::getBBSUserJson( $uid );
		wfDebugLog( 'bbsuser', "Fetch user $uid $data" );
		$value = Utils::getBBSUserValue( $user, $data );
		if ( $value === false ) {
			return 0;
		}
		return $value;
	}

	public static function renderTagUCenterAvatar( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.avatar' ] );
		if ( isset( $args['mili'] ) ) {
			return Html::element( 'p', [ 'class' => 'mili' ], '迷离可爱！' );
		}
		global $wgUCenterURL;
		if ( empty( $wgUCenterURL ) ) {
			return Html::element( 'strong',
			[ 'class' => 'error' ],
			 wfMessage( 'ucenteravatar-noucenterurl' )->text() );
		}
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$image = Html::element( 'img', [
			'src' => "$wgUCenterURL/avatar.php?uid=$uid&size=big",
			'class' => "ucenter-avatar ucenter-avatar-$uid",
			'data-uid' => $uid
		], '' );
		return $image;
	}

	public static function renderTagMCBBSCredit( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.credit' ] );
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$userJson = Utils::getBBSUserJson( $uid );
		if ( $userJson === false ) {
			return Html::element( 'strong', [ 'class' => 'error' ], wfMessage( 'mcbbscredit-notfound' )->text() );
		}
		$credit = Html::element( 'div', [
			'class' => 'userpie',
			'data-user' => $userJson
		], wfMessage( 'mcbbscredit-loading' )->text() );
		return $credit;
	}
}
