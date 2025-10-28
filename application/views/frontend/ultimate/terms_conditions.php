<!-- ====== HERO compact ====== -->
<section class="pp-hero pt-5"<?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container pp-hero-inner">
    <div class="pp-hero-text">
      <h1><?php echo get_phrase("Terms_and_Conditions_– Wayo_Academy") ?></h1>
      <p><?php echo get_phrase("Transparency,_security,_and_control_over_your_data.") ?></p>
      <div class="pp-meta">
        <span class="pill"><?php echo get_phrase("🇲🇦_Law_09-08") ?></span>
        <span class="pill"><?php echo get_phrase("🇪🇺_GDPR_(if_applicable)") ?></span>
        <span class="pill" id="pp-updated" aria-label="Last updated"><?php echo get_phrase("Updated:_2025-10-07") ?></span>
      </div>
    </div>
    <div class="pp-hero-art" aria-hidden="true">
      <div class="shield"></div>
    </div>
  </div>
</section>

<!-- ====== Breadcrumb ====== -->
<nav class="pp-breadcrumb" aria-label="Breadcrumb"<?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <a href="/"><?php echo get_phrase("Home"); ?></a>
    <span aria-hidden="true">›</span>
    <span><?php echo get_phrase("Terms_and_Conditions"); ?></span>
  </div>
</nav>

<!-- ====== LAYOUT ====== -->
<main class="pp-layout container" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <!-- TOC sticky -->
  <aside class="pp-toc" aria-label="<?php echo get_phrase("Table_of_contents"); ?>">
    <div class="pp-toc-card">
      <div class="pp-search">
        <input id="pp-search" type="search" placeholder="<?php echo get_phrase("Search_in_page…"); ?>" aria-label="<?php echo get_phrase("Search_in_privacy_policy"); ?>" />
      </div>
      <ol id="tocList">
        <li><a href="#s1-intro"><?php echo get_phrase("1._Introduction"); ?></a></li>
        <li><a href="#s2-definitions"><?php echo get_phrase("2._Definitions"); ?></a></li>
        <li><a href="#s3-access"><?php echo get_phrase("3._Access_and_Use"); ?></a></li>
        <li><a href="#s4-protection"><?php echo get_phrase("4._Content_protection_and_use"); ?></a></li>
        <li><a href="#s5-services"><?php echo get_phrase("5._Features_and_services"); ?></a></li>
        <li><a href="#s6-maintenance"><?php echo get_phrase("6._Technical_support_and_maintenance"); ?></a></li>
        <li><a href="#s7-responsibilities"><?php echo get_phrase("7._User_rights_and_responsibilities"); ?></a></li>
        <li><a href="#s8-property"><?php echo get_phrase("8._Intellectual_property"); ?></a></li>
        <li><a href="#s9-data"><?php echo get_phrase("9._Personal_data"); ?></a></li>
        <li><a href="#s10-liability"><?php echo get_phrase("10._Limitation_of_liability"); ?></a></li>
        <li><a href="#s11-termination"><?php echo get_phrase("11._Termination"); ?></a></li>
        <li><a href="#s12-resolution"><?php echo get_phrase("12._Governing_law_and_dispute_resolution"); ?></a></li>
        <li><a href="#s13-terms"><?php echo get_phrase("13._Amendments_to_the_terms"); ?></a></li>
        <li><a href="#s14-waiver"><?php echo get_phrase("14._Waiver_and_acknowledgment"); ?></a></li>
        <li><a href="#s15-additional"><?php echo get_phrase("15._Additional_clauses"); ?></a></li>
        <li><a href="#coordonnees"><?php echo get_phrase("Contact_Information"); ?></a></li>
      </ol>
      <a class="pp-download" href="#" onclick="window.print();return false;"><?php echo get_phrase("Print_/_PDF"); ?></a>
    </div>
  </aside>

  <!-- Article -->
  <article class="pp-article" id="pp-article">

    <!-- Key Info Banner -->
    <section class="pp-section" id="infos">
      <div class="pp-card">
        <h2 class="h5 mb-2"><?php echo get_phrase("Terms_and_Conditions_–_Wayo_Academy"); ?></h2>
        <p class="mb-1"><strong><?php echo get_phrase("Last_updated:"); ?></strong> <?php echo get_phrase("October_7,_2025"); ?></p>
        <p class="mb-1"><strong><?php echo get_phrase("Website:"); ?></strong> <a href="https://preprod.wayo.site" rel="noopener">https://preprod.wayo.site</a></p>
        <p class="mb-0"><strong><?php echo get_phrase("Contact:"); ?></strong> <a href="mailto:info@wayo.cloud">info@wayo.cloud</a></p>
        <p class="mb-0"><strong><?php echo get_phrase("Data_Controller:"); ?></strong> <?php echo get_phrase("Wayo_Academy,_[R320_Umm_Hurair_2,_Dubai,_UAE]"); ?></p>
      </div>
    </section>

    <!-- 1. Introduction -->
