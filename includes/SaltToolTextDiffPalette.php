<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use OutputPage;

class SaltToolTextDiffPalette implements ISaltTool {
	public function outHead( OutputPage $out, $arg ) {
	}

	public function outBody( OutputPage $out, $arg ) {
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.textdiff.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.textdiff' );
		$subtitle = Html::element( 'span', [ 'class' => 'subtitle','title' => $out->msg( 'salttoolbox-textdiff-aboutlevenshtein' )->text() ] );
		$title = Html::rawElement( 'div', [ 'class' => 'title','style' => 'font-weight:bold;' ], $out->msg( 'salttoolbox-textdiff-prefix' )->text() . $subtitle );
		$msg = Html::element( 'div', [ 'class' => 'message' ], $out->msg( 'salttoolbox-textdiff-ready' )->text() );
		$input1 = Html::element( 'textarea', [ 'class' => 'origin' ] );
		$input2 = Html::element( 'textarea', [ 'class' => 'edited' ] );
		$btn = Html::element( 'button', [], $out->msg( 'salttoolbox-textdiff-start' )->text() );
		$result = Html::element( 'div', [ 'title' => $out->msg( 'salttoolbox-textdiff-switch' )->text(),'class' => 'result','style' => 'white-space:pre-wrap;' ] );

		$div = Html::rawElement( 'div', [ 'class' => 'salt-textDiffTool' ], $title . $msg . $input1 . $input2 . $result . $btn );
		$out->addHTML( $div );
	}
}
