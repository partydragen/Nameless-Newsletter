<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter module initialisation file
 */

// Initialise module language
$newsletter_language = new Language(ROOT_PATH . '/modules/Newsletter/language', LANGUAGE);

// Initialise module
require_once(ROOT_PATH . '/modules/Newsletter/module.php');
$module = new Newsletter_Module($language, $newsletter_language, $pages, $cache);