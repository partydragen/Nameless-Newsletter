<div class="card widget-card">
    <div class="card-header header-theme widget-header">{$NEWSLETTER}</div>
    <div class="card-body">
        <form action="{$NEWSLETTER_SUBSCRIBE_LINK}" method="post">
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="inputEmail" placeholder="{$YOUR_EMAIL_ADDRESS}" required>
            </div>
            <div class="form-group">
                <input type="hidden" name="action" value="newsletter_subscribe">
                <input type="hidden" name="token" value="{$NEWSLETTER_TOKEN}">
                <button type="submit" value="{$SUBSCRIBE}" class="btn btn-theme other-btn">{$SUBSCRIBE}</button>
            </div>
        </form>
    </div>
</div>