<section class="pp-section" id="s1-intro">
  <h2><?php echo get_phrase("1._Introduction"); ?></h2>
  <p><?php echo get_phrase("Welcome_to_Wayo_Academy,_your_online_learning_platform_dedicated_to_the_growth_and_recognition_of_professional_coaches_in_various_fields_(Scrum,_agility,_languages,_sciences,_sports,_etc.)._By_accessing_our_website_and/or_mobile_application,_you_agree_to_comply_with_these_Terms_and_Conditions._Please_read_them_carefully,_as_they_define_your_rights_and_obligations_as_a_user."); ?></p>
</section>

<!-- 2. Definitions -->
<section class="pp-section" id="s2-definitions">
  <h2><?php echo get_phrase("2._Definitions"); ?></h2>

  <div class="pp-card" id="s2-definitions">
    <p><strong><?php echo get_phrase("Wayo_Academy"); ?></strong> <?php echo get_phrase("The_entire_set_of_educational_services_available_through_the_website_and_mobile_application."); ?></p>
    <p><strong><?php echo get_phrase("User:"); ?></strong> <?php echo get_phrase("Any_person_(coach,_learner,_or_organization)_holding_an_account_on_the_platform."); ?></p>
    <p><strong><?php echo get_phrase("Content:"); ?></strong> <?php echo get_phrase("All_documents,_videos,_quizzes,_exams,_live_courses,_and_other_educational_resources_accessible_via_the_platform."); ?></p>
    <p><strong><?php echo get_phrase("Usage_Rights:"); ?></strong> <?php echo get_phrase("Access_and_use_rights_to_the_content_are_strictly_personal,_private,_and_non-transferable."); ?></p>
  </div>
</section>

<section class="pp-section" id="s3-access">
  <h2><?php echo get_phrase("3._Access_and_Use"); ?></h2>
 <div class="pp-card" id="s2-apprenants">
    <p><?php echo get_phrase("The_user_must_create_an_account_by_providing_accurate_and_up-to-date_information."); ?></p>
    <p><?php echo get_phrase("The_minimum_age_required_is"); ?> <strong><?php echo get_phrase("18_years"); ?></strong> <?php echo get_phrase("or_parental_authorization_for_minors."); ?></p>
    <p><?php echo get_phrase("The_user_is_responsible_for_keeping_their_login_details_confidential._Any_unauthorized_use_must_be_reported_immediately."); ?></p>
     <p><?php echo get_phrase("The_platform_is_an_edtech_platform_and_is_intened_to_commercialize_coaching_and_mentoring."); ?></p>
  </div>
</section>

<section class="pp-section" id="s4-protection">
  <h2><?php echo get_phrase("4._Content_Protection_and_Use"); ?></h2>
  <div class="pp-card" id="s4-protection">
    <p><?php echo get_phrase("All_educational_materials_remain_the"); ?> <strong><?php echo get_phrase("intellectual_property_of_the_coaches."); ?></strong></p>
    <p><?php echo get_phrase("Reproduction,_distribution,_sale,_or_sharing_of_content_is_strictly_prohibited."); ?></p>
    <p><?php echo get_phrase("Any_violation_will_result_in_the"); ?> <strong><?php echo get_phrase("immediate_suspension"); ?></strong> <?php echo get_phrase("of_the_account_and_may_lead_to"); ?> <strong><?php echo get_phrase("legal_action."); ?></strong></p>
  </div>
