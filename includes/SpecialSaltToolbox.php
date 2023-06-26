<?php
namespace MediaWiki\Extension\MCBBSWiki;

use HtmlArmor;
use IncludableSpecialPage;
use TitleValue;
use Xml;

class SpecialSaltToolbox extends IncludableSpecialPage {
	/** @var ISaltTool[] */
	private $tools;

	public function __construct() {
		parent::__construct( 'SaltToolbox' );
		$this->tools = [
			'home' => new SaltToolWelcome(),
			'credit' => new SaltToolCredit(),
			'miner' => new SaltToolMinerSimulator(),
			'wealth' => new SaltToolWealthSimulator(),
			'contrast' => new SaltToolContrastCalculator(),
			'textdiff' => new SaltToolTextDiffPalette(),
		];
	}

	public function execute( $par ) {
		$this->setHeaders();
		$toolId = '';
		$toolObj = null;
		$args=explode("/",$par,2);
		$arg=$args[0]??null;
		foreach ( $this->tools as $key => $value ) {
			if ( $arg === $key ) {
				$toolId = $key;
				$toolObj = $value;
			}
		}
		if ( $toolObj === null ) {
			$toolObj = new SaltToolWelcome();
			$toolId = 'home';
		}
		if ( !$this->including() ) {
			$this->addNavigationLinks( $toolId );
			$toolObj->outHead( $this->getOutput(),$args[1]??null );
		}
		$toolObj->outBody( $this->getOutput(),$args[1]??null );
	}

	protected function addNavigationLinks( $pageType ) {
		$links = [];

		foreach ( $this->tools as $name => $page ) {
			$msgName = "salttoolbox-topnav-$name";

			$msg = $this->msg( $msgName )->parse();

			if ( $name === $pageType ) {
				$links[] = Xml::tags( 'strong', null, $msg );
			} else {
				$links[] = $this->getLinkRenderer()->makeLink(
					new TitleValue( NS_SPECIAL, 'SaltToolbox/' . $name ),
					new HtmlArmor( $msg )
				);
			}
		}

		$linkStr = $this->msg( 'parentheses' )
			->rawParams( $this->getLanguage()->pipeList( $links ) )
			->text();
		$linkStr = $this->msg( 'salttoolbox-topnav' )->parse() . " $linkStr";

		$linkStr = Xml::tags( 'div', [ 'class' => 'mw-salttoolbox-navigation' ], $linkStr );

		$this->getOutput()->setSubtitle( $linkStr );
	}
}
