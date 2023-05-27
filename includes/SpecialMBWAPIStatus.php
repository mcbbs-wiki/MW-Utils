<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Exception;
use Html;
use MediaWiki\MediaWikiServices;
use OOUI\MessageWidget;
use SpecialPage;

class SpecialMBWAPIStatus extends SpecialPage {
	public function __construct() {
		parent::__construct( 'MBWAPIStatus' );
	}

	public function execute( $par ) {
		$apiStatus = $this->testAPIStatus();
		$apiUserStatus = $this->testAPIUserStatus();
		$this->setHeaders();
		$out = $this->getOutput();
		$out->enableOOUI();
		if ( $apiStatus === false ) {
			$apiInfo = new MessageWidget( [
				'type' => 'error',
				'inline' => 'true',
				'label' => $this->msg( 'mbwapi-fail' )->text()
			] );
		} else {
			$apiInfo = new MessageWidget( [
				'type' => 'success',
				'inline' => 'true',
				'label' => $this->msg( 'mbwapi-ok' )->text()
			] );
		}
		if ( $apiUserStatus === false ) {
			$apiUserInfo = new MessageWidget( [
				'type' => 'error',
				'inline' => 'true',
				'label' => $this->msg( 'mbwapi-user-fail' )->text()
			] );
		} else {
			$apiUserInfo = new MessageWidget( [
				'type' => 'success',
				'inline' => 'true',
				'label' => $this->msg( 'mbwapi-user-ok' )->text()
			] );
		}
		$out->addHTML( Html::element( 'p', [], $this->msg( 'mbwapi-status' )->text() ) );
		$out->addHTML( $apiInfo . $apiUserInfo );
	}

	private function testAPIStatus() {
		$apiurl = $this->getConfig()->get( 'MBWAPIURL' );
		$statusRequest = MediaWikiServices::getInstance()->getHttpRequestFactory()->create( $apiurl );
		$apiStatus = true;
		try{
			$apiResstatus = $statusRequest->execute();
		} catch ( Exception $e ) {
			$apiStatus = false;
		}
		if ( !$apiResstatus->isOK() || $statusRequest->getStatus() !== 200 ) {
			$apiStatus = false;
		}
		return $apiStatus;
	}

	private function testAPIUserStatus() {
		$apiurl = $this->getConfig()->get( 'MBWAPIURL' );
		$statusRequest = MediaWikiServices::getInstance()->getHttpRequestFactory()->create( $apiurl . 'users/' . '3038' );
		$apiStatus = true;
		try{
			$apiResstatus = $statusRequest->execute();
		} catch ( Exception $e ) {
			$apiStatus = false;
		}
		if ( !$apiResstatus->isOK() || $statusRequest->getStatus() !== 200 ) {
			$apiStatus = false;
		}
		return $apiStatus;
	}
}
