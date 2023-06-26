<?php

namespace MediaWiki\Extension\MCBBSWiki;

use SpecialPage;
use UnlistedSpecialPage;

class SpecialMCBBSCredit extends UnlistedSpecialPage {
	public function __construct( ) {
		parent::__construct( 'MCBBSCredit' );
	}

	public function execute( $par ) {
		$output = $this->getOutput();
		$request = $this->getRequest();
		if ( $par ) {
			$output->redirect( SpecialPage::getTitleFor("SaltToolbox/credit")->getLinkURL([
				'wpUID'=>$par
			]));
			return;
		}
		$uid = $request->getText( 'wpUID' );
		$output->redirect( SpecialPage::getTitleFor("SaltToolbox/credit")->getLinkURL([
			'wpUID'=>$uid
		]) );
	}
}
