<?php

namespace MediaWiki\Extension\MCBBSWiki;

use ConfigFactory;
use Html;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use Parser;
use PPFrame;
use Skin;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook, BeforePageDisplayHook {
	private string $ucenter;
	private string $appver;

	public function __construct( ConfigFactory $configFactory ) {
		$config = $configFactory->makeConfig( 'MCBBSWikiUtils' );
		$this->ucenter = $config->get( 'UCenterURL' );
		$this->appver = $config->get( 'MBWVER' );
	}

	public function onSkinAddFooterLinks( Skin $skin, string $key, array &$footerlinks ) {
		if ( $key === 'info' ) {
			$msg = $skin->msg( 'footerinfo' );
			if ( !$msg->isDisabled() ) {
				$footerlinks['footerinfo'] = $msg->parse();
			}
		}
	}

	public function onBeforePageDisplay( $out, $skin ): void {
		$out->addJsConfigVars( 'wgMBWVER', $this->appver );
	}

	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ $this,'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit',  [ $this,'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili',  [ $this,'renderTagBilibili' ] );
		$parser->setHook( 'ext-img',  [ $this,'renderTagExtimg' ] );
		$parser->setFunctionHook( 'mcbbscreditvalue', [ $this,'renderCreditValue' ] );
		$parser->setFunctionHook( 'inline-css', [ $this,'renderInlineCSS' ] );
	}

	public function renderInlineCSS( Parser $parser, $css ) {
		$dataUrl = 'data:text/css;charset=UTF-8;base64,';
		$url = $dataUrl . base64_encode( $css );
		$head = Html::linkedStyle( $url );
		$parser->getOutput()->addHeadItem( $head );
		return '';
	}

	public function renderCreditValue( Parser $parser, $uid = '3038', $data = 'diamond' ) {
		$user = Utils::getBBSUserJson( $uid );
		wfDebugLog( 'bbsuser', "Fetch user $uid $data" );
		$value = Utils::getBBSUserValue( $user, $data );
		if ( $value === false ) {
			return 0;
		}
		return $value;
	}

	public function renderTagExtimg( $input, array $args, Parser $parser, PPFrame $frame ) {
		if ( !isset( $args['src'] ) ) {
			return '';
		}
		if ( !Utils::checkDomain( $args['src'] ) ) {
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight: bold;' ],
			 wfMessage( 'extimg-invalidurl' )->text() );
		}
		$image = Html::element( 'img', [
			'src' => $args['src'],
			'title' => $args['title'] ?? null,
			'alt' => $args['alt'] ?? null,
			'width' => $args['width'] ?? null,
			'height' => $args['height'] ?? null,
			'class' => "ext-img"
		], '' );
		return $image;
	}

	public function renderTagUCenterAvatar( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.avatar' ] );
		if ( isset( $args['mili'] ) ) {
			return Html::element( 'p', [ 'class' => 'mili' ], '迷离可爱！' );
		}
		$ucenter = $this->ucenter;
		if ( empty( $ucenter ) ) {
			return Html::element( 'strong',
			[ 'class' => 'error' ],
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

	public function renderTagMCBBSCredit( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.credit-loader' ] );
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

	public function renderTagBilibili( $input, array $args, Parser $parser, PPFrame $frame ) {
		$attr = [
			'allowfullscreen' => 'true',
			'frameborder' => '0',
			'framespacing' => '0',
			'sandbox' => 'allow-top-navigation allow-same-origin allow-forms allow-scripts allow-popups',
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
			return Html::element( 'strong',
			[ 'class' => 'error' ],
			wfMessage( 'bilibili-nobvid' )->text() );
		}
	}
}
