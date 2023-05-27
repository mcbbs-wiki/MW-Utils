<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use Parser;
use PPFrame;

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

	public static function renderFirework( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( 'ext.mcbbswikiutils.firework' );
		$hueDiff = Html::element( 'span', [ 'id' => 'saltFireworkHueDiff' ], $args['huediff'] );
		$hueRange = Html::element( 'span', [ 'id' => 'saltFireworkHueRange' ], $args['hue'] );
		$count = Html::element( 'span', [ 'id' => 'saltFireworkCount' ], $args['count'] );
		$fireworkConfig = Html::rawElement( 'span', [ 'style' => 'display:none;' ], $hueRange . $hueDiff . $count );
		return $fireworkConfig;
	}
}
