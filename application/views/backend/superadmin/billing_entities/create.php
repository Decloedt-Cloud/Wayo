<form method="POST" class="billingEntityForm" action="<?php echo route('billing_entities/create'); ?>">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="code"><?php echo get_phrase('Entity Code'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="code" name="code" placeholder="MA, UAE, EU..." required maxlength="10" style="text-transform: uppercase;">
            <small class="form-text text-muted"><?php echo get_phrase('Unique identifier (e.g., MA for Morocco)'); ?></small>
        </div>
        <div class="form-group col-md-8">
            <label for="name"><?php echo get_phrase('Entity Name'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Decloedt SARL" required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="legal_name"><?php echo get_phrase('Legal Name (Full)'); ?> <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="legal_name" name="legal_name" placeholder="Decloedt SARL - Société à Responsabilité Limitée" required>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-globe mr-2"></i><?php echo get_phrase('Country Information'); ?></h6>
    
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="country_code"><?php echo get_phrase('Country Code'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="country_code" name="country_code" placeholder="MA" required maxlength="3" style="text-transform: uppercase;">
        </div>
        <div class="form-group col-md-6">
            <label for="country_name"><?php echo get_phrase('Country Name'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="country_name" name="country_name" placeholder="Morocco" required>
        </div>
        <div class="form-group col-md-3">
            <label for="country_flag"><?php echo get_phrase('Flag Emoji'); ?></label>
            <input type="text" class="form-control" id="country_flag" name="country_flag" placeholder="🇲🇦" maxlength="10">
        </div>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-percent mr-2"></i><?php echo get_phrase('VAT & Currency'); ?></h6>
    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="vat_rate"><?php echo get_phrase('VAT Rate (%)'); ?> <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" max="100" class="form-control" id="vat_rate" name="vat_rate" placeholder="20.00" required>
        </div>
        <div class="form-group col-md-4">
            <label for="currency_code"><?php echo get_phrase('Currency Code'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="currency_code" name="currency_code" placeholder="MAD" required maxlength="3" style="text-transform: uppercase;">
        </div>
        <div class="form-group col-md-4">
            <label for="currency_symbol"><?php echo get_phrase('Currency Symbol'); ?></label>
            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" placeholder="DH" maxlength="10">
        </div>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-credit-card mr-2"></i><?php echo get_phrase('Payment & Banking'); ?></h6>
    
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="psp_name"><?php echo get_phrase('PSP Name'); ?></label>
            <input type="text" class="form-control" id="psp_name" name="psp_name" placeholder="Stripe, PayPal, CMI...">
        </div>
        <div class="form-group col-md-6">
            <label for="psp_type"><?php echo get_phrase('PSP Type'); ?></label>
            <select class="form-control" id="psp_type" name="psp_type">
                <option value="international"><?php echo get_phrase('International'); ?></option>
                <option value="local"><?php echo get_phrase('Local'); ?></option>
            </select>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="bank_name"><?php echo get_phrase('Bank Name'); ?></label>
            <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Banque Populaire">
        </div>
        <div class="form-group col-md-6">
            <label for="bank_country"><?php echo get_phrase('Bank Country'); ?></label>
            <input type="text" class="form-control" id="bank_country" name="bank_country" placeholder="Morocco">
        </div>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-palette mr-2"></i><?php echo get_phrase('Display Settings'); ?></h6>
    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="color_primary"><?php echo get_phrase('Primary Color'); ?></label>
            <input type="color" class="form-control" id="color_primary" name="color_primary" value="#c62828" style="height: 40px;">
        </div>
        <div class="form-group col-md-4">
            <label for="color_secondary"><?php echo get_phrase('Secondary Color'); ?></label>
            <input type="color" class="form-control" id="color_secondary" name="color_secondary" value="#e53935" style="height: 40px;">
        </div>
        <div class="form-group col-md-4">
            <label for="display_order"><?php echo get_phrase('Display Order'); ?></label>
            <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0">
        </div>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-cog mr-2"></i><?php echo get_phrase('Options'); ?></h6>
    
    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                <label class="custom-control-label" for="is_active"><?php echo get_phrase('Active'); ?></label>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1">
                <label class="custom-control-label" for="is_default"><?php echo get_phrase('Set as Default'); ?></label>
            </div>
        </div>
    </div>
    
    <hr>
    <h6 class="text-muted"><i class="fas fa-link mr-2"></i><?php echo get_phrase('Tax Residence Mappings'); ?></h6>
    <p class="text-muted small"><?php echo get_phrase('Enter comma-separated codes that should map to this entity (e.g., MA,MAR,MOR)'); ?></p>
    
    <div class="form-group">
        <label for="mappings"><?php echo get_phrase('Tax Residence Codes'); ?></label>
        <input type="text" class="form-control" id="mappings" name="mappings" placeholder="MA, MAR">
        <small class="form-text text-muted"><?php echo get_phrase('These codes from settings_school.Tax_residence will use this entity'); ?></small>
    </div>
    
    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-2"></i><?php echo get_phrase('Create Entity'); ?>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('.billingEntityForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status) {
                    toastr.success(data.notification);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.notification);
                }
            },
            error: function() {
                toastr.error('<?php echo get_phrase('An error occurred'); ?>');
            }
        });
    });
});
</script>

