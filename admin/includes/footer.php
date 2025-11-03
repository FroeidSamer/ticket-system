 </div>
 <!-- /.container-fluid -->

 </div>
 <!-- End of Main Content -->
 <!-- Footer -->
 <footer class="sticky-footer bg-white">
     <div class="container my-auto">
         <div class="copyright text-center my-auto">
             <span>Copyright &copy; Saleh Shafie <?= date('Y', time()) ?></span>
         </div>
     </div>
 </footer>
 <!-- End of Footer -->

 </div>
 <!-- End of Content Wrapper -->

 </div>
 <!-- End of Page Wrapper -->

 <!-- Scroll to Top Button-->
 <a class="scroll-to-top rounded" href="#page-top">
     <i class="fas fa-angle-up"></i>
 </a>

 <!-- Logout Modal-->
 <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel"><?= tr('readytoleave') ?></h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">×</span>
                 </button>
             </div>
             <div class="modal-body"><?= tr('areyousure') ?></div>
             <div class="modal-footer">
                 <button class="btn btn-secondary" type="button" data-dismiss="modal"><?= tr('cancel') ?></button>
                 <a class="btn btn-primary" href="ajax.php?action=logout"><?= tr('logout') ?></a>
             </div>
         </div>
     </div>
 </div>

 <!-- Bootstrap core JavaScript-->
 <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

 <!-- Core plugin JavaScript-->
 <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

 <!-- Custom scripts for all pages-->
 <script src="js/sb-admin-2.min.js"></script>


 <script>
     window.start_load = function() {
         $('body').prepend('<di id="preloader2"></di>')
     }
     window.end_load = function() {
         $('#preloader2').fadeOut('fast', function() {
             $(this).remove();
         })
     }
     window.viewer_modal = function($src = '') {
         start_load()
         var t = $src.split('.')
         t = t[1]
         if (t == 'webm' || t == "mp4") {
             var view = $("<video src='assets/uploads/" + $src + "' controls autoplay></video>")
         } else {
             var view = $("<img src='assets/uploads/" + $src + "' />")
         }
         $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
         $('#viewer_modal .modal-content').append(view)
         $('#viewer_modal').modal({
             show: true,
             backdrop: 'static',
             keyboard: false,
             focus: true
         })
         end_load()

     }
     window.uni_modal = function($title = '', $url = '', $size = "") {
         start_load()
         $.ajax({
             url: $url,
             error: err => {
                 console.log()
                 alert("An error occured")
             },
             success: function(resp) {
                 if (resp) {
                     $('#uni_modal .modal-title').html($title)
                     $('#uni_modal .modal-body').html(resp)
                     if ($size != '') {
                         $('#uni_modal .modal-dialog').addClass($size)
                     } else {
                         $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
                     }
                     $('#uni_modal').modal({
                         show: true,
                         backdrop: 'static',
                         keyboard: false,
                         focus: true
                     })
                     end_load()
                 }
             }
         })
     }
     window.show_modal = function($title = '', $url = '', $size = "") {
         start_load()
         $.ajax({
             url: $url,
             error: err => {
                 console.log()
                 alert("An error occured")
             },
             success: function(resp) {
                 if (resp) {
                     $('#show_modal .modal-title').html($title)
                     $('#show_modal .modal-body').html(resp)
                     if ($size != '') {
                         $('#show_modal .modal-dialog').addClass($size)
                     } else {
                         $('#show_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
                     }
                     $('#show_modal').modal({
                         show: true,
                         backdrop: 'static',
                         keyboard: false,
                         focus: true
                     })
                     end_load()
                 }
             }
         })
     }
     window._conf = function($msg = '', $func = '', $params = []) {
         $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
         $('#confirm_modal .modal-body').html($msg)
         $('#confirm_modal').modal('show')
     }
     window.alert_toast = function($msg = 'TEST', $bg = 'success') {
         $('#alert_toast').removeClass('bg-success')
         $('#alert_toast').removeClass('bg-danger')
         $('#alert_toast').removeClass('bg-info')
         $('#alert_toast').removeClass('bg-warning')

         if ($bg == 'success')
             $('#alert_toast').addClass('bg-success')
         if ($bg == 'danger')
             $('#alert_toast').addClass('bg-danger')
         if ($bg == 'info')
             $('#alert_toast').addClass('bg-info')
         if ($bg == 'warning')
             $('#alert_toast').addClass('bg-warning')
         $('#alert_toast .toast-body').html($msg)
         $('#alert_toast').toast({
             delay: 3000
         }).toast('show');
     }
     $(document).ready(function() {
         $('#preloader').fadeOut('fast', function() {
             $(this).remove();
         })
         $('.langBtn').click(function(e) {
             e.preventDefault();
             var language = $(this).data('lang');
             $.ajax({
                 url: 'ajax.php?action=set_lang',
                 method: 'POST',
                 data: {
                     lang: language,
                 },
                 success: function(resp) {
                     setTimeout(() => {
                         location.reload();
                     }, 500);
                 }
             })
         })
     })
 </script>
 </body>

 </html>