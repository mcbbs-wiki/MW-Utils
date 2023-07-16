<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

class SaltToolWelcome implements ISaltTool {
	public function outHead( OutputPage $out, $arg, TemplateParser $tmpl ) {
	}

	public function outBody( OutputPage $out, $arg, TemplateParser $tmpl ) {
		$out->addHTML( $out->msg( 'salttoolbox-welcome' )->parse() );
	}
}
