<?php
$__trial_roles = ['admin', 'teacher'];
$__current_role = strtolower($this->session->userdata('user_type'));
$__current_role_alt = strtolower($this->session->userdata('role')); // Fallback
if (!$__current_role || $__current_role === '') {
    $__current_role = $__current_role_alt;
}
$__is_member = ($__current_role === 'student' || $__current_role === 'member');
$__is_mentor = ($__current_role === 'teacher' || $__current_role === 'mentor');
$__trial_expired = false;

// Utiliser la variable du contrôleur si disponible (priorité)
if (isset($this->trial_expired) && $this->trial_expired === true) {
    $__trial_expired = true;
}

if (in_array($__current_role, $__trial_roles, true)) {
    $__school_id = $this->session->userdata('active_school_id');
    if (!$__school_id) {
        $__school_id = $this->session->userdata('school_id');
    }

    if ($__school_id) {
        $__school = $this->db->get_where('schools', ['id' => $__school_id])->row_array();
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

// Try to point admins to the unpaid community invoice payment page when trial expired
// Falls back to community list if none is found.
// IMPORTANT: we only read existing invoices here to avoid duplicate creations.
$__payment_url = $__community_payment_url;
if ($__school_id && $__trial_expired) {
    // Chercher d'abord une facture avec payment_type = subscription_admin
    $this->db->order_by('id', 'DESC');
    $this->db->where('school_id', $__school_id);
    $this->db->where('payment_type', 'subscription_admin');
    $this->db->where('status', 'unpaid');
    $unpaid_invoice = $this->db->get('invoices')->row_array();

    // Si aucune facture subscription_admin trouvée, chercher une autre facture impayée
    if (empty($unpaid_invoice['id'])) {
        $this->db->order_by('id', 'DESC');
        $this->db->where('school_id', $__school_id);
        $this->db->where('status', 'unpaid');
        $other_invoice = $this->db->get('invoices')->row_array();
        if (!empty($other_invoice['id'])) {
            $unpaid_invoice = $other_invoice;
        }
    }
    // Déterminer l'URL de paiement si une facture impayée existe
    if (!empty($unpaid_invoice['id'])) {
        if (isset($unpaid_invoice['payment_type']) && $unpaid_invoice['payment_type'] === 'subscription_admin') {
            $__payment_url = site_url('admin/payment/subscription_admin/' . $unpaid_invoice['id']);
        } else {
            $__payment_url = site_url('student/payment/community/' . $unpaid_invoice['id']);
        }
    }
}
?>

<?php if ($__trial_expired): ?>
<style>
  #trialExpiredModal .trial-community-wrapper {
    margin-top: 1rem;
  }
  #trialExpiredModal .trial-community-list {
    border: 1px solid #e4e7ec;
    border-radius: 16px;
    padding: 8px;
    max-height: 260px;
    overflow-y: auto;
    background: #fff;
  }
  #trialExpiredModal .trial-community-item {
    width: 100%;
    border: none;
    background: transparent;
    padding: 10px 14px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    transition: background 0.2s ease, box-shadow 0.2s ease;
    font-weight: 500;
  }
  #trialExpiredModal .trial-community-item:hover {
    background: #f5f7fb;
  }
  #trialExpiredModal .trial-community-item.active {
    background: #eef3ff;
    box-shadow: inset 0 0 0 1px #4c6fff;
  }
  #trialExpiredModal .trial-community-item .community-name {
    color: #111;
  }
  #trialExpiredModal .role-badge {
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 999px;
    font-weight: 600;
  }
  #trialExpiredModal .role-badge.role-student {
    background: #e9f1ff;
    color: #2563eb;
  }
  #trialExpiredModal .role-badge.role-teacher,
  #trialExpiredModal .role-badge.role-mentor {
    background: #e6fbf3;
    color: #0f8b60;
  }
  #trialExpiredModal .role-badge.role-admin {
    background: #e6edff;
    color: #3347ff;
  }
  #trialExpiredModal .role-badge.role-superadmin {
    background: #fff3dc;
    color: #b45309;
  }
  #trialExpiredModal .trial-community-empty {
    text-align: center;
    padding: 18px 10px;
    font-size: 14px;
  }
  #trialExpiredModal .trial-community-divider {
    height: 1px;
    background: #f0f2f6;
    margin: 8px 0;
  }
  #trialExpiredModal .trial-community-item.switch-member {
    justify-content: flex-start;
    gap: 10px;
    font-weight: 600;
  }
  #trialExpiredModal .trial-community-item .switch-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #f5f7fb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #4c6fff;
  }
  <?php if ($__is_member || $__is_mentor): ?>
  #trialExpiredModal #paymentPageBtn {
    display: none !important;
  }
  <?php endif; ?>
