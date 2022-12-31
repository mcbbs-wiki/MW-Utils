<?php

namespace MediaWiki\Extension\MCBBSWiki;

use ConfigFactory;
use Html;
use MediaWiki\Hook\LinkerMakeExternalLinkHook;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use Parser;
use PPFrame;
use Skin;
use SpecialPage;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook, LinkerMakeExternalLinkHook {
	private $ucenter;
	private $excludeList;
	private $enableURLWarning;

	public function __construct( ConfigFactory $configFactory ) {
		$config = $configFactory->makeConfig( 'MCBBSWikiUtils' );
		$this->ucenter = $config->get( 'UCenterURL' );
		$this->excludeList = $config->get( 'URLWarningExclude' );
		$this->enableURLWarning = $config->get( 'EnableURLWarning' );
	}

	private function checkURL( string $url ) {
		foreach ( $this->excludeList as $index => $exclude ) {
			if ( strpos( $url, $exclude ) !== false ) {
				return false;
			} else {
				continue;
			}
		}
		return true;
	}

	public function onLinkerMakeExternalLink( &$url, &$text, &$link, &$attribs, $linkType ) {
		if ( $this->enableURLWarning ) {
			if ( $this->checkURL( $url ) ) {
				$url = SpecialPage::getTitleFor( 'ExternalLinkWarning' )
				->getLocalURL( [ 'wpURL' => base64_encode( $url ) ] );
			}
		}
	}

	public function onSkinAddFooterLinks( Skin $skin, string $key, array &$footerlinks ) {
		if ( $key === 'info' ) {
			$msg = $skin->msg( 'footerinfo' );
			if ( !$msg->isDisabled() ) {
				$footerlinks['footerinfo'] = $msg->parse();
			}
		}
	}

	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ $this,'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit',  [ $this,'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili',  [ $this,'renderTagBilibili' ] );
	}

	public function renderTagUCenterAvatar( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( 'ext.mcbbswikiutils.avatar' );
		if ( isset( $args['mili'] ) ) {
			return Html::element( 'p', [ 'class' => 'mili' ], '迷离可爱！' );
		}
		$ucenter = $this->ucenter;
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

	public function renderTagMCBBSCredit( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( 'ext.mcbbswikiutils.credit' );
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '-1';
		$credit = Html::element( 'div', [
			'class' => 'userpie',
			'data-uid' => $uid
		], wfMessage( 'mcbbscredit-loading' )->text() );
		return $credit;
	}

	public function renderTagBilibili( $input, array $args, Parser $parser, PPFrame $frame ) {
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
