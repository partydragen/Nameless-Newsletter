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
                        <h3 style="display:inline;">{$NEWSLETTER}</h3>
                        <span class="float-md-right"><a href="{$NEW_NEWSLETTER_LINK}" class="btn btn-primary">{$NEW_NEWSLETTER}</a></span>
                        <hr>

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}
                        
                        <p>{$SUBSCRIBERS}</p>

                        </br>

                        <h5>{$RECENT_NEWSLETTERS}</h5>
                        <hr>
                        {if isset($NEWSLETTER_LIST)}
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Views</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$NEWSLETTER_LIST item=newsletter}
                                        <tr>
                                            <td>{$newsletter.id}</td>
                                            <td>{$newsletter.title}</td>
                                            <td>{$newsletter.date}</td>
                                            <td>{$newsletter.views}</td>
                                            <td>
                                                <div class="float-md-right">
                                                    <a href="{$newsletter.view_link}" target="_blank" class="btn btn-primary btn-sm">View</a>
                                                    <a class="btn btn-warning btn-sm" href="{$newsletter.edit_link}"><i class="fas fa-edit fa-fw"></i></a>
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="showDeleteModal('{$newsletter.id}')"><i class="fas fa-trash fa-fw"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            {$NO_RECENT_NEWSLETTERS}
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
    
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_DELETE_NEWSLETTER}
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="deleteId" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <button type="button" onclick="deleteNewsletter()" class="btn btn-primary">{$YES}</button>
                </div>
             </div>
        </div>
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showDeleteModal(id) {
        $('#deleteId').attr('value', id);
         $('#deleteModal').modal().show();
    }

    function deleteNewsletter() {
        const id = $('#deleteId').attr('value');
        if (id) {
            const response = $.post("{$DELETE_LINK}", { id, action: 'delete', token: "{$TOKEN}" });
            response.done(function () { window.location.reload(); });
        }
    }
</script>

</body>
</html>