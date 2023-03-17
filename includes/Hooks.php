<?php

namespace MediaWiki\Extension\MCBBSWiki;

use ConfigFactory;
use Html;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use Parser;
use PPFrame;
use Skin;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook {
	private string $ucenter;
	private $whitelistDomains;
	private function checkDomain( string $url ) {
		$domain = parse_url( $url, PHP_URL_HOST );
	
		// Check if we match the domain exactly
		if ( in_array( $domain, $this->whitelistDomains ) )
			return true;
	
		$valid = false;
	
		foreach( $this->whitelistDomains as $whitelistDomain ) {
			$whitelistDomain = '.' . $whitelistDomain; // Prevent things like 'evilsitetime.com'
			if( strpos( $domain, $whitelistDomain ) === ( strlen( $domain ) - strlen( $whitelistDomain ) ) ) {
				$valid = true;
				break;
			}
		}
		return $valid;
	}
	public function __construct( ConfigFactory $configFactory ) {
		$config = $configFactory->makeConfig( 'MCBBSWikiUtils' );
		$this->whitelistDomains = $config->get("ExtImgWhiteList");
		$this->ucenter = $config->get( 'UCenterURL' );
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
		$parser->setHook( 'ext-img',  [ $this,'renderTagExtimg' ] );
	}
	public function renderTagExtimg( $input, array $args, Parser $parser, PPFrame $frame ) {
		if(!isset($args['src'])){
			return '';
		}
		if(!$this->checkDomain($args['src'])){
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight: bold;' ],
			 wfMessage( 'extimg-invalidurl' )->text() );
		}
		$image = Html::element( 'img', [
			'src' => $args['src'],
			'title' => $args['title'] ?? null,
			'alt' => $args['alt'] ?? null,
			'width'=>$args['width'] ?? null,
			'height'=>$args['height'] ?? null,
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
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight:bold;' ],
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
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight:bold;' ],
			wfMessage( 'bilibili-nobvid' )->text() );
		}
	}
}
