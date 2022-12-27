<?php

namespace MediaWiki\Extension\MCBBSWiki;

use SpecialPage;

class SpecialVME50 extends SpecialPage {
	public function __construct() {
		parent::__construct( 'VME50', '', false );
	}

	public function execute( $par ) {
		throw new VME50Exception();
	}
}
