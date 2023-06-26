<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\MediaWikiServices;
use OutputPage;
use HTMLForm;
use Html;
use FormatJson;
use SpecialPage;

class SaltToolCredit implements ISaltTool{
    public function outHead( OutputPage $out,$arg ) {
	}

	public function outBody( OutputPage $out,$arg ) {
        if ( $arg ) {
			$out->redirect( SpecialPage::getTitleFor("SaltToolbox/credit")->getLinkURL( [
				'wpUID' => $arg,
			] ) );
			return;
		}
        $uid=$out->getRequest()->getInt( 'wpUID' );
        $hasuid = $uid !== 0;
        $out->enableOOUI();
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
		$form = HTMLForm::factory( 'ooui', $formDescriptor, $out->getContext() );
		$form
			->setMethod( 'get' )
			->setWrapperLegendMsg( 'mcbbscredit-form-legend' )
			->prepareForm()
			->displayForm( false );
        if ( $hasuid ) {
            /**@var MCBBSCredit */
            $credit=MediaWikiServices::getInstance()->getService("MBWUtils.MCBBSCredit");
			$out->addModules( [ 'ext.mcbbswikiutils.credit' ] );
			$user = $credit->getUserInfo( $uid );
			if ( $user === null ) {
				$html = Html::element( 'strong', [ 'class' => 'error' ], $out->msg( 'mcbbscredit-notfound' )->text() );
			}
			$userJson = FormatJson::encode( $user );
			$html = Html::element( 'div', [
				'class' => 'userpie',
				'data-user' => $userJson
			], $out->msg( 'mcbbscredit-loading' )->text() );
		}
        $out->addHTML( $html );
    }
}