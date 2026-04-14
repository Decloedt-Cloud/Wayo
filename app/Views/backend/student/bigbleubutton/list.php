<?php
$school_id = school_id();
$meetings = db()->table('sessions_meetings')->where('school_id', $school_id)->get()->getResultArray();

$classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
$rooms = db()->table('rooms')->where('Etat', 1)->get()->getResultArray();

?>


<style>
        .meeting-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: 0.3s;
        }
        .meeting-card:hover {
            box-shadow: 4px 4px 15px rgba(0,0,0,0.2);
        }
        .meeting-btn {
            width: 100%;
        }
        .meeting-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .meeting-time {
            color: #777;
            font-size: 14px;
        }
         body[dir="rtl"] .meeting-time {
            font-size: 1.2rem !important;
        }
        #calendar {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

<div class="container mt-4">
    <!-- <div class="d-flex justify-content-between">
        <input type="text" class="form-control w-25" placeholder="🔍 Search">
        <a href="<?php // echo base_url('bigbluebutton/create'); ?>" class="btn btn-primary">+ New Room</a>
    </div> -->
 
</div>



   





<div class="row mt-4">
    <?php foreach ($rooms as $room):
        $classData = db()->table('classes')->where('id', $room['class_id'])->get()->getRowArray();
        $className = $classData['name'] ?? '';
        $status = '<span class="badge bg-danger">' . get_phrase('Non Démarrée') . '</span>'; // Par défaut, réunion non démarrée

        $user_id   = session()->get('user_id');
        
        $check_student = db()->table('students')
            ->where('user_id', $user_id)
            ->where('school_id', $room['school_id'])
            ->get()
            ->getRowArray();    
        

        $query = db()->table('enrols')
            ->where('student_id', $check_student['id'])
            ->where('school_id', $room['school_id'])
            ->where('class_id', $room['class_id'])
            ->get();
        $count = count($query);

        if($count != 0 ){
    ?>
        <div class="col-md-4">
            <div class="meeting-card">
                <div class="meeting-title"><?php echo $room['name']; ?></div>
                <div class="meeting-time"><?php echo get_phrase("Classe :") ?> <?php echo $className; ?></div>
                <div class="meeting-status" id="status-<?php echo $room['id']; ?>"><?php echo $status; ?></div>

                <!-- Nombre de participants -->
                <div class="meeting-participants" id="participants-<?php echo $room['id']; ?>">👥 0 <?php echo get_phrase("participants") ?></div>
                

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <!-- Bouton "Join" désactivé par défaut -->
                    <a href="#" 
                    target="_blank" 
                    class="btn btn-secondary meeting-btn join-btn disabled"
                    id="join-btn-<?php echo $room['id']; ?>"
                    data-meeting-id="<?php echo $room['id']; ?>">
                        <?php echo get_phrase("Join") ?> 
                    </a>


                </div>
            </div>
        </div>
    <?php
         }

        endforeach; ?>
</div>

<div id="calendar"></div>





</div>


<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel"><?php echo get_phrase('gerer_le_rendez_vous'); ?></h5>
     
                <button type="button" onclick="closeModal()" class="btn-close" ></button>


            </div>
            <div class="modal-body">
               

                    <div class="form-group">
                        <label for="appointmentTitle"><?php echo get_phrase('titre_du_rendez_vous'); ?></label>
                        <input type="text" class="form-control" id="appointmentTitle" disabled>
                    </div>
                    <div class="form-group">
                        <label for="appointmentDate"><?php echo get_phrase('date_heure'); ?></label>
                        <input type="datetime-local" class="form-control"  id="appointmentDate" disabled>
                    </div>
                    <div class="form-group">
                        <label for="appointmentDescription"><?php echo get_phrase('description'); ?></label>
                        <textarea class="form-control" id="appointmentDescription" rows="3" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="section"><?php echo get_phrase('section'); ?></label>
                        <select class="form-control" name="section[]" id="section" multiple disabled >
                        </select>
                    </div>
                
            </div>
        </div>
    </div>
</div>
	
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">
   $(document).ready(function () {

        $('#section').selectpicker();
    
    function closeModal() {
        $("#appointmentModal").modal("hide");
    }
        var calendar = $('#calendar').fullCalendar({
            titleRangeSeparator: ' - ',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            selectable: true,
            selectHelper: true,
            editable: true,
            eventLimit: true,
            events: "<?= base_url('student/get_appointments'); ?>", // Charge les rendez-vous
           

            // 👉 Ouvrir la popup quand on clique sur une date
            select: function (start, end, allDay) {
           

                $('#appointmentModal_NonID').modal('show');
            },
            eventRender: function(event, element) {
                let time = moment(event.start).format('HH:mm'); // Extraire l'heure correctement
                let title = event.title.replace(/(\d{2})a/, '$1:00 -'); // Nettoyer le titre
                
                element.html(`<strong>${time}</strong> - ${title}`);
            },
            

            // 👉 Modifier un rendez-vous quand on clique dessus
            eventClick: function (event) {
      
                $('#appointmentId').val(event.id); // Stocker l'ID
                $('#appointmentTitle').val(event.title);
                $('#appointmentDate').val(moment(event.start).format('YYYY-MM-DD HH:mm'));
                $('#appointmentDescription').val(event.description);
                // $('#section').val(event.section);
                $('#classe_id').val(event.classe_id);
                $('#room_id').val(event.room_id);
             

                     // Charger les sections dynamiquement
                     $.ajax({
                        url: "<?= base_url('student/get_sections'); ?>",
                        type: "POST",
                        data: { classe_id: event.classe_id },
                        success: function (response) {

                       

                      

                                var $sections = JSON.parse(response);
                                $('#section').empty();

                                $.each(sections, function (key, value) {
                                    $('#section').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                                });

                                // 👇 Sélection multiple
                                let selectedSections = event.section ? event.section.split(',') : [];
                              

                                // ⚠️ Attendre que les <option> soient bien injectés
                                setTimeout(function () {
                                    $('#section').val(selectedSections).trigger('change');
                                    $('#section').selectpicker('destroy'); // Supprime Bootstrap Select
                                    $('#section').selectpicker();
                                }, 100);
                        },
                        error: function () {
                            console.error("Erreur lors du chargement des sections.");
                        }
                    });
                $('#appointmentModal').modal('show');

                // Supprimer l'événement


               

            }
        });

  
    });
</script>
