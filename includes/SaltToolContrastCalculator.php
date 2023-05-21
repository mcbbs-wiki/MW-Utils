<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use OutputPage;

class SaltToolContrastCalculator implements ISaltTool {
	public function outHead( OutputPage $out ) {
	}

	public function outBody( OutputPage $out ) {
		$out->addModuleStyles( [ 'ext.mcbbswikiutils.salttool.contrast.styles' ] );
		$out->addModules( [ 'ext.mcbbswikiutils.salttool.contrast' ] );
		$color1Input = Html::element( 'input', [
			'maxlength' => 20,
			'placeholder' => $out->msg( 'salttoolbox-contrast-color1input' )->text()
		] );
		$color1Show = Html::element( 'div', [ 'class' => 'color-show' ] );
		$color1 = Html::rawElement( 'div', [ 'class' => 'color left' ], $color1Input . $color1Show );
		$color2Input = Html::element( 'input', [
			'maxlength' => 20,
			'placeholder' => $out->msg( 'salttoolbox-contrast-color2input' )->text()
		] );
		$color2Show = Html::element( 'div', [ 'class' => 'color-show' ] );
		$color2 = Html::rawElement( 'div', [ 'class' => 'color right' ], $color2Input . $color2Show );
		$resShow = Html::element( 'div', [ 'class' => 'res-show' ], $out->msg( 'salttoolbox-contrast-res' )->text() );
		$resShowBig = Html::element( 'div', [ 'class' => 'res-show big' ], $out->msg( 'salttoolbox-contrast-resbig' )->text() );
		$resText = Html::element( 'div', [ 'class' => 'res-text' ] );
		$res = Html::rawElement( 'div', [ 'class' => 'res' ], $resShow . $resShowBig . $resText );
		$div = Html::rawElement( 'div', [ 'id' => 'saltContrastCalculator' ], $color1 . $color2 . $res );
		$out->addHTML( $div );
	}
}
