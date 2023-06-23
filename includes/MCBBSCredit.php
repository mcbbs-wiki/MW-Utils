<?php
class MCBBSCredit {
    public $userGroupRegex ='/<em class="xg1">用户组&nbsp;&nbsp;<\/em><span[\s\S]+?><a href="home\.php\?mod=spacecp&amp;ac=usergroup&amp;gid=([0-9]+)" target="_blank">([\s\S]+?)<\/a>/';
    public $errRegex = '/<div id="messagetext" class="alert_error">/';
    public $userNameRegex = '/<h2 class="mt">\n([\s\S]+?)<\/h2>/';
}