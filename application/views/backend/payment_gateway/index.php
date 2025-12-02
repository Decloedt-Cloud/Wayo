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
						action="<?php echo route('payment_success/stripe/' . $invoice_id.'/'.$amount_to_pay);?>">

												<input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
												
												<input type="hidden" name="type" value="<?php echo $type;?>" />

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
													<?php echo get_phrase('Pay');?> <?php echo number_format($amount_to_pay, 2) .' '.$stripe_currency;  ?></strong>
												</button>
												
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
							- marks (x1)
						</span>
						
						
						<strong><?php echo number_format($amount_to_pay, 2) .' '.$stripe_currency; ?></strong>
					</div>

					<div class="summary-divider my-4"></div>

					<div class="d-flex justify-content-between summary-total">
						<span>Total</span>
						<strong class="text-orange"><?php echo number_format($amount_to_pay, 2) .' '. $stripe_currency; ?></strong>
					</div>
				</section>
			</div>
		</div>

			<!-- CRITICAL: Load Stripe.js first -->
		<script src="https://js.stripe.com/v3/"></script>
		
		<!-- CRITICAL: Define variables BEFORE loading stripe.js -->
		<script type="text/javascript">
			<?php 
				// Debug: Let's see what we have
				//error_log('Public Key: ' . (isset($stripe_public_key) ? $stripe_public_key : 'NOT SET'));
				//error_log('Stripe Currency: ' . (isset($stripe_currency) ? $stripe_currency : 'NOT SET'));
			?>
			
			var stripe_key = '<?php echo isset($stripe_public_key) ? $stripe_public_key : ""; ?>';
			var stripe_currency = '<?php echo isset($stripe_currency) ? $stripe_currency : "MAD"; ?>';
			
			
			
			// Stop if no key
			if (!stripe_key || stripe_key.length === 0) {
				//alert('ERREUR: Clé Stripe manquante. Vérifiez votre configuration de paiement.');
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
				
				methodButtons.forEach(button => {
					button.addEventListener('click', () => {
						methodButtons.forEach(btn => btn.classList.remove('active'));
						button.classList.add('active');
						
						const method = button.dataset.method;
						paymentForms.forEach(form => {
							form.classList.toggle('active', form.id === `${method}-form`);
						});
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
                        return actions.payment.create({
                            payment: {
                                transactions: [
                                    {
                                        amount: { 
                                            total: '<?php echo $amount_to_pay; ?>', 
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
                                url: '<?php echo route('payment_success/paypal/' . $invoice_id . '/' . $amount_to_pay . '/0/' . $type); ?>',
                                method: 'POST',
                                data: {
                                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                                    paymentID: data.paymentID,
                                    payerID: data.payerID
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