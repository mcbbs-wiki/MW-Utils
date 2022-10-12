<?php

namespace MediaWiki\Extension\MCBBSWiki;

use SpecialPage;
use Html;
use HTMLForm;

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
        $uid = $request->getInt('wpUID');
        $hasuid = $uid !== 0;
        $output->enableOOUI();
        $this->setHeaders();
        $formDescriptor = [
			'uid' => [
				'type' => 'number',
				'name' => 'wpUID',
				'exists' => true,
                'class' => 'HTMLTextField',
				'label-message' => 'mcbbscredit-input-uid',
				'required' => true,
				'default' => $hasuid ? $uid : ''
			]
		];
        $form = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
        $form
			->setMethod( 'get' )
			->prepareForm()
			->displayForm( false );
        $html = '';
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
