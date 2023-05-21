<?php
namespace MediaWiki\Extension\MCBBSWiki;

use OutputPage;

class SaltToolWelcome implements ISaltTool {
	public function outHead( OutputPage $out ) {
	}

	public function outBody( OutputPage $out ) {
		$out->addHTML( $out->msg( 'salttoolbox-welcome' )->parse() );
	}
}
