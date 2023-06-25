<?php
namespace MediaWiki\Extension\MCBBSWiki;

use DateTime;
use Html;
use MWTimestamp;
use Parser;
use PPFrame;
use Wikimedia\Timestamp\ConvertibleTimestamp;

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
		$out = $parser->getOutput();
		$vars = $out->getJsConfigVars();
		if ( array_key_exists( "wgFireworkCount", $vars ) || array_key_exists( "wgFireworkHueRange", $vars ) || array_key_exists( "wgFireworkHueDiff", $vars ) ) {
			return Html::element( 'strong', [ 'class' => 'error' ], wfMessage( 'exists-firework' )->text() );
		}
		$count = Utils::checkNumber( $args['count'] ?? null, 110, 1, 500 );
		$huediff = Utils::checkNumber( $args['huediff'] ?? null, 30, 0, 180 );
		$hue = $args['hue'] ?? null;
		if ( $hue === null ) {
			$hue = self::genFireworkHue();
		} else {
			$hue = preg_replace( '/[\s\n_]+/', '', $hue );
			$hue = preg_replace( '/[;|\/\\，；、-]+/', ',', $hue );
			$hue = explode( ',', $hue );
			$hue = array_map( fn( $val ): int=>(int)$val, $hue );
			$hue = array_filter( $hue, fn( $val ): bool=>$val > 0 && $val < 361 );
			if ( count( $hue ) < 1 || count( $hue ) > 360 ) {
				$hue = self::genFireworkHue();
			}
		}
		$out->setJsConfigVar( "wgFireworkCount", $count );
		$out->setJsConfigVar( "wgFireworkHueDiff", $huediff );
		$out->setJsConfigVar( "wgFireworkHueRange", $hue );
		$out->addModules( [ 'ext.mcbbswikiutils.firework' ] );
		return '';
	}
	public static function renderTimediff( $input, array $args, Parser $parser, PPFrame $frame ) {
		//global $wgLocaltimezone;
		$parser->getOutput()->addModules(["ext.mcbbswikiutils.timediff"]);
		$class=['salt-time-diff'];
		if($args['complex']==="true"){
			$class[]='complex';
		}
		if($args['unix']==="true"){
			$startStamp=new ConvertibleTimestamp(intval($args['start']));
		} else {
			$startTimeTxt=$args['start'];
			$startTimeTxt=str_replace(["年","月"],'/',$startTimeTxt);
			$startTimeTxt=str_replace("日","",$startTimeTxt);
			$startStamp=new ConvertibleTimestamp(strtotime($startTimeTxt));
		}
		if($args['realtime']==="true"){
			$class[]='real-time';
			$end="REALTIME";
		} else {
			if($args['unix']==="true"){
				$endStamp=new ConvertibleTimestamp(intval($args['end']));
			} else {
				$endTimeTxt=$args['end'];
				$endTimeTxt=str_replace(["年","月"],'/',$endTimeTxt);
				$endTimeTxt=str_replace("日","",$endTimeTxt);
				$endStamp=new ConvertibleTimestamp(strtotime($endTimeTxt));
			}
		}
		//$class[]='utc';
		$start=$startStamp->getTimestamp()*1000;
		if($args['realtime']!=="true"){
			$end=$endStamp->getTimestamp()*1000;
		}
		if($args['simple']==="true"){
			$class[]='simple';
		}
		$classList=implode(' ',$class).' '.$args['class'];
		$cmd=$args['cmd'] ?? 'd';
		$cmd=preg_replace('/年份?/u','y',$cmd);
		$cmd=preg_replace('/月份?/u','o',$cmd);
		$cmd=preg_replace('/[天日]/u','d',$cmd);
		$cmd=preg_replace('/小?时/u','h',$cmd);
		$cmd=preg_replace('/分钟?/u','m',$cmd);
		$cmd=preg_replace('/毫秒/u','M',$cmd);
		$cmd=preg_replace('/秒/u','s',$cmd);
		return Html::element('span',[
			'class'=>$classList,
			'data-salt-time-diff-command'=>$cmd,
			'data-salt-time-diff-start'=>$start,
			'data-salt-time-diff-end'=>$end
		]);
	}
	private static function genFireworkHue() {
		$hue = [];
		for ( $i = 1; $i < 361; $i++ ) {
			$hue[] = $i;
		}
		return $hue;
	}
}
