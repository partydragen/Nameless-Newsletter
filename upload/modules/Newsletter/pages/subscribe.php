<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.0.2
 *
 *  License: MIT
 *
 *  Newsletter subscribe page
 */

if (Input::exists()) {
    if (Token::check()) {
        $validation = Validate::check($_POST, [
            'email' => [
                Validate::REQUIRED => true,
                Validate::EMAIL => true,
                Validate::UNIQUE => 'newsletter_subscribers'
            ]
        ]);

        if ($validation->passed()) {
            DB::getInstance()->insert('newsletter_subscribers', [
                'email' => Input::get('email'),
                'subscribed' => date('U'),
                'code' => SecureRandom::alphanumeric()
            ]);

            Session::flash('home', $newsletter_language->get('general', 'successfully_subscribed'));
        } else {
            Session::flash('home', $newsletter_language->get('general', 'already_subscribed'));
        }
    }
}

Redirect::to(URL::build('/'));
