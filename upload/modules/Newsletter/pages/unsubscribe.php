<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter unsubscribe page
 */

// Always define page name
const PAGE = 'unsubscribe';
$page_title = $newsletter_language->get('general', 'unsubscribe');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

if (isset($_GET['c'])) {
    $subscriber = DB::getInstance()->query('SELECT * FROM nl2_newsletter_subscribers WHERE code = ?', [$_GET['c']]);
    if ($subscriber->count()) {
        $subscriber = $subscriber->first();
        
        if (Input::exists()) {
            $errors = [];

            if (Token::check()) {
                DB::getInstance()->delete('newsletter_subscribers', ['id', $subscriber->id]);

                Session::flash('home', $newsletter_language->get('general', 'successfully_unsubscribed'));
                Redirect::to(URL::build('/'));
            } else {
                // Invalid token
                $errors[] = $language->get('general', 'invalid_token');
            }
        }
    } else {
        Redirect::to(URL::build('/'));
    }
} else {
    Redirect::to(URL::build('/'));
}

$template->getEngine()->addVariables([
    'NEWSLETTER' => $newsletter_language->get('general', 'newsletter'),
    'UNSUBSCRIBE' => $newsletter_language->get('general', 'unsubscribe'),
    'TOKEN' => Token::get(),
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('newsletter/unsubscribe');