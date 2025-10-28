<!-- ====== HERO compact ====== -->
<section class="pp-hero pt-5" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container pp-hero-inner">
    <div class="pp-hero-text">
      <h1><?php echo get_phrase("Privacy_Policy") ?></h1>
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
<nav class="pp-breadcrumb" aria-label="Breadcrumb" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <a href="/"><?php echo get_phrase("Home"); ?></a>
    <span aria-hidden="true">›</span>
    <span><?php echo get_phrase("Privacy_Policy"); ?></span>
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
      <ul id="tocList">
        <li><a href="#s1-intro"><?php echo get_phrase("1._Introduction"); ?></a></li>
        <li>
          <a href="#s2-donnees"><?php echo get_phrase("2._Data_We_Collect"); ?></a>
          <ol>
            <li><a href="#s2-mentors"><?php echo get_phrase("2.1_Mentors"); ?></a></li>
            <li><a href="#s2-apprenants"><?php echo get_phrase("2.2_Learners"); ?></a></li>
            <li><a href="#s2-techniques"><?php echo get_phrase("2.3_Technical_Information"); ?></a></li>
            <li><a href="#s2-paiement"><?php echo get_phrase("2.4_Payment_Data"); ?></a></li>
            <li><a href="#s2-mineurs"><?php echo get_phrase("2.5_Data_of_Minors"); ?></a></li>
          </ol>
        </li>
        <li><a href="#s3-finalites"><?php echo get_phrase("3._Purpose"); ?></a></li>
        <li><a href="#s4-bases"><?php echo get_phrase("4._Legal_Bases"); ?></a></li>
        <li><a href="#s5-partage"><?php echo get_phrase("5._Sharing_of_Your_Information"); ?></a></li>
        <li><a href="#s6-securite"><?php echo get_phrase("6._Data_Security"); ?></a></li>
        <li><a href="#s7-responsabilites"><?php echo get_phrase("7._User_Responsibilities"); ?></a></li>
        <li><a href="#s8-cookies"><?php echo get_phrase("8._Cookies_and_Tracking_Technologies"); ?></a></li>
        <li><a href="#s9-liens"><?php echo get_phrase("9._Links_to_Third-Party_Sites"); ?></a></li>
        <li><a href="#s10-durees"><?php echo get_phrase("10._Retention_Periods"); ?></a></li>
        <li><a href="#s11-enfants"><?php echo get_phrase("11._Children_Privacy"); ?></a></li>
        <li><a href="#s12-droits"><?php echo get_phrase("12._Your_Rights"); ?></a></li>
        <li><a href="#s13-transferts"><?php echo get_phrase("13._International_Transfers"); ?></a></li>
        <li><a href="#s14-modifs"><?php echo get_phrase("14._Changes"); ?></a></li>
        <li><a href="#coordonnees"><?php echo get_phrase("Contact_Information"); ?></a></li>
      </ul>
      <a class="pp-download" href="#" onclick="window.print();return false;"><?php echo get_phrase("Print_/_PDF"); ?></a>
    </div>
  </aside>

  <!-- Article -->
  <article class="pp-article" id="pp-article">

    <!-- Key Info Banner -->
    <section class="pp-section" id="infos">
      <div class="pp-card">
        <h2 class="h5 mb-2"><?php echo get_phrase("Privacy_Policy_–_Wayo_Academy"); ?></h2>
        <p class="mb-1"><strong><?php echo get_phrase("Last_updated:"); ?></strong> <?php echo get_phrase("October_7,_2025"); ?></p>
        <p class="mb-1"><strong><?php echo get_phrase("Website:"); ?></strong> <a href="https://preprod.wayo.site" rel="noopener">www.wayo.ma</a></p>
        <p class="mb-0"><strong><?php echo get_phrase("Contact:"); ?></strong> <a href="mailto:info@wayo.cloud">info@wayo.cloud</a></p>
        <p class="mb-0"><strong><?php echo get_phrase("Data_Controller:"); ?></strong> <?php echo get_phrase("Wayo_Academy,_[R320 Umm Hurair 2, Dubai, UAE]"); ?></p>
      </div>
    </section>

    <!-- 1. Introduction -->
<section class="pp-section" id="s1-intro">
  <h2><?php echo get_phrase("1._Introduction"); ?></h2>
  <p><?php echo get_phrase("At_Wayo_Academy,_we_are_committed_to_collecting_and_using_your_data_responsibly,_in_compliance_with_applicable_data_protection_laws,_including_Moroccan_Law_No._09-08_and,_when_applicable,_the_GDPR_(EU_2016/679)."); ?></p>
  <p><?php echo get_phrase("By_using_our_platform,_you_agree_to_the_collection,_processing,_and_use_of_your_data_as_described_in_this_Privacy_Policy."); ?></p>
