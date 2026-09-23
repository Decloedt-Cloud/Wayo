<?php
$__trial_roles = ['admin', 'teacher'];
$__current_role = strtolower(session()->get('user_type'));
$__current_role_alt = strtolower(session()->get('role')); // Fallback
if (!$__current_role || $__current_role === '') {
    $__current_role = $__current_role_alt;
}
$__is_member = ($__current_role === 'student' || $__current_role === 'member');
$__is_mentor = ($__current_role === 'teacher' || $__current_role === 'mentor');
$__trial_expired = false;

// Utiliser la variable du contrôleur si disponible (priorité)
if (community_billing_enabled() && isset($this->trial_expired) && $this->trial_expired === true) {
    $__trial_expired = true;
}

if (community_billing_enabled() && in_array($__current_role, $__trial_roles, true)) {
    $__school_id = session()->get('active_school_id');
    if (!$__school_id) {
        $__school_id = session()->get('school_id');
    }
    
    if ($__school_id) {
        $__school = db()->table('schools')->where('id', $__school_id)->get()->getRowArray();
        if ($__school) {
            $__now             = time();
            $__is_trial        = isset($__school['is_trial']) ? (int) $__school['is_trial'] : 0;
            $__is_paid         = isset($__school['is_paid']) ? (int) $__school['is_paid'] : 0;
            $__trial_end       = isset($__school['trial_end']) ? (int) $__school['trial_end'] : 0;
            $__subscription_end = isset($__school['subscription_end']) ? (int) $__school['subscription_end'] : 0;
            
            // Vérifier si l'essai de 14 jours est expiré
            if ($__is_trial === 1 && $__is_paid === 0 && $__trial_end > 0 && $__now > $__trial_end) {
                $__trial_expired = true;
            }
            
            // Vérifier si l'abonnement mensuel est expiré
            // Si subscription_end existe et est passé, l'abonnement est expiré (peu importe is_paid)
            // On vérifie seulement si l'école n'est pas en période d'essai (is_trial = 0)
            if ($__is_trial === 0 && $__subscription_end > 0 && $__now > $__subscription_end) {
                $__trial_expired = true;
            }
        }
    }
}

$__community_payment_url = site_url('home/communities');

// DEBUG
$__debug_info = [
    'current_role' => $__current_role ?? 'null',
    'school_id' => $__school_id ?? 'null',
    'trial_expired' => $__trial_expired,
    'is_member' => $__is_member ?? false,
    'is_mentor' => $__is_mentor ?? false
];

// Try to point admins to the unpaid community invoice payment page when trial expired
// Falls back to community list if none is found.
// For subscription expired admins, ensure a renewal invoice exists
$__payment_url = $__community_payment_url ?? '';
if (!empty($__school_id) && $__trial_expired) {
    // For admins with subscription issues, ensure renewal invoice exists
    // DEBUG: log current role
    log_message('error', 'trial_expired_modal: current_role=' . ($__current_role ?? 'null'));
    
    if (isset($__current_role) && ($__current_role === 'admin' || $__current_role === 'superadmin')) {
        $__subscription_service = null;
        if (isset($this->subscriptionService) && is_object($this->subscriptionService)) {
            $__subscription_service = $this->subscriptionService;
        } elseif (class_exists(\App\Libraries\SubscriptionService::class)) {
            $__subscription_service = new \App\Libraries\SubscriptionService();
        }

        // Ensure renewal invoice exists using the subscription service when available
        if ($__subscription_service && method_exists($__subscription_service, 'ensureRenewalInvoice')) {
            $invoice_result = $__subscription_service->ensureRenewalInvoice($__school_id);
            // DEBUG
            log_message('error', 'trial_expired_modal: invoice_result=' . json_encode($invoice_result));
            if ($invoice_result['success']) {
                $__payment_url = site_url('admin/payment/' . $invoice_result['invoice_id']);
            }
        }
    }

    // Fallback: search for existing unpaid invoices if service method failed or not available
    if (empty($__payment_url) || $__payment_url === $__community_payment_url) {
        // DEBUG
        log_message('error', 'trial_expired_modal DEBUG: school_id=' . ($__school_id ?? 'null') . ', payment_url=' . $__payment_url);
        
        // Chercher d'abord une facture avec payment_type = subscription_admin
        $unpaid_invoice = $this->db->table('invoices')
            ->orderBy('id', 'DESC')
            ->where('school_id', $__school_id)
            ->where('payment_type', 'subscription_admin')
            ->where('status', 'unpaid')
            ->where('status <>', 'cancelled')
            ->get()->getRowArray();
        
        // DEBUG
        log_message('error', 'trial_expired_modal: subscription_admin invoice found: ' . ($unpaid_invoice['id'] ?? 'none'));

        // Si aucune facture subscription_admin trouvée, chercher une autre facture impayée
        if (empty($unpaid_invoice['id'])) {
            $other_invoice = $this->db->table('invoices')
                ->orderBy('id', 'DESC')
                ->where('school_id', $__school_id)
                ->where('status', 'unpaid')
                ->get()->getRowArray();
            // DEBUG
            log_message('error', 'trial_expired_modal: any unpaid invoice found: ' . ($other_invoice['id'] ?? 'none'));
            if (!empty($other_invoice['id'])) {
                $unpaid_invoice = $other_invoice;
            }
        }

        // Déterminer l'URL de paiement si une facture impayée existe
        if (!empty($unpaid_invoice['id'])) {
            $__payment_url = site_url('admin/payment/' . $unpaid_invoice['id']);
        }
    }
}
?>

