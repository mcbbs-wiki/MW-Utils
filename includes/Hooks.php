<?php

namespace MediaWiki\Extension\MCBBSWiki;

use ConfigFactory;
use Exception;
use FormatJson;
use Html;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;
use MediaWiki\Http\HttpRequestFactory;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use Parser;
use PPFrame;
use Skin;
use WANObjectCache;

class Hooks implements ParserFirstCallInitHook, SkinAddFooterLinksHook, BeforePageDisplayHook {
	private string $ucenter;
	private string $apiurl;
	private $whitelistDomains;
	private string $appver;
	private WANObjectCache $cache;
	private HttpRequestFactory $http;
	private function checkDomain( string $url ) {
		$domain = parse_url( $url, PHP_URL_HOST );
	
		// Check if we match the domain exactly
		if ( in_array( $domain, $this->whitelistDomains ) )
			return true;
	
		$valid = false;
	
		foreach( $this->whitelistDomains as $whitelistDomain ) {
			$whitelistDomain = '.' . $whitelistDomain; // Prevent things like 'evilsitetime.com'
			if( strpos( $domain, $whitelistDomain ) === ( strlen( $domain ) - strlen( $whitelistDomain ) ) ) {
				$valid = true;
				break;
			}
		}
		return $valid;
	}
	public function __construct( ConfigFactory $configFactory, HttpRequestFactory $http, WANObjectCache $cache) {
		$config = $configFactory->makeConfig( 'MCBBSWikiUtils' );
		$this->whitelistDomains = $config->get("ExtImgWhiteList");
		$this->ucenter = $config->get( 'UCenterURL' );
		$this->http = $http;
		$this->cache = $cache;
		$this->appver = $config->get('MBWVER');
		$this->apiurl = $config->get('MBWAPIURL');
	}

