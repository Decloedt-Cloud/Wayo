<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	    <title><?php echo get_phrase($page_title); ?> | <?php echo $this->db->get_where('schools', array('id' => school_id()))->row('name'); ?></title>
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

		$vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
		$tax_residence  = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

		// Utiliser les valeurs TVA de la facture si elles existent, sinon calculer
		if (isset($invoice_details['vat_amount']) && isset($invoice_details['vat_rate']) && isset($invoice_details['sub_total'])) {
			// Les colonnes TVA existent dans la facture, les utiliser directement
			$sub_total = (float)$invoice_details['sub_total'];
			$vat_amount = (float)$invoice_details['vat_amount'];
			$vat_rate = (float)$invoice_details['vat_rate'];
			$grand_total = (float)$invoice_details['total_amount']; // total_amount est toujours TTC
		} else {
			// Fallback : calculer la TVA (pour les anciennes factures sans colonnes TVA)
			$vat_rate = 0;
			if ($vat_applicable) {
				if ($tax_residence === 'MA') {
					$vat_rate = 20;
				} elseif ($tax_residence === 'UAE') {
					$vat_rate = 5;
				}
			}
			
			$invoice_amount = (float)$amount_to_pay;
			
			if (isset($invoice_details['payment_type']) && $invoice_details['payment_type'] === 'school_join') {
				// Le montant est déjà TTC
				if ($vat_rate > 0) {
					$sub_total = $invoice_amount / (1 + ($vat_rate / 100));
					$vat_amount = $invoice_amount - $sub_total;
					$grand_total = $invoice_amount;
				} else {
					$sub_total = $invoice_amount;
					$vat_amount = 0;
					$grand_total = $invoice_amount;
				}
			} else {
				// Le montant est HT
				$sub_total = $invoice_amount;
				$vat_amount = $sub_total * ($vat_rate / 100);
				$grand_total = $sub_total + $vat_amount;
			}
		}
		
		// --------- CURRENCY CONVERSION (FX RATES) ---------
		// Devise originale de la facture
		$original_currency = isset($fx_original_currency) ? $fx_original_currency : (isset($currency) ? strtoupper($currency) : 'USD');
		$conversion_needed = isset($fx_conversion_needed) ? $fx_conversion_needed : false;
		$fx_stale_flag = isset($fx_stale) ? $fx_stale : false;
		$fx_rate_date = isset($fx_rate_date) ? $fx_rate_date : date('Y-m-d');
		
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
		$paypal_fx_rate_val = isset($paypal_fx_rate) ? (float)$paypal_fx_rate : 1.0;
		$paypal_sub_total = round($original_sub_total * $paypal_fx_rate_val, 2);
		$paypal_vat_amount = round($original_vat_amount * $paypal_fx_rate_val, 2);
		$paypal_grand_total = isset($paypal_converted_amount) ? (float)$paypal_converted_amount : $grand_total;
		
		// Déterminer si Stripe ou PayPal a besoin de conversion
		$stripe_needs_conversion = (strtoupper($stripe_currency) !== $original_currency);
		$paypal_needs_conversion = (strtoupper($paypal_currency) !== $original_currency);
		?>

		<div class="checkout-container container p-0" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
			<div class="row g-0">
				<!-- LEFT SIDE -->
				<section class="payment-section col-12 col-md-7 p-4">
					<h2 class="mb-4"><?php echo get_phrase('Complete your payment'); ?></h2>
					
					<!-- Payment method buttons - Modified -->
					<div class="row g-3 mb-4">
						<?php if ($stripe_enabled && $paypal_enabled): ?>
							<!-- Both enabled - show 50/50 -->
							<div class="col-6">
								<button class="method-btn active" data-method="card">
									<img src="<?php echo base_url('assets\backend\images\payments\stripe.png'); ?>" alt="Stripe">
								</button>
							</div>
							<div class="col-6">
								<button class="method-btn" data-method="paypal">
									<img src="<?php echo base_url('assets\backend\images\payments\Paypal1.png'); ?>" alt="PayPal">
								</button>
							</div>
						<?php elseif ($stripe_enabled): ?>
							<!-- Only Stripe enabled -->
							<div class="col-12">
								<button class="method-btn active" data-method="card">
									<img src="<?php echo base_url('assets\backend\images\payments\stripe.png'); ?>" alt="Stripe">
								</button>
							</div>
						<?php elseif ($paypal_enabled): ?>
							<!-- Only PayPal enabled -->
							<div class="col-12">
								<button class="method-btn active" data-method="paypal">
									<img src="<?php echo base_url('assets\backend\images\payments\Paypal1.png'); ?>" alt="PayPal">
								</button>
							</div>
						<?php else: ?>
							<!-- No payment methods configured -->
							<div class="col-12">
								<div class="alert alert-danger">
									<?php echo get_phrase('No payment methods are configured. Please contact support.'); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<!-- STRIPE FORM - Modified -->
					<?php if ($stripe_enabled): ?>
					<form id="card-form" class="payment-form <?php echo ($stripe_enabled && !$paypal_enabled) || ($stripe_enabled && $paypal_enabled) ? 'active' : ''; ?>" method="post"
						action="<?php echo route('payment_success/stripe/' . $invoice_id.'/'.$stripe_grand_total);?>">

												<input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
												<input type="hidden" name="currency" value="<?php echo $stripe_currency;?>" />
												<input type="hidden" name="payment_type" value="<?php echo $payment_type;?>" />
												<input type="hidden" name="vat_amount" value="<?php echo $stripe_vat_amount;?>" />
												<input type="hidden" name="vat_rate" value="<?php echo $vat_rate;?>" />
												<input type="hidden" name="sub_total" value="<?php echo $stripe_sub_total;?>" />
												<input type="hidden" name="total_amount" value="<?php echo $stripe_grand_total;?>" />
												<input type="hidden" name="type" value="<?php echo $type;?>" />
												
												<!-- Currency Conversion Data -->
												<input type="hidden" name="original_currency" value="<?php echo $original_currency;?>" />
												<input type="hidden" name="original_amount" value="<?php echo $original_grand_total;?>" />
												<input type="hidden" name="fx_rate" value="<?php echo $stripe_fx_rate_val;?>" />
												<input type="hidden" name="fx_rate_date" value="<?php echo $fx_rate_date;?>" />
												<input type="hidden" name="conversion_applied" value="<?php echo $stripe_needs_conversion ? '1' : '0';?>" />
									
												<div class="form-group">
													<label for="email"><?php echo get_phrase('Email address'); ?></label>
													<div class="info-box"><?php echo $user_details['email']; ?></div>
												</div>

												<div class="form-group">
													<label for="card-holder"><?php echo get_phrase('Name on card'); ?></label>
													<div class="info-box"><?php echo $user_details['name']; ?></div>
												</div>

												<label>
													<div id="card-element" class="field"></div>
													<span><span style="color: #111827; font-weight : 600;"><?php echo get_phrase('credit_/_debit_card');?></span></span>
												</label>
												
												<button type="submit">
													<?php echo get_phrase('Pay');?> <?php echo number_format($stripe_grand_total, 2) .' '.$stripe_currency;  ?></strong>
												</button>
												
												<?php if ($stripe_needs_conversion && $conversion_needed): ?>
												<div class="fx-conversion-notice mt-2" style="font-size: 12px; color: #666; background: #f8f9fa; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #28a745;">
													<i class="fa fa-info-circle"></i> 
													<?php echo get_phrase('currency_conversion'); ?>: 
													<strong><?php echo number_format($original_grand_total, 2) . ' ' . $original_currency; ?></strong>
													→ 
													<strong><?php echo number_format($stripe_grand_total, 2) . ' ' . $stripe_currency; ?></strong>
													<br/>
													<small><?php echo get_phrase('exchange_rate'); ?>: 1 <?php echo $original_currency; ?> = <?php echo number_format($stripe_fx_rate_val, 4); ?> <?php echo $stripe_currency; ?> 
													(<?php echo $fx_rate_date; ?>)
													<?php if ($fx_stale_flag): ?>
														<span style="color: #f0ad4e;"><i class="fa fa-exclamation-triangle"></i> <?php echo get_phrase('rate_may_be_outdated'); ?></span>
													<?php endif; ?>
													</small>
												</div>
												<?php endif; ?>
												
												<div class="outcome">
													<div class="error" role="alert"></div>
													<div class="success">
														Success! Your Stripe token is <span class="token"></span>
													</div>
												</div>
												
												<div class="package-details mt-3">
													<strong><?php echo get_phrase('Member_name');?> | <?php echo $user_details['name'];?></strong>
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

					<div class="security-badge">
						<i class="fa fa-lock"></i><?php echo get_phrase('100% secure payment')?> 
					</div>
				</section>

				<!-- RIGHT SIDE -->
				<section class="summary-section col-12 col-md-5 p-4">
					<h2 class="mb-4"><?php echo get_phrase('Order Summary'); ?></h2>

					<div class="d-flex justify-content-between mb-3">
						<span>
							<?php 
								if ($type == "classe") {
									// class payment → show class name
									echo htmlspecialchars($class_name);
								} else {
									// community payment → show community name
									echo htmlspecialchars($community_name);
								}
							?>
							
						</span>
						
						<strong><span class="amount-subtotal"><?php echo number_format($stripe_sub_total, 2); ?></span> <span class="currency-display"><?php echo $stripe_currency; ?></span></strong>
					</div>

					<?php if ($vat_rate > 0): ?>
					<div class="d-flex justify-content-between mb-2">
						<span><?php echo get_phrase('vat'); ?> <?php echo '(' . $vat_rate . '%)'; ?></span>
						<strong><span class="amount-vat"><?php echo number_format($stripe_vat_amount, 2); ?></span> <span class="currency-display"><?php echo $stripe_currency; ?></span></strong>
					</div>
					<?php endif; ?>

					<div class="summary-divider my-4"></div>

					<div class="d-flex justify-content-between summary-total">
						<span><?php echo get_phrase('Total'); ?></span>
						<strong class="text-orange"><span class="amount-total"><?php echo number_format($stripe_grand_total, 2); ?></span> <span class="currency-display"><?php echo $stripe_currency; ?></span></strong>
					</div>
					
					<?php if ($conversion_needed && ($stripe_needs_conversion || $paypal_needs_conversion)): ?>
					<div class="original-amount-info mt-3 pt-3" style="border-top: 1px dashed #dee2e6;">
						<div class="d-flex justify-content-between text-muted" style="font-size: 13px;">
							<span><?php echo get_phrase('original_price'); ?></span>
							<strong><?php echo number_format($original_grand_total, 2) . ' ' . $original_currency; ?></strong>
						</div>
						<div class="fx-rate-info text-muted" style="font-size: 11px; margin-top: 4px;">
							<i class="fa fa-exchange-alt"></i>
							<?php echo get_phrase('exchange_rate'); ?>: 
							<span id="current-fx-rate">1 <?php echo $original_currency; ?> = <?php echo number_format($stripe_fx_rate_val, 4); ?> <?php echo $stripe_currency; ?></span>
							<?php if ($fx_stale_flag): ?>
								<br/><span style="color: #f0ad4e;"><i class="fa fa-exclamation-triangle"></i> <?php echo get_phrase('rate_may_be_outdated'); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
				</section>
			</div>
		</div>

			<!-- CRITICAL: Load Stripe.js first -->
		<script src="https://js.stripe.com/v3/"></script>
		
		<!-- CRITICAL: Define variables BEFORE loading stripe.js -->
		<script type="text/javascript">
			// ========== DEVISES PAR MODE DE PAIEMENT ==========
			var stripe_key = '<?php echo isset($stripe_public_key) ? $stripe_public_key : ""; ?>';
			var stripe_currency = '<?php echo $stripe_currency; ?>';
			var paypal_currency = '<?php echo $paypal_currency; ?>';
			var original_currency = '<?php echo $original_currency; ?>';
			
			// ========== MONTANTS CONVERTIS ==========
			var fxData = {
				conversion_needed: <?php echo $conversion_needed ? 'true' : 'false'; ?>,
				original_currency: '<?php echo $original_currency; ?>',
				original_sub_total: <?php echo $original_sub_total; ?>,
				original_vat_amount: <?php echo $original_vat_amount; ?>,
				original_grand_total: <?php echo $original_grand_total; ?>,
				stripe: {
					currency: '<?php echo $stripe_currency; ?>',
					sub_total: <?php echo $stripe_sub_total; ?>,
					vat_amount: <?php echo $stripe_vat_amount; ?>,
					grand_total: <?php echo $stripe_grand_total; ?>,
					fx_rate: <?php echo $stripe_fx_rate_val; ?>,
					needs_conversion: <?php echo $stripe_needs_conversion ? 'true' : 'false'; ?>
				},
				paypal: {
					currency: '<?php echo $paypal_currency; ?>',
					sub_total: <?php echo $paypal_sub_total; ?>,
					vat_amount: <?php echo $paypal_vat_amount; ?>,
					grand_total: <?php echo $paypal_grand_total; ?>,
					fx_rate: <?php echo $paypal_fx_rate_val; ?>,
					needs_conversion: <?php echo $paypal_needs_conversion ? 'true' : 'false'; ?>
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
				
				// Fonction pour formater un nombre avec 2 décimales
				function formatNumber(num) {
					return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
				}
				
				// Fonction pour mettre à jour l'affichage des montants et devises
				function updatePaymentDisplay(method) {
					const data = (method === 'card') ? fxData.stripe : fxData.paypal;
					const currency = data.currency;
					
					// Mettre à jour les devises
					currencyDisplays.forEach(el => {
						el.textContent = currency;
					});
					
					// Mettre à jour les montants
					if (amountSubtotal) {
						amountSubtotal.textContent = formatNumber(data.sub_total);
					}
					if (amountVat) {
						amountVat.textContent = formatNumber(data.vat_amount);
					}
					if (amountTotal) {
						amountTotal.textContent = formatNumber(data.grand_total);
					}
					
					// Mettre à jour l'info du taux de change
					if (fxRateInfo && fxData.conversion_needed) {
						fxRateInfo.textContent = '1 ' + fxData.original_currency + ' = ' + data.fx_rate.toFixed(4) + ' ' + currency;
					}
				}
				
				methodButtons.forEach(button => {
					button.addEventListener('click', () => {
						methodButtons.forEach(btn => btn.classList.remove('active'));
						button.classList.add('active');
						
						const method = button.dataset.method;
						paymentForms.forEach(form => {
							form.classList.toggle('active', form.id === `${method}-form`);
						});
						
						// Mettre à jour les montants et devises selon le mode de paiement
						updatePaymentDisplay(method);
					});
				});
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
                            $.ajax({
                                url: '<?php echo route('payment_success/paypal/' . $invoice_id . '/' . $paypal_grand_total . '/0/' . $type); ?>',
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
                                window.location = '<?php echo route('invoice'); ?>';
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