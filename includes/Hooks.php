<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Exception;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use Parser;
use Skin;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook, BeforePageDisplayHook {
	public static function onLoadExtensionSchemaUpdates( $updater ) {
		$dir = __DIR__ . '/../sql';
		$dbType = $updater->getDB()->getType();
		if ( $dbType === 'mysql' ) {
			$updater->addExtensionTable( 'mbw_usercredit', "{$dir}/tables-generated.sql" );
		} elseif ( $dbType === 'sqlite' ) {
			$updater->addExtensionTable( 'mbw_usercredit', "{$dir}/sqlite/tables-generated.sql" );
		} elseif ( $dbType === 'postgres' ) {
			$updater->addExtensionTable( 'mbw_usercredit', "{$dir}/postgres/tables-generated.sql" );
		} else {
			throw new Exception( 'Database type not currently supported' );
		}
		return true;
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
		global $wgMBWVER;
		$out->addJsConfigVars( 'wgMBWVER', $wgMBWVER );
		$out->addModuleStyles( 'ext.mcbbswikiutils.testie.styles' );
		$out->addModules( "ext.mcbbswikiutils.testie" );
	}

	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ TagsMCBBS::class,'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit',  [ TagsMCBBS::class,'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili',  [ TagsMedia::class,'renderTagBilibili' ] );
		$parser->setHook( 'skinview',  [ TagsMCBBS::class,'renderTagSkinview' ] );
		$parser->setHook( 'skinview-lite',  [ TagsMCBBS::class,'renderTagSkinviewLite' ] );
		$parser->setHook( 'cardeffect',  [ TagsMCBBS::class,'renderTagCardEffect' ] );
		$parser->setHook( 'ext-img',  [ TagsMedia::class,'renderTagExtimg' ] );
		$parser->setHook( '163music',  [ TagsMedia::class,'render163Music' ] );
		$parser->setHook( 'salt-album',  [ TagsMedia::class,'renderTagSaltAlbum' ] );
		$parser->setHook( 'firework', [ TagsUtils::class,'renderFirework' ] );
		$parser->setHook( 'custom-audio',  [ TagsMedia::class,'renderTagAudio' ] );
		$parser->setHook( 'timediff',  [ TagsUtils::class,'renderTimediff' ] );
		$parser->setHook( 'topsign',  [ TagsUtils::class,'renderTopSign' ] );
		$parser->setFunctionHook( 'mcbbscreditvalue', [ TagsMCBBS::class,'renderCreditValue' ] );
		$parser->setFunctionHook( 'unsafe-css', [ TagsUtils::class,'renderInlineCSS' ], Parser::SFH_OBJECT_ARGS );
	}
}
