<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\Html;
use OutputPage;

class SaltToolWealthSimulator implements ISaltTool {
	public function outHead( OutputPage $out, $arg ) {
	}

	public function outBody( OutputPage $out, $arg ) {
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.wealth.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.wealth' );
		$after = Html::element( 'span', [ 'class' => 'after' ],$out->msg('salttoolbox-topnav-wealth')->text() );
		$before = Html::element( 'span', [ 'class' => 'before' ],$out->msg('salttoolbox-author')->text() );
		$resul = Html::element( 'ul', [ 'class' => 'resul' ] );
		$simipt = Html::element( 'input', [ 'class' => 'input','placeholder' => $out->msg( 'salttoolbox-wealth-simipt' )->text() ] );
		$simbtn = Html::element( 'div', [ 'class' => 'sim' ], $out->msg( 'salttoolbox-wealth-simbtn' )->text() );
		$clsbtn = Html::element( 'div', [ 'class' => 'cls' ], $out->msg( 'salttoolbox-wealth-clsbtn' )->text() );
		$resshow = Html::element( 'div', [ 'class' => 'resshow' ], $out->msg( 'salttoolbox-wealth-resshow' )->text() );

		$div = Html::rawElement( 'div', [ 'class' => 'salt-acquire-wealth-simulator' ], $before.$resul . $simipt . $simbtn . $clsbtn . $resshow.$after );
		$out->addHTML( $div );
	}
}