</style>

<div id="trialExpiredModal" class="modal fade show" tabindex="-1" role="dialog" style="display:block; background: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo get_phrase('your_trial_has_ended'); ?></h5>
      </div>
      <div class="modal-body text-center">
        <p><?php // echo get_phrase('enjoy_a_14-day_free_trial.'); ?></p>
        <p class="mb-0"><strong><?php echo get_phrase('to_continue_using_your_community_you_need_to_activate_your_subscription.'); ?></strong></p>
        <?php if ($__is_mentor): ?>
        <div class="alert alert-warning mt-3 mb-0">
          <i class="mdi mdi-information"></i>
          <strong><?php echo get_phrase('note'); ?>:</strong> <?php echo get_phrase('as_a_mentor_please_contact_your_community_administrator_to_renew_the_subscription.'); ?>
        </div>
        <?php endif; ?>
        <div class="trial-community-wrapper text-start">
          <label class="form-label mb-2">
            <?php echo get_phrase('community'); ?>
          </label>
          <div class="trial-community-list" id="trialCommunityList">
            <div class="trial-community-empty text-muted">
              <?php echo get_phrase('please_wait'); ?>...
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" id="closeTrialModal">
          <?php echo get_phrase('logout'); ?>
        </button>
        <?php if (!$__is_member && !$__is_mentor): ?>

                                          <!-- <button class="btn btn-wayo btn-sm flex-fill btn-apply"><?php echo get_phrase("Sign up") ?></button> -->
         <button type="button"
                 class="btn btn-primary"
                 id="paymentPageBtn"
                 data-payment-url="<?php echo $__payment_url; ?>">
          <?php echo htmlspecialchars(get_phrase("go_to_payment_page")); ?>
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
        var currentRole = "<?php echo strtolower($__current_role); ?>";
        var logoutUrl = currentRole === 'teacher' ? "<?php echo site_url('login/logout'); ?>" : "<?php echo site_url('login/logout'); ?>";
        window.location.href = logoutUrl;
      });
    }

    function switchToMemberAccount() {
      var params = new URLSearchParams();
      var csrfInput = document.getElementById('csrf_token');
      if (csrfInput) {
        params.append(csrfInput.name, csrfInput.value);
      }

      fetch("<?php echo site_url('home/switch_to_member_account'); ?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
      })
        .then(function (response) {
          return response.text();
        })
        .then(function (text) {
          var data;
          try {
            data = JSON.parse(text);
          } catch (error) {
            throw new Error('parse_error');
          }

          if (data.status === 'success' && data.redirect_url) {
            window.location.replace(data.redirect_url);
          } else {
            alert(data.message || "<?php echo get_phrase('unexpected_error'); ?>");
          }
        })
        .catch(function () {
          alert("<?php echo get_phrase('unexpected_error'); ?>");
        });
    }

    // Masquer le bouton de paiement pour les comptes membres et mentors (double vérification)
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
      trialCommunityList.innerHTML = '<div class="trial-community-empty text-muted">' + message + '</div>';
    }

    function populateTrialCommunityList() {
      if (!trialCommunityList) return;
      setTrialCommunityState("<?php echo get_phrase('please_wait'); ?>...");

      fetch("<?php echo site_url('home/get_user_communities'); ?>", {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function(response) { return response.text(); })
        .then(function(text) {
          var data = {};
          try {
            data = JSON.parse(text);
          } catch (error) {
            throw new Error('parse_error');
          }

          // Vérifier le rôle actif et masquer le bouton de paiement si c'est un membre ou mentor
          var activeRole = (data.active_role || '').toLowerCase();
          var isMemberRole = activeRole === 'student' || activeRole === 'member';
          var isMentorRole = activeRole === 'teacher' || activeRole === 'mentor';
          var paymentBtn = document.getElementById('paymentPageBtn');
          if (isMemberRole || isMentorRole) {
            if (paymentBtn) paymentBtn.style.display = 'none';
          }

          var communities = Array.isArray(data.data) ? data.data : [];
          trialCommunityList.innerHTML = '';

          if (!communities.length) {
            setTrialCommunityState("<?php echo get_phrase('no_data_found'); ?>");
          } else {
            communities.forEach(function(item) {
              var role = (item.role || '').toLowerCase();
              if (!item.school_id || !role) return;

              var button = document.createElement('button');
              button.type = 'button';
              button.className = 'trial-community-item';
              button.dataset.schoolId = item.school_id;
              button.dataset.role = role;

              var nameSpan = document.createElement('span');
              nameSpan.className = 'community-name';
              nameSpan.textContent = item.community_name || '-';

              var badgeSpan = document.createElement('span');
              badgeSpan.className = 'role-badge role-' + role;
              badgeSpan.textContent = formatRoleLabel(role);

              button.appendChild(nameSpan);
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

          var currentRole = "<?php echo strtolower($__current_role); ?>";
          if (currentRole !== 'student') {
            var divider = document.createElement('div');
            divider.className = 'trial-community-divider';
            trialCommunityList.appendChild(divider);

            var switchMemberBtn = document.createElement('button');
            switchMemberBtn.type = 'button';
            switchMemberBtn.className = 'trial-community-item switch-member';
            switchMemberBtn.innerHTML =
              '<span class="switch-icon"><i class="fas fa-user-circle"></i></span>' +
              '<span><?php echo get_phrase('switch_to_member_account'); ?></span>';
            switchMemberBtn.addEventListener('click', switchToMemberAccount);
            trialCommunityList.appendChild(switchMemberBtn);
          }
        })
        .catch(function() {
          setTrialCommunityState("<?php echo get_phrase('unexpected_error'); ?>");
        });
    }

    if (trialCommunityList) {
      populateTrialCommunityList();
    }

    // Redirection vers la page de paiement dédiée (communautés)
    var paymentBtn = document.getElementById('paymentPageBtn');
    if (paymentBtn) {
      paymentBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = this.getAttribute('data-payment-url');
        var communityUrl = "<?php echo $__community_payment_url; ?>";
        console.log('Payment URL:', target);
        console.log('Community URL:', communityUrl);
        
        if (target && target.length > 0 && target !== communityUrl && target.indexOf('admin/payment') !== -1) {
          // Forcer la redirection vers la page de paiement admin
          console.log('Redirecting to:', target);
          window.location.href = target;
        } else if (target && target.length > 0 && target !== communityUrl) {
          // Autre type de facture
          console.log('Redirecting to:', target);
          window.location.href = target;
        } else {
          console.error('Invalid payment URL:', target);
          alert("<?php echo get_phrase('payment_url_not_found') ?: 'URL de paiement non trouvée'; ?>");
        }
      });
    }

    function switchCommunityRole(schoolId, role) {
      if (!schoolId || !role || isSwitchingCommunity) return;
      isSwitchingCommunity = true;

      var params = new URLSearchParams();
      params.append('school_id', schoolId);
      params.append('role', role);
      var csrfInput = document.getElementById('csrf_token');
      if (csrfInput) {
        params.append(csrfInput.name, csrfInput.value);
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
          var data = {};
          try {
            data = JSON.parse(text);
          } catch (error) {
            throw new Error('parse_error');
          }

          if (data.status === 'success' && data.redirect_url) {
            window.location.replace(data.redirect_url);
          } else {
            isSwitchingCommunity = false;
            alert(data.message || "<?php echo get_phrase('unexpected_error'); ?>");
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