	public function onSkinAddFooterLinks( Skin $skin, string $key, array &$footerlinks ) {
		if ( $key === 'info' ) {
			$msg = $skin->msg( 'footerinfo' );
			if ( !$msg->isDisabled() ) {
				$footerlinks['footerinfo'] = $msg->parse();
			}
		}
	}
	public function onBeforePageDisplay( $out, $skin ):void
	{
		$out->addJsConfigVars( 'wgMBWVER', $this->appver );
	}
	public function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'ucenter-avatar', [ $this,'renderTagUCenterAvatar' ] );
		$parser->setHook( 'mcbbs-credit',  [ $this,'renderTagMCBBSCredit' ] );
		$parser->setHook( 'bilibili',  [ $this,'renderTagBilibili' ] );
		$parser->setHook( 'ext-img',  [ $this,'renderTagExtimg' ] );
		$parser->setFunctionHook('mcbbscreditvalue',[$this,'renderCreditValue']);
	}
	public function renderCreditValue(Parser $parser, $uid = '3038', $data = 'diamond')
	{
		$user=$this->getBBSUserJson($uid);
		wfDebugLog('bbsuser',"Fetch user $uid $data");
		$value=$this->getBBSUserValue($user,$data);
		if($value===false){
			return 0;
		}
		return $value;
	}
	private function getBBSUserValue($userJson,$data='username'){
		$user = FormatJson::decode( $userJson, true );
		if ( !$user ) {
			wfDebugLog('bbsuser','Failed to parse user');
		}
		switch ($data) {
			case 'uid':
				return $user['uid'];
			case 'nickname':
				return $user['nickname'];
			case 'post':
				return $user['activites']['post'];
			case 'thread':
				return $user['activites']['thread'];
			case 'groupid':
				return $user['activites']['currentGroupID'];
			case 'grouptext':
				return $user['activites']['currentGroupText'];
			case 'digiest':
				return $user['activites']['digiest'];
			case 'diamond':
				return $user['credits']['diamond'];
			case 'popularity':
				return $user['credits']['popularity'];
			case 'heart':
				return $user['credits']['heart'];
			case 'contribute':
				return $user['credits']['contribute'];
			case 'gem':
				return $user['credits']['gem'];
			case 'star':
				return $user['credits']['star'];
			case 'nugget':
				return $user['credits']['nugget'];
			case 'ingot':
				return $user['credits']['ingot'];
			case 'credit':
				return $user['credits']['credit'];
			default:
				return false;
		}
	}
	private function getBBSUserJson($uid){
		$userCacheKey=$this->cache->makeKey('bbsuser-'.$uid);
		$userJson=$this->cache->get($userCacheKey);
		if(!$userJson){
			wfDebugLog('bbsuser',"Fetch user $uid from API");
			$userJson=$this->getBBSUserFromAPI($uid);
			if($userJson===false){
				return false;
			}
			$this->cache->set($userCacheKey,$userJson,10800);
		} else {
			wfDebugLog('bbsuser',"Fetch user $uid from cache");
		}
		return $userJson;
	}
	private function getBBSUserFromAPI($uid){
		$request=$this->http->create($this->apiurl.$uid);
		try{
			$status = $request->execute();
		} catch (Exception $e){
			wfDebugLog('bbsuser','Failed to fetch user: '.$e->getMessage());
			return false;
		}
		if ( !$status->isOK() ) {
			wfDebugLog('bbsuser','Failed to fetch user: '.$status->getErrorsArray()[0][0]);
			return false;
		}
		if ($request->getStatus()===500 && $request->getStatus()===404){
			return false;
		}
		return $request->getContent();
	}
	public function renderTagExtimg( $input, array $args, Parser $parser, PPFrame $frame ) {
		if(!isset($args['src'])){
			return '';
		}
		if(!$this->checkDomain($args['src'])){
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight: bold;' ],
			 wfMessage( 'extimg-invalidurl' )->text() );
		}
		$image = Html::element( 'img', [
			'src' => $args['src'],
			'title' => $args['title'] ?? null,
			'alt' => $args['alt'] ?? null,
			'width'=>$args['width'] ?? null,
			'height'=>$args['height'] ?? null,
			'class' => "ext-img"
		], '' );
		return $image;
	}
	public function renderTagUCenterAvatar( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModuleStyles( [ 'ext.mcbbswikiutils.avatar' ] );
		if ( isset( $args['mili'] ) ) {
			return Html::element( 'p', [ 'class' => 'mili' ], '迷离可爱！' );
		}
		$ucenter = $this->ucenter;
		if ( empty( $ucenter ) ) {
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight:bold;' ],
			 wfMessage( 'ucenteravatar-noucenterurl' )->text() );
		}
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$image = Html::element( 'img', [
			'src' => "$ucenter/avatar.php?uid=$uid&size=big",
			'class' => "ucenter-avatar ucenter-avatar-$uid",
			'data-uid' => $uid
		], '' );
		return $image;
	}

	public function renderTagMCBBSCredit( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parser->getOutput()->addModules( [ 'ext.mcbbswikiutils.credit-loader' ] );
		$uid = isset( $args['uid'] ) ? htmlspecialchars( $args['uid'] ) : '1';
		$userJson = $this->getBBSUserJson($uid);
		if($userJson === false) {
			return Html::element('strong',['class'=>'error'],wfMessage( 'mcbbscredit-notfound' )->text());
		}
		$credit = Html::element( 'div', [
			'class' => 'userpie',
			'data-user' => $userJson
		], wfMessage( 'mcbbscredit-loading' )->text() );
		return $credit;
	}

	public function renderTagBilibili( $input, array $args, Parser $parser, PPFrame $frame ) {
		$attr = [
			'allowfullscreen' => 'true',
			'frameborder' => '0',
			'framespacing' => '0',
			'sandbox' => 'allow-top-navigation allow-same-origin allow-forms allow-scripts allow-popups',
			'scrolling' => 'no',
			'border' => '0',
			'width' => isset( $args['width'] ) ? htmlspecialchars( $args['width'] ) : '800',
			'height' => isset( $args['height'] ) ? htmlspecialchars( $args['height'] ) : '600'
		];
		if ( isset( $args['bv'] ) ) {
			$bvid = htmlspecialchars( $args['bv'] );
			$src = "https://player.bilibili.com/player.html?bvid=$bvid&high_quality=1";
			$attr['src'] = $src;
			$video = Html::element( 'iframe', $attr, '' );
			return Html::rawElement( 'div', [ 'class' => "bilibili-video bilibili-video-$bvid" ], $video );
		} else {
			return Html::element( 'p',
			[ 'style' => 'color:#d33;font-size:larger;font-weight:bold;' ],
			wfMessage( 'bilibili-nobvid' )->text() );
		}
	}
}
