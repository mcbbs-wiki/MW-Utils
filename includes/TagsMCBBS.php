<?php
namespace MediaWiki\Extension\MCBBSWiki;

use FormatJson;
use Html;
use MediaWiki\MediaWikiServices;
use Parser;
use PPFrame;
use Title;

class TagsMCBBS {
	public static function renderTagSkinviewLite( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.skinview.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.skinview-lite' ] );
		$isURL = filter_var( $args['src'], FILTER_VALIDATE_URL );
		$width = $args['width'] ?? 250;
		$height = $args['height'] ?? 350;
		$speed = $args['speed'] ?? 'slow';
		$src = '';
		if ( $isURL !== false ) {
			if ( !Utils::checkDomain( $args['src'] ) ) {
				return Html::element( 'strong',
				[ 'class' => 'error' ],
				 wfMessage( 'extimg-invalidurl' )->text() );
			}
			$src = $args['src'];
		} else {
			$title = Title::makeTitle( NS_FILE, $args['src'] );
			if ( !$title->exists() ) {
				$lr = MediaWikiServices::getInstance()->getLinkRenderer();
				return $lr->makeBrokenLink( $title );
			}
			$src = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title )->getUrl();
		}
		$output = $parser->recursiveTagParse( $input, $frame );
		$controller = Html::element( 'div', [ 'class' => 'skinview-controller-lite' ] );
		$fix = Html::element( 'div', [ 'class' => 'skinview-controller-fix' ] );
		$canvas = Html::rawElement( 'div', [ 'class' => 'skinview-canvas','style' => "height:{$height}px;" ] );
		return Html::rawElement( 'div', [
				'class' => 'skinview-lite skinview-loading',
				'data-speed' => $speed,
				'data-src' => $src,
				'style' => "width:{$width}px;"
			], $canvas . $controller . $fix );
	}

	public static function renderTagSkinview( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.skinview.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.skinview' ] );
		$isURL = filter_var( $args['src']??'', FILTER_VALIDATE_URL );
		$width = $args['width'] ?? 250;
		$height = $args['height'] ?? 350;
		$speed = $args['speed'] ?? 'slow';
		$src = '';
		if ( $isURL !== false ) {
			if ( !Utils::checkDomain( $args['src'] ) ) {
				return Html::element( 'strong',
				[ 'class' => 'error' ],
				 wfMessage( 'extimg-invalidurl' )->text() );
			}
			$src = $args['src'];
		} else {
			$title = Title::makeTitle( NS_FILE, $args['src'] );
			if ( !$title->exists() ) {
				$lr = MediaWikiServices::getInstance()->getLinkRenderer();
				return $lr->makeBrokenLink( $title );
			}
			$src = MediaWikiServices::getInstance()->getRepoGroup()->findFile( $title )->getUrl();
		}
		$controller = Html::element( 'div', [ 'class' => 'skinview-controller' ] );
		$canvas = Html::rawElement( 'div', [ 'class' => 'skinview-canvas','style' => "height:{$height}px;" ] );
		return Html::rawElement( 'div', [
				'class' => 'skinview skinview-loading',
				'data-speed' => $speed,
				'data-src' => $src,
				'style' => "width:{$width}px;"
			], $canvas . $controller );
	}

	public static function renderCreditValue( Parser $parser, $uid = '3038', $data = 'diamond' ) {
		/** @var MCBBSCredit */
		$realUID=intval($uid);
		if($realUID===0){
			return 0;
		}
		$cr = MediaWikiServices::getInstance()->getService( 'MBWUtils.MCBBSCredit' );
		$user = $cr->getUserInfo( $realUID );
		if ( $user['notfound'] === true && $user === null ) {
			return 0;
		}
		wfDebugLog( 'bbsuser', "Fetch user $realUID $data" );
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
		$cr = MediaWikiServices::getInstance()->getService( 'MBWUtils.MCBBSCredit' );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.credit' ] );
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$user = $cr->getUserInfo( $uid );
		if ( $user['notfound'] === true || $user === null ) {
			return Html::element( 'strong', [ 'class' => 'error' ], wfMessage( 'mcbbscredit-notfound' )->text() );
		}
		$userJson = FormatJson::encode( $user );
		$credit = Html::element( 'div', [
			'class' => 'userpie',
			'data-user' => $userJson
		], wfMessage( 'mcbbscredit-loading' )->text() );
		return $credit;
	}
}
