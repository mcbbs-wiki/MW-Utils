<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use Parser;

class TagsUtils {
	public static function renderInlineCSS( Parser $parser, $frame, $args ) {
		$stripState = $parser->getStripState();
		$realCSS = $stripState->unstripBoth( $args[0] );
		$realCSS = trim( $realCSS );
		if ( !$realCSS ) {
			return '';
		}
		$dataUrl = 'data:text/css;charset=UTF-8;base64,';
		$url = $dataUrl . base64_encode( $realCSS );
		$head = Html::linkedStyle( $url );
		$parser->getOutput()->addHeadItem( $head );
		return '';
	}
}
