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
	private $_language, $_cache;

    public function __construct(Smarty $smarty, Language $language, Cache $cache) {
		$this->_smarty = $smarty;
		$this->_language = $language;
		$this->_cache = $cache;

        // Get widget
        $widget_query = self::getData('Newsletter Subscribe');

        parent::__construct(self::parsePages($widget_query));

		// Set widget variables
		$this->_module = 'Store';
		$this->_name = 'Newsletter Subscribe';
		$this->_location = $widget_query->location;
		$this->_description = 'Show a widget with allow users to subscribe to the newsletter';
		$this->_order = $widget_query->order;
	}

	public function initialise(): void {
		// Generate HTML code for widget
		$this->_content = $this->_smarty->fetch('newsletter/widgets/newsletter.tpl');
	}
}