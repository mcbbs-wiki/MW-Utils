<?php

namespace MCBBSWiki;

use SpecialPage;
use Html;
use OOUI;

class SpecialMCBBSCredit extends SpecialPage
{
    function __construct()
    {
        parent::__construct('MCBBSCredit');
    }
    function execute($par)
    {
        $output = $this->getOutput();
        $request = $this->getRequest();
        $uid = $request->getText('uid', '-1');
        $hasuid = $uid !== '-1';
        $output->enableOOUI();
        $this->setHeaders();
        $action = new OOUI\ActionFieldLayout(
            new OOUI\NumberInputWidget([
                'name' => 'uid',
                'placeholder' => $this->msg('mcbbscredit-input-uid')->text(),
                'value' => $hasuid ? $uid : ''
            ]),
            new OOUI\ButtonInputWidget([
                'label' => $this->msg('mcbbscredit-query')->text(),
                'type' => 'submit',
                'flags' => ['primary', 'progressive']
            ])
        );
        $form = new OOUI\FormLayout([
            'method' => 'GET',
            'action' => SpecialPage::getTitleFor('MCBBSCredit')->getPrefixedText(),
            'items' => [$action]
        ]);
        $html = $form;
        if ($hasuid) {
            $output->addModules('ext.mcbbswikiutils.credit');
            $html .= Html::element('div', [
                'id' => 'userpie',
                'data-uid' => $uid
            ], wfMessage('mcbbscredit-loading')->text());
        }

        $output->addHTML($html);
    }
}
