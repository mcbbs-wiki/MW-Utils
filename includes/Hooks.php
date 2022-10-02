<?php
namespace MCBBSWiki;

use Skin;
use Html;
class PageHooks {
    public static function customFooter( Skin $skin, string $key, array &$footerlinks  ) {
        if ( $key === 'places' ) {
        //echo var_dump($footerLinks);
            $footerlinks['git'] = Html::element( 'a',
                [
                    'href' => 'https://github.com/mcbbs-wiki',
                    'rel' => 'noreferrer noopener'
                ],
            'Github'
            );
            $footerlinks['status'] = Html::element( 'a',
                [
                    'href' => 'https://stats.uptimerobot.com/DZLkwIoKBB',
                    'rel' => 'noreferrer noopener'
                ],
            '服务状态'
            );
        };
        if ( $key === 'info' ) {
            $footerlinks['hello'] = $skin->msg( 'footerinfo' );
        };
    }
}