</section>


<!-- 2. Data We Collect -->
<section class="pp-section" id="s2-donnees">
  <h2><?php echo get_phrase("2._Data_We_Collect"); ?></h2>
  <p><?php echo get_phrase("We_only_collect_data_that_is_necessary,_in_accordance_with_Law_09-08_and,_where_applicable,_the_GDPR."); ?></p>

  <div class="pp-card" id="s2-mentors">
    <h3><?php echo get_phrase("2.1._For_Mentors_(Coaches_/_Trainers)"); ?></h3>
    <p><strong><?php echo get_phrase("Identification_and_contact_data:"); ?></strong> <?php echo get_phrase("first_name,_last_name,_email_address,_phone_number."); ?></p>
    <p><strong><?php echo get_phrase("Profile:"); ?></strong> <?php echo get_phrase("profile_photo_(if_provided),_biography,_expertise."); ?></p>
    <p><strong><?php echo get_phrase("Content_created:"); ?></strong> <?php echo get_phrase("courses,_videos,_educational_resources_you_upload._Unless_otherwise_stated_in_a_contract,_you_retain_ownership;_you_only_grant_the_necessary_rights_for_hosting_and_sharing_on_the_platform."); ?></p>
    <p><strong><?php echo get_phrase("Usage_data:"); ?></strong> <?php echo get_phrase("interactions_with_the_platform_(content_creation,_engagement_statistics,_messages)."); ?></p>
  </div>

  <div class="pp-card" id="s2-apprenants">
    <h3><?php echo get_phrase("2.2._For_Learners"); ?></h3>
    <p><strong><?php echo get_phrase("Identification_and_contact_data:"); ?></strong> <?php echo get_phrase("name,_email."); ?></p>
    <p><strong><?php echo get_phrase("Learning_progress:"); ?></strong> <?php echo get_phrase("course_progress,_quiz/test_results,_certificates_earned,_participation_in_live_classes,_comments."); ?></p>
    <p><strong><?php echo get_phrase("Preferences:"); ?></strong> <?php echo get_phrase("language,_interests_(if_provided)."); ?></p>
  </div>

  <div class="pp-card" id="s2-techniques">
    <h3><?php echo get_phrase("2.3._Technical_Information_(all_users)"); ?></h3>
    <p><strong><?php echo get_phrase("Technical_data:"); ?></strong> <?php echo get_phrase("IP_address,_browser_type_and_version,_device_type,_operating_system,_session_identifiers,_timestamps,_pages_visited,_clicks,_referrers."); ?></p>
    <p><strong><?php echo get_phrase("Cookies_and_similar_technologies:"); ?></strong> <?php echo get_phrase("see_section_8_(Cookies)."); ?></p>
  </div>

  <div class="pp-card" id="s2-paiement">
    <h3><?php echo get_phrase("2.4._Payment_Data"); ?></h3>
    <p><strong><?php echo get_phrase("Transactions:"); ?></strong> <?php echo get_phrase("amounts,_currency,_date,_payment_method,_billing_address."); ?></p>
    <p><em><?php echo get_phrase("Important:"); ?></em> <?php echo get_phrase("card_data_is_processed_exclusively_by_our_payment_provider_(e.g.,_Stripe)._Wayo_Academy_does_not_store_credit_card_information."); ?></p>
  </div>

  <div class="pp-card" id="s2-mineurs">
    <h3><?php echo get_phrase("2.5._Data_of_Minors"); ?></h3>
    <p><?php echo get_phrase("For_learners_under_18,_data_is_collected_only_with_the_explicit_consent_of_a_parent_or_legal_guardian._Parents/guardians_can_request_access,_correction,_or_deletion_of_their_child's_data_via"); ?> <a href="mailto:info@wayo.cloud"><?php echo get_phrase("info@wayo.cloud"); ?></a>.</p>
  </div>
</section>

