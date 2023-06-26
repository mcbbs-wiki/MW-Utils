<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Exception;
use FormatJson;
use MediaWiki\Http\HttpRequestFactory;
use MWTimestamp;
use WANObjectCache;
use Wikimedia\Rdbms\ILoadBalancer;

class MCBBSCredit {
	public static $userGroupRegex = '/<em class="xg1">用户组&nbsp;&nbsp;<\/em><span[\s\S]+?><a href="home\.php\?mod=spacecp&amp;ac=usergroup&amp;gid=([0-9]+)" target="_blank">([\s\S]+?)<\/a>/';
	public static $errClass = 'alert_error';
	public static $userNameRegex = '/<h2 class="mt">\n([\s\S]+?)<\/h2>/';
	public static $userCreditRegex = "/<li><em>积分<\/em>(-?[0-9]+)<\/li><li><em>人气<\/em>(-?[0-9]+) 点<\/li>\n<li><em>金粒<\/em>(-?[0-9]+) 粒<\/li>\n<li><em>金锭\[已弃用\]<\/em>(-?[0-9]+) 块<\/li>\n<li><em>宝石<\/em>(-?[0-9]+) 颗<\/li>\n<li><em>下界之星<\/em>(-?[0-9]+) 枚<\/li>\n<li><em>贡献<\/em>(-?[0-9]+) 份<\/li>\n<li><em>爱心<\/em>(-?[0-9]+) 心<\/li>\n<li><em>钻石<\/em>(-?[0-9]+) 颗<\/li>/";
	public static $postRegex = '/ target="_blank">回帖数 (-?[0-9]+?)<\/a>/';
	public static $threadRegex = '/ target="_blank">主题数 (-?[0-9]+?)<\/a>/';
	public static $fontRegex = '/<font .*?>(.*?)<\/font>/';

	private ILoadBalancer $lb;
	private WANObjectCache $cache;
	private HttpRequestFactory $http;

	public function __construct(
		ILoadBalancer $lb,
		WANObjectCache $cache,
		HttpRequestFactory $http ) {
		$this->lb = $lb;
		$this->cache = $cache;
		$this->http = $http;
	}

	public function getUserInfo( int $uid = 3038 ) {
		$userObj = $this->getUserInfoFromCache( $uid );
		if ( $userObj === null ) {
			$userObj = $this->getUserInfoFromWEB( $uid );
			if ( $userObj === null ) {
				$userObj = $this->getUserInfoFromDB( $uid );
				if ( $userObj === null ) {
					wfDebugLog( 'bbsuser', "Fetch user $uid failed" );
					return null;
				} else {
					wfDebugLog( 'bbsuser', "Fetch user $uid from fallback db" );
				}
			} else {
				wfDebugLog( 'bbsuser', "Fetch user $uid from bbs" );
			}
		} else {
			wfDebugLog( 'bbsuser', "Fetch user $uid from cache" );
		}
		return $userObj;
	}

	private function getUserInfoFromWEB( int $uid ) {
		$user = [];
		$user['uid'] = $uid;
		$user['update'] = MWTimestamp::getLocalInstance()->format( "Y-m-d H:i:s" );
		$userCacheKey = $this->cache->makeKey( 'bbsuser', $uid );
		$doc = $this->fetchUserDoc( $uid );
		if ( $doc === null ) {
			return null;
		}
		if ( strpos( $doc, self::$errClass ) ) {
			$user['notfound'] = true;
			$this->cache->set( $userCacheKey, $user,21600 );
			$this->writeDBUserCredit( $user );
			return $user;
		}
		$user['notfound'] = false;
		preg_match( self::$userNameRegex, $doc, $matchName );
		$user['nickname'] = $matchName[1];
		$this->getUserCredit( $doc, $user );
		$this->getUserActivities( $doc, $user );
		$this->cache->set( $userCacheKey, $user,10800 );
		$this->writeDBUserCredit( $user );
		return $user;
	}

	private function getUserInfoFromCache( int $uid ) {
		$userCacheKey = $this->cache->makeKey( 'bbsuser', $uid );
		$cacheObj = $this->cache->get( $userCacheKey );
		if ( $cacheObj === false ) {
			return null;
		}
		return $cacheObj;
	}

