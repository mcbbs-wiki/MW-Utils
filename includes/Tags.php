<?php
namespace MCBBSWiki;

use Parser;
use PPFrame;
use Html;

class Tags {
    public static function renderTagMCBBSAvatar($input,array $args,Parser $parser,PPFrame $frame){
        $uid=htmlspecialchars( $args['uid'] );
        $image = Html::element('img',['src'=>'https://www.mcbbs.net/uc_server/avatar.php?uid='.$uid.'&size=big','class'=>'mcbbs-avatar mcbbs-avatar-'.$uid],'');
        return $image;
    }
}