</section>

<section class="pp-section" id="s5-services">
  <h2><?php echo get_phrase("5._Features_and_Services"); ?></h2>
  <div class="pp-card" id="s5-services">
    <p><strong><?php echo get_phrase("Live_courses:"); ?></strong> <?php echo get_phrase("with_interactive_tools_(polls,_Q&A)."); ?></p>
    <p><strong><?php echo get_phrase("Quizzes_and_exams:"); ?></strong> <?php echo get_phrase("Users_agree_to_provide_honest_answers."); ?></p>
    <p><strong><?php echo get_phrase("Calendar_and_notifications:"); ?></strong> <?php echo get_phrase("to_track_classes_and_deadlines."); ?></p>
    <p><strong><?php echo get_phrase("Content_library:"); ?></strong> <?php echo get_phrase("(videos,_documents,_downloadable_materials)."); ?></p>
  </div>
</section>

<section class="pp-section" id="s6-maintenance">
  <h2><?php echo get_phrase("6._Technical_Support_and_Maintenance"); ?></h2>
  <div class="pp-card" id="s6-maintenance">
    <p><?php echo get_phrase("Support_available_Monday_to_Friday,_from"); ?> <strong><?php echo get_phrase("9:00_AM_to_5:00_PM_(UTC+1)"); ?></strong> <?php echo get_phrase("via_email_or_integrated_chat."); ?></p>
    <p><?php echo get_phrase("Estimated_response_time:"); ?> <strong><?php echo get_phrase("24_to_48_hours."); ?></strong></p>
    <p><?php echo get_phrase("Planned_maintenance:_Users_will_be_notified_in_advance_of_any_scheduled_downtime."); ?></p>
  </div>
</section>

<section class="pp-section" id="s7-responsibilities">
  <h2><?php echo get_phrase("7._User_Rights_and_Responsibilities"); ?></h2>
  <p><?php echo get_phrase("For_Coaches:"); ?></p>
  <ul>
    <li><?php echo get_phrase("Full_ownership_of_their_content."); ?></li>
    <li><?php echo get_phrase("Personalize_the_learning_experience_and_recommend_relevant_content."); ?></li>
  </ul>
  <p><?php echo get_phrase("For_Learners:"); ?></p>
  <ul>
    <li><?php echo get_phrase("Obligation_to_attend_and_participate_respectfully_and_diligently."); ?></li>
    <li><?php echo get_phrase("Commitment_not_to_copy,_share,_or_redistribute_any_content."); ?></li>
  </ul>
  <p><?php echo get_phrase("Prohibited_Behavior:"); ?></p>
  <ul>
    <li><?php echo get_phrase("Fraud_(e.g.,_cheating_on_exams,_identity_theft)."); ?></li>
    <li><?php echo get_phrase("Illegal,_offensive,_or_harmful_content."); ?></li>
    <li><?php echo get_phrase("Attempting_to_hack_or_disrupt_the_platform."); ?></li>
  </ul>
  <p><?php echo get_phrase("Any_breach_may_result_in"); ?> <strong><?php echo get_phrase("permanent_suspension"); ?></strong> <?php echo get_phrase("of_the_account."); ?></p>
</section>