<!-- 3. Purpose -->
<section class="pp-section" id="s3-finalites">
  <h2><?php echo get_phrase("3._Purpose_of_Using_Your_Data"); ?></h2>
  <p><?php echo get_phrase("We_use_your_data_to:"); ?></p>
  <ul>
    <li><?php echo get_phrase("Provide,_maintain,_and_improve_our_services_(course_access,_video_hosting,_progress_tracking)."); ?></li>
    <li><?php echo get_phrase("Personalize_the_learning_experience_and_recommend_relevant_content."); ?></li>
    <li><?php echo get_phrase("Manage_contractual_relationships_(accounts,_subscriptions,_billing,_support)."); ?></li>
    <li><?php echo get_phrase("Communicate_service_information_(updates,_changes,_alerts)."); ?></li>
    <li><?php echo get_phrase("Ensure_platform_security_and_integrity_(abuse/fraud_detection)."); ?></li>
    <li><?php echo get_phrase("Perform_usage_analysis_(audience_measurement,_R&D)_with_aggregated_or_anonymized_data_whenever_possible."); ?></li>
  </ul>
  <p><?php echo get_phrase("All_use_is_in_accordance_with_applicable_laws_(Law_09-08,_GDPR_if_relevant)."); ?></p>
</section>

<!-- 4. Legal Bases -->
<section class="pp-section" id="s4-bases">
  <h2><?php echo get_phrase("4._Legal_Bases_for_Processing"); ?></h2>
  <p><?php echo get_phrase("Depending_on_the_case,_we_rely_on:"); ?></p>
  <ul>
    <li><strong><?php echo get_phrase("Your_consent"); ?></strong> <?php echo get_phrase("(_e.g.,_non-essential_cookies,_newsletters,_optional_data)."); ?></li>
    <li><strong><?php echo get_phrase("Performance_of_a_contract"); ?></strong> <?php echo get_phrase("or_pre-contractual_measures_(e.g.,_registration,_course_access,_billing)."); ?></li>
    <li><strong><?php echo get_phrase("Compliance_with_a_legal_obligation"); ?></strong> <?php echo get_phrase("(_e.g.,_invoice_retention,_tax/accounting_obligations)."); ?></li>
    <li><strong><?php echo get_phrase("Our_legitimate_interest"); ?></strong> <?php echo get_phrase("(_e.g.,_security,_service_improvement,_internal_analyses)."); ?></li>
  </ul>
</section>

<!-- 5. Sharing -->
<section class="pp-section" id="s5-partage">
  <h2><?php echo get_phrase("5._Sharing_Your_Information"); ?></h2>
  <p><?php echo get_phrase("Wayo_Academy_does_not_sell_your_personal_data._Limited_sharing_may_occur:"); ?></p>
  <ul>
    <li><strong><?php echo get_phrase("Essential_service_providers:"); ?></strong>
      <ul>
        <li><strong><?php echo get_phrase("Stripe"); ?></strong> <?php echo get_phrase("(_secure_payments):_processes_only_necessary_payment_data."); ?></li>
        <li><strong><?php echo get_phrase("Hosting_providers"); ?></strong> <?php echo get_phrase("(_servers_preferably_within_the_EU):_content_storage_and_delivery,_databases."); ?></li>
        <li><strong><?php echo get_phrase("Emailing_tools"); ?></strong> <?php echo get_phrase("(_e.g.,_Brevo/Mailchimp):_transactional_emails_and,_with_consent,_marketing."); ?></li>
        <li><strong><?php echo get_phrase("Analytics"); ?></strong> <?php echo get_phrase("(_e.g.,_Google_Analytics):_audience_measurement,_subject_to_consent_for_non-essential_cookies."); ?></li>
      </ul>
    </li>
    <li><strong><?php echo get_phrase("With_Mentors:"); ?></strong> <?php echo get_phrase("learner_data_strictly_necessary_for_course_delivery_(name,_email,_progress,_results)."); ?></li>
    <li><strong><?php echo get_phrase("Legal_reasons:"); ?></strong> <?php echo get_phrase("if_required_by_law_or_in_response_to_a_legitimate_request_from_a_competent_authority."); ?></li>
  </ul>
  <p><?php echo get_phrase("Where_applicable,_contractual_clauses_impose_confidentiality,_security,_and_compliance_guarantees_on_our_subprocessors_(Law_09-08_/_GDPR)."); ?></p>
</section>

