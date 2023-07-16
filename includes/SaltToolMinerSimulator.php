<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

class SaltToolMinerSimulator implements ISaltTool {
	public function outHead( OutputPage $out, $arg, TemplateParser $tmpl ) {
	}

	public function outBody( OutputPage $out, $arg, TemplateParser $tmpl ) {
		$data = [
			'msg-salttoolbox-topnav-miner' => $out->msg( 'salttoolbox-topnav-miner' )->text(),
			'msg-salttoolbox-author' => $out->msg( 'salttoolbox-author' )->text(),
			'msg-salttoolbox-miner-simbtn' => $out->msg( 'salttoolbox-miner-simbtn' )->text(),
			'msg-salttoolbox-miner-s10btn' => $out->msg( 'salttoolbox-miner-s10btn' )->text()
		];
		$out->addModuleStyles( 'ext.mcbbswikiutils.salttool.miner.styles' );
		$out->addModules( 'ext.mcbbswikiutils.salttool.miner' );
		$div = $tmpl->processTemplate( "Miner", $data );
		$out->addHTML( $div );
	}
}
