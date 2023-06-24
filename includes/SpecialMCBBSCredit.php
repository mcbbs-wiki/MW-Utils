<?php

namespace MediaWiki\Extension\MCBBSWiki;

use FormatJson;
use Html;
use HTMLForm;
use SpecialPage;

class SpecialMCBBSCredit extends SpecialPage {
	private MCBBSCredit $credit;
	public function __construct(MCBBSCredit $credit) {
		parent::__construct( 'MCBBSCredit' );
		$this->credit=$credit;
	}

	public function execute( $par ) {
		$output = $this->getOutput();
		$request = $this->getRequest();
		if ( $par ) {
			$output->redirect( $this->getPageTitle()->getLinkURL( [
				'wpUID' => $par,
			] ) );
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
		if ( $hasuid ) {
			$output->addModules( [ 'ext.mcbbswikiutils.credit' ] );
			$user=$this->credit->getUserInfo($uid);
			if ( isset( $args['mili'] ) ) {
				$html= Html::element( 'strong', [ 'class' => 'error' ], wfMessage( 'mcbbscredit-notfound' )->text() );
			}
			$userJson = FormatJson::encode($user);
			$html = Html::element( 'div', [
				'class' => 'userpie',
				'data-user' => $userJson
			], $this->msg( 'mcbbscredit-loading' )->text() );
		}

		$output->addHTML( $html );
	}
}
