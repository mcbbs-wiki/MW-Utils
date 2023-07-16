<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

class SaltToolContrastCalculator implements ISaltTool {
	public function outHead( OutputPage $out, $arg, TemplateParser $tmpl ) {
	}

	public function outBody( OutputPage $out, $arg, TemplateParser $tmpl ) {
		$data = [
			'msg-salttoolbox-contrast-color1input' => $out->msg( 'salttoolbox-contrast-color1input' )->text(),
			'msg-salttoolbox-contrast-color2input' => $out->msg( 'salttoolbox-contrast-color2input' )->text(),
			'msg-salttoolbox-contrast-res' => $out->msg( 'salttoolbox-contrast-res' )->text(),
			'msg-salttoolbox-contrast-resbig' => $out->msg( 'salttoolbox-contrast-resbig' )->text()
		];
		$out->addModuleStyles( [ 'ext.mcbbswikiutils.salttool.contrast.styles' ] );
		$out->addModules( [ 'ext.mcbbswikiutils.salttool.contrast' ] );
		$div = $tmpl->processTemplate( "Contrast", $data );
		$out->addHTML( $div );
	}
}
