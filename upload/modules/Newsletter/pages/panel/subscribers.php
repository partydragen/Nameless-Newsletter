<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter module - panel newsletter subscribers page
 */

// Can the user view the StaffCP?
if (!$user->handlePanelPageLoad('admincp.newsletter')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'newsletter_subscribers');
define('PANEL_PAGE', 'newsletter_subscribers');
$page_title = $newsletter_language->get('general', 'newsletter');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if (!isset($_GET['action'])) {
    // List all newsletters
    $subscribers_query = DB::getInstance()->query('SELECT * FROM nl2_newsletter_subscribers');
    if ($subscribers_query->count()) {

        $subscribers_list = [];
        foreach ($subscribers_query->results() as $subscriber) {
            $subscribers_list[] = [
                'id' => Output::getClean($subscriber->id),
                'email' => Output::getClean($subscriber->email),
                'date' => date(DATE_FORMAT, $subscriber->subscribed)
            ];
        }

        $template->getEngine()->addVariable('SUBSCRIBERS_LIST', $subscribers_list);
    } else {
        $template->getEngine()->addVariable('NO_SUBSCRIBERS', $newsletter_language->get('general', 'no_subscribers'));
    }

    $template->getEngine()->addVariables([
        'RECENT_NEWSLETTERS' => $newsletter_language->get('general', 'recent_newsletters'),
        'SUBSCRIBERS' => $newsletter_language->get('general', 'subscribers_x', [
            'subscribers' => DB::getInstance()->query('SELECT COUNT(*) as c FROM nl2_newsletter_subscribers')->first()->c
        ]),
        'UNSUBSCRIBE' => $newsletter_language->get('general', 'unsubscribe'),
        'UNSUBSCRIBE_LINK' => URL::build('/panel/newsletter/subscribers', 'action=unsubscribe'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_UNSUBSCRIBE' => $newsletter_language->get('general', 'confirm_unsubscribe'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no'),
    ]);
} else {
    switch ($_GET['action']) {
        case 'unsubscribe';
            // Unsubscribe
            if (Input::exists()) {
                $errors = [];

                if (Token::check()) {
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        DB::getInstance()->delete('newsletter_subscribers', ['id', $_POST['id']]);

                        Session::flash('newsletter_success', $newsletter_language->get('general', 'successfully_unsubscribed'));
                        Redirect::to(URL::build('/panel/newsletter/subscribers'));
                    }
                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
        break;
        default:
            Redirect::to(URL::build('/panel/newsletter/subscribers'));
        break;
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('newsletter_success'))
    $success = Session::flash('newsletter_success');

if (isset($success))
    $template->getEngine()->addVariables([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $template->getEngine()->addVariables([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$template->getEngine()->addVariables([
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'NEWSLETTER' => $newsletter_language->get('general', 'newsletter'),
	'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('newsletter/subscribers');