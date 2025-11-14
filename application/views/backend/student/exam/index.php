<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block">
                <i class="fas fa-file-signature fa-fw"></i> <?php echo get_phrase('certifications'); ?>
            </h4>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="mb-3">
    <div class="main-card">
      <div class="card-body">
            <div class="card-body exam_content">
                <!-- Ne pas inclure list.php directement -->
                <!-- Le contenu sera chargé via AJAX -->
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script>
$(document).ready(function() {
    // Appeler showAllExams au chargement de la page pour charger le contenu
    showAllExams();
});

var showAllExams = function () {
    var url = '<?php echo route('exam/list'); ?>';

    $.ajax({
        type: 'GET',
        url: url,
        success: function(response) {
            $('.exam_content').html(response);
            // Pas besoin d'appeler initDataTable ici, car list.php gère l'initialisation
        }
    });
}
</script>