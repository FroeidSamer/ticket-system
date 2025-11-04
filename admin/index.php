<?php
include 'includes/header.php';
?>
<div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
</div>
<main id="view-panel">
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
    <?php include $page . '.php' ?>
</main>

<div id="preloader"></div>
<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= tr('confirmation') ?></h5>
            </div>
            <div class="modal-body">
                <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='confirm' onclick=""><?= tr('continue') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= tr('close') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()"><?= tr('save') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= tr('close') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="show_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= tr('close') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
            <img src="" alt="">
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>