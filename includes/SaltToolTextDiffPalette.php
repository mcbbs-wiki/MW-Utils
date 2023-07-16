<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

class SaltToolTextDiffPalette implements ISaltTool {
	public function outHead( OutputPage $out, $arg, TemplateParser $tmpl ) {
	}

	public function outBody( OutputPage $out, $arg, TemplateParser $tmpl ) {
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.textdiff.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.textdiff' );
		$data = [
			'msg-salttoolbox-textdiff-aboutlevenshtein' => $out->msg( 'salttoolbox-textdiff-aboutlevenshtein' )->text(),
			'msg-salttoolbox-textdiff-prefix' => $out->msg( 'salttoolbox-textdiff-prefix' )->text(),
			'msg-salttoolbox-textdiff-ready' => $out->msg( 'salttoolbox-textdiff-ready' )->text(),
			'msg-salttoolbox-textdiff-start' => $out->msg( 'salttoolbox-textdiff-start' )->text(),
			'msg-salttoolbox-textdiff-switch' => $out->msg( 'salttoolbox-textdiff-switch' )->text()
		];
		$div = $tmpl->processTemplate( "TextDiff", $data );
		$out->addHTML( $div );
	}
}
