let nameCheckInProgress = false;
let communityNameIsUnique = true;
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('createCommunityModal');
    const form = document.getElementById('create-community-form');
    const step1 = document.getElementById('modal-step-1');
    const step2 = document.getElementById('modal-step-2');
    const continueBtn = document.getElementById('modal-continue-btn');
    const backBtn = document.getElementById('modal-back-btn');
    const steps = document.querySelectorAll('.step-community');

    const iAmSelect = document.getElementById('i_am');
    const taxSelect = document.getElementById('Tax_residence');
    const priceInput = document.getElementById('community-price');
    const monetizationNotice = document.getElementById('monetizationNotice');
    const currencySymbol = document.getElementById('currencySymbol');
    const privateToggle = document.getElementById('isPrivate');

    taxSelect?.addEventListener('change', function () {
        updateCurrency();
        togglePriceField();
    });
    
    // === DEVISE DYNAMIQUE ===
    function updateCurrency() {
        const tax = taxSelect?.value || '';
        const symbolSpan = document.getElementById('currencySymbol');
        const priceInput = document.getElementById('community-price');
        const planCurrency = document.getElementById('planCurrency');

        if (!symbolSpan || !priceInput) return;

        let symbol = '—';
        let placeholder = '299.00';
        let planSymbol = '';

        if (tax === 'MA') {
            symbol = 'MAD';
            planSymbol = 'MAD';
            placeholder = '299.00';
        } else if (tax === 'UAE') {
            symbol = 'AED';
            planSymbol = 'AED';
            placeholder = '299.00';
        }

        symbolSpan.textContent = symbol;
        symbolSpan.style.color = symbol === '—' ? '#9CA3AF' : '#6B7280';

        if (planCurrency) planCurrency.textContent = planSymbol || '—';

        // Ne pas écraser la valeur saisie
        if (!priceInput.value || priceInput.value === '0') {
            priceInput.placeholder = placeholder;
        }
    }

    // === PRIX BLOQUÉ SI PARTICULIER ===
    function togglePriceField() {
        const isParticulier = iAmSelect?.value === 'Particulier';
        const isPrivateCommunity = privateToggle?.checked;
        const shouldDisable = isParticulier || isPrivateCommunity;

        if (shouldDisable) {
            priceInput.value = '0';
            priceInput.disabled = true;
            priceInput.classList.add('bg-light');

            if (monetizationNotice) {
                let message = '';
                if (isParticulier) {
                    message = monetizationNotice.dataset.particulierMessage || '';
                } else if (isPrivateCommunity) {
                    message = monetizationNotice.dataset.privateMessage || '';
                }
                monetizationNotice.textContent = message;
                monetizationNotice.style.display = message ? 'block' : 'none';
            }
        } else {
            priceInput.disabled = false;
            priceInput.classList.remove('bg-light');
            if (priceInput.value === '0') priceInput.value = '';

            if (monetizationNotice) {
                monetizationNotice.style.display = 'none';
                monetizationNotice.textContent = '';
            }
        }
    }

    // === FONCTION DE VALIDATION COMPLÈTE (temps réel) ===
    function validateStep1() {
        const required = [{
            el: document.getElementById('i_am'),
            msg: 'Veuillez sélectionner votre statut'
        },
        {
            el: document.getElementById('Tax_residence'),
            msg: 'Veuillez sélectionner votre résidence fiscale'
        },
        {
            el: document.getElementById('category'),
            msg: 'Veuillez sélectionner une catégorie'
        },
        {
            el: document.getElementById('community-name'),
            msg: 'Veuillez entrer le nom de la communauté'
        },
        {
            el: document.getElementById('community-description'),
            msg: 'Description trop courte (min. 40 caractères)'
        },
        {
            el: document.getElementById('street'),
            msg: 'Rue requise'
        },
        {
            el: document.getElementById('number'),
            msg: 'Numéro requis'
        },
        {
            el: document.getElementById('city'),
            msg: 'Ville requise'
        },
        {
            el: document.getElementById('code_postal'),
            msg: 'Code postal requis'
        }
        ];

        let allValid = true;

        required.forEach(field => {
            const value = field.el.value.trim();
            let valid = true;

            if (field.el.id === 'community-description') {
                valid = value.length >= 40;
            } else if (field.el.id === 'community-name') {
                valid = value !== '' && value.length <= 80;
            } else {
                valid = value !== '';
            }

            if (valid) {
                field.el.style.borderColor = '#E5E7EB';
            } else {
                allValid = false;
            }
        });

        return allValid;
    }

    // === MISE À JOUR DU BOUTON CONTINUE ===
    function updateContinueButton() {
        const baseValid = validateStep1();
        const nameUnique = communityNameIsUnique;
        const isValid = baseValid && nameUnique && !nameCheckInProgress;

        continueBtn.disabled = !isValid;
        continueBtn.style.opacity = isValid ? '1' : '0.5';
        continueBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
    }
    // === VÉRIFICATION DU NOM DE COMMUNAUTÉ EN TEMPS RÉEL ===
    function checkCommunityNameUniqueness() {
        const nameInput = document.getElementById('community-name');
        const name = nameInput.value.trim();

        clearFieldError(nameInput);
        nameInput.style.borderColor = '#E5E7EB';
        nameInput.classList.remove('is-valid');
        communityNameIsUnique = true;

        if (name.length < 3) {
            updateContinueButton();
            return;
        }

        if (nameCheckInProgress) return;
        nameCheckInProgress = true;

        $.ajax({
            url: window.checkCommunityNameUrl,
            type: 'POST',
            data: Object.assign({
                school_name: name
            }, getCsrfData()), // ← Ajouté
            success: function (response) {
                nameCheckInProgress = false;
                let res = typeof response === 'string' ? JSON.parse(response) : response;

                if (response.csrf?.csrfHash) {
                    window.csrfTokenValue = response.csrf.csrfHash;
                    const input = document.getElementById('csrf_token');
                    if (input) input.value = response.csrf.csrfHash;
                }

                if (res.exists === true) {
                    communityNameIsUnique = false;
                    nameInput.style.borderColor = '#EF4444';
                    showFieldError(nameInput, res.message);
                } else {
                    communityNameIsUnique = true;
                    nameInput.style.borderColor = '#10B981';
                    nameInput.classList.add('is-valid');
                }
                updateContinueButton();
            },
            error: function () {
                nameCheckInProgress = false;
                communityNameIsUnique = true;
                updateContinueButton();
            }
        });
    }

    function showFieldError(input, message) {
        clearFieldError(input);
        const error = document.createElement('small');
        error.className = 'text-danger form-error-text';
        error.style.display = 'block';
        error.style.marginTop = '4px';
        error.style.fontSize = '0.8rem';
        error.textContent = message;
        input.parentNode.appendChild(error);
    }

    function clearFieldError(input) {
        const existing = input.parentNode.querySelector('.form-error-text');
        if (existing) existing.remove();
    }
    // === CHANGEMENT D'ÉTAPE (amélioré avec validation) ===
    function goToStep(step) {
        if (step === 2) {
            if (!validateStep1()) {
                return;
            }
        }

        step1.style.display = step === 1 ? 'block' : 'none';
        step2.style.display = step === 2 ? 'block' : 'none';

        steps.forEach((s, i) => {
            s.classList.toggle('active', i < step);
        });
    }

    // === ÉCOUTEURS SUR TOUS LES CHAMPS (validation en temps réel) ===
    document.querySelectorAll('#modal-step-1 input, #modal-step-1 select, #modal-step-1 textarea').forEach(el => {
        el.addEventListener('input', updateContinueButton);
        el.addEventListener('change', updateContinueButton);
    });

    iAmSelect?.addEventListener('change', () => {
        togglePriceField();
        updateContinueButton();
    });

    privateToggle?.addEventListener('change', () => {
        togglePriceField();
    });

    // === ÉCOUTEURS BOUTONS ===
    continueBtn?.addEventListener('click', () => goToStep(2));
    backBtn?.addEventListener('click', () => goToStep(1));

    updateContinueButton();

    // === VÉRIFICATION DU NOM DE COMMUNAUTÉ ===
    const communityNameInput = document.getElementById('community-name');
    communityNameInput?.addEventListener('blur', checkCommunityNameUniqueness);
    communityNameInput?.addEventListener('input', function () {
        if (!nameCheckInProgress) {
            communityNameIsUnique = true;
            this.style.borderColor = '#E5E7EB';
            this.classList.remove('is-valid');
            clearFieldError(this);
            updateContinueButton();
        }
    });

    document.getElementById('open-create-modal')?.addEventListener('click', function (e) {
        e.preventDefault();

        // Ferme le dropdown
        menu.classList.remove('show');
        trigger.parentElement.classList.remove('open');
        isOpen = false;
        window.removeEventListener('scroll', updateMenuPosition);

        // Ouvre le modal
        modal.classList.add('show');
        goToStep(1);
    });

    // === APERÇU IMAGE ===
    function setupImagePreview(inputId, previewId, boxId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(boxId).querySelector('.upload-placeholder-saas');

        input?.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.backgroundImage = 'none';
                placeholder.style.display = 'flex';
            }
        });
    }

    setupImagePreview('logo-upload', 'logo-preview', 'logo-upload-box');
    setupImagePreview('cover-upload', 'cover-preview', 'cover-upload-box');

    // === FERMETURE MODAL ===
    document.querySelector('.close-button-saas')?.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    window.addEventListener('click', e => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                this.reportValidity();
                return;
            }

            const formData = new FormData(this);
            const csrfInput = document.getElementById('csrf_token');
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';

            $.ajax({
                url: this.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                    // Mise à jour du token CSRF
                    if (data.csrf?.csrfHash) {
                        const input = document.getElementById('csrf_token');
                        if (input) input.value = data.csrf.csrfHash;
                    }

                    if (data.status === true || data.status === 'success') {
                        toastr.success(data.message || 'Communauté créée !');
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '<?php echo site_url("home/communities"); ?>';
                        }, 1500);
                    } else {
                        toastr.error(data.message || 'Erreur lors de la création');
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.querySelector(`[name="${field}"]`);
                                if (input) showFieldError(input, data.errors[field]);
                            });
                        }
                    }
                },
                error: function (xhr, status, err) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    console.error('AJAX Error:', xhr.responseText);
                    toastr.error('Erreur réseau ou serveur (403 ? Vérifie le contrôleur)');
                }
            });
        });
    }

    // === INITIALISATION ===
    togglePriceField();
    updateCurrency();

    function getCsrfData() {
        return {
            [window.csrfTokenName]: window.csrfTokenValue
        };
    }
});