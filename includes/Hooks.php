<?php

namespace MCBBSWiki;

use Skin;
use Parser;
use MCBBSWiki\Tags;

class PageHooks
{
    public static function customFooter(Skin $skin, string $key, array &$footerlinks)
    {
        if ($key === 'info') {
            $msg = $skin->msg('footerinfo');
            if (!$msg->isDisabled()) {
                $footerlinks['footerinfo'] = $msg->parse();
            }
        };
    }
    public static function onParserFirstCallInit(Parser $parser)
    {
        $parser->setHook('mcbbs-avatar', [Tags::class, 'renderTagMCBBSAvatar']);
        $parser->setHook('bilibili', [Tags::class, 'renderTagBilibili']);
    }
}
