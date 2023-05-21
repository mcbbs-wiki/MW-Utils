<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use OutputPage;

class SaltToolWealthSimulator implements ISaltTool {
	public function outHead( OutputPage $out ) {
	}

	public function outBody( OutputPage $out ) {
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.wealth.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.wealth' );
		$resul = Html::element( 'ul', [ 'class' => 'resul' ] );
		$simipt = Html::element( 'input', [ 'class' => 'input','placeholder' => $out->msg( 'salttoolbox-wealth-simipt' )->text() ] );
		$simbtn = Html::element( 'div', [ 'class' => 'sim' ], $out->msg( 'salttoolbox-wealth-simbtn' )->text() );
		$clsbtn = Html::element( 'div', [ 'class' => 'cls' ], $out->msg( 'salttoolbox-wealth-clsbtn' )->text() );
		$resshow = Html::element( 'div', [ 'class' => 'resshow' ], $out->msg( 'salttoolbox-wealth-resshow' )->text() );

		$div = Html::rawElement( 'div', [ 'class' => 'salt-acquire-wealth-simulator' ], $resul . $simipt . $simbtn . $clsbtn . $resshow );
		$out->addHTML( $div );
	}
}
