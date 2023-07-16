<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

class SaltToolWealthSimulator implements ISaltTool {
	public function outHead( OutputPage $out, $arg, TemplateParser $tmpl ) {
	}

	public function outBody( OutputPage $out, $arg, TemplateParser $tmpl ) {
		$data = [
			'msg-salttoolbox-topnav-wealth' => $out->msg( 'salttoolbox-topnav-wealth' )->text(),
			'msg-salttoolbox-author' => $out->msg( 'salttoolbox-author' )->text(),
			'msg-salttoolbox-wealth-simipt' => $out->msg( 'salttoolbox-wealth-simipt' )->text(),
			'msg-salttoolbox-wealth-simbtn' => $out->msg( 'salttoolbox-wealth-simbtn' )->text(),
			'msg-salttoolbox-wealth-clsbtn' => $out->msg( 'salttoolbox-wealth-clsbtn' )->text(),
			'msg-salttoolbox-wealth-resshow' => $out->msg( 'salttoolbox-wealth-resshow' )->text()
		];
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.wealth.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.wealth' );
		$div = $tmpl->processTemplate( 'Wealth', $data );
		$out->addHTML( $div );
	}
}
