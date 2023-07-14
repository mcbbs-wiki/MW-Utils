<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\Html;
use OutputPage;

class SaltToolMinerSimulator implements ISaltTool {
	public function outHead( OutputPage $out, $arg ) {
	}

	public function outBody( OutputPage $out, $arg ) {
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.miner.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.miner' );
		$after = Html::element( 'span', [ 'class' => 'after' ],$out->msg('salttoolbox-topnav-miner')->text() );
		$before = Html::element( 'span', [ 'class' => 'before' ],$out->msg('salttoolbox-author')->text() );
		$resul = Html::element( 'ul', [ 'class' => 'resul' ] );
		$simbtn = Html::element( 'div', [ 'class' => 'sim s1' ], $out->msg( 'salttoolbox-miner-simbtn' )->text() );
		$s10btn = Html::element( 'div', [ 'class' => 'sim s10' ], $out->msg( 'salttoolbox-miner-s10btn' )->text() );
		$div = Html::rawElement( 'div', [ 'class' => 'salt-miner-simulator' ], $before.$resul . $simbtn . $s10btn .$after);
		$out->addHTML( $div );
	}
}
