<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter page
 */

// Always define page name
const PAGE = 'newsletter';
$page_title = $newsletter_language->get('general', 'newsletter');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get newsletter ID
$nid = explode('/', $route);
$nid = $nid[count($nid) - 1];

if (!strlen($nid)) {
    require_once(ROOT_PATH . '/404.php');
    die();
}

$nid = explode('-', $nid);
if (!is_numeric($nid[0])) {
    require_once(ROOT_PATH . '/404.php');
    die();
}
$nid = $nid[0];

// Get newsletter info from URL
$newsletter = DB::getInstance()->get('newsletter', ['id', $nid]);
if (!$newsletter->count()) {
    require(ROOT_PATH . '/404.php');
    die();
}

$newsletter = $newsletter->first();

if ($newsletter->deleted != 0) {
    require(ROOT_PATH . '/403.php');
    die();
}

// View count
if ($user->isLoggedIn() || Cookie::exists('alert-box')) {
    if(!Cookie::exists('nl-newsletter-' . $newsletter->id)) {
        DB::getInstance()->increment('newsletter', $newsletter->id, 'views');
        Cookie::put('nl-newsletter-' . $newsletter->id, "true", 3600);
    }
} else {
    if(!Session::exists('nl-newsletter-' . $newsletter->id)){
        DB::getInstance()->increment('newsletter', $newsletter->id, 'views');
        Session::put("nl-newsletter-" . $newsletter->id, "true");
    }
}

$template->getEngine()->addVariables([
    'NEWSLETTER' => $newsletter_language->get('general', 'newsletter'),
    'UNSUBSCRIBE' => $newsletter_language->get('general', 'unsubscribe'),
    'TOKEN' => Token::get(),
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$content = EventHandler::executeEvent('renderCustomPage', [
    'content' => $newsletter->content,
    'skip_purify' => true
])['content'];

$template->getEngine()->addVariables([
    'NEWSLETTER' => $newsletter_language->get('general', 'newsletter'),
    'WIDGETS_LEFT' => $widgets->getWidgets('left'),
    'WIDGETS_RIGHT' => $widgets->getWidgets('right'),
    'CONTENT' => $content,
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('newsletter/newsletter');