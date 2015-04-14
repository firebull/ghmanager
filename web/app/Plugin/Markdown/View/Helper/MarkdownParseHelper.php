<?php

App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'Markdown.Markdown/Michelf/Markdown');
App::import('Vendor', 'Markdown.Markdown/Michelf/MarkdownExtra');

/**
 * Markdown Helper
 *
 * Parsing markdown syntax into HTML
 **/
class MarkdownParseHelper extends AppHelper {

	public function afterRenderFile($viewFile, $content) {
		$parser = new Michelf\MarkdownExtra;
		$html = $parser->defaultTransform($content);
		return $html;
	}
}