<section class="pp-section" id="s8-property">
  <h2><?php echo get_phrase("8._Intellectual_Property"); ?></h2>
  <ul>
    <li><?php echo get_phrase("Coaches_retain_intellectual_property_rights_to_their_courses."); ?></li>
    <li><?php echo get_phrase("By_publishing_content,_the_coach_grants"); ?> <strong><?php echo get_phrase("Wayo_Academy"); ?></strong> <?php echo get_phrase("a"); ?> <strong><?php echo get_phrase("non-exclusive_license"); ?></strong> <?php echo get_phrase("to_host,_display,_and_make_it_accessible_to_learners."); ?></li>
    <li><strong><?php echo get_phrase("Compliance_with_a_legal_obligation:"); ?></strong> <?php echo get_phrase("(e.g.,_invoice_retention,_tax/accounting_obligations)."); ?></li>
    <li><?php echo get_phrase("The_entire_platform_(trademarks,_logos,_design,_code,_etc.)_is_protected_by"); ?> <strong><?php echo get_phrase("copyright_law"); ?></strong> <?php echo get_phrase("and_international_conventions."); ?></li>
  </ul>
</section>

<section class="pp-section" id="s9-data">
  <h2><?php echo get_phrase("9._Personal_Data"); ?></h2>
  <ul>
    <li><?php echo get_phrase("The_collection_and_processing_of_data_are_carried_out_in_accordance_with_the"); ?> <strong><?php echo get_phrase("Privacy_Policy."); ?></strong></li>
    <li><?php echo get_phrase("Users_have_the_right_to_access,_correct,_and_delete_their_personal_data."); ?></li>
  </ul>
</section>

<section class="pp-section" id="s10-liability">
  <h2><?php echo get_phrase("10._Limitation_of_Liability"); ?></h2>
  <p><?php echo get_phrase("Wayo_Academy_shall_not_be_held_responsible_for:"); ?></p>
  <ul>
    <li><?php echo get_phrase("The_quality_or_relevance_of_content_provided_by_coaches."); ?></li>
    <li><?php echo get_phrase("The_results_achieved_by_learners."); ?></li>
    <li><?php echo get_phrase("Disputes_between_coaches_and_learners."); ?></li>
    <li><?php echo get_phrase("Service_interruptions,_data_loss,_or_technical_issues."); ?></li>
  </ul>
  <p><?php echo get_phrase("Wayo_Academy’s_liability_is_limited_to"); ?> <strong><?php echo get_phrase("up_to_the_amount_of_the_subscription_fee_paid."); ?></strong></p>
</section>

<section class="pp-section" id="s11-termination">
  <h2><?php echo get_phrase("11._Termination"); ?></h2>
  <ul>
    <li><?php echo get_phrase("Wayo_Academy_may_suspend_or_terminate_an_account_in_case_of_violation_of_these_Terms."); ?></li>
    <li><?php echo get_phrase("Users_may_close_their_account_through_the_platform_interface_at_any_time."); ?></li>
  </ul>
</section>

<section class="pp-section" id="s12-resolution">
  <h2><?php echo get_phrase("12._Governing_Law_and_Dispute_Resolution"); ?></h2>
  <ul>
    <li><strong><?php echo get_phrase("Governing_Law:"); ?></strong> <?php echo get_phrase("The_laws_in_force_in_the"); ?> <strong><?php echo get_phrase("United_Arab_Emirates"); ?></strong> <?php echo get_phrase("(or_another_jurisdiction_depending_on_Wayo_Academy’s_location)."); ?></li>
    <li><strong><?php echo get_phrase("Analytical_cookies:"); ?></strong> <?php echo get_phrase("(e.g.,_Google_Analytics)_for_audience_measurement."); ?></li>
    <li><?php echo get_phrase("Both_parties_agree_to_seek"); ?> <strong><?php echo get_phrase("amicable_resolution"); ?></strong> <?php echo get_phrase("before_initiating_any_legal_proceedings."); ?></li>
  </ul>
</section>

<section class="pp-section" id="s13-terms">
  <h2><?php echo get_phrase("13._Amendments_to_the_Terms"); ?></h2>
  <ul>
    <li><?php echo get_phrase("Wayo_Academy_may_modify_these_Terms_at_any_time."); ?></li>
    <li><?php echo get_phrase("Users_will_be_notified_of_any_changes,_which_will_take_effect"); ?> <strong><?php echo get_phrase("15_days_after_notification."); ?></strong></li>
  </ul>
</section>

