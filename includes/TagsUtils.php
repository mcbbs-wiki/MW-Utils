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
		$out=$parser->getOutput();
		$vars=$out->getJsConfigVars();
		if(array_key_exists("wgFireworkCount",$vars)||array_key_exists("wgFireworkHueRange",$vars)||array_key_exists("wgFireworkHueDiff",$vars)) {
			return Html::element('strong',['class'=>'error'],wfMessage( 'exists-firework' )->text());
		}
		$count=Utils::checkNumber($args['count']??null,110,1,500);
		$huediff=Utils::checkNumber($args['huediff']??null,30,0,180);
		$hue=$args['hue']??null;
		if($hue===null){
			$hue=TagsUtils::genFireworkHue();
		} else {
			$hue=preg_replace('/[\s\n_]+/','',$hue);
			$hue=preg_replace('/[;|\/\\，；、-]+/',',',$hue);
			$hue=explode(',',$hue);
			$hue=array_map(fn($val):int=>(int)$val,$hue);
			$hue=array_filter($hue,fn($val):bool=>$val>0&&$val<361);
			if(count($hue)<1||count($hue)>360){
				$hue=TagsUtils::genFireworkHue();
			}
		}
		$out->setJsConfigVar("wgFireworkCount",$count);
		$out->setJsConfigVar("wgFireworkHueDiff",$huediff);
		$out->setJsConfigVar("wgFireworkHueRange",$hue);
		$out->addModules( ['ext.mcbbswikiutils.firework'] );
		return '';
	}
	private static function genFireworkHue(){
		$hue=[];
		for ($i=1; $i < 361; $i++) { 
			$hue[]=$i;
		}
		return $hue;
	}
}
