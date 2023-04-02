<?php

namespace MediaWiki\Extension\MCBBSWiki;

use UnlistedSpecialPage;

class SpecialVME50 extends UnlistedSpecialPage {
	public function __construct() {
		parent::__construct( 'VME50' );
	}

	public function execute( $par ) {
		throw new VME50Exception();
	}
}
