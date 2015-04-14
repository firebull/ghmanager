<?php

App::uses('ModelBehavior', 'Model');
App::import('Vendor', 'Markdown.Markdown/Michelf/Markdown');
App::import('Vendor', 'Markdown.Markdown/Michelf/MarkdownExtra');

/**
 * Markdown Behavior
 * 
 * Parsing markdown text to html in beforeSave
 **/
class MarkdownBehavior extends ModelBehavior {
	
	public function setup(Model $Model, $config = array()) {
		$settings = array_merge(array(
			'field' => 'content'
		), $config);
		$this->settings[$Model->alias] = $settings;
	}

	public function beforeSave(Model $Model) {
		extract($this->settings[$Model->alias]);

		if (!is_array($field)) {
			$field = array($field);
		}
		foreach ($field as $k => $val) {
			if (!empty($Model->data[$Model->alias][$val])) {
				$text = $Model->data[$Model->alias][$val];
				$parser = new Michelf\MarkdownExtra;
				$html = $parser->defaultTransform($text);
				$Model->data[$Model->alias][$val] = $html;
			}
		}

		return true;
	}
}
