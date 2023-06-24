<?php
use MediaWiki\Extension\MCBBSWiki\MCBBSCredit;
use MediaWiki\MediaWikiServices;

return [
	'MBWUtils.MCBBSCredit' => static function ( MediaWikiServices $services ): MCBBSCredit {
		return new MCBBSCredit(
			$services->getDBLoadBalancer(),
			$services->getMainWANObjectCache(),
			$services->getHttpRequestFactory()
		);
	}
];
