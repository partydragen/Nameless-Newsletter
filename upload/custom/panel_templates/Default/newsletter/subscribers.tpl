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
                        <h3 style="display:inline;">{$SUBSCRIBERS}</h3>
                        <hr>

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if isset($SUBSCRIBERS_LIST)}
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Subscribed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$SUBSCRIBERS_LIST item=subscriber}
                                        <tr>
                                            <td>{$subscriber.email}</td>
                                            <td>{$subscriber.date}</td>
                                            <td>
                                                <div class="float-md-right">
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="showUnsubscribeModal('{$subscriber.id}')">{$UNSUBSCRIBE}</button>
                                                </div>
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            {$NO_SUBSCRIBERS}
                        {/if}

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

    <div class="modal fade" id="unsubscribeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_UNSUBSCRIBE}
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="unsubscribeId" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <button type="button" onclick="unsubscribe()" class="btn btn-primary">{$YES}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showUnsubscribeModal(id) {
        $('#unsubscribeId').attr('value', id);
        $('#unsubscribeModal').modal().show();
    }

    function unsubscribe() {
        const id = $('#unsubscribeId').attr('value');
        if (id) {
            const response = $.post("{$UNSUBSCRIBE_LINK}", { id, action: 'unsubscribe', token: "{$TOKEN}" });
            response.done(function () { window.location.reload(); });
        }
    }
</script>

</body>
</html>