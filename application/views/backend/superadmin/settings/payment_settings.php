<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/payment-settings.min.css">
<?php
  $paypal = json_decode(get_payment_settings('paypal_settings',1));
  $stripe = json_decode(get_payment_settings('stripe_settings',1));
  $school_data = $this->settings_model->get_current_school_data();
  $settings_school = $this->settings_model->get_current_settings_school_data();
  
  // Charger les billing entities
  $CI =& get_instance();
  $CI->load->library('BillingEntityService', null, 'billingEntityService');
  $billing_entities = $CI->billingEntityService->get_all_active();
?>

<!-- Billing Entities & Payment Methods Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="main-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="m-0 text-white"><i class="fas fa-building mr-2"></i><?php echo get_phrase('Billing Architecture'); ?></h4>
                        <p class="mb-0 mt-1" style="opacity: 0.9;"><?php echo get_phrase('Manage billing entities and payment methods by country/region'); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo site_url('superadmin/billing_entities'); ?>" class="btn btn-light mr-2">
                            <i class="fas fa-building mr-1"></i> <?php echo get_phrase('Billing Entities'); ?>
                        </a>
                        <a href="<?php echo site_url('superadmin/payment_methods'); ?>" class="btn btn-outline-light">
                            <i class="fas fa-credit-card mr-1"></i> <?php echo get_phrase('Payment Methods'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Entities Overview -->
                <div class="row mt-3">
                    <?php if (!empty($billing_entities)): ?>
                        <?php foreach ($billing_entities as $entity): ?>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 rounded" style="background: rgba(255,255,255,0.15);">
                                <div class="d-flex align-items-center">
                                    <span style="font-size: 28px; margin-right: 12px;"><?php echo $entity['country_flag'] ?? '🏳️'; ?></span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($entity['name']); ?></strong>
                                        <div style="font-size: 12px; opacity: 0.9;">
                                            TVA <?php echo $entity['vat_rate']; ?>% • <?php echo $entity['currency_code']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning mb-0" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <?php echo get_phrase('No billing entities configured.'); ?>
                                <a href="<?php echo site_url('superadmin/billing_entities'); ?>" class="text-white" style="text-decoration: underline;">
                                    <?php echo get_phrase('Configure now'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    <div class="mb-3">
    <div class="main-card">
        <div class="card-body">
        <h4 class="header-title"><?php echo get_phrase('Community_pricing') ;?></h4>
        <form method="POST" class="col-12 systempriceAjaxForm" action="<?php echo route('payment_settings/price') ;?>" id = "price_settings">
          <!-- Champ caché pour le jeton CSRF -->
           <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

            <div class="col-12">


              <div class="form-group row mb-3">
                <label class="col-md-3 col-form-label" for="price"> <?php echo get_phrase('Price'); ?> </label>
                <div class="col-md-9">
                  <input <?php if ($settings_school['type'] == 'Particulier'): ?> readonly value="0" <?php endif; ?> type="text" id="price_community" name="price_community" class="form-control"   value="<?php echo $school_data['price']; ?>" placeholder="price"  oninput="checkPriceForParticulier(this)" />
            <small id="price-warning" class="text-danger" <?php if ($settings_school['type'] != 'Particulier'): ?>   style="display:none;" <?php endif; ?>>
                <?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?>
            </small>
                </div>
              </div>


              <div class="row justify-content-md-center">
                <div class="form-group col-md-4">
                  <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit" onclick="updateSystemPrice()" >
                    <i class="mdi mdi-account-check"></i> <?php echo get_phrase('update_price'); ?>
                  </button>
                </div>
              </div>
      </div>
      </form>

      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    <div class="mb-3">
    <div class="main-card">
        <div class="card-body">
        <h4 class="header-title"><?php echo get_phrase('VAT') ;?></h4>
        <form method="POST" class="col-12 systemvatAjaxForm," action="<?php echo route('payment_settings/vat') ;?>" id = "vat_settings">
          <!-- Champ caché pour le jeton CSRF -->
           <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

            <div class="col-12">
              <div class="form-group row mb-3">
                <label class="col-md-3 col-form-label" for="vat_applicable"> <?php echo get_phrase('VAT_applicable'); ?> </label>
                <div class="col-md-9">
                  <?php $result = $this->db->get_where('settings_school', array('school_id' => school_id()))->row_array(); ?>
                  <select class="form-control" name="vat_applicable" id="vat_applicable">
                    <option value=""><?php echo get_phrase('select_Vat'); ?></option>
                    <option value="1"<?php if ($result['vat'] == 1): ?> selected <?php endif; ?>> <?php echo get_phrase('Yes'); ?> </option>
                    <option value="0"<?php if ($result['vat'] == 0): ?> selected <?php endif; ?>> <?php echo get_phrase('No'); ?> </option>
                  </select>
                </div>
              </div>

              <div class="form-group row mb-3">
                <label class="col-md-3 col-form-label" for="vat_rate"> <?php echo get_phrase('VAT_rate'); ?> </label>
                <div class="col-md-9">
                  <input type="text" id="vat_rate" name="vat_rate" class="form-control" readonly value="<?php echo $result['vat']; ?>" placeholder="--" />
                </div>
              </div>

              <div class="form-group row mb-3">
                <div class="col-md-12">
                  <small class="text-muted">
                    <i class="mdi mdi-information-outline"></i>
                    <?php echo get_phrase('wayo_is_not_responsible_for_the_choice,_consult_an_accountant_to_define_if_your_entity_is_subject_to_VAT.'); ?>
                  </small>
                </div>
              </div>

              <div class="row justify-content-md-center">
                <div class="form-group col-md-4">
                  <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit" onclick="updateSystemVat()">
                    <i class="mdi mdi-account-check"></i> <?php echo get_phrase('update_Vat'); ?>
                  </button>
                </div>
              </div>
      </div>
      </form>

      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    <div class="mb-3">
    <div class="main-card">
        <div class="card-body">
        <h4 class="header-title"><?php echo get_phrase('system_currency') ;?></h4>
        <form method="POST" class="col-12 systemAjaxForm" action="<?php echo route('payment_settings/system') ;?>" id = "system_settings">
          <!-- Champ caché pour le jeton CSRF -->
           <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

            <div class="col-12">
              <div class="form-group row mb-3">
                <label class="col-md-3 col-form-label" for="system_currency"> <?php echo get_phrase('system_currency') ;?> <span class="required"> * </span></label>
                <div class="col-md-9">
                  <select class="form-control"  id = "system_currency" name="system_currency" required>
                    <option value=""><?php echo get_phrase('select_system_currency'); ?></option>
                    <?php
                    $currencies = $this->settings_model->get_currencies();
                    
                    foreach ($currencies as $currency):?>
                    <option value="<?php echo $currency['code'];?>"
                      <?php if ($result['system_currency'] == $currency['code'])echo 'selected';?>> <?php echo $currency['code'];?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <?php 
            // Utiliser country depuis schools table (source unique de vérité)
            $school_country = $this->db->get_where('schools', array('id' => school_id()))->row('country');
            ?>
            <input type="hidden" name="tax_residence"  id="tax_residence"  value="<?php echo $school_country ?? ''; ?>">
            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="currency_position"> <?php echo get_phrase('currency_position') ;?><span class="required"> * </span> </label>
              <div class="col-md-9">
              <select class="form-control"  id = "currency_position" name="currency_position" required>
                  <option value="left" <?php if ($result['currency_position'] == 'left') echo 'selected';?> ><?php echo get_phrase('left'); ?></option>
                  <option value="right" <?php if ($result['currency_position'] == 'right') echo 'selected';?> ><?php echo get_phrase('right'); ?></option>
                  <option value="left-space" <?php if ($result['currency_position'] == 'left-space') echo 'selected';?> ><?php echo get_phrase('left_with_a_space'); ?></option>
                  <option value="right-space" <?php if ($result['currency_position'] == 'right-space') echo 'selected';?> ><?php echo get_phrase('right_with_a_space'); ?></option>
                </select>
              </div>
            </div>

            <div class="row justify-content-md-center">
              <div class="form-group col-md-4">
                <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit" onclick="updateSystemCurrencyInfo()"><i class="mdi mdi-account-check"></i><?php echo get_phrase('update_system_currency'); ?></button>
              </div>
            </div>
      </div>
      </form>

      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div>

  <!-- PayPal and Stripe settings are now managed per billing entity -->
  <!-- Access via: Billing Entities > API Keys -->
  </div>
  <?php if(addon_status('payumoney') == 1): ?>
    <?php include 'payumoney_settings.php'; ?>
  <?php endif; ?>
  <?php if(addon_status('paystack') == 1): ?>
    <?php include 'paystack_settings.php'; ?>
  <?php endif; ?>
  
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#paypal_active', '#paypal_mode', '#stripe_active', '#stripe_mode', '#paypal_currency', '#stripe_currency', '#system_currency', '#payment_settings_type']);
  $('#date').daterangepicker();

// Fonction pour récupérer et retourner le token CSRF
function getCsrfToken() {
         // Récupérer le nom du token CSRF depuis le champ input caché
          var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
         // Récupérer la valeur (hash) du token CSRF depuis le champ input caché
           var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
         // Retourner un objet contenant le nom du token et sa valeur
         return { csrfName: csrfName, csrfHash: csrfHash };
      }


 // Soumission du formulaire de logo
 $(".paypalAjaxForm,.systemAjaxForm,.stripeAjaxForm,systemvatAjaxForm,systempriceAjaxForm").submit(function(e) {
    e.preventDefault();

           // Cible uniquement le bouton de ce formulaire
        var submitButton = $(this).find('button[type="submit"]');
        var updating_text = "<?php echo get_phrase('updating'); ?>...";
        
        // Désactive et met à jour uniquement ce bouton
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>'+updating_text);
         // Récupérer le token CSRF avant l'envoi
         var csrf = getCsrfToken(); // Appel de la fonction pour obtenir le token
         const formData = new FormData(this);// Crée une nouvelle instance de FormData en passant l'élément du formulaire courant

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.status) { // Vérifie si la mise à jour a réussi
                // Met à jour le token CSRF
                $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                // Rafraîchissement de la page après un léger délai pour s'assurer que les modifications sont appliquées
                setTimeout(function() {
                  location.reload();
                }, 3500);// Attendre 3500ms avant de recharger la page
            } else {
              error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>')
                
            }
        },
        error: function () {
          error_notify(<?= js_phrase(get_phrase('an_error_occurred_during_submission')); ?>)
        }
      });
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const vatApplicable = document.getElementById('vat_applicable');
    const vatRate       = document.getElementById('vat_rate');
    const taxResidence  = document.getElementById('tax_residence');

    // Map of default VAT rates by tax residence (extend as needed)
    const DEFAULT_VAT_BY_COUNTRY = {
      'MA': '20%',   // Morocco
      'UAE': '5%',  // United Arab Emirates
      // 'SA': '15%',  // Saudi Arabia
      // 'FR': '20%',
      // 'BE': '21%',
      // ...
    };

    function computeVatRate() {
      const applicable = String(vatApplicable.value); // '1' or '0' or ''
      const country    = String((taxResidence?.value || '').toUpperCase());

      if (applicable === '1') {
        vatRate.value = DEFAULT_VAT_BY_COUNTRY[country] || '--';
      } else {
        vatRate.value = '--';
      }
    }

    // Initialize on load (in case the server preselected values)
    computeVatRate();

    // React to changes
    vatApplicable.addEventListener('change', computeVatRate);
  });

  function checkPriceForParticulier(input) {
    const userType = "<?php echo $type; ?>";
    const price = parseFloat(input.value) || 0;
    const warning = document.getElementById('price-warning');

    // Autorise uniquement les chiffres et le point
    input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');

    if (userType === "Particulier" && price > 0) {
        warning.style.display = 'block';
    } else {
        warning.style.display = 'none';
    }
}
</script>
