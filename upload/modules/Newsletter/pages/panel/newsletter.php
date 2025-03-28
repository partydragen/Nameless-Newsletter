<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter module - panel newsletter page
 */

// Can the user view the StaffCP?
if (!$user->handlePanelPageLoad('admincp.newsletter')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'newsletter');
define('PANEL_PAGE', 'newsletter');
$page_title = $newsletter_language->get('general', 'newsletter');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if (!isset($_GET['action'])) {
    // List all newsletters
    $newsletters_query = DB::getInstance()->query('SELECT * FROM nl2_newsletter WHERE deleted = 0 ORDER BY created DESC');
    if ($newsletters_query->count()) {

        $newsletter_list = [];
        foreach ($newsletters_query->results() as $newsletter) {
            $newsletter_list[] = [
                'id' => Output::getClean($newsletter->id),
                'title' => Output::getClean($newsletter->title),
                'date' => date(DATE_FORMAT, $newsletter->created),
                'views' => Output::getClean($newsletter->views),
                'view_link' => URL::build('/newsletter/' . $newsletter->id),
                'edit_link' => URL::build('/panel/newsletter', 'action=edit&id=' . $newsletter->id),
            ];
        }

        $template->getEngine()->addVariable('NEWSLETTER_LIST', $newsletter_list);
    } else {
        $template->getEngine()->addVariable('NO_RECENT_NEWSLETTERS', $newsletter_language->get('general', 'no_recent_newsletters'));
    }

    $template->getEngine()->addVariables([
        'NEW_NEWSLETTER' => $newsletter_language->get('general', 'new_newsletter'),
        'NEW_NEWSLETTER_LINK' => URL::build('/panel/newsletter', 'action=new'),
        'RECENT_NEWSLETTERS' => $newsletter_language->get('general', 'recent_newsletters'),
        'SUBSCRIBERS' => $newsletter_language->get('general', 'subscribers_x', [
            'subscribers' => DB::getInstance()->query('SELECT COUNT(*) as c FROM nl2_newsletter_subscribers')->first()->c
        ]),
        'EDIT' => $language->get('general', 'edit'),
        'DELETE' => $language->get('general', 'delete'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_NEWSLETTER' => $newsletter_language->get('general', 'confirm_delete_newsletter'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no'),
        'DELETE_LINK' => URL::build('/panel/newsletter', 'action=delete'),
    ]);

    $template_file = 'newsletter/newsletter';
} else {
    switch ($_GET['action']) {
        case 'new';
            // Create new newsletter
            if (Input::exists()) {
                $errors = [];

                if (Token::check()) {
                    $validation = Validate::check($_POST, [
                        'title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 128
                        ],
                        'content' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 500000
                        ]
                    ]);

                    if ($validation->passed()) {
                        DB::getInstance()->insert('newsletter', [
                            'author_id' => $user->data()->id,
                            'title' => Input::get('title'),
                            'content' => Input::get('content'),
                            'created' => date('U')
                        ]);
                        $newsletter_id = DB::getInstance()->lastId();

                        $title = Input::get('title');
                        $content = Output::getPurified(Input::get('content'));

                        $subscribers_query = DB::getInstance()->query('SELECT * FROM nl2_newsletter_subscribers');
                        if ($subscribers_query->count()) {
                            $subscribers_query = $subscribers_query->results();

                            // Send alert to registered subscribers
                            if (isset($_POST['alerts']) && $_POST['alerts'] == 'on' && !(isset($_POST['alert_all_users']) && $_POST['alert_all_users'] == 'on')) {
                                foreach ($subscribers_query as $subscriber) {
                                    $sub_user = DB::getInstance()->query('SELECT * FROM nl2_users WHERE email = ?', [$subscriber->email]);
                                    if ($sub_user->count()) {
                                        $sub_user = $sub_user->first();

                                        // Send alert to user
                                        DB::getInstance()->insert('alerts', [
                                            'user_id' => $sub_user->id,
                                            'type' => 'newsletter',
                                            'url' => URL::build('/newsletter/' . $newsletter_id),
                                            'content_short' => $title,
                                            'content' => $title,
                                            'created' => date('U')
                                        ]);
                                    }
                                }
                            }

                            // Send emails to subscribers
                            if (isset($_POST['emails']) && $_POST['emails'] == 'on') {
                                $reply_to = Email::getReplyTo();
                                foreach ($subscribers_query as $subscriber) {
                                    $sent = Email::send(
                                        ['email' => $subscriber->email, 'name' => SITE_NAME],
                                        $title,
                                        str_replace(['{email}', '{sitename}'], [$subscriber->email, SITE_NAME], $content .  '</br></br><center><a href="'.URL::getSelfURL() . ltrim(URL::build('/newsletter/unsubscribe', 'c=' . $subscriber->code), '/').'" target="_blank">'. $newsletter_language->get('general', 'unsubscribe') .'</a></center>'),
                                        $reply_to
                                    );
                                }
                            }
                        }

                        // Send alert to all registerd users
                        if (isset($_POST['alert_all_users']) && $_POST['alert_all_users'] == 'on') {
                            $users = DB::getInstance()->query('SELECT id FROM nl2_users');

                            if ($users->count()) {
                                foreach ($users->results() as $user) {
                                    // Send alert to user
                                     DB::getInstance()->insert('alerts', [
                                        'user_id' => $user->id,
                                        'type' => 'newsletter',
                                        'url' => URL::build('/newsletter/' . $newsletter_id),
                                        'content_short' => $title,
                                        'content' => $title,
                                        'created' => date('U')
                                    ]);
                                }
                            }
                        }

                        // Send email to all registerd users
                        if (isset($_POST['email_all_users']) && $_POST['email_all_users'] == 'on') {
                            $users = DB::getInstance()->query('SELECT id, email FROM nl2_users');

                            if ($users->count()) {
                                $reply_to = Email::getReplyTo();

                                foreach ($users->results() as $user) {
                                    // Send email to user
                                    $sent = Email::send(
                                        ['email' => $user->email, 'name' => SITE_NAME],
                                        $title,
                                        str_replace(['{email}', '{sitename}'], [$subscriber->email, SITE_NAME], $content .  '</br></br><center><a href="'.URL::getSelfURL() . ltrim(URL::build('/newsletter/unsubscribe', 'c=' . $subscriber->code), '/').'" target="_blank">'. $newsletter_language->get('general', 'unsubscribe') .'</a></center>'),
                                        $reply_to
                                    );
                                }
                            }
                        }

                        Session::flash('newsletter_success', $newsletter_language->get('general', 'newsletter_created_successfully'));
                        Redirect::to(URL::build('/panel/newsletter'));
                    } else {
                        // Validation errors
                        $errors = $validation->errors();
                    }
                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $template->getEngine()->addVariables([
                'CREATING_NEWSLETTER' => $newsletter_language->get('general', 'creating_newsletter'),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/newsletter'),
                'NEWSLETTER_TITLE' => $newsletter_language->get('general', 'newsletter_title'),
                'NEWSLETTER_CONTENT' => $newsletter_language->get('general', 'newsletter_content')
            ]);

            $template->assets()->include([
                AssetTree::TINYMCE,
            ]);

            $template->addJSScript(Input::createTinyEditor($language, 'inputContent', null, false, true));

            $template_file = 'newsletter/newsletter_new';
        break;
        case 'edit';
            // Edit newsletter
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                Redirect::to(URL::build('/panel/newsletter'));
            }

            $newsletter = DB::getInstance()->get('newsletter', ['id', $_GET['id']]);
            if (!$newsletter->count()) {
                Redirect::to(URL::build('/panel/newsletter'));
            }
            $newsletter = $newsletter->first();

            if (Input::exists()) {
                $errors = [];

                if (Token::check()) {
                    $validation = Validate::check($_POST, [
                        'title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 128
                        ],
                        'content' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 500000
                        ]
                    ]);

                    if ($validation->passed()) {
                        DB::getInstance()->update('newsletter', $newsletter->id, [
                            'title' => Input::get('title'),
                            'content' => Input::get('content')
                        ]);

                        Session::flash('newsletter_success', $newsletter_language->get('general', 'newsletter_updated_successfully'));
                        Redirect::to(URL::build('/panel/newsletter'));
                    } else {
                        // Validation errors
                        $errors = $validation->errors();
                    }
                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $template->getEngine()->addVariables([
                'CREATING_NEWSLETTER' => $newsletter_language->get('general', 'creating_newsletter'),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/newsletter'),
                'NEWSLETTER_TITLE' => $newsletter_language->get('general', 'newsletter_title'),
                'NEWSLETTER_TITLE_VALUE' => Output::getClean($newsletter->title),
                'NEWSLETTER_CONTENT' => $newsletter_language->get('general', 'newsletter_content'),
                'NEWSLETTER_CONTENT_VALUE' => Output::getClean($newsletter->content)
            ]);

            $template->assets()->include([
                AssetTree::TINYMCE,
            ]);

            $template->addJSScript(Input::createTinyEditor($language, 'inputContent', null, false, true));

            $template_file = 'newsletter/newsletter_edit';
        break;
        case 'delete':
            if (Input::exists()) {
                if (Token::check(Input::get('token'))) {
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        DB::getInstance()->update('newsletter', ['id', $_POST['id']], [
                            'deleted' => date('U')
                        ]);

                        Session::flash('newsletter_success', $newsletter_language->get('general', 'newsletter_deleted_successfully'));
                    }
                }
            }
            die();
        break;
        default:
            Redirect::to(URL::build('/panel/newsletter'));
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
$template->displayTemplate($template_file);