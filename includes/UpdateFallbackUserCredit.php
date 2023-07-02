<?php
namespace MediaWiki\Extension\MCBBSWiki;

use DeferrableUpdate;
use FormatJson;
use Wikimedia\Rdbms\ILoadBalancer;

class UpdateFallbackUserCredit implements DeferrableUpdate {
	private $lb;
	private $user;

	public function __construct( ILoadBalancer $lb, $user ) {
		$this->lb = $lb;
		$this->user = $user;
	}

	public function doUpdate() {
		$this->user['fallback'] = true;
		$uid = $this->user['uid'];
		$dbw = $this->lb->getConnection( DB_PRIMARY );
		$data = $dbw->newSelectQueryBuilder()
			->select( [ 'mbwuc_id' ] )
			->from( 'mbw_usercredit' )
			->where( [ 'mbwuc_id' => $this->user['uid'] ] )
			->caller( __METHOD__ )
			->fetchField();
		if ( $data === false ) {
			$dbw->insert( 'mbw_usercredit', [ 'mbwuc_id' => $uid,'mbwuc_data' => FormatJson::encode( $this->user ) ] );
		} else {
			$dbw->update( 'mbw_usercredit', [ 'mbwuc_data' => FormatJson::encode( $this->user ) ], [ 'mbwuc_id' => $uid ] );
		}
	}
}
