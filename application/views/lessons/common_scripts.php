<script type="text/javascript">
//First sections
//saving the current progress and starting from the saved progress
var newProgress;
var savedProgress;
var currentProgress = '<?php echo lesson_progress($lesson_id); ?>';
var lessonType = '<?php echo $lesson_details['lesson_type']; ?>';
var videoProvider = '<?php echo $provider; ?>';
let remainingTime;
let timerInterval;
let currentQuestion = 1; // Variable pour suivre la question actuelle
let quizSubmitted = false;  // Drapeau pour vérifier si le quiz a été soumis
var isUpdatingProgress = false; // Flag to prevent concurrent AJAX requests

// Initialize savedProgress
savedProgress = currentProgress ? parseFloat(currentProgress) : 0;
if (isNaN(savedProgress)) savedProgress = 0;

function markThisLessonAsCompleted(lesson_id) {
  // If an update is already in progress, retry in 500ms to avoid CSRF token conflict
  if (isUpdatingProgress) {
    setTimeout(function() { markThisLessonAsCompleted(lesson_id); }, 500);
    return;
  }

  isUpdatingProgress = true;
  $('#sidebar-content').css('opacity', '0.5');
  $('#lesson_list_loader').show();
  var progress;
  
  // Robust CSRF token retrieval
  var csrfInput = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]');
  var csrfName = csrfInput.attr('name');
  var csrfHash = csrfInput.val();
  
  // Use the new checkbox ID format: lesson-{id}
  var checkbox = document.getElementById('lesson-' + lesson_id);
  if (checkbox && checkbox.checked) {
    progress = 1;
  } else {
    progress = 0;
  }
  
  var data = {lesson_id : lesson_id, progress : progress};
  if (csrfName) {
      data[csrfName] = csrfHash;
  }

  $.ajax({
    type : 'POST',
    url : '<?php echo site_url('addons/courses/save_course_progress'); ?>',
    data : data,
    dataType: 'json',
    success : function(response){
      currentProgress = response.html;

      // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
      if(response.csrf) {
          var newCsrfName = response.csrf.csrfName;
          var newCsrfHash = response.csrf.csrfHash;
          $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF
      }

      $('#sidebar-content').css('opacity', '1');
      $('#lesson_list_loader').hide();
    },
    complete: function() {
      isUpdatingProgress = false;
    },
    error: function() {
      isUpdatingProgress = false;
      $('#sidebar-content').css('opacity', '1');
      $('#lesson_list_loader').hide();
    }
  });
}


var timer = setInterval(function(){

  if (lessonType == 'video' && videoProvider == 'html5' && currentProgress != 1) {
    getCurrentTime();
  }
}, 1000);

$(document).ready(function() {
  if (lessonType == 'video' && videoProvider == 'html5') {
    var playerEl = document.querySelector('#player');
    if (playerEl) {
      var totalDuration = playerEl.duration;

      if (currentProgress == 1 || currentProgress == totalDuration) {
        playerEl.currentTime = 0;
      } else {
        playerEl.currentTime = currentProgress;
      }
    }
  }
});

var counter = 0;
if (typeof player !== 'undefined' && player && typeof player.on === 'function') {
  player.on('canplay', event => {
    if (counter == 0) {
      var playerEl = document.querySelector('#player');
      if (playerEl) {
        if (currentProgress == 1) {
          playerEl.currentTime = 0;
        } else {
          playerEl.currentTime = currentProgress;
        }
      }
    }
    counter++;
  });
}

function getCurrentTime() {
  if (isUpdatingProgress) return; // Skip if another request is in progress

  var lesson_id = '<?php echo $lesson_id; ?>';
  var playerEl = document.querySelector('#player');
  if (!playerEl) return;
  
  newProgress = playerEl.currentTime;
  var totalDuration = playerEl.duration;

  console.log('Current Progress is '+currentProgress);
  console.log('New Progress is '+newProgress);

  if (newProgress != savedProgress && newProgress > 0 && currentProgress != 1) {

    // Throttle updates: only update if difference is > 5 seconds or video finished
    if (Math.abs(newProgress - savedProgress) < 5 && newProgress != totalDuration) {
        return;
    }

    // if the user watches the entire video the lesson will be marked as seen automatically.
    if (totalDuration == newProgress) {
      newProgress = 1;
      // Use the new checkbox ID format
      var checkbox = document.getElementById('lesson-' + lesson_id);
      if (checkbox) checkbox.checked = true;
    }

    var csrfInput = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]');
    var csrfName = csrfInput.attr('name');
    var csrfHash = csrfInput.val();

    isUpdatingProgress = true;

    // update the video prgress here.
    var data = {lesson_id : lesson_id, progress : newProgress};
    if (csrfName) {
        data[csrfName] = csrfHash;
    }

    $.ajax({
      type : 'POST',
      url : '<?php echo site_url('addons/courses/save_course_progress'); ?>',
      data : data,
      dataType: 'json',
      success : function(response){
        savedProgress = response.html;
        if(response.csrf) {
            var newCsrfName = response.csrf.csrfName;
            var newCsrfHash = response.csrf.csrfHash;
            $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
        }
      },
      complete: function() {
        isUpdatingProgress = false;
      },
      error: function() {
        isUpdatingProgress = false;
      }
    });
  }
}




