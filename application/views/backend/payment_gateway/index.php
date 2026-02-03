<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	    <title><?php echo get_phrase($page_title); ?><?php 
            $school_id = school_id();
            if ($school_id > 0) {
                echo ' | ' . $this->db->get_where('schools', array('id' => $school_id))->row('name');
            }
        ?></title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
		
	    <meta content="Creativeitem" name="author" />
	    <!-- App favicon -->
		<link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">

		<?php include 'includes_top.php';?>

		<link href="<?php echo base_url('assets/payment/css/stripe.css');?>"
        rel="stylesheet">
		
	</head>
	<body>
		
		<?php
		// --------- DEFINE payment_type from invoice_details ---------
		$payment_type = isset($invoice_details['payment_type']) ? $invoice_details['payment_type'] : '';
		
		// --------- TVA / VAT CALCULATION ---------
		// On récupère les réglages fiscaux de la communauté / école
		$school_id_for_vat = isset($invoice_details['school_id']) ? $invoice_details['school_id'] : school_id();
		$settings_school = $this->settings_model->get_settings_school_data($school_id_for_vat);
		
		// Récupérer le country depuis la table schools (source principale)
		$school_data = $this->db->get_where('schools', ['id' => $school_id_for_vat])->row_array();
		
		// Support both old 'vat' and new 'vat_enabled' column names
		$vat_applicable = (isset($settings_school['vat_enabled']) && (int)$settings_school['vat_enabled'] === 1) 
		                || (isset($settings_school['vat']) && (int)$settings_school['vat'] === 1);
		
		// Utiliser country depuis schools table (source unique de vérité)
		$tax_residence = null;
		if (!empty($school_data['country'])) {
			$tax_residence = strtoupper($school_data['country']); // MA, AE, FR...
		}
	
		// IMPORTANT: Pour les paiements subscription_admin (abonnement communauté), 
		// toujours appliquer TVA Maroc par défaut si pas de country configuré
        // ET forcer l'application de la TVA pour les juridictions connues (MA, UAE, AE)
		if (isset($invoice_details['payment_type']) && $invoice_details['payment_type'] === 'subscription_admin') {
            if (empty($tax_residence)) {
			    $tax_residence = 'MA'; // Default to Morocco for subscription payments
            }
            
            if (in_array($tax_residence, ['MA', 'AE', 'UAE'])) {
			    $vat_applicable = true; // Force VAT applicable regardless of school settings
            }
		}
		// DEBUG: Afficher les valeurs pour le debug
		// error_log("DEBUG VAT: school_id=$school_id_for_vat, vat_applicable=$vat_applicable, tax_residence='$tax_residence'");

		// Déterminer la devise d'affichage selon country (AE ou UAE pour Emirats)
		$display_currency = (in_array($tax_residence, ['UAE', 'AE'])) ? 'AED' : 'MAD';
		$original_currency = isset($invoice_details['currency']) ? $invoice_details['currency'] : 'MAD'; // Toujours MAD en base par défaut

		// Initialiser les variables VAT par défaut
		$sub_total = (float)$amount_to_pay;
		$vat_amount = 0;
		$vat_rate = 0;
		$grand_total = (float)$amount_to_pay;

		// Forcer les valeurs pour UAE/AE si détecté (même sans calculs VAT complets)
		if (in_array($tax_residence, ['UAE', 'AE']) && $display_currency === 'AED') {
			// Calculs simplifiés pour UAE
			$vat_rate = 5;
            
            // Si la devise d'origine est déjà AED, pas de conversion
            if ($original_currency === 'AED') {
                $sub_total = round($amount_to_pay / 1.05, 2);
                $vat_amount = round($amount_to_pay - $sub_total, 2);
                $grand_total = (float)$amount_to_pay; // Keep original amount to avoid rounding issues
            } else {
                // Conversion approximative si devise MAD (legacy)
                $sub_total = round($amount_to_pay / 1.05, 2); 
                $vat_amount = round($sub_total * 0.05, 2);
                $grand_total = round($sub_total + $vat_amount, 2);
            }
		} elseif ($tax_residence === 'MA') {
            // Calculs simplifiés pour MA (20%) - Comportement identique à AE
            $vat_rate = 20;
            $sub_total = round($amount_to_pay / 1.20, 2);
            $vat_amount = round($amount_to_pay - $sub_total, 2);
            $grand_total = (float)$amount_to_pay;
        }

		// Load Morocco B2B service for calculations
		$CI =& get_instance();
		$CI->load->library('MoroccoB2BService', null, 'moroccoService');
		
		// =====================================================
		// BILLING ENTITY SYNC - Récupère l'entité depuis Superadmin
		// =====================================================
		$CI->load->library('BillingEntityService', null, 'billingEntityService');
		$billing_entity = $CI->billingEntityService->get_entity_for_tax_residence($tax_residence);

		// Si une entité est trouvée, utiliser ses paramètres
		if ($billing_entity) {
			$entity_vat_rate = $billing_entity['vat_rate'] ?? ($tax_residence === 'MA' ? 20 : 5);
			$entity_currency = $billing_entity['currency_code'] ?? ($tax_residence === 'MA' ? 'MAD' : 'AED');
			$entity_name = $billing_entity['name'] ?? ($tax_residence === 'MA' ? 'Decloedt SARL' : 'Bouhouti');
			$entity_legal_name = $billing_entity['legal_name'] ?? $entity_name;
			$entity_psp = $billing_entity['psp_name'] ?? 'Stripe';
			$entity_color = $billing_entity['color_primary'] ?? '#1a237e';
			$entity_id = $billing_entity['id'] ?? null;
		} else {
			// Fallback si pas d'entité configurée
			$entity_vat_rate = ($tax_residence === 'MA') ? 20 : 5;
			$entity_currency = ($tax_residence === 'MA') ? 'MAD' : 'AED';
			$entity_name = ($tax_residence === 'MA') ? 'Decloedt SARL' : 'Bouhouti';
			$entity_legal_name = $entity_name;
			$entity_psp = 'Stripe';
			$entity_color = ($tax_residence === 'MA') ? '#c62828' : '#00695c';
			$entity_id = null;
		}

		// =====================================================
		// PAYMENT METHODS SYNC - Filtre selon l'entité Superadmin
		// =====================================================
		$CI->load->model('PaymentMethod_model', 'payment_method_model');
		
		// Récupérer les méthodes autorisées pour cette entité
		$entity_payment_methods = [];
		$stripe_allowed_by_entity = false;
		$paypal_allowed_by_entity = false;
		
		if ($entity_id) {
			// Méthodes liées à l'entité dans Superadmin
			$entity_methods = $CI->payment_method_model->get_for_entity($entity_id, true);
			
			foreach ($entity_methods as $method) {
				$entity_payment_methods[] = $method['code'];
				if ($method['code'] === 'stripe') $stripe_allowed_by_entity = true;
				if ($method['code'] === 'paypal') $paypal_allowed_by_entity = true;
			}
		}
		
		// Si aucune méthode configurée dans l'entité, autoriser toutes (fallback)
		if (empty($entity_payment_methods)) {
			$stripe_allowed_by_entity = true;
			$paypal_allowed_by_entity = true;
		}
		
		// =====================================================
		// LOGIQUE DE CREDENTIALS SELON LE TYPE DE PAIEMENT
		// =====================================================
		// - subscription_admin (Admin → Superadmin) : billing_entity_credentials
		// - autres (Student → Admin) : payment_settings (ancien comportement)
		
		$is_subscription_admin = isset($invoice_details['payment_type']) && $invoice_details['payment_type'] === 'subscription_admin';
		
		if ($is_subscription_admin && $entity_id) {
			// ========== ADMIN → SUPERADMIN : billing_entity_credentials ==========
			$CI->load->model('BillingEntityCredentials_model', 'billing_entity_credentials_model');
			
			// Vérifier Stripe dans billing_entity_credentials
			$stripe_creds = $CI->billing_entity_credentials_model->get_stripe_credentials($entity_id);
			$stripe_has_entity_creds = !empty($stripe_creds) && !empty($stripe_creds['public_key']) && !empty($stripe_creds['secret_key']);
			
			// Vérifier PayPal dans billing_entity_credentials
			$paypal_creds = $CI->billing_entity_credentials_model->get_paypal_credentials($entity_id);
			$paypal_has_entity_creds = !empty($paypal_creds) && !empty($paypal_creds['client_id']);
			
			// Activer uniquement si credentials présents dans billing_entity_credentials
			$stripe_enabled = $stripe_has_entity_creds && $stripe_allowed_by_entity;
			$paypal_enabled = $paypal_has_entity_creds && $paypal_allowed_by_entity;
			
			// Mettre à jour les variables de configuration si credentials trouvés
			if ($stripe_has_entity_creds) {
				$stripe_public_key = $stripe_creds['public_key'];
				$stripe_private_key = $stripe_creds['secret_key'];
				$stripe_currency = $stripe_creds['currency'] ?? 'MAD';
			}
			if ($paypal_has_entity_creds) {
				$paypal_client_id_sandbox = ($paypal_creds['mode'] === 'sandbox') ? $paypal_creds['client_id'] : '';
				$paypal_client_id_production = ($paypal_creds['mode'] === 'production') ? $paypal_creds['client_id'] : '';
				$paypal_mode = $paypal_creds['mode'];
				$paypal_currency = $paypal_creds['currency'] ?? 'EUR';
			}
		} else {
			// ========== STUDENT → ADMIN : payment_settings (ancien comportement) ==========
			// Utiliser directement les valeurs passées depuis Admin.php/Student.php
			// PAS de filtrage par billing_entity - juste payment_settings de l'école
			$stripe_enabled = isset($stripe_enabled) && $stripe_enabled;
			$paypal_enabled = isset($paypal_enabled) && $paypal_enabled;
			// Les variables $stripe_public_key, $paypal_client_id_sandbox, etc. 
			// sont déjà définies depuis le contrôleur via payment_settings
		}

		// Check if Morocco B2B applies
		// Morocco B2B rules apply ONLY for subscription_admin payments AND Tax_residence = MA
		$is_morocco_b2b = false;
		if ($invoice_details['payment_type'] === 'subscription_admin' && $tax_residence === 'MA') {
			$morocco_context = [
				'country' => 'MA',
				'customer_type' => 'B2B',
				'payment_type' => 'subscription_admin'
			];
			$is_morocco_b2b = $CI->moroccoService->shouldApplyMoroccoB2BRules($morocco_context);
		}

		// Apply Morocco B2B calculations for subscription invoices
		if ($is_morocco_b2b) {
			// Calculate Morocco B2B values
			$morocco_calculations = $CI->moroccoService->calculateMoroccoB2BAmounts($invoice_details['total_amount']);

			// Override invoice details with Morocco calculations BEFORE using them
			$invoice_details['sub_total'] = $morocco_calculations['sale_ht'];
			$invoice_details['vat_amount'] = $morocco_calculations['sale_vat'];
			$invoice_details['vat_rate'] = $morocco_calculations['vat_rate'] * 100; // Store as percentage
			$invoice_details['processor_fee_ht'] = $morocco_calculations['fee_ht'];
			$invoice_details['processor_fee_vat'] = $morocco_calculations['fee_vat'];
			$invoice_details['processor_fee_ttc'] = $morocco_calculations['fee_ttc'];
			$invoice_details['net_cash'] = $morocco_calculations['net_cash'];
			$invoice_details['net_economic'] = $morocco_calculations['net_economic'];
			
			// Also set the display variables for Morocco B2B
			$sub_total = $morocco_calculations['sale_ht'];
			$vat_amount = $morocco_calculations['sale_vat'];
			$vat_rate = $morocco_calculations['vat_rate'] * 100; // 20%
			$grand_total = $invoice_details['total_amount']; // TTC stays the same
			$display_currency = 'MAD';
		}

		// Define has_vat_config for JavaScript protection
		// Includes Morocco B2B and general VAT configurations
		$has_general_vat = $vat_applicable && in_array($tax_residence, ['MA', 'AE', 'UAE']);
		$has_vat_config = $is_morocco_b2b || $has_general_vat;

		// VAT CALCULATION BASED ON COMMUNITY TAX RESIDENCE
		// Always apply VAT calculations if community has Tax_residence configured
		// SKIP if Morocco B2B already calculated (to avoid override)
		if (!$is_morocco_b2b && $vat_applicable && !empty($tax_residence) && in_array($tax_residence, ['MA', 'AE', 'UAE'])) {
			// Load VatResolver service
			$CI->load->library('VatResolver', null, 'vatResolver');

			try {
				// Calculate VAT using VatResolver (toujours en MAD d'abord)
				$vat_calculation = $CI->vatResolver->calculateVatBreakdown((float)$amount_to_pay, $tax_residence);

				// Apply calculated values (en MAD)
				$sub_total_mad = $vat_calculation['sub_total'];
				$vat_amount_mad = $vat_calculation['vat_amount'];
				$vat_rate = $vat_calculation['vat_rate'] * 100; // Store as percentage
				$grand_total_mad = $vat_calculation['total_ttc'];

				// Pour UAE, convertir les montants vers AED
				if ($tax_residence === 'UAE' || $tax_residence === 'AE') {
                    // Check if invoice is already in AED
                    $invoice_currency = isset($invoice_details['currency']) ? $invoice_details['currency'] : 'MAD';
                    
                    // IGNORE CONVERSION: Always treat amounts as 1:1 regardless of currency label
                    // This assumes that for AE context, the amount provided IS the AED amount
                    $conversion_rate = 1.0;
                    
                    // Recalculate VAT from the total amount (assuming total is TTC)
                    // We use the total from VAT calculation (which is in MAD/Base currency) as the AED total
                    $grand_total = $grand_total_mad;
                    $sub_total = round($grand_total / 1.05, 2);
                    $vat_amount = round($grand_total - $sub_total, 2);
                    
                    /* 
                    if ($invoice_currency === 'AED') {
                        // Already in AED, no conversion needed
                        $conversion_rate = 1.0;
                        $sub_total = round($amount_to_pay / 1.05, 2);
                        $vat_amount = round($amount_to_pay - $sub_total, 2);
                        $grand_total = (float)$amount_to_pay;
                    } else {
                        // Récupérer le taux de conversion MAD → AED via FxRatesService
                        $CI->load->library('FxRatesService', null, 'fxrates_service');
                        
                        // Utiliser la méthode convert pour obtenir le taux
                        $test_convert = $CI->fxrates_service->convert(1, 'MAD', 'AED');
                        $conversion_rate = ($test_convert !== false) ? $test_convert : 0.37; // Fallback rate
                        
                        $sub_total = round($sub_total_mad * $conversion_rate, 2);
                        $vat_amount = round($vat_amount_mad * $conversion_rate, 2);
                        $grand_total = round($grand_total_mad * $conversion_rate, 2);
                    }
                    */
					
					// Log pour debug
					error_log("UAE Conversion IGNORED: Rate=1.0, sub_total={$sub_total}, vat={$vat_amount}, total={$grand_total}");
				} else {
					$sub_total = $sub_total_mad;
					$vat_amount = $vat_amount_mad;
					$grand_total = $grand_total_mad;
					$conversion_rate = 1.0;
				}

				// Store VAT information for display
				$vat_info = [
					'vat_rate' => $vat_rate,
					'vat_amount' => $vat_amount,
					'sub_total' => $sub_total,
					'grand_total' => $grand_total,
					'legal_entity_name' => $vat_calculation['legal_entity_name'],
					'legal_entity_country' => $vat_calculation['legal_entity_country'],
					'tax_country' => $vat_calculation['country_name'],
					'display_currency' => $display_currency,
					'conversion_rate' => $conversion_rate
				];

			} catch (Exception $e) {
				// Fallback if VAT calculation fails
				error_log("VAT calculation failed for tax_residence {$tax_residence}: " . $e->getMessage());
				// Keep existing values (from Simplified or B2B logic) instead of resetting to 0
				// $sub_total = (float)$amount_to_pay;
				// $vat_amount = 0;
				// $vat_rate = 0;
				// $grand_total = (float)$amount_to_pay;
			}
		} 
		// REMOVED ELSE BLOCK to prevent overwriting B2B/Simplified logic
		// If no VAT logic applies, values remain at defaults (initialized at top) or as set by Simplified Logic
		
		// --------- CURRENCY CONVERSION (FX RATES) ---------
		// Devise originale de la facture
		$original_currency = isset($fx_original_currency) ? $fx_original_currency : (isset($currency) ? strtoupper($currency) : 'USD');
		$conversion_needed = isset($fx_conversion_needed) ? $fx_conversion_needed : false;
		$fx_stale_flag = isset($fx_stale) ? $fx_stale : false;
		$fx_rate_date = isset($fx_rate_date) ? $fx_rate_date : date('Y-m-d');
		
		// IMPORTANT: DISABLE ALL CONVERSIONS FOR SUBSCRIPTION_ADMIN
		// Payments between Admin and Superadmin should always use the invoice amount directly
		if ($is_subscription_admin) {
			$conversion_needed = false;
			$stripe_fx_rate = 1.0;
			$stripe_converted_amount = $grand_total;
			$paypal_fx_rate_val = 1.0;
			$paypal_converted_amount = $grand_total;
			$paypal_sub_total_converted = $sub_total;
			$paypal_vat_amount_converted = $vat_amount;
			$stripe_sub_total_converted = $sub_total;
			$stripe_vat_amount_converted = $vat_amount;
			
			// Force display currencies to match invoice currency
			$stripe_currency = $display_currency;
			$paypal_currency = $display_currency;
		}

		// Montants originaux dans la devise de la facture
		$original_sub_total = $sub_total;
		$original_vat_amount = $vat_amount;
		$original_grand_total = $grand_total;

		// Montants convertis pour Stripe
		$stripe_fx_rate_val = isset($stripe_fx_rate) ? (float)$stripe_fx_rate : 1.0;
		$stripe_sub_total = round($original_sub_total * $stripe_fx_rate_val, 2);
		$stripe_vat_amount = round($original_vat_amount * $stripe_fx_rate_val, 2);
		$stripe_grand_total = isset($stripe_converted_amount) ? (float)$stripe_converted_amount : $grand_total;
		
		// Montants convertis pour PayPal
		$paypal_fx_rate_val = isset($paypal_fx_rate_val) ? (float)$paypal_fx_rate_val : 1.0;

		// Utiliser les montants calculés depuis Admin.php si disponibles, sinon calculer localement
		if (isset($paypal_sub_total_converted) && isset($paypal_vat_amount_converted)) {
			// Utiliser les valeurs passées depuis Admin.php (déjà calculées correctement)
			$paypal_sub_total = (float)$paypal_sub_total_converted;
			$paypal_vat_amount = (float)$paypal_vat_amount_converted;
		} else {
			// Fallback : calculer localement (ancienne méthode)
			$paypal_sub_total = round($original_sub_total * $paypal_fx_rate_val, 2);
			$paypal_vat_amount = round($original_vat_amount * $paypal_fx_rate_val, 2);
		}

		$paypal_grand_total = isset($paypal_converted_amount) ? (float)$paypal_converted_amount : $grand_total;
		
		// Déterminer si Stripe ou PayPal a besoin de conversion
		$stripe_needs_conversion = ($is_subscription_admin) ? false : (strtoupper($stripe_currency) !== $original_currency);
		$paypal_needs_conversion = ($is_subscription_admin) ? false : (strtoupper($paypal_currency) !== $original_currency);
		?>

		<div class="checkout-container container p-0" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
			<div class="row g-0">
				<!-- LEFT SIDE -->
				<section class="payment-section col-12 col-md-7 p-4">
					<!-- Header avec icône -->
					<div class="payment-header mb-4">
						<div class="d-flex align-items-center">
							<div class="header-icon">
								<i class="fa fa-credit-card"></i>
							</div>
							<div class="ml-3">
								<h2 class="mb-0"><?php echo get_phrase('Complete your payment'); ?></h2>
								<p class="text-muted mb-0" style="font-size: 13px;"><?php echo get_phrase('Choose your preferred payment method'); ?></p>
							</div>
						</div>
					</div>
					
					<!-- VAT Info Banner - ONLY FOR SUBSCRIPTION_ADMIN (Admin → Superadmin) -->
					<?php if ($is_subscription_admin && $has_vat_config && in_array($tax_residence, ['MA', 'AE', 'UAE'])): ?>
					<div class="vat-banner mb-3 <?php echo ($tax_residence === 'UAE' || $tax_residence === 'AE') ? 'uae' : ''; ?>" style="--entity-color: <?php echo $entity_color; ?>;">
						<div class="vat-banner-main" style="background: linear-gradient(135deg, <?php echo $entity_color; ?> 0%, <?php echo $billing_entity['color_secondary'] ?? $entity_color; ?> 100%);">
							<div class="vat-banner-country">
								<?php echo $billing_entity['country_flag'] ?? ($tax_residence === 'MA' ? '🇲🇦' : '🇦🇪'); ?>
							</div>
							<div class="vat-banner-info">
								<div class="vat-banner-title">
									<?php echo get_phrase('Vat applied'); ?> - <?php echo $billing_entity['country_name'] ?? (($tax_residence === 'MA') ? get_phrase('Morocco') : get_phrase('United Arab Emirates')); ?>
								</div>
								<div class="vat-banner-details">
									<?php echo get_phrase('Vat rate'); ?>: <?php echo $entity_vat_rate; ?>% • <?php echo get_phrase('Legal entity'); ?>: <?php echo htmlspecialchars($entity_name); ?>
								</div>
							</div>
							<div class="vat-banner-badge">
								<?php echo ($tax_residence === 'MA') ? 'TVA' : 'VAT'; ?> <?php echo $entity_vat_rate; ?>%
							</div>
						</div>
						<div class="vat-banner-notice">
							<i class="fa fa-info-circle"></i>
							<?php echo get_phrase('Vat calculated automatically based on community tax residence'); ?>
							<span class="ml-2" style="color: #888;">
								<i class="fa fa-building"></i> <?php echo htmlspecialchars($entity_legal_name); ?>
							</span>
						</div>

					</div>
					<?php endif; ?>

					<!-- Payment method info from Billing Entity - ONLY FOR SUBSCRIPTION_ADMIN -->
					<?php if ($is_subscription_admin && $has_vat_config && $entity_id): ?>
					<div class="text-center mb-3 p-2 rounded" style="background: linear-gradient(135deg, <?php echo $entity_color; ?>15 0%, <?php echo $entity_color; ?>05 100%); border: 1px solid <?php echo $entity_color; ?>30;">
						<small style="color: <?php echo $entity_color; ?>;">
							<i class="fas fa-building mr-1"></i>
							<?php echo get_phrase('Payment methods for'); ?> <strong><?php echo htmlspecialchars($entity_name); ?></strong>
							<?php if (!empty($entity_payment_methods)): ?>
							<span class="text-muted ml-2">
								(<?php echo implode(', ', array_map('ucfirst', $entity_payment_methods)); ?>)
							</span>
							<?php endif; ?>
						</small>
					</div>
					<?php endif; ?>


					<!-- Payment method buttons - Original Design -->
					<div class="row g-3 mb-4">
						<?php if ($stripe_enabled && $paypal_enabled): ?>
							<div class="col-6">
								<button class="method-btn active" data-method="card">
									<img src="<?php echo base_url('assets/backend/images/payments/stripe.png'); ?>" alt="Stripe">
								</button>
							</div>
							<div class="col-6">
								<button class="method-btn" data-method="paypal">
									<img src="<?php echo base_url('assets/backend/images/payments/Paypal1.png'); ?>" alt="PayPal">
								</button>
							</div>
						<?php elseif ($stripe_enabled): ?>
							<div class="col-12">
								<button class="method-btn active" data-method="card">
									<img src="<?php echo base_url('assets/backend/images/payments/stripe.png'); ?>" alt="Stripe">
								</button>
							</div>
						<?php elseif ($paypal_enabled): ?>
							<div class="col-12">
								<button class="method-btn active" data-method="paypal">
									<img src="<?php echo base_url('assets/backend/images/payments/Paypal1.png'); ?>" alt="PayPal">
								</button>
							</div>
						<?php else: ?>
							<div class="col-12">
								<div class="alert alert-danger">
									<i class="fas fa-exclamation-triangle mr-2"></i>
									<strong><?php echo get_phrase('No payment methods configured'); ?></strong>
									<br><small>
										<?php echo get_phrase('Please configure Stripe or PayPal in'); ?> 
										<a href="<?php echo site_url('admin/payment_settings'); ?>" class="alert-link">
											<?php echo get_phrase('Payment Settings'); ?>
										</a>
									</small>
								</div>
							</div>
						<?php endif; ?>
					</div>
					
					<!-- Info: Méthodes non configurées -->
					<?php if ($stripe_enabled && !$paypal_enabled): ?>
					<div class="alert alert-info alert-sm py-2 mb-3" style="font-size: 12px;">
						<i class="fas fa-info-circle mr-1"></i>
						<?php echo get_phrase('PayPal is not configured.'); ?> 
						<a href="<?php echo site_url('admin/payment_settings'); ?>" class="alert-link"><?php echo get_phrase('Configure PayPal'); ?></a>
					</div>
					<?php elseif ($paypal_enabled && !$stripe_enabled): ?>
					<div class="alert alert-info alert-sm py-2 mb-3" style="font-size: 12px;">
						<i class="fas fa-info-circle mr-1"></i>
						<?php echo get_phrase('Stripe is not configured.'); ?> 
						<a href="<?php echo site_url('admin/payment_settings'); ?>" class="alert-link"><?php echo get_phrase('Configure Stripe'); ?></a>
					</div>
					<?php endif; ?>

					<!-- STRIPE FORM - Modified -->
					<?php if ($stripe_enabled): ?>
					<?php 
					// Montant à payer selon le type de paiement
					$form_amount = $is_subscription_admin ? $grand_total : $amount_to_pay;
					$form_currency = $is_subscription_admin ? $display_currency : ($currency ?? 'MAD');
					
					// Pour les paiements school_join ou community, toujours utiliser student/payment_success
					// car admin/teacher utilisent le flux student pour rejoindre une communauté
					$is_community_payment = ($payment_type === 'school_join' || $type === 'community');
					if ($is_community_payment) {
						$stripe_action_url = site_url('student/payment_success/stripe/' . $invoice_id.'/'.$form_amount.'/0/community');
					} else {
						$stripe_action_url = route('payment_success/stripe/' . $invoice_id.'/'.$form_amount);
					}
					?>
					<form id="card-form" class="payment-form <?php echo ($stripe_enabled && !$paypal_enabled) || ($stripe_enabled && $paypal_enabled) ? 'active' : ''; ?>" method="post"
						action="<?php echo $stripe_action_url; ?>">

												<input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
												<input type="hidden" name="currency" value="<?php echo $form_currency;?>" />
												<input type="hidden" name="payment_type" value="<?php echo $payment_type;?>" />
												<?php if ($is_subscription_admin): ?>
												<input type="hidden" name="vat_amount" value="<?php echo $vat_amount;?>" />
												<input type="hidden" name="vat_rate" value="<?php echo $vat_rate;?>" />
												<input type="hidden" name="sub_total" value="<?php echo $sub_total;?>" />
												<?php else: ?>
												<input type="hidden" name="vat_amount" value="0" />
												<input type="hidden" name="vat_rate" value="0" />
												<input type="hidden" name="sub_total" value="<?php echo $amount_to_pay;?>" />
												<?php endif; ?>
												<input type="hidden" name="total_amount" value="<?php echo $form_amount;?>" />
												<input type="hidden" name="type" value="<?php echo $type;?>" />
												
												<!-- Currency Conversion Data -->
												<input type="hidden" name="original_currency" value="<?php echo $original_currency;?>" />
												<input type="hidden" name="original_amount" value="<?php echo $original_grand_total;?>" />
												<input type="hidden" name="fx_rate" value="<?php echo $stripe_fx_rate_val;?>" />
												<input type="hidden" name="fx_rate_date" value="<?php echo $fx_rate_date;?>" />
												<input type="hidden" name="conversion_applied" value="<?php echo $stripe_needs_conversion ? '1' : '0';?>" />
									
												<div class="form-group">
													<label for="email"><?php echo get_phrase('Email address'); ?></label>
													<div class="info-box">
														<?php 
															if (!empty($user_details['email'])) {
																echo htmlspecialchars($user_details['email']); 
															} elseif (!empty($invoice_details['student_id'])) {
																$ci =& get_instance();
																$stu_id = $invoice_details['student_id'];
																$usr_email = 'N/A';
																
																// 1. Try finding student by ID
																$stu = $ci->db->get_where('students', ['id' => $stu_id])->row_array();
																if ($stu) {
																	$usr = $ci->db->get_where('users', ['id' => $stu['user_id']])->row_array();
																	$usr_email = $usr ? $usr['email'] : 'N/A';
																} else {
																	// 2. Fallback: Check if $stu_id is actually a user_id (Data Inconsistency Fix)
																	$usr_direct = $ci->db->get_where('users', ['id' => $stu_id])->row_array();
																	if ($usr_direct) {
																		$usr_email = $usr_direct['email'];
																	}
																}
																echo htmlspecialchars($usr_email);
															} else {
																echo 'N/A';
															}
														?>
													</div>
												</div>

												<div class="form-group">
													<label for="card-holder"><?php echo get_phrase('Name on card'); ?></label>
													<div class="info-box">
														<?php 
															if (!empty($user_details['name'])) {
																echo htmlspecialchars($user_details['name']); 
															} elseif (!empty($invoice_details['student_id'])) {
																$ci =& get_instance();
																$stu_id = $invoice_details['student_id'];
																$usr_name = 'N/A';
																
																// 1. Try finding student by ID
																$stu = $ci->db->get_where('students', ['id' => $stu_id])->row_array();
																if ($stu) {
																	$usr = $ci->db->get_where('users', ['id' => $stu['user_id']])->row_array();
																	$usr_name = $usr ? $usr['name'] : 'N/A';
																} else {
																	// 2. Fallback: Check if $stu_id is actually a user_id
																	$usr_direct = $ci->db->get_where('users', ['id' => $stu_id])->row_array();
																	if ($usr_direct) {
																		$usr_name = $usr_direct['name'];
																	}
																}
																echo htmlspecialchars($usr_name);
															} else {
																echo 'N/A';
															}
														?>
													</div>
												</div>

												<label>
													<div id="card-element" class="field"></div>
													<span><span style="color: #111827; font-weight : 600;"><?php echo get_phrase('credit_/_debit_card');?></span></span>
												</label>
												
												<!-- Prix affiché dynamiquement selon la méthode -->
												<button type="submit" id="stripe-pay-button">
													<?php echo get_phrase('Pay');?> 
													<?php if ($is_subscription_admin): ?>
													<span id="stripe-pay-amount"><?php echo number_format($grand_total, 2); ?></span> <span id="stripe-pay-currency"><?php echo $display_currency; ?></span>
													<?php else: ?>
													<span id="stripe-pay-amount"><?php echo number_format($amount_to_pay, 2); ?></span> <span id="stripe-pay-currency"><?php echo $currency ?? 'MAD'; ?></span>
													<?php endif; ?>
												</button>

												<!-- Informations Stripe (mises à jour dynamiquement) -->
												<div class="stripe-payment-info mt-2" style="font-size: 12px; color: #666; background: #e3f2fd; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #2196f3;">
													<i class="fa fa-credit-card"></i>
													<strong>Stripe Payment:</strong>
													<?php if ($is_subscription_admin && $has_vat_config): ?>
														<!-- SUBSCRIPTION_ADMIN: Afficher TVA et conversion -->
														<span id="stripe-base-amount"><?php echo number_format($grand_total, 2); ?></span> <?php echo $display_currency; ?>
														<br/>
														<small>
															<?php if (in_array($tax_residence, ['UAE', 'AE'])): ?>
																Base: <?php echo number_format($amount_to_pay, 2); ?> MAD → <?php echo number_format($grand_total, 2); ?> AED
															<?php else: ?>
																HT: <?php echo number_format($sub_total, 2); ?> + TVA <?php echo $vat_rate; ?>%: <?php echo number_format($vat_amount, 2); ?> = <?php echo number_format($grand_total, 2); ?> <?php echo $display_currency; ?>
															<?php endif; ?>
														</small>
													<?php else: ?>
														<!-- AUTRES PAIEMENTS: Simple montant -->
														<span id="stripe-base-amount"><?php echo number_format($amount_to_pay, 2); ?></span> <?php echo $currency ?? 'MAD'; ?>
													<?php endif; ?>
												</div>

												
												<div class="outcome">
													<div class="error" role="alert"></div>
													<div class="success">
														Success! Your Stripe token is <span class="token"></span>
													</div>
												</div>
												
												<div class="package-details mt-3">
													<strong><?php echo get_phrase('Member_name');?> | <?php echo htmlspecialchars($user_details['name']);?></strong>
												</div>
												<input type="hidden" name="stripeToken" value="">
					</form>
					<?php endif; ?>

					<!-- PAYPAL FORM - Modified -->
					<?php if ($paypal_enabled): ?>
					<div id="paypal-form" class="payment-form <?php echo (!$stripe_enabled && $paypal_enabled) ? 'active' : ''; ?>">
						<p class="p-3 text-muted border rounded bg-light">
							<?php echo get_phrase('You will be redirected to PayPal to complete your purchase.'); ?>			
						</p>
						
						<div id="paypal-button-container" class="mt-3"  style="text-align: center; max-width: 400px; margin: 0 auto;"></div>
					</div>
					<?php endif; ?>

					<!-- Security Badge Enhanced -->
					<div class="security-features mt-4">
						<div class="security-badge-main">
							<i class="fa fa-shield-alt"></i>
							<span><?php echo get_phrase('100% secure payment')?></span>
						</div>
						<div class="security-icons mt-3">
							<div class="security-item">
								<i class="fa fa-lock"></i>
								<span>SSL 256-bit</span>
							</div>
							<div class="security-item">
								<i class="fa fa-eye-slash"></i>
								<span>PCI DSS</span>
							</div>
							<div class="security-item">
								<i class="fa fa-user-shield"></i>
								<span>3D Secure</span>
							</div>
						</div>
					</div>
				</section>

				<!-- RIGHT SIDE -->
				<section class="summary-section col-12 col-md-5 p-4">
					<!-- Summary Header -->
					<div class="summary-header mb-4">
						<div class="d-flex align-items-center justify-content-between">
							<h2 class="mb-0">
								<i class="fa fa-receipt mr-2" style="color: #6366f1;"></i>
								<?php echo get_phrase('Order Summary'); ?>
							</h2>
							<span class="order-badge">
								<i class="fa fa-shopping-cart"></i>
							</span>
						</div>
					</div>

					<!-- Product/Service Card - Enhanced -->
					<div class="product-card mb-4">
						<div class="product-card-header">
							<div class="product-icon">
								<?php if ($type == "classe"): ?>
									<i class="fa fa-graduation-cap"></i>
								<?php else: ?>
									<i class="fa fa-users"></i>
								<?php endif; ?>
							</div>
							<div class="product-info">
								<h4 class="product-name mb-1">
									<?php echo htmlspecialchars($type == "classe" ? $class_name : $community_name); ?>
								</h4>
								<span class="product-type">
									<?php echo ($type == "classe") ? get_phrase('Class Subscription') : get_phrase('Community Subscription'); ?>
								</span>
							</div>
						</div>
						<?php if ($has_vat_config): ?>
						<div class="product-card-footer">
							<span class="vat-included-badge">
								<i class="fa fa-check-circle mr-1"></i>
								<?php echo get_phrase('VAT Included'); ?>
							</span>
						</div>
						<?php endif; ?>
					</div>

					<!-- Tax Residence Badge for UAE/MA - ONLY FOR SUBSCRIPTION_ADMIN -->
					<?php if ($is_subscription_admin && $has_vat_config && in_array($tax_residence, ['MA', 'AE', 'UAE'])): ?>
					<div class="tax-residence-card mb-4 <?php echo ($tax_residence === 'UAE' || $tax_residence === 'AE') ? 'uae' : 'morocco'; ?>">
						<div class="tax-residence-flag">
							<?php echo ($tax_residence === 'UAE' || $tax_residence === 'AE') ? '🇦🇪' : '🇲🇦'; ?>
						</div>
						<div class="tax-residence-info">
							<span class="tax-residence-label"><?php echo get_phrase('Tax Residence'); ?></span>
							<span class="tax-residence-country">
								<?php 
								if ($tax_residence === 'UAE' || $tax_residence === 'AE') {
									echo get_phrase('United Arab Emirates');
								} else {
									echo get_phrase('Morocco');
								}
								?>
							</span>
						</div>
						<div class="tax-residence-rate">
							<?php echo ($tax_residence === 'UAE' || $tax_residence === 'AE') ? 'VAT 5%' : 'TVA 20%'; ?>
						</div>
					</div>
					<?php endif; ?>

					<!-- Price Breakdown -->
					<div class="price-breakdown" style="background: #fff; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
						
						<?php if ($is_subscription_admin && ($vat_rate > 0 || in_array($tax_residence, ['UAE', 'AE']))): ?>
						<!-- Subtotal HT - ONLY FOR SUBSCRIPTION_ADMIN -->
						<div class="d-flex justify-content-between p-3" style="border-bottom: 1px solid #f1f3f4;">
							<span style="color: #5f6368;">
								<i class="fa fa-file-invoice mr-2" style="color: #9aa0a6;"></i>
								<?php echo get_phrase('subtotal'); ?> (HT)
							</span>
							<strong style="color: #202124;">
								<span class="amount-subtotal"><?php echo number_format($sub_total, 2); ?></span> 
								<span class="currency-display"><?php echo $display_currency; ?></span>
							</strong>
						</div>
						
						<!-- VAT Amount - ONLY FOR SUBSCRIPTION_ADMIN -->
						<div class="d-flex justify-content-between p-3" style="background: #f8f9fa; border-bottom: 1px solid #f1f3f4;">
							<span style="color: #5f6368;">
								<i class="fa fa-percent mr-2" style="color: #9aa0a6;"></i>
								<?php echo get_phrase('vat'); ?> 
								<span class="badge badge-secondary ml-1" style="font-size: 10px; vertical-align: middle;">
									<?php echo ($vat_rate > 0 ? $vat_rate : 5); ?>%
								</span>
							</span>
							<strong style="color: #202124;">
								<span class="amount-vat"><?php echo number_format($vat_amount, 2); ?></span> 
								<span class="currency-display"><?php echo $display_currency; ?></span>
							</strong>
						</div>
						<?php endif; ?>

						<!-- Total -->
						<div class="d-flex justify-content-between p-3 summary-total" style="background: linear-gradient(135deg, #1a237e 0%, #283593 100%); color: white;">
							<span style="font-size: 16px;">
								<i class="fa fa-calculator mr-2"></i>
								<?php echo get_phrase('Total'); ?> 
								<?php if ($is_subscription_admin && ($vat_rate > 0 || in_array($tax_residence, ['UAE', 'AE']))): ?>
								<small style="opacity: 0.8;">(TTC)</small>
								<?php endif; ?>
							</span>
							<strong style="font-size: 20px;">
								<?php if ($is_subscription_admin): ?>
								<span class="amount-total"><?php echo number_format($grand_total, 2); ?></span> 
								<span class="currency-display"><?php echo $display_currency; ?></span>
								<?php else: ?>
								<span class="amount-total"><?php echo number_format($amount_to_pay, 2); ?></span> 
								<span class="currency-display"><?php echo $currency ?? 'MAD'; ?></span>
								<?php endif; ?>
							</strong>
						</div>
					</div>

					<!-- VAT Details Card - ONLY FOR SUBSCRIPTION_ADMIN -->
					<?php if ($is_subscription_admin && $has_vat_config): ?>
					<div class="vat-details-card mt-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 8px; border: 1px solid #90caf9; overflow: hidden;">
						<div class="p-2 text-center" style="background: rgba(25, 118, 210, 0.1); border-bottom: 1px solid #90caf9;">
							<span style="font-size: 12px; font-weight: 600; color: #1565c0; text-transform: uppercase; letter-spacing: 0.5px;">
								<i class="fa fa-info-circle mr-1"></i>
								<?php echo get_phrase('VAT Information'); ?>
							</span>
						</div>
						<div class="p-3">
							<div class="row" style="font-size: 12px;">
								<div class="col-6 mb-2">
									<div style="color: #5f6368; font-size: 10px; text-transform: uppercase;"><?php echo get_phrase('VAT Rate'); ?></div>
									<strong style="color: #1565c0; font-size: 14px;"><?php echo ($vat_rate > 0 ? $vat_rate : 0); ?>%</strong>
								</div>
								<div class="col-6 mb-2">
									<div style="color: #5f6368; font-size: 10px; text-transform: uppercase;"><?php echo get_phrase('Currency'); ?></div>
									<strong style="color: #1565c0; font-size: 14px;"><?php echo $display_currency; ?></strong>
								</div>
								<div class="col-12">
									<div style="color: #5f6368; font-size: 10px; text-transform: uppercase;"><?php echo get_phrase('Legal Entity'); ?></div>
									<strong style="color: <?php echo $entity_color; ?>; font-size: 13px;">
										<?php echo htmlspecialchars($entity_name); ?> - <?php echo htmlspecialchars($billing_entity['country_name'] ?? ''); ?>
									</strong>
									<?php if (!empty($entity_psp)): ?>
									<div style="font-size: 10px; color: #888; margin-top: 2px;">
										<i class="fa fa-credit-card"></i> PSP: <?php echo htmlspecialchars($entity_psp); ?>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<!-- Currency Conversion Info (for UAE showing MAD equivalent) - ONLY FOR SUBSCRIPTION_ADMIN -->
					<?php if ($is_subscription_admin && ($tax_residence === 'UAE' || $tax_residence === 'AE') && isset($conversion_rate) && $conversion_rate != 1.0 && $original_currency !== 'AED'): ?>
					<div class="conversion-info mt-3 p-3" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 8px; border: 1px solid #ffb74d;">
						<div class="text-center mb-2">
							<span style="font-size: 11px; font-weight: 600; color: #e65100; text-transform: uppercase; letter-spacing: 0.5px;">
								<i class="fa fa-exchange-alt mr-1"></i>
								<?php echo get_phrase('Currency Conversion'); ?>
							</span>
						</div>
						<div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
							<div class="text-center" style="flex: 1;">
								<div style="color: #5f6368; font-size: 10px;"><?php echo get_phrase('Base Price'); ?></div>
								<strong style="color: #bf360c; font-size: 16px;">
									<?php echo number_format($amount_to_pay, 2); ?> MAD
								</strong>
							</div>
							<div class="text-center px-2">
								<i class="fa fa-arrow-right" style="color: #ff6f00; font-size: 18px;"></i>
							</div>
							<div class="text-center" style="flex: 1;">
								<div style="color: #5f6368; font-size: 10px;"><?php echo get_phrase('Converted'); ?></div>
								<strong style="color: #00695c; font-size: 16px;">
									<?php echo number_format($grand_total, 2); ?> AED
								</strong>
							</div>
						</div>
						<div class="text-center mt-2 pt-2" style="border-top: 1px dashed #ffb74d; font-size: 11px; color: #5f6368;">
							<i class="fa fa-chart-line mr-1"></i>
							<?php echo get_phrase('Exchange Rate'); ?>: 1 MAD = <?php echo number_format($conversion_rate, 4); ?> AED
							<br/>
							<small style="color: #9e9e9e;">
								<i class="fa fa-clock mr-1"></i>
								<?php echo get_phrase('Rate as of'); ?>: <?php echo date('d/m/Y'); ?>
							</small>
						</div>
					</div>
					<?php endif; ?>

					<!-- Original Amount Info (for other conversions) -->
					<?php if ($conversion_needed && ($stripe_needs_conversion || $paypal_needs_conversion) && $tax_residence !== 'UAE' && $tax_residence !== 'AE'): ?>
					<div class="original-amount-info mt-3 p-3" style="background: #f5f5f5; border-radius: 8px; border: 1px dashed #bdbdbd;">
						<div class="d-flex justify-content-between" style="font-size: 13px; color: #616161;">
							<span><i class="fa fa-tag mr-1"></i> <?php echo get_phrase('original_price'); ?></span>
							<strong><?php echo number_format($original_grand_total, 2) . ' ' . $original_currency; ?></strong>
						</div>
						<div class="mt-2 pt-2" style="border-top: 1px dashed #e0e0e0; font-size: 11px; color: #9e9e9e;">
							<i class="fa fa-exchange-alt mr-1"></i>
							<?php echo get_phrase('exchange_rate'); ?>: 
							<span id="current-fx-rate">1 <?php echo $original_currency; ?> = <?php echo number_format($stripe_fx_rate_val, 4); ?> <?php echo $stripe_currency; ?></span>
							<?php if ($fx_stale_flag): ?>
								<br/><span style="color: #f0ad4e;"><i class="fa fa-exclamation-triangle"></i> <?php echo get_phrase('rate_may_be_outdated'); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

					<!-- Security & Trust Badges -->
					<div class="trust-badges mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
						<div class="row text-center" style="font-size: 11px; color: #6c757d;">
							<div class="col-4">
								<i class="fa fa-lock mb-1" style="font-size: 18px; color: #28a745;"></i>
								<div><?php echo get_phrase('Secure'); ?></div>
							</div>
							<div class="col-4">
								<i class="fa fa-shield-alt mb-1" style="font-size: 18px; color: #17a2b8;"></i>
								<div><?php echo get_phrase('Protected'); ?></div>
							</div>
							<div class="col-4">
								<i class="fa fa-check-circle mb-1" style="font-size: 18px; color: #007bff;"></i>
								<div><?php echo get_phrase('Verified'); ?></div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>

			<!-- CRITICAL: Load Stripe.js first -->
		<script src="https://js.stripe.com/v3/"></script>

		<!-- CRITICAL: Define PHP variables for fxData -->
		<?php
			// Utiliser les taux de change calculés dans Admin.php, pas les anciennes variables
			$paypal_fx_rate_val = isset($paypal_fx_rate_val) && $paypal_fx_rate_val > 0 ? (float)$paypal_fx_rate_val : 1.0;
			$stripe_fx_rate_val = isset($stripe_fx_rate_val) && $stripe_fx_rate_val > 0 ? (float)$stripe_fx_rate_val : 1.0;

			// Variables PayPal
			if (isset($paypal_sub_total_converted) && isset($paypal_vat_amount_converted)) {
				$paypal_sub_total = (float)$paypal_sub_total_converted;
				$paypal_vat_amount = (float)$paypal_vat_amount_converted;
				$paypal_grand_total = isset($paypal_converted_amount) ? (float)$paypal_converted_amount : $grand_total;
			} else {
				$paypal_sub_total = round($original_sub_total * $paypal_fx_rate_val, 2);
				$paypal_vat_amount = round($original_vat_amount * $paypal_fx_rate_val, 2);
				$paypal_grand_total = isset($paypal_converted_amount) ? (float)$paypal_converted_amount : $grand_total;
			}

			// Variables Stripe
			if (isset($stripe_sub_total_converted) && isset($stripe_vat_amount_converted)) {
				$stripe_sub_total = (float)$stripe_sub_total_converted;
				$stripe_vat_amount = (float)$stripe_vat_amount_converted;
				$stripe_grand_total = isset($stripe_converted_amount) ? (float)$stripe_converted_amount : $grand_total;
			} else {
				$stripe_sub_total = round($original_sub_total * $stripe_fx_rate_val, 2);
				$stripe_vat_amount = round($original_vat_amount * $stripe_fx_rate_val, 2);
				$stripe_grand_total = isset($stripe_converted_amount) ? (float)$stripe_converted_amount : $grand_total;
			}
		?>

		<!-- CRITICAL: Define variables BEFORE loading stripe.js -->
		<script type="text/javascript">
			// ========== DEVISES PAR MODE DE PAIEMENT ==========
			var stripe_key = '<?php echo isset($stripe_public_key) ? $stripe_public_key : ""; ?>';
			var stripe_currency = '<?php echo $stripe_currency; ?>';
			var paypal_currency = '<?php echo $paypal_currency; ?>';
			var original_currency = '<?php echo $original_currency; ?>';
			
			// ========== MONTANTS CONVERTIS ==========
			var fxData = {
				conversion_needed: <?php echo ($conversion_needed && !$has_vat_config) ? 'true' : 'false'; ?>,
				has_vat_config: <?php echo $has_vat_config ? 'true' : 'false'; ?>,
				original_currency: '<?php echo $original_currency; ?>',
				display_currency: '<?php echo $display_currency; ?>',
				original_sub_total: <?php echo $is_morocco_b2b ? $sub_total : $original_sub_total; ?>,
				original_vat_amount: <?php echo $is_morocco_b2b ? $vat_amount : $original_vat_amount; ?>,
				original_grand_total: <?php echo $is_morocco_b2b ? $grand_total : $original_grand_total; ?>,
				stripe: {
					currency: '<?php echo $has_vat_config ? $display_currency : $stripe_currency; ?>',
					sub_total: <?php echo $has_vat_config ? $sub_total : (isset($stripe_sub_total_converted) ? $stripe_sub_total_converted : $stripe_sub_total); ?>,
					vat_amount: <?php echo $has_vat_config ? $vat_amount : (isset($stripe_vat_amount_converted) ? $stripe_vat_amount_converted : $stripe_vat_amount); ?>,
					grand_total: <?php echo $has_vat_config ? $grand_total : (isset($stripe_converted_amount) ? $stripe_converted_amount : $stripe_grand_total); ?>,
					fx_rate: <?php echo $has_vat_config ? 1.0 : (isset($stripe_fx_rate_val) && $stripe_fx_rate_val != 1.0 ? $stripe_fx_rate_val : (isset($stripe_sub_total_converted) && $stripe_sub_total_converted > 0 ? ($stripe_sub_total_converted / $original_sub_total) : 1.0)); ?>,
					needs_conversion: <?php echo ($stripe_needs_conversion && !$has_vat_config) ? 'true' : 'false'; ?>
				},
				paypal: {
					currency: '<?php echo $has_vat_config ? $display_currency : $paypal_currency; ?>',
					sub_total: <?php echo $has_vat_config ? $sub_total : (isset($paypal_sub_total_converted) ? $paypal_sub_total_converted : $paypal_sub_total); ?>,
					vat_amount: <?php echo $has_vat_config ? $vat_amount : (isset($paypal_vat_amount_converted) ? $paypal_vat_amount_converted : $paypal_vat_amount); ?>,
					grand_total: <?php echo $has_vat_config ? $grand_total : (isset($paypal_converted_amount) ? $paypal_converted_amount : $paypal_grand_total); ?>,
					fx_rate: <?php echo $has_vat_config ? 1.0 : (isset($paypal_fx_rate_val) && $paypal_fx_rate_val != 1.0 ? $paypal_fx_rate_val : (isset($paypal_sub_total_converted) && $paypal_sub_total_converted > 0 ? ($paypal_sub_total_converted / $original_sub_total) : 1.0)); ?>,
					needs_conversion: <?php echo ($paypal_needs_conversion && !$has_vat_config) ? 'true' : 'false'; ?>
				},
				rate_date: '<?php echo $fx_rate_date; ?>',
				stale: <?php echo $fx_stale_flag ? 'true' : 'false'; ?>
			};
			
			// Stop if no key
			if (!stripe_key || stripe_key.length === 0) {
				//console.error('CRITICAL: Stripe public key is empty or not set');
			}
		</script>
		
		<!-- Load your custom stripe.js -->
		<script src="<?php echo base_url('assets/payment/js/stripe.js');?>"></script>

		<!-- VAT Protection Script - Prevents JavaScript from changing VAT-calculated values -->
		<script type="text/javascript">
			// Protect VAT-calculated values from being modified by other JavaScript
			document.addEventListener('DOMContentLoaded', function() {
				<?php if ($has_vat_config): ?>
				// For VAT-configured payments, lock the values to prevent changes
				var vatProtection = {
					originalTotal: <?php echo $grand_total; ?>,
					originalSubtotal: <?php echo $sub_total; ?>,
					originalVat: <?php echo $vat_amount; ?>,

					lockValues: function() {
						// Find and lock amount display elements
						var amountElements = document.querySelectorAll('.amount-total, .amount-subtotal, .amount-vat');
						amountElements.forEach(function(element) {
							element.setAttribute('data-vat-locked', 'true');
						});

						console.log('VAT values locked to prevent JavaScript modifications');
					},

					monitorChanges: function() {
						// Monitor for any changes to amount displays (as fallback protection)
						var warningsLogged = { total: false, subtotal: false, vat: false };
						
						var checkInterval = setInterval(function() {
							var totalElement = document.querySelector('.amount-total');
							if (totalElement) {
								var currentTotal = parseFloat(totalElement.textContent.replace(/[^\d.-]/g, '')) || 0;
								if (Math.abs(currentTotal - vatProtection.originalTotal) > 0.01) {
									if (!warningsLogged.total) {
										console.warn('VAT total amount was modified, restoring...');
										warningsLogged.total = true;
									}
									totalElement.textContent = vatProtection.originalTotal.toFixed(2);
								}
							}

							var subtotalElement = document.querySelector('.amount-subtotal');
							if (subtotalElement) {
								var currentSubtotal = parseFloat(subtotalElement.textContent.replace(/[^\d.-]/g, '')) || 0;
								if (Math.abs(currentSubtotal - vatProtection.originalSubtotal) > 0.01) {
									if (!warningsLogged.subtotal) {
										console.warn('VAT subtotal amount was modified, restoring...');
										warningsLogged.subtotal = true;
									}
									subtotalElement.textContent = vatProtection.originalSubtotal.toFixed(2);
								}
							}

							var vatElement = document.querySelector('.amount-vat');
							if (vatElement) {
								var currentVat = parseFloat(vatElement.textContent.replace(/[^\d.-]/g, '')) || 0;
								if (Math.abs(currentVat - vatProtection.originalVat) > 0.01) {
									if (!warningsLogged.vat) {
										console.warn('VAT amount was modified, restoring...');
										warningsLogged.vat = true;
									}
									vatElement.textContent = vatProtection.originalVat.toFixed(2);
								}
							}
						}, 2000); // Check every 2 seconds (less frequent)

						// Stop monitoring after 30 seconds
						setTimeout(function() {
							clearInterval(checkInterval);
							console.log('VAT monitoring stopped');
						}, 30000);
					}
				};

				// Initialize protection
				vatProtection.lockValues();
				vatProtection.monitorChanges();

				console.log('VAT Protection Active: Values locked to prevent JavaScript modifications');
				<?php endif; ?>
			});
		</script>

		<!-- Enhanced Styles for Payment Gateway -->
		<style>
			/* ========== VARIABLES & BASE ========== */
			:root {
				--primary-color: #6366f1;
				--primary-dark: #4f46e5;
				--success-color: #10b981;
				--warning-color: #f59e0b;
				--danger-color: #ef4444;
				--gray-50: #f9fafb;
				--gray-100: #f3f4f6;
				--gray-200: #e5e7eb;
				--gray-300: #d1d5db;
				--gray-500: #6b7280;
				--gray-700: #374151;
				--gray-900: #111827;
				--morocco-color: #c62828;
				--uae-color: #00695c;
				--radius-sm: 8px;
				--radius-md: 12px;
				--radius-lg: 16px;
				--shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
				--shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
				--shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
			}

			/* ========== GENERAL IMPROVEMENTS ========== */
			.checkout-container {
				max-width: 1100px;
				margin: 0 auto;
				border-radius: var(--radius-lg);
				overflow: hidden;
				box-shadow: var(--shadow-lg);
			}
			
			.payment-section {
				background: #ffffff;
			}
			
			.summary-section {
				background: linear-gradient(180deg, var(--gray-50) 0%, #ffffff 100%);
				border-left: 1px solid var(--gray-200);
			}
			
			@media (max-width: 767px) {
				.summary-section {
					border-left: none;
					border-top: 1px solid var(--gray-200);
				}
			}

			/* ========== PAYMENT HEADER ========== */
			.payment-header {
				padding-bottom: 20px;
				border-bottom: 1px solid var(--gray-200);
			}

			.payment-header h2 {
				font-size: 22px;
				font-weight: 700;
				color: var(--gray-900);
			}

			.header-icon {
				width: 48px;
				height: 48px;
				background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
				border-radius: var(--radius-md);
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-size: 20px;
				box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
			}

			/* ========== SECTION LABELS ========== */
			.section-label {
				display: block;
				font-size: 12px;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 0.5px;
				color: var(--gray-500);
			}

			/* ========== VAT BANNER (Original Design) ========== */
			.vat-banner {
				border-radius: var(--radius-md);
				overflow: hidden;
				box-shadow: var(--shadow-sm);
			}

			.vat-banner-main {
				background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
				padding: 16px 20px;
				display: flex;
				align-items: center;
				gap: 16px;
				color: white;
			}

			.vat-banner-country {
				font-size: 28px;
				font-weight: 700;
				opacity: 0.9;
				min-width: 50px;
			}

			.vat-banner-info {
				flex: 1;
			}

			.vat-banner-title {
				font-size: 15px;
				font-weight: 600;
				margin-bottom: 2px;
			}

			.vat-banner-details {
				font-size: 12px;
				opacity: 0.9;
			}

			.vat-banner-badge {
				background: rgba(255,255,255,0.2);
				padding: 8px 16px;
				border-radius: 6px;
				font-size: 14px;
				font-weight: 700;
			}

			.vat-banner-notice {
				background: #f5f5f5;
				padding: 10px 20px;
				font-size: 12px;
				color: #666;
				border-top: 1px solid #e0e0e0;
			}

			.vat-banner-notice i {
				color: #2196f3;
				margin-right: 8px;
			}

			.vat-banner-b2b {
				background: #fff3e0;
				padding: 10px 20px;
				font-size: 12px;
				color: #e65100;
				border-top: 1px solid #ffe0b2;
			}

			.vat-banner-b2b i {
				margin-right: 8px;
			}

			/* UAE variant */
			.vat-banner.uae .vat-banner-main {
				background: linear-gradient(135deg, #00897b 0%, #00695c 100%);
			}

			/* ========== PAYMENT METHOD BUTTONS (Original Design) ========== */
			.method-btn {
				width: 100%;
				padding: 20px;
				border: 2px dashed var(--gray-300);
				border-radius: var(--radius-md);
				background: white;
				cursor: pointer;
				transition: all 0.2s ease;
				display: flex;
				align-items: center;
				justify-content: center;
				position: relative;
			}

			.method-btn img {
				max-height: 28px;
				width: auto;
			}

			.method-btn:hover {
				border-color: var(--primary-color);
				background: var(--gray-50);
			}

			.method-btn.active {
				border: 2px solid var(--primary-color);
				border-style: solid;
				background: white;
			}

			/* ========== SUMMARY HEADER ========== */
			.summary-header h2 {
				font-size: 20px;
				font-weight: 700;
				color: var(--gray-900);
			}

			.order-badge {
				width: 40px;
				height: 40px;
				background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-size: 16px;
			}

			/* ========== PRODUCT CARD ========== */
			.product-card {
				background: white;
				border-radius: var(--radius-md);
				border: 1px solid var(--gray-200);
				overflow: hidden;
				transition: all 0.3s ease;
			}

			.product-card:hover {
				box-shadow: var(--shadow-md);
				transform: translateY(-2px);
			}

			.product-card-header {
				padding: 16px;
				display: flex;
				align-items: center;
				gap: 12px;
				border-bottom: 1px solid var(--gray-100);
			}

			.product-icon {
				width: 44px;
				height: 44px;
				background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
				border-radius: var(--radius-sm);
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-size: 18px;
			}

			.product-info {
				flex: 1;
			}

			.product-name {
				font-size: 15px;
				font-weight: 600;
				color: var(--gray-900);
				margin: 0;
			}

			.product-type {
				font-size: 12px;
				color: var(--gray-500);
			}

			.product-card-footer {
				padding: 10px 16px;
				background: var(--gray-50);
			}

			.vat-included-badge {
				font-size: 11px;
				color: var(--success-color);
				font-weight: 500;
			}

			/* ========== TAX RESIDENCE CARD ========== */
			.tax-residence-card {
				display: flex;
				align-items: center;
				gap: 12px;
				padding: 14px 16px;
				border-radius: var(--radius-md);
				color: white;
				transition: all 0.3s ease;
			}

			.tax-residence-card.morocco {
				background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
				box-shadow: 0 4px 12px rgba(198, 40, 40, 0.3);
			}

			.tax-residence-card.uae {
				background: linear-gradient(135deg, #00695c 0%, #004d40 100%);
				box-shadow: 0 4px 12px rgba(0, 105, 92, 0.3);
			}

			.tax-residence-card:hover {
				transform: scale(1.02);
			}

			.tax-residence-flag {
				font-size: 28px;
			}

			.tax-residence-info {
				flex: 1;
				display: flex;
				flex-direction: column;
			}

			.tax-residence-label {
				font-size: 10px;
				text-transform: uppercase;
				letter-spacing: 1px;
				opacity: 0.85;
			}

			.tax-residence-country {
				font-size: 14px;
				font-weight: 600;
			}

			.tax-residence-rate {
				background: rgba(255,255,255,0.2);
				padding: 6px 12px;
				border-radius: 20px;
				font-size: 12px;
				font-weight: 700;
			}

			/* ========== PRICE BREAKDOWN ========== */
			.price-breakdown {
				background: white;
				border-radius: var(--radius-md);
				border: 1px solid var(--gray-200);
				overflow: hidden;
				box-shadow: var(--shadow-sm);
				transition: all 0.3s ease;
			}

			.price-breakdown:hover {
				box-shadow: var(--shadow-md);
			}

			.summary-total {
				background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
			}

			/* ========== VAT DETAILS CARD ========== */
			.vat-details-card {
				transition: all 0.3s ease;
			}

			.vat-details-card:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(25, 118, 210, 0.2);
			}

			/* ========== CONVERSION INFO ========== */
			.conversion-info {
				transition: all 0.3s ease;
			}

			.conversion-info:hover {
				transform: scale(1.02);
			}

			/* ========== TRUST BADGES ========== */
			.trust-badges .col-4 {
				transition: transform 0.2s ease;
			}

			.trust-badges .col-4:hover {
				transform: scale(1.1);
			}

			.trust-badges .col-4:hover i {
				animation: pulse-icon 0.5s ease;
			}

			@keyframes pulse-icon {
				0% { transform: scale(1); }
				50% { transform: scale(1.2); }
				100% { transform: scale(1); }
			}

			/* ========== SECURITY FEATURES ========== */
			.security-features {
				padding-top: 20px;
				border-top: 1px solid var(--gray-200);
			}

			.security-badge-main {
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 8px;
				padding: 12px;
				background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
				border-radius: var(--radius-md);
				color: white;
				font-weight: 600;
				font-size: 14px;
			}

			.security-icons {
				display: flex;
				justify-content: space-around;
			}

			.security-item {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 4px;
				font-size: 10px;
				color: var(--gray-500);
			}

			.security-item i {
				font-size: 16px;
				color: var(--gray-400);
			}

			/* ========== CURRENCY & AMOUNT UPDATES ========== */
			.method-btn.currency-active {
				border-color: var(--success-color) !important;
				background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.1) 100%) !important;
			}

			.method-btn.currency-active::after {
				content: "✓";
				position: absolute;
				top: -8px;
				right: -8px;
				background: var(--success-color);
				color: white;
				border-radius: 50%;
				width: 22px;
				height: 22px;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 12px;
				font-weight: bold;
				box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
			}

			.currency-indicator {
				position: absolute;
				bottom: 5px;
				left: 50%;
				transform: translateX(-50%);
				text-align: center;
			}

			.currency-indicator small {
				background: rgba(255,255,255,0.95);
				padding: 3px 8px;
				border-radius: 12px;
				border: 1px solid var(--success-color);
				font-weight: 500;
				font-size: 10px;
				color: var(--success-color);
			}

			/* Animation de conversion automatique */
			.amount-subtotal, .amount-vat, .amount-total, .currency-display {
				transition: all 0.3s ease;
			}

			.amount-subtotal.updating, .amount-vat.updating, .amount-total.updating {
				background: rgba(245, 158, 11, 0.2);
				border-radius: 4px;
				animation: highlight-pulse 0.5s ease-in-out;
			}

			@keyframes highlight-pulse {
				0% { background: rgba(245, 158, 11, 0.2); }
				50% { background: rgba(245, 158, 11, 0.4); }
				100% { background: rgba(245, 158, 11, 0.2); }
			}

			.method-btn.active .currency-indicator small {
				background: var(--success-color);
				color: white;
			}

			/* ========== RESPONSIVE ========== */
			@media (max-width: 575px) {
				.payment-header h2 {
					font-size: 18px;
				}

				.header-icon {
					width: 40px;
					height: 40px;
					font-size: 16px;
				}

				.method-btn {
					padding: 15px;
				}

				.method-btn img {
					max-height: 24px;
				}

				.tax-residence-card {
					padding: 12px;
				}

				.tax-residence-flag {
					font-size: 24px;
				}

				.security-icons {
					flex-wrap: wrap;
					gap: 12px;
				}
			}
		</style>

		<!-- Payment method switcher -->
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const methodButtons = document.querySelectorAll('.method-btn');
				const paymentForms = document.querySelectorAll('.payment-form');
				const currencyDisplays = document.querySelectorAll('.currency-display');
				const amountSubtotal = document.querySelector('.amount-subtotal');
				const amountVat = document.querySelector('.amount-vat');
				const amountTotal = document.querySelector('.amount-total');
				const fxRateInfo = document.getElementById('current-fx-rate');

				// Debug: Afficher les données disponibles
				console.log('🔄 AUTOMATISATION DES CONVERSIONS - Initialisation');
				console.log('fxData disponible:', fxData);
				console.log('Méthodes de paiement trouvées:', methodButtons.length);
				console.log('Formulaires trouvés:', paymentForms.length);

				// Fonction pour formater un nombre avec 2 décimales
				function formatNumber(num) {
					return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
				}

				// Fonction pour mettre à jour l'affichage des montants et devises
				function updatePaymentDisplay(method) {
					console.log('🔄 Mise à jour affichage pour méthode:', method);

					try {
						const data = (method === 'card') ? fxData.stripe : fxData.paypal;
						console.log('📊 Données utilisées:', data);

						if (!data) {
							console.error('❌ Données non trouvées pour méthode:', method);
							return;
						}

								// PROTECTION VAT: Pour les paiements VAT, garder les montants fixes MAIS permettre la conversion devise
						if (fxData.has_vat_config) {
							console.log('🛡️ PROTECTION VAT: Montants fixes, mais conversion devise autorisée');

							// Pour les paiements VAT, utiliser la devise d'affichage appropriée
							const currency = fxData.display_currency || fxData.original_currency;
							console.log('💱 Devise d\'affichage utilisée:', currency);

							// Mettre à jour les devises d'affichage
							currencyDisplays.forEach(el => {
								el.textContent = currency;
							});

							// Mettre à jour le bouton Pay Stripe si c'est la méthode Stripe
							if (method === 'card') {
								const stripePayCurrency = document.getElementById('stripe-pay-currency');
								if (stripePayCurrency) {
									stripePayCurrency.textContent = currency;
								}
							}

							// Mettre à jour l'indicateur visuel de devise active
							updateCurrencyIndicator(method, currency, {
								...data,
								currency: currency,
								needs_conversion: false // Pas de conversion dynamique pour VAT
							});
							console.log('✅ Mise à jour devise terminée (VAT protégé)');
							return; // Sortir sans changer les montants (qui sont déjà en devise d'affichage)
						}

						const currency = data.currency;
						console.log('💱 Devise:', currency);

						// Mettre à jour les devises
						currencyDisplays.forEach(el => {
							el.textContent = currency;
							console.log('✅ Devise mise à jour:', el, currency);
						});

						// Mettre à jour les montants avec animation
						if (amountSubtotal) {
							amountSubtotal.classList.add('updating');
							setTimeout(() => {
								amountSubtotal.textContent = formatNumber(data.sub_total);
								amountSubtotal.classList.remove('updating');
							}, 150);
							console.log('✅ Sous-total mis à jour:', formatNumber(data.sub_total));
						} else {
							console.warn('⚠️ Élément .amount-subtotal non trouvé');
						}

						if (amountVat) {
							amountVat.classList.add('updating');
							setTimeout(() => {
								amountVat.textContent = formatNumber(data.vat_amount);
								amountVat.classList.remove('updating');
							}, 200);
							console.log('✅ TVA mis à jour:', formatNumber(data.vat_amount));
						} else {
							console.warn('⚠️ Élément .amount-vat non trouvé');
						}

						if (amountTotal) {
							amountTotal.classList.add('updating');
							setTimeout(() => {
								amountTotal.textContent = formatNumber(data.grand_total);
								amountTotal.classList.remove('updating');
							}, 250);
							console.log('✅ Total mis à jour:', formatNumber(data.grand_total));
						} else {
							console.warn('⚠️ Élément .amount-total non trouvé');
						}

						// Mettre à jour le bouton Pay Stripe si c'est la méthode Stripe
						if (method === 'card') {
							const stripePayAmount = document.getElementById('stripe-pay-amount');
							const stripePayCurrency = document.getElementById('stripe-pay-currency');
							if (stripePayAmount) {
								stripePayAmount.textContent = formatNumber(data.grand_total);
								console.log('✅ Bouton Pay Stripe mis à jour:', formatNumber(data.grand_total));
							}
							if (stripePayCurrency) {
								stripePayCurrency.textContent = currency;
							}
						}

						// Mettre à jour les informations Stripe si c'est la méthode Stripe
						if (method === 'card') {
							const stripeConvertedAmount = document.getElementById('stripe-converted-amount');
							const stripeTargetCurrency = document.getElementById('stripe-target-currency');
							const stripeExchangeRate = document.getElementById('stripe-exchange-rate');
							const stripeRateCurrency = document.getElementById('stripe-rate-currency');

							if (stripeConvertedAmount) {
								stripeConvertedAmount.textContent = formatNumber(data.grand_total);
							}
							if (stripeTargetCurrency) {
								stripeTargetCurrency.textContent = currency;
							}
							if (stripeExchangeRate) {
								stripeExchangeRate.textContent = data.fx_rate.toFixed(4);
							}
							if (stripeRateCurrency) {
								stripeRateCurrency.textContent = currency;
							}
							console.log('✅ Informations Stripe mises à jour');
						}

						// Mettre à jour l'indicateur visuel de devise active
						updateCurrencyIndicator(method, currency, data);

						// Mettre à jour l'info du taux de change
						if (fxRateInfo && fxData.conversion_needed) {
							fxRateInfo.textContent = '1 ' + fxData.original_currency + ' = ' + data.fx_rate.toFixed(4) + ' ' + currency;
						}

						console.log('✅ Mise à jour terminée pour', method);

					} catch (error) {
						console.error('❌ Erreur dans updatePaymentDisplay:', error);
					}
				}

				// Fonction pour mettre à jour l'indicateur visuel de devise
				function updateCurrencyIndicator(method, currency, data) {
					// Supprimer les indicateurs existants
					methodButtons.forEach(btn => {
						const existingIndicator = btn.querySelector('.currency-indicator');
						if (existingIndicator) {
							existingIndicator.remove();
						}
						btn.classList.remove('currency-active');
					});

					// Ajouter l'indicateur à la méthode active
					const activeButton = document.querySelector(`.method-btn[data-method="${method}"]`);
					if (activeButton) {
						activeButton.classList.add('currency-active');

						// Créer un indicateur visuel
						const indicator = document.createElement('div');
						indicator.className = 'currency-indicator';
						indicator.innerHTML = `
							<small style="display: block; font-size: 10px; color: #666; margin-top: 2px;">
								${currency} ${data.needs_conversion ? '(converti)' : '(original)'}
							</small>
						`;
						activeButton.appendChild(indicator);
					}
				}

				// Déterminer automatiquement la méthode par défaut
				let defaultMethod = null;

				// Logique normale : Stripe > PayPal (même pour VAT)
				const paypalButton = document.querySelector('.method-btn[data-method="paypal"]');
				const stripeButton = document.querySelector('.method-btn[data-method="card"]');

				if (stripeButton && fxData.stripe && fxData.stripe.currency) {
					defaultMethod = 'card';
					console.log('🎯 Stripe détecté comme disponible (priorité par défaut)');
				} else if (paypalButton && fxData.paypal && fxData.paypal.currency) {
					defaultMethod = 'paypal';
					console.log('🎯 PayPal détecté comme disponible (fallback)');
				} else {
					console.warn('⚠️ Aucune méthode de paiement disponible');
				}

				console.log('🎯 Méthode par défaut sélectionnée:', defaultMethod);

				// Activer le bouton par défaut et mettre à jour l'affichage
				const defaultButton = document.querySelector(`.method-btn[data-method="${defaultMethod}"]`);
				if (defaultButton) {
					defaultButton.classList.add('active');
					console.log('✅ Bouton par défaut activé:', defaultMethod);

					// PROTECTION VAT: Pour les paiements VAT, garder tous les boutons actifs mais avec valeurs fixes
					if (fxData.has_vat_config) {
						console.log('🛡️ PROTECTION VAT: Toutes les méthodes disponibles avec valeurs fixes');
					}

					// Masquer/afficher les formulaires
					paymentForms.forEach(form => {
						form.classList.toggle('active', form.id === `${defaultMethod}-form`);
					});

					// Mettre à jour l'affichage automatiquement
					setTimeout(() => {
						updatePaymentDisplay(defaultMethod);
					}, 100); // Petit délai pour s'assurer que tout est chargé
				} else {
					console.warn('⚠️ Aucun bouton par défaut trouvé');
				}

				// Écouter les changements de méthode
				methodButtons.forEach(button => {
					button.addEventListener('click', () => {
						console.log('🖱️ Clic sur bouton:', button.dataset.method);

						// Désactiver tous les boutons
						methodButtons.forEach(btn => btn.classList.remove('active'));

						// Activer le bouton cliqué
						button.classList.add('active');

						const method = button.dataset.method;
						console.log('🔄 Changement vers méthode:', method);

						// Masquer/afficher les formulaires
						paymentForms.forEach(form => {
							form.classList.toggle('active', form.id === `${method}-form`);
						});

						// Mettre à jour les montants et devises AUTOMATIQUEMENT
						updatePaymentDisplay(method);
					});
				});

				console.log('🚀 Automatisation des conversions initialisée avec succès');
			});
		</script>

		<!-- PayPal SDK -->
