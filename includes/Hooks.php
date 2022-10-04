<?php
namespace MCBBSWiki;

use Skin;
use Parser;
use Tags;
class PageHooks {
    public static function customFooter( Skin $skin, string $key, array &$footerlinks  ) {
        if ( $key === 'info' ) {
            $footerlinks['hello'] = $skin->msg( 'footerinfo' )->parse();
        };
    }
    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook('mcbbs-avatar',[Tags::class,'renderTagMCBBSAvatar']);
    }
}