//SECONDS SECTIONS
function toggleAccordionIcon(elem, section_id) {
  var accordion_section_ids = [];
  $(".accordion_icon").each(function(){ accordion_section_ids.push(this.id); });
  accordion_section_ids.forEach(function(item) {
    if (item === 'accordion_icon_'+section_id) {
      if ($('#'+item).html().trim() === '<i class="fa fa-plus"></i>') {
        $('#'+item).html('<i class="fa fa-minus"></i>')
      }else {
        $('#'+item).html('<i class="fa fa-plus"></i>')
      }
    }else{
      $('#'+item).html('<i class="fa fa-plus"></i>')
    }
  });
}

function checkCourseProgression() {
  $.ajax({
    url: '<?php echo site_url('home/check_course_progress/'.$course_id);?>',
    success: function(response)
    {
      if (parseInt(response) === 100) {
        $('#download_certificate_area').show();
        $('#certificate-alert-success').show();
        $('#certificate-alert-warning').hide();
      }else{
        $('#download_certificate_area').hide();
        $('#certificate-alert-success').hide();
        $('#certificate-alert-warning').show();
      }
      $('#progression').text(Math.round(response));
      $('#course_progress_area').attr('data-percent', Math.round(response));
      initProgressBar(Math.round(response));
    }
  });
}

function initProgressBar(dataPercent) {
  console.log("Data Percent" + dataPercent);
  var totalProgress, progress;
  const circles = document.querySelectorAll('.circular-progress');
  for(var i = 0; i < circles.length; i++) {
    totalProgress = circles[i].querySelector('circle').getAttribute('stroke-dasharray');
    progress = dataPercent;

    circles[i].querySelector('.bar').style['stroke-dashoffset'] = totalProgress * progress / 100;
  }
}




//THIRD SECTIONS
function toggle_lesson_view() {
    $('.lessons-sidebar').toggle();
}




//FORTH SECTIONS
// JavaScript pour gérer le chronomètre
function getStarted(first_quiz_question) {
    $('#quiz-header').hide();
    $('#lesson-summary').hide();
    $('#question-number-' + first_quiz_question).removeClass('hidden').show();
    currentQuestion = first_quiz_question;
    startTimer(30);
}

function showNextQuestion(next_question) {
    $('#question-number-' + (next_question - 1)).addClass('hidden').hide();
    $('#question-number-' + next_question).removeClass('hidden').show();
    currentQuestion = next_question;
    startTimer(30);
}

function startTimer(duration) {
    // Effacer tout intervalle existant pour éviter les doublons
    if (timerInterval) {
        clearInterval(timerInterval);
    }

    // Initialiser le temps restant
    remainingTime = duration;

    // Reset timer bar animation
    var timerBar = document.getElementById('timer-bar-' + currentQuestion);
    if (timerBar) {
        timerBar.style.animation = 'none';
        timerBar.offsetHeight; // Trigger reflow
        timerBar.style.animation = 'timerCountdown 30s linear forwards';
    }

    // Démarrer le nouvel intervalle de mise à jour du chronomètre
    timerInterval = setInterval(updateTimer, 1000);
}

function updateTimer() {
    let minutes = Math.floor(remainingTime / 60);
    let seconds = remainingTime % 60;
    
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
 
    var timerEl = document.getElementById('timer' + currentQuestion);
    if (timerEl) {
        timerEl.textContent = minutes + ':' + seconds;
    }
    
    if (remainingTime <= 0) {
        clearInterval(timerInterval);
        
        // Si ce n'est pas la dernière question, passer à la question suivante
        if (currentQuestion < <?php echo isset($quiz_questions) && $quiz_questions ? count($quiz_questions->result_array()) : 0; ?>) {
           currentQuestion++; 
          showNextQuestion(currentQuestion); // Passe automatiquement à la question suivante
        } else {
            // Vérifier si le quiz n'a pas déjà été soumis
            if (!quizSubmitted) {
                quizSubmitted = true;  // Marquer le quiz comme soumis
                submitQuiz(); // Soumettez le quiz une seule fois
            }
        }
    } else {
        remainingTime--;
    }
}

function stopTimer() {
    clearInterval(timerInterval);
}

function submitQuiz() {
  quizSubmitted = true;
  var lesson_id = '<?php echo $lesson_id; ?>';

  // Sérialiser le formulaire AVANT tout autre appel AJAX (pour garder un CSRF valide)
  var formData = $('form#quiz_form').serialize();

  $.ajax({
      url: '<?php echo site_url('addons/lessons/submit_quiz'); ?>',
      type: 'post',
      data: formData,
      success: function(response) {
          // Afficher les résultats
          $('.quiz-wrapper #quiz-header').hide();
          $('.quiz-wrapper form').hide();
          $('#quiz-result').html(response).show();

          // Marquer la leçon comme complétée APRÈS la soumission du quiz
          var checkbox = document.getElementById('lesson-' + lesson_id);
          if (checkbox) {
            checkbox.checked = true;
          }
          markThisLessonAsCompleted(lesson_id);
      }
  });
}

function check_result() {
  quizSubmitted = true;
  var lesson_id = '<?php echo $lesson_id; ?>';

  var formData = $('form#quiz_form').serialize();

  $.ajax({
      url: '<?php echo site_url('addons/lessons/check_result'); ?>',
      type: 'post',
      data: formData,
      success: function(response) {
          $('.quiz-wrapper #quiz-header').hide();
          $('.quiz-wrapper form').hide();
          $('#quiz-result').html(response).show();

          var checkbox = document.getElementById('lesson-' + lesson_id);
          if (checkbox) {
            checkbox.checked = true;
          }
          markThisLessonAsCompleted(lesson_id);
      }
  });
}

function enableNextButton(quizID) {
    $('#next-btn-'+quizID).prop('disabled', false);
}

function retakeQuiz() {
    window.location.reload();
}
</script>
