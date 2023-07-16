<?php
namespace MediaWiki\Extension\MCBBSWiki;

use MediaWiki\Html\TemplateParser;
use OutputPage;

interface ISaltTool {
	public function outHead( OutputPage $out, $par, TemplateParser $tmpl );

	public function outBody( OutputPage $out, $par, TemplateParser $tmpl );
}
