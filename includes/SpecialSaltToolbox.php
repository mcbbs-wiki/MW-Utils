<?php
namespace MediaWiki\Extension\MCBBSWiki;
use IncludableSpecialPage;
use HtmlArmor;
use Xml;
use TitleValue;
class SpecialSaltToolbox extends IncludableSpecialPage {
    /**@var ISaltTool[] */
    private $tools;
    public function __construct()
    {
        parent::__construct('SaltToolbox');
        $this->tools = [
            'home'=> new SaltToolWelcome(),
            'miner'=>new SaltToolMinerSimulator(),
            'acquire'=>new SaltToolAcquireWealthSimulator(),
            'textdiff'=>new SaltToolTextDiffPalette(),
            'contrast'=>new SaltToolContrastCalculator()
        ];
    }
    public function execute($arg){
        $this->setHeaders();
        $toolId='';
        $toolObj = null;
        foreach ($this->tools as $key => $value) {
            if($arg===$key){
                $toolId=$key;
                $toolObj = $value;
            }
        }
        if($toolObj===null){
            $toolObj=new SaltToolWelcome();
            $toolId='home';
        }
        if(!$this->including()){
            $this->addNavigationLinks($toolId);
            $toolObj->outHead($this->getOutput());
        }
        $toolObj->outBody($this->getOutput());
    }
    protected function addNavigationLinks( $pageType ) {
		$links = [];

		foreach ( $this->tools as $name=>$page ) {
			$msgName = "salttoolbox-topnav-$name";

			$msg = $this->msg( $msgName )->parse();

			if ( $name === $pageType ) {
				$links[] = Xml::tags( 'strong', null, $msg );
			} else {
				$links[] = $this->getLinkRenderer()->makeLink(
					new TitleValue( NS_SPECIAL, 'SaltToolbox/'.$name ),
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