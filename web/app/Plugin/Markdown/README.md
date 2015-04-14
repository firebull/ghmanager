# CakePHP Markdown Plugin

Plugin for parsing [markdown text](http://daringfireball.net/) into HTML. Behavior and 2 helpers can be used.
It uses PHP [markdown](https://github.com/michelf/php-markdown) as vendor.


## Requirements

[CakePHP v2.x](https://github.com/cakephp/cakephp)   


## Install

Checkout the plugin into Plugin folder with submodules (Vendor)

	cd app/Plugin
	git clone --recursive https://github.com/LubosRemplik/CakePHP-Markdown-Plugin.git Markdown

## Usage

Using the MarkdownHelper in views to parse parts

	<?php
	// in controller
	public $helpers = array(
		'Markdown.Markdown'
	);

	// in view
	$text = '## Whatever'; // parsing as h2 tag
	echo $this->Markdown->parse($text);

Using the MarkdownParseHelper to parse whole views (layouts, elements, views)

	<?php
	// in controller
	public $helpers = array(
		'Markdown.MarkdownParse'
	);

Using the MarkdownBehavir in model
	
	<?php
	// in model
	public $actsAs = array(
		'Markdown.Markdown' => array(
			'field' => array('title', 'content')
		)
	);

## Issues

If you have any issue/question please submit it into [issue tracker](https://github.com/LubosRemplik/CakePHP-Markdown-Plugin/issues)
