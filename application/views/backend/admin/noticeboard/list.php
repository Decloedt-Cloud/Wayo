<style>
  .alert-modern {
    background-color: #ffffff; /* fond blanc */
    border-left: 6px solid #6c757d; /* bordure verticale grise */
    color: #495057; /* texte gris foncé */
       font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 18px; /* taille augmentée */
    font-weight: 600; /* texte gras */
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
  }

  .alert-modern .icon {
    background-color: #6c757d; /* icône gris */
    color: #fff;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .alert-modern:hover {
    background-color: #f8f9fa; /* légère surbrillance au hover */
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  }

  .alert-modern .text {
    line-height: 1.5;
  }
</style>
<div class="alert-modern d-flex align-items-center p-3 rounded-2 mb-3" role="alert">
  <div class="icon flex-shrink-0 me-3">
    <i class="dripicons-information"></i>
  </div>
  <div class="text flex-grow-1">
    <?php echo get_phrase('this_tab_allows_you_to_publish_an_announcement_showcase_website'); ?><!--<strong><?php echo get_phrase('user'); ?> ( <?php echo get_phrase('backend'); ?> ) <?php echo get_phrase('panel_events'); ?></strong>.-->
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div id="calendar" class="notice-calendar-section"></div>
      </div>
    </div>
  </div>
</div>
