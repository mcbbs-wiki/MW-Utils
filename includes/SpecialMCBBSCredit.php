<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use HTMLForm;
use SpecialPage;

class SpecialMCBBSCredit extends SpecialPage {
	public function __construct() {
		parent::__construct( 'MCBBSCredit' );
	}

	public function execute( $par ) {
		$output = $this->getOutput();
		$request = $this->getRequest();
		if ($par) {
			$output->redirect($this->getPageTitle()->getLinkURL([
				'wpUID' => $par,
			]));
			return;
		}
		$uid = $request->getInt( 'wpUID' );
		$hasuid = $uid !== 0;
		$output->enableOOUI();
		$this->setHeaders();
		$formDescriptor = [
			'uid' => [
				'type' => 'number',
				'name' => 'wpUID',
				'exists' => true,
				'class' => 'HTMLTextField',
				'label-message' => 'mcbbscredit-input-uid',
				'required' => true,
				'default' => $hasuid ? $uid : ''
			]
		];
		$form = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$form
			->setMethod( 'get' )
			->setWrapperLegendMsg( 'mcbbscredit-form-legend' )
			->prepareForm()
			->displayForm( false );
		$html = '';
		if ( $hasuid ) {
			$output->addModules( 'ext.mcbbswikiutils.credit' );
			$html .= Html::element( 'div', [
				'class' => 'userpie',
				'data-uid' => $uid
			], $this->msg( 'mcbbscredit-loading' )->text() );
		}

		$output->addHTML( $html );
	}
}
