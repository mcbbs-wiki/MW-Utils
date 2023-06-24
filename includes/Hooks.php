<?php

namespace MediaWiki\Extension\MCBBSWiki;

use ConfigFactory;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use Parser;
use Exception;
use Skin;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook, BeforePageDisplayHook {
	private string $ucenter;
	private string $appver;

	public function __construct( ConfigFactory $configFactory ) {
		$config = $configFactory->makeConfig( 'MCBBSWikiUtils' );
		$this->ucenter = $config->get( 'UCenterURL' );
		$this->appver = $config->get( 'MBWVER' );
	}
	public static function onLoadExtensionSchemaUpdates( $updater ) {
		$dir = __DIR__ . '/../sql';
		$dbType = $updater->getDB()->getType();
		if ( $dbType==='mysql' ) {
			$updater->addExtensionTable( 'mbw_usercredit', "{$dir}/tables-generated.sql" );
		} else if ($dbType === 'sqlite') {
			$updater->addExtensionTable( 'mbw_usercredit', "{$dir}/sqlite/tables-generated.sql" );
		} else if($dbType==='postgres') {
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
		$out->addJsConfigVars( 'wgMBWVER', $this->appver );
		$out->addModuleStyles( 'ext.mcbbswikiutils.testie.styles' );
		$out->addModules( "ext.mcbbswikiutils.testie" );
	}

	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ TagsMCBBS::class,'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit',  [ TagsMCBBS::class,'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili',  [ TagsMedia::class,'renderTagBilibili' ] );
		$parser->setHook( 'skinview',  [ TagsMCBBS::class,'renderTagSkinview' ] );
		$parser->setHook( 'skinview-lite',  [ TagsMCBBS::class,'renderTagSkinviewLite' ] );
		$parser->setHook( 'ext-img',  [ TagsMedia::class,'renderTagExtimg' ] );
		$parser->setHook( '163music',  [ TagsMedia::class,'render163Music' ] );
		$parser->setHook( 'salt-album',  [ TagsMedia::class,'renderTagSaltAlbum' ] );
		$parser->setHook( 'firework', [ TagsUtils::class,'renderFirework' ] );
		$parser->setHook( 'custom-audio',  [ TagsMedia::class,'renderTagAudio' ] );
		$parser->setFunctionHook( 'mcbbscreditvalue', [ TagsMCBBS::class,'renderCreditValue' ] );
		$parser->setFunctionHook( 'unsafe-css', [ TagsUtils::class,'renderInlineCSS' ], Parser::SFH_OBJECT_ARGS );
	}

}