<!-- 6. Security -->
<section class="pp-section" id="s6-securite">
  <h2><?php echo get_phrase("6._Data_Security"); ?></h2>
  <ul>
    <li><?php echo get_phrase("TLS/HTTPS_encryption_for_data_in_transit."); ?></li>
    <li><?php echo get_phrase("Access_controls_based_on_need-to-know."); ?></li>
    <li><?php echo get_phrase("Regular_backups_and_restoration_procedures.:"); ?></li>
    <li><?php echo get_phrase("Security_updates_and_periodic_audits."); ?></li>
    <li><?php echo get_phrase("Logging_and_detection_of_abnormal_activities."); ?></li>
  </ul>
  <p><?php echo get_phrase("In_case_of_a_data_breach,_we_will_inform_affected_users_promptly_and_provide_recommended_protective_measures,_in_accordance_with_applicable_laws."); ?></p>
</section>

<!-- 7. User Responsibilities -->
<section class="pp-section" id="s7-responsabilites">
  <h2><?php echo get_phrase("7._User_Responsibilities_and_Content_Protection"); ?></h2>
  <ul>
    <li><?php echo get_phrase("Respect_the_intellectual_property_rights_of_mentors_and_Wayo_Academy."); ?></li>
    <li><?php echo get_phrase("Do_not_copy,_share,_redistribute,_or_resell_content_without_authorization."); ?></li>
    <li><?php echo get_phrase("Understand_that_any_violation_may_result_in_account_suspension_and,_if_applicable,_legal_action."); ?></li>
  </ul>
</section>

<!-- 8. Cookies -->
<section class="pp-section" id="s8-cookies">
  <h2><?php echo get_phrase("8._Cookies_and_Tracking_Technologies"); ?></h2>
  <p><?php echo get_phrase("We_use:"); ?></p>
  <ul>
    <li><strong><?php echo get_phrase("Essential_cookies"); ?></strong> <?php echo get_phrase("(_site_functionality,_security,_session)."); ?></li>
    <li><strong><?php echo get_phrase("Analytical_cookies"); ?></strong> <?php echo get_phrase("(_e.g.,_Google_Analytics)_for_audience_measurement."); ?></li>
    <li><strong><?php echo get_phrase("Marketing_cookies"); ?></strong> <?php echo get_phrase("(_if_applicable)_to_personalize_the_experience."); ?></li>
  </ul>
  <p><?php echo get_phrase("Non-essential_cookies_are_only_set_with_your_consent_via_a_cookie_banner_on_your_first_visit._You_can_withdraw_or_modify_your_preferences_at_any_time_via_the_banner_or_your_browser_settings._For_more_information,_see_our_Cookie_Policy."); ?></p>
</section>

<!-- 9. Links -->
<section class="pp-section" id="s9-liens">
  <h2><?php echo get_phrase("9._Links_to_Third-Party_Sites"); ?></h2>
  <p><?php echo get_phrase("The_platform_may_contain_links_to_external_sites._Wayo_Academy_is_not_responsible_for_their_content_or_privacy_practices._We_encourage_you_to_review_their_respective_privacy_policies."); ?></p>
</section>

<!-- 10. Retention -->
<section class="pp-section" id="s10-durees">
  <h2><?php echo get_phrase("10._Data_Retention_Periods"); ?></h2>
  <p><?php echo get_phrase("We_retain_your_data_only_for_as_long_as_necessary_for_the_purposes_described_above_or_as_required_by_law._Afterward,_data_is_deleted_or_anonymized."); ?></p>

  <div class="table-responsive">
    <table class="table table-sm table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th><?php echo get_phrase("Data_Category"); ?></th>
          <th><?php echo get_phrase("Indicative_Retention_Period"); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo get_phrase("User_account_(inactive)"); ?></td>
          <td><?php echo get_phrase("3_years_after_last_activity"); ?></td>
        </tr>
        <tr>
          <td><?php echo get_phrase("Learning_data_(progress,_results)"); ?></td>
          <td><?php echo get_phrase("While_the_account_is_active_or_as_per_contractual_obligations"); ?></td>
        </tr>
        <tr>
          <td><?php echo get_phrase("Billing_and_accounting_data"); ?></td>
          <td><?php echo get_phrase("10_years_(legal_requirement)"); ?></td>
        </tr>
        <tr>
          <td><?php echo get_phrase("Marketing_data_(prospects/clients)"); ?></td>
          <td><?php echo get_phrase("3_years_after_last_contact"); ?></td>
        </tr>
        <tr>
          <td><?php echo get_phrase("Technical_logs"); ?></td>
          <td><?php echo get_phrase("6–24_months_depending_on_purpose"); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<!-- 11. Children's Privacy -->
