<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use MediaWiki\MediaWikiServices;
use Parser;
use PPFrame;

class Tags {
	public static function renderTagUCenterAvatar( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( 'ext.mcbbswikiutils.avatar' );
		if ( isset( $args['mili'] ) ) {
			return Html::element( 'p', [ 'class' => 'mili' ], '迷离可爱！' );
		}
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'MCBBSWikiUtils' );
		$ucenter = $config->get( 'UCenterURL' );
		if ( empty( $ucenter ) ) {
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:160%;font-weight:bold' ],
			 wfMessage( 'ucenteravatar-noucenterurl' )->text() );
		}
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$image = Html::element( 'img', [
			'src' => "$ucenter/avatar.php?uid=$uid&size=big",
			'class' => "ucenter-avatar ucenter-avatar-$uid",
			'data-uid' => $uid
		], '' );
		return $image;
	}

	public static function renderTagMCBBSCredit( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( 'ext.mcbbswikiutils.credit' );
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '-1';
		$credit = Html::element( 'div', [
			'class' => 'userpie',
			'data-uid' => $uid
		], wfMessage( 'mcbbscredit-loading' )->text() );
		return $credit;
	}

	public static function renderTagBilibili( $input, array $args, Parser $parser, PPFrame $frame ) {
		$attr = [
			'allowfullscreen' => 'true',
			'frameborder' => '0',
			'framespacing' => '0',
			'sandbox' => 'allow-top-navigation allow-same-origin allow-forms allow-scripts',
			'scrolling' => 'no',
			'border' => '0',
			'width' => isset( $args['width'] ) ? htmlspecialchars( $args['width'] ) : '800',
			'height' => isset( $args['height'] ) ? htmlspecialchars( $args['height'] ) : '600'
		];
		if ( isset( $args['bv'] ) ) {
			$bvid = htmlspecialchars( $args['bv'] );
			$src = "https://player.bilibili.com/player.html?bvid=$bvid&high_quality=1";
			$attr['src'] = $src;
			$video = Html::element( 'iframe', $attr, '' );
			return Html::rawElement( 'div', [ 'class' => "bilibili-video bilibili-video-$bvid" ], $video );
		} else {
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:160%;font-weight:bold' ],
			wfMessage( 'bilibili-nobvid' )->text() );
		}
	}
}
