<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use Parser;
use PPFrame;

class TagsMedia {
	public static function renderTagExtimg( $input, array $args, Parser $parser, PPFrame $frame ) {
		if ( !isset( $args['src'] ) ) {
			return '';
		}
		if ( !Utils::checkDomain( $args['src'] ) ) {
			return Html::element( 'strong',
			[ 'class' => 'error' ],
			 wfMessage( 'extimg-invalidurl' )->text() );
		}
		$image = Html::element( 'img', [
			'src' => $args['src'],
			'title' => $args['title'] ?? null,
			'alt' => $args['alt'] ?? null,
			'width' => $args['width'] ?? null,
			'height' => $args['height'] ?? null,
			'class' => "ext-img"
		], '' );
		return $image;
	}

	public static function render163Music( $input, array $args, Parser $parser, PPFrame $frame ) {
		$musicId = $args['id'];
		$autoplay = $args['auto'] ?? 0;
		$width = $args['width'] ?? 300;
		$height = $args['height'] ?? 66;
		$src = "https://music.163.com/outchain/player?type=2&id=$musicId&auto=$autoplay&height=$height";
		$embed = Html::element( 'embed', [
			'width' => $width,
			'height' => $height + 20,
			'src' => $src
		] );
		return $embed;
	}

	public static function renderTagBilibili( $input, array $args, Parser $parser, PPFrame $frame ) {
		$attr = [
			'allowfullscreen' => 'true',
			'frameborder' => '0',
			'framespacing' => '0',
			'sandbox' => 'allow-top-navigation allow-same-origin allow-forms allow-scripts allow-popups',
			'scrolling' => 'no',
			'border' => '0',
			'width' => isset( $args['width'] ) ? htmlspecialchars( $args['width'] ) : '800',
			'height' => isset( $args['height'] ) ? htmlspecialchars( $args['height'] ) : '600'
		];
		if ( isset( $args['bv'] ) ) {
			$bvid = htmlspecialchars( $args['bv'] );
			$src = "https://player.bilibili.com/player.html?bvid=$bvid&high_quality=1";
			$attr['src'] = $src;
			$video = Html::element( 'iframe', $attr, '' );
			return Html::rawElement( 'div', [ 'class' => "bilibili-video bilibili-video-$bvid" ], $video );
		} else {
			return Html::element( 'strong',
			[ 'class' => 'error' ],
			wfMessage( 'bilibili-nobvid' )->text() );
		}
	}

	public static function renderTagSaltAlbum( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.saltalbum.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.saltalbum' ] );
		$width = $args['width'] ?? '100%';
		$height = $args['height'] ?? '680px';
		$content = $parser->recursiveTagParse( $input, $frame );
		return Html::rawElement( 'div', [ 'class' => 'salt-album','style' => "display:none;width:{$width};--height:{$height};" ], $content );
	}
}