<?php if ($__trial_expired): ?>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    /* Modern Dashboard Standard Variables */
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-lighter: #e0e7ff;
    --primary-dark: #4338ca;
    --secondary: #10b981;
    --bg-main: #f8fafc;
    --bg-card: #ffffff;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
    --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);

    --font-primary: 'DM Sans', sans-serif;
    --font-heading: 'Outfit', sans-serif;
    --radius-card: 20px;
    --radius-btn: 12px;
  }

  #trialExpiredModal {
    font-family: var(--font-primary);
    backdrop-filter: blur(8px);
    z-index: 10000; /* Ensure it's on top */
  }

  #trialExpiredModal .modal-content {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-card);
    box-shadow: var(--shadow-xl);
    background: var(--bg-card);
    overflow: hidden;
  }

  #trialExpiredModal .modal-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-bottom: none;
    padding: 1.5rem 2rem;
    position: relative;
    color: white;
  }

  #trialExpiredModal .modal-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    pointer-events: none;
  }

  #trialExpiredModal .modal-title {
    font-family: var(--font-heading);
    font-weight: 700;
    font-size: 1.35rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  #trialExpiredModal .modal-body {
    padding: 2rem;
    color: var(--text-dark);
  }

  #trialExpiredModal .status-icon-wrapper {
    width: 72px;
    height: 72px;
    background: #fff1f2;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #e11d48;
    font-size: 2rem;
    box-shadow: var(--shadow-sm);
  }

  #trialExpiredModal .trial-message h4 {
    font-family: var(--font-heading);
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
  }

  #trialExpiredModal .trial-message p {
    color: var(--text-muted);
    font-size: 1rem;
    line-height: 1.6;
    max-width: 90%;
    margin: 0 auto;
  }

  #trialExpiredModal .community-section {
    background: var(--bg-main);
    border-radius: 16px;
    padding: 1.5rem;
    margin-top: 2rem;
    border: 1px solid var(--border-color);
  }

  #trialExpiredModal .section-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 1rem;
    display: block;
    font-family: var(--font-heading);
  }

  #trialExpiredModal .trial-community-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 240px;
    overflow-y: auto;
    padding-right: 6px;
  }

  /* Custom Scrollbar */
  #trialExpiredModal .trial-community-list::-webkit-scrollbar {
    width: 5px;
  }
  #trialExpiredModal .trial-community-list::-webkit-scrollbar-track {
    background: transparent;
  }
  #trialExpiredModal .trial-community-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
  }

  #trialExpiredModal .trial-community-item {
    width: 100%;
    border: 1px solid var(--border-color);
    background: white;
    padding: 1rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }

  #trialExpiredModal .trial-community-item:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }

  #trialExpiredModal .trial-community-item.active {
    background: var(--primary-lighter);
    border-color: var(--primary);
    box-shadow: 0 0 0 1px var(--primary);
  }

  #trialExpiredModal .community-info {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  #trialExpiredModal .community-avatar {
    width: 42px;
    height: 42px;
    background: var(--primary-lighter);
    color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    font-family: var(--font-heading);
  }

  #trialExpiredModal .community-name {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1rem;
    font-family: var(--font-heading);
  }

  #trialExpiredModal .role-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.85rem;
    border-radius: 30px;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: 0.02em;
  }

  #trialExpiredModal .role-badge.role-student { background: #f0f9ff; color: #0284c7; }
  #trialExpiredModal .role-badge.role-teacher { background: #f0fdf4; color: #16a34a; }
  #trialExpiredModal .role-badge.role-admin { background: #eef2ff; color: #4f46e5; }
  #trialExpiredModal .role-badge.role-superadmin { background: #fffbeb; color: #d97706; }

  #trialExpiredModal .modal-footer {
    padding: 1.5rem 2rem;
    background: var(--bg-main);
    border-top: 1px solid var(--border-color);
    gap: 1rem;
  }

  .btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-btn);
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    transition: all 0.2s;
    border: none;
    font-family: var(--font-primary);
  }

  .btn-modern-secondary {
    background: white;
    border: 1px solid var(--border-color);
    color: var(--text-muted);
  }
  .btn-modern-secondary:hover {
    background: #f1f5f9;
    color: var(--text-dark);
    border-color: #cbd5e1;
  }

  .btn-modern-primary {
    background: var(--primary);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  }
  .btn-modern-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
  }

  <?php if ($__is_member || $__is_mentor): ?>
  #trialExpiredModal #paymentPageBtn {
    display: none !important;
  }
  <?php endif; ?>
