<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Newsletter
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 *
 *  Newsletter module file
 */

class Newsletter_Module extends Module {
    
    private Language $_language;
    private Language $_newsletter_language;

    public function __construct(Language $language, Language $newsletter_language, Pages $pages, Cache $cache) {
        $this->_language = $language;
        $this->_newsletter_language = $newsletter_language;

        $name = 'Newsletter';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.0.2';
        $nameless_version = '2.1.0';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('Newsletter', '/newsletter', 'pages/newsletter.php', 'newsletter', true);
        $pages->add('Newsletter', '/newsletter/subscribe', 'pages/subscribe.php');
        $pages->add('Newsletter', '/newsletter/unsubscribe', 'pages/unsubscribe.php');

        $pages->add('Newsletter', '/panel/newsletter', 'pages/panel/newsletter.php');
        $pages->add('Newsletter', '/panel/newsletter/subscribers', 'pages/panel/subscribers.php');

        // Check if module version changed
        $cache->setCache('newsletter_module_cache');
        if (!$cache->isCached('module_version')) {
            $cache->store('module_version', $module_version);
        } else {
            if ($module_version != $cache->retrieve('module_version')) {
                // Version have changed, Perform actions
                //$this->initialiseUpdate($cache->retrieve('module_version'));

                $cache->store('module_version', $module_version);

                if ($cache->isCached('update_check')) {
                    $cache->erase('update_check');
                }
            }
        }
    }

    public function onInstall() {
        // Initialise
        $this->initialise();
    }

    public function onUninstall() {
        // Not necessary for Newsletter
    }

    public function onEnable() {
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable() {
        // Not necessary for Newsletter
    }

    public function onPageLoad(User $user, Pages $pages, Cache $cache, Smarty $smarty, $navs, Widgets $widgets, ?TemplateBase $template) {
        // Permissions
        PermissionHandler::registerPermissions('Newsletter', [
            'admincp.newsletter' => $this->_language->get('moderator', 'staff_cp')  . ' &raquo; ' .  $this->_newsletter_language->get('general', 'newsletter')
        ]);

		// Widgets
		require_once(ROOT_PATH . '/modules/Newsletter/widgets/NewsletterWidget.php');
		$widgets->add(new NewsletterWidget($smarty, $this->_language, $cache));

        if (defined('BACK_END')) {
            if ($user->hasPermission('admincp.newsletter')) {
                $cache->setCache('panel_sidebar');
                if (!$cache->isCached('newsletter_order')) {
                    $order = 99;
                    $cache->store('newsletter_order', 99);
                } else {
                    $order = $cache->retrieve('newsletter_order');
                }

                if (!$cache->isCached('newsletter_icon')) {
                    $icon = '<i class="nav-icon fa-solid fa-newspaper"></i>';
                    $cache->store('newsletter_icon', $icon);
                } else {
                    $icon = $cache->retrieve('newsletter_icon');
                }

                $navs[2]->add('newsletter_divider', mb_strtoupper($this->_newsletter_language->get('general', 'newsletter'), 'UTF-8'), 'divider', 'top', null, $order, '');
                $navs[2]->add('newsletter', $this->_newsletter_language->get('general', 'newsletter'), URL::build('/panel/newsletter'), 'top', null, $order + 0.1, $icon);
                $navs[2]->add('newsletter_subscribers', $this->_newsletter_language->get('general', 'subscribers'), URL::build('/panel/newsletter/subscribers'), 'top', null, $order + 0.2, $icon);
            }
        } else {
            $smarty->assign([
                'NEWSLETTER_TOKEN' => Token::get(),
                'NEWSLETTER' => $this->_newsletter_language->get('general', 'newsletter'),
                'NEWSLETTER_SUBSCRIBE_LINK' => URL::build('/newsletter/subscribe'),
                'YOUR_EMAIL_ADDRESS' => $this->_newsletter_language->get('general', 'your_email_address'),
                'SUBSCRIBE' => $this->_newsletter_language->get('general', 'subscribe')
            ]);
        }

        // Check for module updates
        if (isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')) {
            // Page belong to this module?
            $page = $pages->getActivePage();
            if ($page['module'] == 'Newsletter') {

                $cache->setCache('newsletter_module_cache');
                if ($cache->isCached('update_check')) {
                    $update_check = $cache->retrieve('update_check');
                } else {
                    require_once(ROOT_PATH . '/modules/Newsletter/classes/Newsletter.php');
                    $update_check = Newsletter::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {  
                    $smarty->assign(array(
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_newsletter_language->get('general', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->_newsletter_language->get('general', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->_newsletter_language->get('general', 'current_version_x', [
                            'version' => Output::getClean($this->getVersion())
                        ]),
                        'NEW_VERSION' => $this->_newsletter_language->get('general', 'new_version_x', [
                            'new_version' => Output::getClean($update_check->new_version)
                        ]),
                        'NAMELESS_UPDATE' => $this->_newsletter_language->get('general', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ));
                }
            }
        }
    }

    public function getDebugInfo(): array {
        return [];
    }

    private function initialise() {
        // Generate tables
        if (!DB::getInstance()->showTables('newsletter')) {
            DB::getInstance()->createTable("newsletter", " `id` int(11) NOT NULL AUTO_INCREMENT, `author_id` int(11) NOT NULL, `title` varchar(128) NOT NULL, `content` mediumtext NOT NULL, `views` int(11) DEFAULT '0', `created` int(11) NOT NULL, `deleted` int(11) DEFAULT '0', PRIMARY KEY (`id`)");
        }

        if (!DB::getInstance()->showTables('newsletter_subscribers')) {
            DB::getInstance()->createTable("newsletter_subscribers", " `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) DEFAULT NULL, `email` varchar(64) DEFAULT NULL, `subscribed` int(11) NOT NULL, `code` varchar(64) NOT NULL, PRIMARY KEY (`id`)");
        }
    }
}