<?php
namespace MediaWiki\Extension\MCBBSWiki;
use OutputPage;
class SaltToolWelcome implements ISaltTool {
    public function outHead(OutputPage $out)
    {
        $out->addHTML('<p>Welcome head</p>');
    }
    public function outBody(OutputPage $out)
    {
        $out->addHTML('<p>Welcome body</p>');
    }
}