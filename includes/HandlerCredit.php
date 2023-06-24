<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Rest\SimpleHandler;
use Wikimedia\ParamValidator\ParamValidator;

class HandlerCredit extends SimpleHandler {
    private MCBBSCredit $credit;
    public function __construct(MCBBSCredit $credit)
    {
        $this->credit=$credit;
    }
    public function run( $uid ) {
        return $this->credit->getUserInfo($uid);
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