<section class="pp-section" id="s14-waiver">
  <h2><?php echo get_phrase("14._Waiver_and_Acknowledgment"); ?></h2>
  <ul>
    <li><strong><?php echo get_phrase("Class_Action_Waiver:"); ?></strong> <?php echo get_phrase("All_disputes_must_be_handled_individually."); ?></li>
    <li><strong><?php echo get_phrase("Limitation_Period:"); ?></strong> <?php echo get_phrase("Any_claim_must_be_filed_within"); ?> <strong><?php echo get_phrase("1_year_maximum."); ?></strong></li>
  </ul>
</section>

<section class="pp-section" id="s15-additional">
  <h2><?php echo get_phrase("15._Additional_Clauses"); ?></h2>
  <ul>
    <li><strong><?php echo get_phrase("Protective_Measures:"); ?></strong> <?php echo get_phrase("Wayo_Academy_may_seek_an_injunction_to_safeguard_its_rights."); ?></li>
    <li><strong><?php echo get_phrase("Severability:"); ?></strong> <?php echo get_phrase("If_any_clause_is_deemed_invalid,_the_remaining_provisions_shall_remain_in_effect."); ?></li>
  </ul>
</section>

<section class="pp-section" id="coordonnees">
  <h2><?php echo get_phrase("Contact_Information"); ?></h2>
  <p><strong><?php echo get_phrase("Data_Controller:"); ?></strong> <?php echo get_phrase("Wayo_Academy"); ?><br>
  <strong><?php echo get_phrase("Email:"); ?></strong> <a href="mailto:info@wayo.cloud"><?php echo get_phrase("info@wayo.cloud"); ?></a><br>
  <strong><?php echo get_phrase("Address:"); ?></strong> <?php echo get_phrase("[R320_Umm_Hurair_2,_Dubai,_UAE]"); ?></p>
</section>

  </article>
</main>



  <!-- ====== JS minimal : smooth + toc active + recherche ====== -->
  <script>
    // Smooth scroll + URL hash
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click',e=>{
        const id=a.getAttribute('href'); if(id.length<2) return;
        const el=document.querySelector(id);
        if(el){ e.preventDefault(); el.scrollIntoView({behavior:"smooth", block:"start"});
          history.replaceState(null,"",id); }
      });
    });

    // TOC highlight on scroll
    const sections = Array.from(document.querySelectorAll('.pp-article .pp-section[id]'));
    const tocLinks = Array.from(document.querySelectorAll('.pp-toc a[href^="#"]'));
    const headerOffset = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--pp-sticky')) || 84;

    const onScroll = () => {
      let current = sections[0]?.id;
      sections.forEach(sec=>{
        if(sec.getBoundingClientRect().top - headerOffset < 0) current = sec.id;
      });
      tocLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + current));
    };
    document.addEventListener('scroll', onScroll, { passive:true }); onScroll();

    // Recherche in-page
    const q = document.getElementById('pp-search');
    const article = document.getElementById('pp-article');
    q?.addEventListener('input', ()=>{
      const needle = q.value.trim().toLowerCase();
      article.querySelectorAll('mark.pp-hit').forEach(m=>{
        const p=m.parentNode; p.replaceChild(document.createTextNode(m.textContent), m); p.normalize();
      });
      if(!needle) return;
      const walker = document.createTreeWalker(article, NodeFilter.SHOW_TEXT, null);
      let node; const nodes=[];
      while(node = walker.nextNode()){ if(node.nodeValue.trim().length>6) nodes.push(node); }
      nodes.slice(0,400).forEach(n=>{
        const i = n.nodeValue.toLowerCase().indexOf(needle);
        if(i>=0){
          const r = document.createRange();
          r.setStart(n,i); r.setEnd(n,i+needle.length);
          const mark=document.createElement('mark'); mark.className='pp-hit'; mark.textContent=r.toString();
          r.deleteContents(); r.insertNode(mark);
        }
      });
    });

    // Burger nav
    const navToggle = document.querySelector(".nav-toggle");
    const mainNav = document.querySelector(".main-nav");
    navToggle?.addEventListener("click", ()=> mainNav.classList.toggle("nav-open"));
  </script>



