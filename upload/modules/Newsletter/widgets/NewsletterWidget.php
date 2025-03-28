<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter module - Newsletter subscribe widget
 */

class NewsletterWidget extends WidgetBase {

    public function __construct(TemplateEngine $engine) {
        $this->_module = 'Store';
        $this->_name = 'Newsletter Subscribe';
        $this->_description = 'Show a widget with allow users to subscribe to the newsletter';
		$this->_engine = $engine;
	}

	public function initialise(): void {
		// Generate HTML code for widget
		$this->_content = $this->_engine->fetch('newsletter/widgets/newsletter');
	}
}