<?php

namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use Skin;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook {
	public function onSkinAddFooterLinks( Skin $skin, string $key, array &$footerlinks ) {
		if ( $key === 'info' ) {
			$msg = $skin->msg( 'footerinfo' );
			if ( !$msg->isDisabled() ) {
				$footerlinks['footerinfo'] = $msg->parse();
			}
		}
	}

	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ Tags::class, 'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit', [ Tags::class, 'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili', [ Tags::class, 'renderTagBilibili' ] );
	}
}
