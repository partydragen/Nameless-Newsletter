<div class="ui fluid card" id="widget-featured-package">
    <div class="content">
        <h4 class="ui header">{$NEWSLETTER}</h4>
        <form class="ui form" action="{$NEWSLETTER_SUBSCRIBE_LINK}" method="post">
            <div class="field">
                <input type="email" name="email" id="inputEmail" placeholder="{$YOUR_EMAIL_ADDRESS}" required>
            </div>
            <div class="field">
                <input type="hidden" name="action" value="newsletter_subscribe">
                <input type="hidden" name="token" value="{$NEWSLETTER_TOKEN}">
                <input type="submit" value="{$SUBSCRIBE}" class="ui primary button">
            </div>
        </form>
    </div>
</div>