<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<!-- PayPal Button Configuration -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const paypalForm = document.getElementById('paypal-form');
         
        // Check if PayPal configuration exists
        <?php 
        if (empty($paypal_client_id_sandbox) && empty($paypal_client_id_production)) {
            echo "console.error('CRITICAL: No PayPal Client IDs configured');";
            echo "return;";
        }
        ?>
        
        if (paypalForm) {
            try {
                paypal.Button.render({
                    env: '<?php echo $paypal_mode; ?>', // 'sandbox' or 'production'
                    
                    style: {
                        label: 'paypal',
                        size: 'large',
                        shape: 'rect',
                        color: 'white',
                        tagline: false
                    },
                    
                    client: {
                        sandbox: '<?php echo $paypal_client_id_sandbox; ?>',
                        production: '<?php echo $paypal_client_id_production; ?>'
                    },
                    
                    commit: true,
                    
                    payment: function(data, actions) {
                        console.log('Creating payment...');
                        console.log('PayPal Amount: <?php echo $paypal_grand_total; ?> <?php echo $paypal_currency; ?>');
                        return actions.payment.create({
                            payment: {
                                transactions: [
                                    {
                                        amount: { 
                                            total: '<?php echo $paypal_grand_total; ?>', 
                                            currency: '<?php echo $paypal_currency; ?>' 
                                        }
                                    }
                                ]
                            }
                        });
                    },
                    
                    onAuthorize: function(data, actions) {
                        console.log("Payment authorized:", data);
                        return actions.payment.execute().then(function(payment) {
                            console.log("Payment executed successfully:", payment);
                            
                            // Make AJAX call to save payment info
                            <?php 
                            // Pour les paiements school_join ou community, toujours utiliser student/payment_success
                            $is_community_payment = ($payment_type === 'school_join' || $type === 'community');
                            if ($is_community_payment) {
                                $paypal_success_url = site_url('student/payment_success/paypal/' . $invoice_id . '/' . $grand_total . '/0/community');
                                $paypal_redirect_url = lang_route('community_details', ($invoice_details['school_id'] ?? ''));
                            } else {
                                $paypal_success_url = route('payment_success/paypal/' . $invoice_id . '/' . $grand_total . '/0/' . $type);
                                $paypal_redirect_url = route('invoice');
                            }
                            ?>
                            $.ajax({
                                url: '<?php echo $paypal_success_url; ?>',
                                method: 'POST',
                                data: {
                                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                                    paymentID: data.paymentID,
                                    payerID: data.payerID,
                                    currency: '<?php echo $paypal_currency; ?>',
                                    original_currency: '<?php echo $original_currency; ?>',
                                    original_amount: '<?php echo $original_grand_total; ?>',
                                    fx_rate: '<?php echo $paypal_fx_rate_val; ?>',
                                    fx_rate_date: '<?php echo $fx_rate_date; ?>',
                                    conversion_applied: '<?php echo $paypal_needs_conversion ? '1' : '0'; ?>'
                                }
                            }).done(function(result) {
                                console.log("AJAX success response:", result);
                                window.location = '<?php echo $paypal_redirect_url; ?>';
                            }).fail(function(xhr, status, error) {
                                console.error("AJAX error:", status, error);
                                console.error("Response:", xhr.responseText);
                                alert('Erreur lors de l\'enregistrement du paiement. Veuillez contacter le support.');
                            });
                        }).catch(function(error) {
                            console.error("Payment execution error:", error);
                            alert('Erreur lors de l\'exécution du paiement.');
                        });
                    },
                    
                    onCancel: function(data) {
                        
                        alert('Paiement annulé');
                    },
                    
                    onError: function(err) {
                        console.error('=== PAYPAL ERROR ===');
                        console.error('Error object:', err);
                        console.error('Error message:', err.message || 'Unknown error');
                        console.error('Error stack:', err.stack || 'No stack trace');
                        console.error('===================');
                        alert('Erreur PayPal: ' + (err.message || 'Erreur inconnue. Vérifiez la console.')); 
                    }
                    
                }, '#paypal-button-container');
                

                
            } catch (error) {
                console.error('Error rendering PayPal button:', error);
                alert('Impossible de charger le bouton PayPal: ' + error.message);
            }
        } else {
            console.error('PayPal form element not found');
        }
    });
</script>

	</body>
</html>