	private function getUserInfoFromDB( int $uid ) {
		$userCacheKey = $this->cache->makeKey( 'bbsuser', $uid );
		$dbr = $this->lb->getConnection( DB_REPLICA );
		$data = $dbr->newSelectQueryBuilder()
			->select( [ 'mbwuc_data' ] )
			->from( 'mbw_usercredit' )
			->where( [ 'mbwuc_id' => $uid ] )
			->caller( __METHOD__ )
			->fetchField();
		if ( $data === false ) {
			return null;
		}
		$user = FormatJson::decode( $data, true );
		$this->cache->set( $userCacheKey, $user,5400 );
		return $user;
	}

	private function fetchUserDoc( int $uid ): string|null {
		$req = $this->http->create( "https://www.mcbbs.net/home.php?mod=space&uid={$uid}" );
		try{
			$status = $req->execute();
		} catch ( Exception $e ) {
			wfDebugLog( 'bbsuser', 'Failed to fetch user: ' . $e->getMessage() );
			return null;
		}
		if ( !$status->isOK() ) {
			wfDebugLog( 'bbsuser', 'Failed to fetch user: ' . $status->getErrorsArray()[0][0] );
			return null;
		}
		if ( $req->getStatus() !== 200 ) {
			return null;
		}
		return $req->getContent();
	}

	private function getUserActivities( string $doc, &$user ) {
		preg_match( self::$postRegex, $doc, $matchPost );
		preg_match( self::$threadRegex, $doc, $matchThread );
		preg_match( self::$userGroupRegex, $doc, $matchGroup );
		preg_match( self::$fontRegex, $matchGroup[2], $matchGroupText );
		$activities = [];
		$activities['post'] = intval( $matchPost[1] );
		$activities['thread'] = intval( $matchThread[1] );
		$activities['digiest'] = $this->calcDigiest( $user['credit'], $activities['post'], $activities['thread'] );
		$activities['currentGroupID'] = intval( $matchGroup[1] );
		$activities['currentGroupText'] = empty( $matchGroupText ) ? $matchGroup[2] : $matchGroupText[1];
		$user['activities'] = $activities;
	}

	private function getUserCredit( string $doc, &$user ) {
		preg_match( self::$userCreditRegex, $doc, $matchCredit );
		$credit = [];
		$credit['credit'] = intval( $matchCredit[1] );
		$credit['popularity'] = intval( $matchCredit[2] );
		$credit['nugget'] = intval( $matchCredit[3] );
		$credit['ingot'] = intval( $matchCredit[4] );
		$credit['gem'] = intval( $matchCredit[5] );
		$credit['star'] = intval( $matchCredit[6] );
		$credit['contribute'] = intval( $matchCredit[7] );
		$credit['heart'] = intval( $matchCredit[8] );
		$credit['diamond'] = intval( $matchCredit[9] );
		$user['credits'] = $credit;
	}

	private function writeDBUserCredit( $user ) {
		$user['fallback'] = true;
		$uid = $user['uid'];
		$dbw = $this->lb->getConnection( DB_PRIMARY );
		$data = $dbw->newSelectQueryBuilder()
			->select( [ 'mbwuc_id' ] )
			->from( 'mbw_usercredit' )
			->where( [ 'mbwuc_id' => $user['uid'] ] )
			->caller( __METHOD__ )
			->fetchField();
		if ( $data === false ) {
			$dbw->insert( 'mbw_usercredit', [ 'mbwuc_id' => $uid,'mbwuc_data' => FormatJson::encode( $user ) ] );
		} else {
			$dbw->update( 'mbw_usercredit', [ 'mbwuc_data' => FormatJson::encode( $user ) ], [ 'mbwuc_id' => $uid ] );
		}
	}

	private function calcDigiest( $credit, int $post, int $thread ) {
		$totalPost = floor( ( $post + $thread ) / 3 );
		$temp1 = $totalPost + $thread * 2 + $credit['heart'] * 4 +
			$credit['diamond'] * 2 + $credit['contribute'] * 10 + $credit['popularity'] * 3;
		$digiest = floor( ( $credit['credit'] - $temp1 ) / 45 );
		return $digiest > 0 ? intval( $digiest ) : 0;
	}
}
