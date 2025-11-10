<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/payment-settings.css">
<?php
  $paypal = json_decode(get_payment_settings('paypal_settings'));
  $stripe = json_decode(get_payment_settings('stripe_settings'));
  $school_data = $this->settings_model->get_current_school_data();
  $settings_school = $this->settings_model->get_current_settings_school_data();
?>
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
        <form method="POST" class="col-12 systemvatAjaxForm" action="<?php echo route('payment_settings/vat') ;?>" id = "vat_settings">
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
            <input type="hidden" name="tax_residence"  id="tax_residence"  value="<?php echo $result['Tax_residence']; ?>">
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

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    <div class="mb-3">
    <div class="main-card">
        <div class="card-body">
        <h4 class="header-title"><?php echo get_phrase('paypal_settings') ;?></h4>
        <form method="POST" class="col-12 paypalAjaxForm" action="<?php echo route('payment_settings/paypal') ;?>" id = "paypal_settings">
          <!-- Champ caché pour le jeton CSRF -->
          <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

          <div class="col-12">
            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="paypal_active"> <?php echo get_phrase('active') ;?> </label>
              <div class="col-md-9">
                <select class="form-control" name="paypal_active" id="paypal_active">
                  <option value="yes" <?php if ($paypal[0]->paypal_active == 'yes'): ?> selected <?php endif; ?>><?php echo get_phrase('yes') ;?></option>
                  <option value="no" <?php if ($paypal[0]->paypal_active == 'no'): ?> selected <?php endif; ?>><?php echo get_phrase('no') ;?></option>
                </select>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="paypal_currency"> <?php echo get_phrase('paypal_currency') ;?> <span class="required"> * </span></label>
              <div class="col-md-9">
                <select class="form-control"  id = "paypal_currency" name="paypal_currency" required>
                  <option value=""><?php echo get_phrase('select_paypal_currency'); ?></option>
                  <?php
                  $currencies = $this->settings_model->get_paypal_supported_currencies();
                  foreach ($currencies as $currency):?>
                  <option value="<?php echo $currency['code'];?>"
                    <?php if ($paypal[0]->paypal_currency == $currency['code'])echo 'selected';?>> <?php echo $currency['code'];?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="paypal_mode"><?php echo get_phrase('mode') ;?></label>
            <div class="col-md-9">
              <select class="form-control" name="paypal_mode" id="paypal_mode">
                <option value="sandbox" <?php if ($paypal[0]->paypal_mode == 'sandbox'): ?> selected <?php endif; ?>><?php echo get_phrase('sandbox') ;?></option>
                <option value="production" <?php if ($paypal[0]->paypal_mode == 'production'): ?> selected <?php endif; ?>><?php echo get_phrase('production') ;?></option>
              </select>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="paypal_client_id_sandbox"> <?php echo get_phrase('client_id_(sandbox)') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="paypal_client_id_sandbox" name="paypal_client_id_sandbox" class="form-control"  value="<?php echo $paypal[0]->paypal_client_id_sandbox; ?>" required>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="paypal_client_id_production"> <?php echo get_phrase('client_id_(production)') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="paypal_client_id_production" name="paypal_client_id_production" class="form-control"  value="<?php echo $paypal[0]->paypal_client_id_production;?>" required>
            </div>
          </div>
          <div class="row justify-content-md-center">
              <div class="form-group col-md-4">
                <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"  onclick="updatePaypalInfo()"><i class="mdi mdi-account-check"></i><?php echo get_phrase('update_paypal_settings') ;?></button>
              </div>
          </div>
        </div>
      </form>

    </div> <!-- end card body-->
  </div> <!-- end card -->
  </div>
</div>
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
    
    <div class="mb-3">
    <div class="main-card">
        <div class="card-body">
        <h4 class="header-title"><?php echo get_phrase('stripe_settings') ;?></h4>
        <form method="POST" class="col-12 stripeAjaxForm" action="<?php echo route('payment_settings/stripe') ;?>" id = "stripe_settings">
          <!-- Champ caché pour le jeton CSRF -->
         <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
          <div class="col-12">
            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="stripe_active"> <?php echo get_phrase('active') ;?></label>
              <div class="col-md-9">
                <select class="form-control" name="stripe_active" id="stripe_active">
                  <option value="yes" <?php if ($stripe[0]->stripe_active == 'yes'): ?> selected <?php endif; ?>><?php echo get_phrase('yes') ;?></option>
                  <option value="no" <?php if ($stripe[0]->stripe_active == 'no'): ?> selected <?php endif; ?>><?php echo get_phrase('no') ;?></option>
                </select>
              </div>
            </div>

            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="stripe_currency"> <?php echo get_phrase('stripe_currency') ;?><span class="required"> * </span></label>
              <div class="col-md-9">
                <select class="form-control"  id = "stripe_currency" name="stripe_currency" required>
                  <option value=""><?php echo get_phrase('select_stripe_currency'); ?></option>
                  <?php
                  $currencies = $this->settings_model->get_stripe_supported_currencies();
                  foreach ($currencies as $currency):?>
                  <option value="<?php echo $currency['code'];?>"
                    <?php if ($stripe[0]->stripe_currency == $currency['code'])echo 'selected';?>> <?php echo $currency['code'];?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="stripe_mode"><?php echo get_phrase('test_mode') ;?></label>
            <div class="col-md-9">
              <select class="form-control" name="stripe_mode" id="stripe_mode">
                <option value="on" <?php if ($stripe[0]->stripe_mode == 'on'): ?> selected <?php endif; ?>><?php echo get_phrase('on') ;?></option>
                <option value="off" <?php if ($stripe[0]->stripe_mode == 'off'): ?> selected <?php endif; ?>><?php echo get_phrase('off') ;?></option>
              </select>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="stripe_test_secret_key"> <?php echo get_phrase('test_secret_key') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="stripe_test_secret_key" name="stripe_test_secret_key" class="form-control"  value="<?php echo $stripe[0]->stripe_test_secret_key;?>" required>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="stripe_test_public_key"> <?php echo get_phrase('test_public_key') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="stripe_test_public_key" name="stripe_test_public_key" class="form-control"  value="<?php echo $stripe[0]->stripe_test_public_key;?>" required>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="stripe_live_secret_key"> <?php echo get_phrase('live_secret_key') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="stripe_live_secret_key" name="stripe_live_secret_key" class="form-control"  value="<?php echo $stripe[0]->stripe_live_secret_key;?>" required>
            </div>
          </div>

          <div class="form-group row mb-3">
            <label class="col-md-3 col-form-label" for="stripe_live_public_key"> <?php echo get_phrase('live_public_key') ;?><span class="required"> * </span></label>
            <div class="col-md-9">
              <input type="text" id="stripe_live_public_key" name="stripe_live_public_key" class="form-control"  value="<?php echo $stripe[0]->stripe_live_public_key;?>" required>
            </div>
          </div>
           <div class="row justify-content-md-center">
            <div class="form-group col-md-4">
              <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit" onclick="updateStripeInfo()"><i class="mdi mdi-account-check"></i><?php echo get_phrase('update_stripe_settings') ;?></button>
            </div>
           </div>         
          
        </div>
      </form>
    </div> <!-- end card body-->
  </div> <!-- end card -->
  </div>
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
