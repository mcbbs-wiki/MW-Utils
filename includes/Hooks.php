<?php
namespace MCBBSWiki;

use Skin;
use Html;
class PageHooks {
    public static function customFooter( Skin $skin, string $key, array &$footerlinks  ) {
        if ( $key === 'places' ) {
            //WIP
        };
        if ( $key === 'info' ) {
            $footerlinks['hello'] = $skin->msg( 'footerinfo' );
        };
    }
}

