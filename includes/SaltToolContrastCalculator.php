<?php
namespace MediaWiki\Extension\MCBBSWiki;

use Html;
use OutputPage;
class SaltToolContrastCalculator implements ISaltTool {
    public function outHead(OutputPage $out)
    {
        
    }
    public function outBody(OutputPage $out)
    {
        $out->addModules(['ext.mcbbswikiutils.salttool.contrast']);
        $out->addHTML(Html::element('div',['id'=>'saltContrastCalculator']));
    }
}