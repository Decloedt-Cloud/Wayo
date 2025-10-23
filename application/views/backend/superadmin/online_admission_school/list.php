<?php
$school_id = school_id();
?>
<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">

  <thead>
    <tr>
      <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
      <th><i class="mdi mdi-email-outline thead-icon"></i><?php echo get_phrase('email'); ?></th>
      <th><i class="mdi mdi-phone-outline thead-icon"></i><?php echo get_phrase('phone'); ?></th>
      <th><i class="mdi mdi-text-box-outline thead-icon"></i><?php echo get_phrase('description'); ?></th>
      <th><i class="mdi mdi-tag-outline thead-icon"></i><?php echo get_phrase('category'); ?></th>
      <th><?php echo get_phrase('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($schools->result_array() as $schools) {
      $user = $this->db->get_where('users', array('school_id' => $schools['id']))->row_array();
      // $student = $this->db->get_where('students', array('user_id' => $application['id']))->row_array();
    ?>
      <tr>
        <!-- <td>
          <img class="rounded-circle" width="50" src="<?php echo $this->user_model->get_user_image($application['id']); ?>">
        </td> -->
        <td><?php echo $schools['name']; ?></td>

        <td class="modern-td">
          <span class="desktop-description"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="<?php echo htmlspecialchars($user['email']); ?>">
            <?php echo strlen($user['email']) > 30 ? substr($user['email'], 0, 30) . '...' : $user['email']; ?>
          </span>

          <span class="d-inline d-md-none ms-2">
            <?php echo strlen($user['email']) > 13 ? substr($user['email'], 0, 13) . '...' : $user['email']; ?>
          </span>
          <button type="button" class="btn btn-sm mobile-description-btn"
            data-description="<?php echo htmlspecialchars($user['email']); ?>"
            onclick="showDescriptionPopup(this)">
            <i class="mdi mdi-eye-outline"></i>
          </button>
        </td>

        <td><?php echo $schools['phone']; ?></td>
        <td class="modern-td">

          <span class="desktop-description"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="<?php echo htmlspecialchars($schools['description']); ?>">
            <?php echo strlen($schools['description']) > 30 ? substr($schools['description'], 0, 30) . '...' : $schools['description']; ?>
          </span>

          <span class="d-inline d-md-none ms-2">
            <?php echo strlen($schools['description']) > 13 ? substr($schools['description'], 0, 13) . '...' : $schools['description']; ?>
          </span>
          <button type="button" class="btn btn-sm mobile-description-btn"
            data-description="<?php echo htmlspecialchars($schools['description']); ?>"
            onclick="showDescriptionPopup(this)">
            <i class="mdi mdi-eye-outline"></i>
          </button>
        </td>


        <td><?php echo $schools['category']; ?></td>
        <td>
          <div class="dropdown text-center">
            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-right">
              <!-- item-->
              <a href="javascript:;" onclick="rightModal('<?php echo site_url('modal/popup/online_admission_school/add/' . $schools['id']) ?>', '<?php echo get_phrase('approved_school'); ?>');" class="dropdown-item"><?php echo get_phrase('approved'); ?></a>
              <!-- item -->
              <a href="javascript:;" class="dropdown-item" onclick="confirmModalRedirect('<?php echo site_url('superadmin/online_admission_school/delete/' . $schools['id']); ?>')"><?php echo get_phrase('delete'); ?></a>
            </div>
          </div>
        </td>
      </tr>
    <?php } ?>
    <div id="description-popup-overlay" class="description-popup-overlay"></div>
    <div id="description-popup" class="description-popup">
      <div class="description-popup-header">
        <h4><?php echo get_phrase('contenu_complet'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
      </div>
      <div id="description-popup-content" class="description-popup-content">
        <!-- Le contenu sera injecté ici par JavaScript -->
      </div>
    </div>
  </tbody>
</table>
<script>
  // Récupérer les éléments de la popup une seule fois
  const popup = document.getElementById('description-popup');
  const overlay = document.getElementById('description-popup-overlay');
  const popupContent = document.getElementById('description-popup-content');

  /**
   * Affiche la popup avec la description
   * @param {HTMLElement} button - Le bouton sur lequel on a cliqué
   */
  function showDescriptionPopup(button) {
    // Récupérer le texte depuis l'attribut data-description
    const descriptionText = button.dataset.description;

    // Mettre le texte dans la popup
    // On utilise innerText pour la sécurité, mais si votre description contient du HTML, utilisez innerHTML
    popupContent.innerText = descriptionText;

    // Afficher la popup et l'overlay
    popup.classList.add('is-visible');
    overlay.classList.add('is-visible');
  }

  /**
   * Cache la popup
   */
  function hideDescriptionPopup() {
    popup.classList.remove('is-visible');
    overlay.classList.remove('is-visible');
  }

  // Bonus : Fermer la popup en cliquant sur le fond noir
  overlay.addEventListener('click', hideDescriptionPopup);
</script>