</style>

<div id="trialExpiredModal" class="modal fade show" tabindex="-1" role="dialog" style="display:block; background: rgba(15, 23, 42, 0.6);">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-clock"></i>
          <?php echo get_phrase('subscription_expired'); ?>
        </h5>
      </div>
      <div class="modal-body">
        <div class="text-center trial-message">
          <div class="status-icon-wrapper">
            <i class="fas fa-lock"></i>
          </div>
          <h4><?php echo get_phrase('access_restricted'); ?></h4>
          <p><?php echo get_phrase('to_continue_using_your_community_you_need_to_activate_your_subscription.'); ?></p>
        </div>

        <?php if ($__is_mentor): ?>
        <div class="alert alert-warning d-flex align-items-center gap-2 mt-3" style="border-radius: 8px; border: 1px solid #fcd34d; background: #fffbeb; color: #92400e;">
          <i class="fas fa-info-circle"></i>
          <div style="font-size: 0.9rem;">
            <?php echo get_phrase('as_a_mentor_please_contact_your_community_administrator_to_renew_the_subscription.'); ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="community-section">
          <span class="section-label"><?php echo get_phrase('select_community_to_access'); ?></span>
          <div class="trial-community-list" id="trialCommunityList">
            <div class="text-center py-4 text-muted">
              <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
              <div style="font-size: 0.85rem;"><?php echo get_phrase('loading_communities'); ?>...</div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-modern btn-modern-secondary" id="closeTrialModal">
          <i class="fas fa-sign-out-alt"></i>
          <?php echo get_phrase('logout'); ?>
        </button>
        <?php if (!$__is_member && !$__is_mentor): ?>
         <button type="button"
                 class="btn btn-modern btn-modern-primary"
                 id="paymentPageBtn"
                 data-payment-url="<?php echo $__payment_url; ?>">
          <i class="fas fa-credit-card"></i>
          <?php echo htmlspecialchars(get_phrase("renew_subscription_now")); ?>
         </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    var closeBtn = document.getElementById('closeTrialModal');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        var logoutUrl = "<?php echo site_url('login/logout'); ?>";
        window.location.href = logoutUrl;
      });
    }

    function hideButtonsForMembers() {
      var currentRole = "<?php echo strtolower($__current_role); ?>";
      var isMember = currentRole === 'student' || currentRole === 'member';
      var isMentor = currentRole === 'teacher' || currentRole === 'mentor';
      var paymentBtn = document.getElementById('paymentPageBtn');
      if (isMember || isMentor) {
        if (paymentBtn) paymentBtn.style.display = 'none';
      }
    }
    
    // Vérifier immédiatement au chargement
    hideButtonsForMembers();

    var trialCommunityList = document.getElementById('trialCommunityList');
    var roleLabels = {
      student: "<?php echo get_phrase('member'); ?>",
      teacher: "<?php echo get_phrase('mentor'); ?>",
      admin: "<?php echo get_phrase('admin'); ?>",
      superadmin: "<?php echo get_phrase('superadmin'); ?>"
    };
    var isSwitchingCommunity = false;

    function formatRoleLabel(role) {
      var key = (role || '').toLowerCase();
      if (roleLabels[key]) return roleLabels[key];
      return key ? key.charAt(0).toUpperCase() + key.slice(1) : '';
    }

    function setTrialCommunityState(message) {
      if (!trialCommunityList) return;
      trialCommunityList.innerHTML = '<div class="text-center py-3 text-muted" style="font-size: 0.9rem;">' + message + '</div>';
    }

    function populateTrialCommunityList() {
      if (!trialCommunityList) return;
      
      fetch("<?php echo site_url('home/get_user_communities'); ?>", {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function(response) { return response.text(); })
        .then(function(text) {
          var $data = {};
          try { $data = JSON.parse(text);
          } catch (error) {
            throw new Error('parse_error');
          }

          var activeRole = ($data.active_role || '').toLowerCase();
          var isMemberRole = activeRole === 'student' || activeRole === 'member';
          var isMentorRole = activeRole === 'teacher' || activeRole === 'mentor';
          var paymentBtn = document.getElementById('paymentPageBtn');
          if (isMemberRole || isMentorRole) {
            if (paymentBtn) paymentBtn.style.display = 'none';
          }

          var communities = Array.isArray($data.data) ? $data.data : [];
          trialCommunityList.innerHTML = '';

          if (!communities.length) {
            setTrialCommunityState("<?php echo get_phrase('no_communities_found'); ?>");
          } else {
            communities.forEach(function(item) {
              var role = (item.role || '').toLowerCase();
              if (!item.school_id || !role) return;

              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'trial-community-item';
              button.dataset.schoolId = item.school_id;
              button.dataset.role = role;

              // Avatar (First letter of community name)
              var initial = (item.community_name || 'C').charAt(0).toUpperCase();
              var avatarDiv = document.createElement('div');
              avatarDiv.className = 'community-avatar';
              avatarDiv.textContent = initial;

              // Info wrapper
              var infoDiv = document.createElement('div');
              infoDiv.className = 'community-info';
              
              var nameSpan = document.createElement('span');
              nameSpan.className = 'community-name';
              nameSpan.textContent = item.community_name || '-';

              infoDiv.appendChild(avatarDiv);
              infoDiv.appendChild(nameSpan);

              // Role Badge
              var badgeSpan = document.createElement('span');
              badgeSpan.className = 'role-badge role-' + role;
              badgeSpan.textContent = formatRoleLabel(role);

              button.appendChild(infoDiv);
              button.appendChild(badgeSpan);

              button.addEventListener('click', function() {
                if (isSwitchingCommunity) return;
                trialCommunityList.querySelectorAll('.trial-community-item').forEach(function(el) {
                  el.classList.toggle('active', el === button);
                });
                switchCommunityRole(this.dataset.schoolId, this.dataset.role);
              });

              if (item.is_active) {
                button.classList.add('active');
              }

              trialCommunityList.appendChild(button);
            });
          }
        })
        .catch(function() {
          setTrialCommunityState("<?php echo get_phrase('unexpected_error'); ?>");
        });
    }

    if (trialCommunityList) {
      populateTrialCommunityList();
    }

    var paymentBtn = document.getElementById('paymentPageBtn');
    if (paymentBtn) {
      paymentBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = this.getAttribute('data-payment-url');
        
        if (target && target.length > 0) {
          window.location.href = target;
        } else {
          alert("<?php echo get_phrase('payment_url_not_found') ?: 'URL de paiement non trouvée'; ?>");
        }
      });
    }

    function getCsrfHashFromCookie() {
      var cookieName = "<?php echo config('Security')->cookieName; ?>";
      var escapedName = cookieName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      var match = document.cookie.match(new RegExp('(?:^|; )' + escapedName + '=([^;]*)'));
      return match && match[1] ? decodeURIComponent(match[1]) : '';
    }

    function switchCommunityRole(schoolId, role) {
      if (!schoolId || !role || isSwitchingCommunity) return;
      isSwitchingCommunity = true;

      var params = new URLSearchParams();
      params.append('school_id', schoolId);
      params.append('role', role);
      var csrfInput = document.getElementById('csrf_token');
      var fallbackCsrfName = "<?php echo csrf_token(); ?>";
      var cookieHash = getCsrfHashFromCookie();
      if (csrfInput && csrfInput.name) {
        params.append(csrfInput.name, cookieHash || csrfInput.value);
      } else if (cookieHash) {
        params.append(fallbackCsrfName, cookieHash);
      }

      fetch("<?php echo site_url('home/switch_community_role'); ?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
      })
        .then(function(response) { return response.text(); })
        .then(function(text) {
          var $data = {};
          try { $data = JSON.parse(text);
          } catch (error) {
            throw new Error('parse_error');
          }

          if ($data.status === 'success' && $data.redirect_url) {
            window.location.replace($data.redirect_url);
          } else {
            isSwitchingCommunity = false;
            alert($data.message || "<?php echo get_phrase('unexpected_error'); ?>");
          }
        })
        .catch(function() {
          isSwitchingCommunity = false;
          alert("<?php echo get_phrase('unexpected_error'); ?>");
        });
    }
  })();
</script>
<?php endif; ?>