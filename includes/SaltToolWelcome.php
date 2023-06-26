<?php
namespace MediaWiki\Extension\MCBBSWiki;

use OutputPage;

class SaltToolWelcome implements ISaltTool {
	public function outHead( OutputPage $out,$arg ) {
	}

	public function outBody( OutputPage $out,$arg ) {
		$out->addHTML( $out->msg( 'salttoolbox-welcome' )->parse() );
	}
}