<section class="pp-section" id="s11-enfants">
  <h2><?php echo get_phrase("11._Children's_Privacy"); ?></h2>
  <p><?php echo get_phrase("For_users_under_18,_collection_and_processing_are_done_only_with_explicit_consent_of_a_parent/guardian._Parents/guardians_can_access,_correct,_or_request_deletion_of_their_child's_data_by_writing_to"); ?> <a href="mailto:info@wayo.cloud">info@wayo.cloud</a>.</p>
</section>

<!-- 12. Your Rights -->
<section class="pp-section" id="s12-droits">
  <h2><?php echo get_phrase("12._Your_Rights"); ?></h2>
  <p><?php echo get_phrase("Under_Law_09-08_(and_GDPR_if_applicable),_you_have_the_following_rights:"); ?></p>
  <ul>
    <li><strong><?php echo get_phrase("Access:"); ?></strong><?php echo get_phrase("_obtain_confirmation_that_data_concerning_you_is_processed_and_receive_a_copy."); ?></li>
    <li><strong><?php echo get_phrase("Rectification:"); ?></strong> <?php echo get_phrase("correct_inaccurate_or_incomplete_data."); ?></li>
    <li><strong><?php echo get_phrase("Erasure:"); ?></strong> <?php echo get_phrase("request_deletion_of_your_data,_subject_to_legal_obligations."); ?></li>
    <li><strong><?php echo get_phrase("Objection:"); ?></strong> <?php echo get_phrase("object_to_processing_for_legitimate_reasons_(including_marketing)."); ?></li>
    <li><strong><?php echo get_phrase("Restriction:"); ?></strong> <?php echo get_phrase("request_temporary_limitation_of_processing_in_certain_cases."); ?></li>
    <li><strong><?php echo get_phrase("Portability_(GDPR):"); ?></strong> <?php echo get_phrase("receive_your_data_in_a_structured,_commonly_used_format."); ?></li>
    <li><strong><?php echo get_phrase("Withdraw_consent:"); ?></strong> <?php echo get_phrase("at_any_time,_without_affecting_the_legality_of_prior_processing."); ?></li>
  </ul>
  <p><?php echo get_phrase("To_exercise_your_rights:"); ?> <a href="mailto:info@wayo.cloud">info@wayo.cloud</a>. <?php echo get_phrase("Proof_of_identity_may_be_required._We_will_respond_within_30_days_(legal_timeframe)."); ?></p>
  <p><strong><?php echo get_phrase("Moroccan_supervisory_authority_(CNDP):"); ?></strong> <a href="https://www.cndp.ma" rel="noopener">www.cndp.ma</a> – <?php echo get_phrase("You_may_contact_the_CNDP_if_you_believe_your_rights_are_not_respected._For_EU_users,_you_may_contact_your_member_state_authority."); ?></p>
</section>

<!-- 13. International Transfers -->
<section class="pp-section" id="s13-transferts">
  <h2><?php echo get_phrase("13._International_Data_Transfers"); ?></h2>
  <p><?php echo get_phrase("We_prefer_hosting_and_processing_within_the_EU_or_Morocco._When_data_is_transferred_to_third_countries_(e.g.,_tools_provided_by_companies_outside_the_EU/Morocco),_we_implement_appropriate_safeguards_(e.g.,_EU_standard_contractual_clauses,_contractual_commitments,_additional_technical_measures)_to_ensure_an_adequate_level_of_protection."); ?></p>
</section>

<!-- 14. Changes -->
<section class="pp-section" id="s14-modifs">
  <h2><?php echo get_phrase("14._Changes_to_this_Policy"); ?></h2>
  <p><?php echo get_phrase("Wayo_Academy_may_update_this_Policy_to_reflect_legal,_technical,_or_operational_changes._Users_will_be_informed_of_significant_changes_via_the_platform_and/or_email._The_last_update_date_is_shown_at_the_top._Continued_use_of_the_platform_constitutes_acceptance_of_the_updated_Policy."); ?></p>
</section>

<!-- Contact Information -->
<section class="pp-section" id="coordonnees">
  <h2><?php echo get_phrase("Contact_Information"); ?></h2>
  <p><strong><?php echo get_phrase("Data_Controller:"); ?></strong> <?php echo get_phrase("Wayo_Academy"); ?><br>
  <strong><?php echo get_phrase("Email:"); ?></strong> <a href="mailto:info@wayo.cloud"><?php echo get_phrase("info@wayo.cloud"); ?></a><br>
  <strong><?php echo get_phrase("Address:"); ?></strong> <?php echo get_phrase("[R320 Umm Hurair 2, Dubai, UAE]"); ?></p>
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



