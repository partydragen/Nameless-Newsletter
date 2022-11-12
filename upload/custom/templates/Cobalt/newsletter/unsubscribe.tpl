{include file='header-top.tpl'}
</head>

<body>
    <div class="container">
        <div class="card login-page-card">

            <div class="login-menu">

                {if $THEME_LR_MODAL_IMAGE == "yes"}

                    {if $THEME_LR_COVERLAY == "yes"}<div class="newsletter-unsubscribe-overlay"></div>{/if}
                    <div class="container-fluid">
                        <div class="row no-margin-row">
                            <div class="col-lg-6">
                                <div class="card lr-card">
                                    <div class="card-body">
                                        <div class="lr-import-container">

                                            <span class="lr-title">{$NEWSLETTER}</span>
                                            <br /><br />
                                            <form role="form" action="" method="post">
                                                <div class="form-group">
                                                    <input type="hidden" name="token" value="{$TOKEN}">
                                                    <button type="submit" class="btn btn-theme btn-block" value="{$UNSUBSCRIBE}">{$UNSUBSCRIBE}</button>
                                                </div>
                                            </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 bg-col">
                                    {if isset($THEME_L_BG) && $THEME_L_BG|count_characters > 4}
                                        <picture>
                                            <source srcset="none" media="(max-width: 767px)">
                                            {if isset($THEME_L_BG_WEBP) && $THEME_L_BG_WEBP|count_characters > 4}
                                                <source srcset="{$THEME_L_BG_WEBP}" type="image/webp">
                                            {/if}
                                            <source srcset="{$THEME_L_BG}">
                                            <img class="lr-bg-img" alt="logo" src='{$THEME_L_BG}'>
                                        </picture>
                                    {/if}
                                </div>
                            </div>
                        </div>

                    {else}

                        <div class="container-fluid">
                            <div class="card lr-card">
                                <div class="card-body">
                                    <div class="lr-import-container">
                                        <span class="lr-title">{$NEWSLETTER}</span>
                                        <br /><br />
                                             <form role="form" action="" method="post">
                                                <div class="form-group">
                                                    <input type="hidden" name="token" value="{$TOKEN}">
                                                    <button type="submit" class="btn btn-theme btn-block" value="{$UNSUBSCRIBE}">{$UNSUBSCRIBE}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {/if}
                    </div>

                </div>
            </div>

            {include file='scripts.tpl'}
            {if !isset($EXCLUDE_END_BODY)}
            </body>

            </html>
        {/if}