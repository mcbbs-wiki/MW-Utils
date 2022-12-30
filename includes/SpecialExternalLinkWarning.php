<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use OOUI\ButtonWidget;
use OOUI\HtmlSnippet;
use OOUI\MessageWidget;
use SpecialPage;

class SpecialExternalLinkWarning extends SpecialPage {
	public function __construct() {
		parent::__construct( 'ExternalLinkWarning', '', false );
	}

	public function execute( $par ) {
		$output = $this->getOutput();
		$request = $this->getRequest();
		$output->enableOOUI();
		$this->setHeaders();
		$base64link = $request->getText( 'wpURL' );
		if ( $base64link === '' ) { return;
		}
		$link = base64_decode( $base64link, true );
		if ( !$link ) { return;
		}
		$message = new MessageWidget( [
			'type' => 'warning',
			'label' => new HtmlSnippet( Html::rawElement( 'div',
			[ 'style' => 'line-height:30px;' ],
			$this->msg( 'externallinkwarning-info', $link )->text() ) )
		] );
		$go = new ButtonWidget( [
			'label' => $this->msg( 'externallinkwarning-go' )->text(),
			'flags' => [ 'primary', 'progressive' ],
			'href' => $link
		] );
		$output->addHTML( $message . '<br>' . $go );
	}
}
