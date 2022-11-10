<?php

namespace MediaWiki\Extension\MCBBSWiki;

use Skin;
use MediaWiki\Extension\MCBBSWiki\Tags;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Hook\SkinAddFooterLinksHook;

class Hooks implements ParserFirstCallInitHook,SkinAddFooterLinksHook
{
    public function onSkinAddFooterLinks(Skin $skin, string $key, array &$footerlinks)
    {
        if ($key === 'info') {
            $msg = $skin->msg('footerinfo');
            if (!$msg->isDisabled()) {
                $footerlinks['footerinfo'] = $msg->parse();
            }
        };
    }
    public function onParserFirstCallInit($parser)
    {
        $parser->setHook('ucenter-avatar', [Tags::class, 'renderTagUCenterAvatar']);
        $parser->setHook('mcbbs-credit', [Tags::class, 'renderTagMCBBSCredit']);
        $parser->setHook('bilibili', [Tags::class, 'renderTagBilibili']);
    }
}
