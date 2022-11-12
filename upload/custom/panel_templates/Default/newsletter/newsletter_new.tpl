{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$NEWSLETTER}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$NEWSLETTER}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 style="display:inline">{$CREATING_NEWSLETTER}</h5>
                        <div class="float-md-right">
                            <a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                        </div>
                        <hr>

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form role="form" action="" method="post">
                            <div class="form-group">
                                <label for="inputTitle">{$NEWSLETTER_TITLE}</label>
                                <input type="text" name="title" class="form-control" id="inputTitle" placeholder="{$TITLE}" value="{$TITLE_VALUE}">
                            </div>

                            <div class="form-group">
                                <label for="inputContent">{$NEWSLETTER_CONTENT}</label>
                                <textarea id="inputContent" name="content" class="form-control">{$CONTENT_VALUE}</textarea>
                            </div>

                            <div class="form-group custom-control custom-switch">
                              <input id="inputEmails" name="emails" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputEmails">Send email to all subscribers</label>
                            </div>
  
                            <div class="form-group custom-control custom-switch">
                              <input id="inputAlerts" name="alerts" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputAlerts">Send alert to all registered subscribers</label>
                            </div>

                            <hr>

                            <div class="form-group custom-control custom-switch">
                              <input id="inputEmailAllUsers" name="email_all_users" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputEmailAllUsers">Send email to all registered users</label>
                            </div>

                            <div class="form-group custom-control custom-switch">
                              <input id="inputAlertAllUsers" name="alert_all_users" type="checkbox" class="custom-control-input" />
                              <label class="custom-control-label" for="inputAlertAllUsers">Send alert to all registered users</label>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>

                        <center><p>Newsletter Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a></br>Support on <a href="https://discord.gg/TtH6tpp" target="_blank">Discord</a></p></center>
                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

</body>
</html>