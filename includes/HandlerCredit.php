<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Rest\SimpleHandler;
use Wikimedia\ParamValidator\ParamValidator;

class HandlerCredit extends SimpleHandler {
	private MCBBSCredit $credit;

	public function __construct( MCBBSCredit $credit ) {
		$this->credit = $credit;
	}

	public function run( $uid ) {
		$user= $this->credit->getUserInfo( $uid );
		if($user===null){
			return $this->getResponseFactory()->createHttpError(500);
		}
		if($user['notfound']){
			return $this->getResponseFactory()->createHttpError(404,$user);
		} else {
			return $this->getResponseFactory()->createJson($user);
		}
	}

	public function needsWriteAccess() {
		return false;
	}

	public function getParamSettings() {
		return [
			'uid' => [
				self::PARAM_SOURCE => 'path',
				ParamValidator::PARAM_TYPE => 'integer',
				ParamValidator::PARAM_REQUIRED => true,
			]
		];
	}
}
