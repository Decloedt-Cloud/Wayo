<?php
  $invoice_details   = $this->crud_model->get_invoice_by_id($invoice_id);
  $student_details   = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);

  // --------- TVA / VAT CALCULATION ---------
  // On récupère les réglages fiscaux de la communauté / école
  $settings_school = $this->settings_model->get_current_settings_school_data();

  $vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
  $tax_residence  = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

  $vat_rate = 0; // en pourcentage
  if ($vat_applicable) {
      if ($tax_residence === 'MA') {
          // 1 - Communauté au Maroc  => 20% de TVA
          $vat_rate = 20;
      } elseif ($tax_residence === 'UAE') {
          // 2 - Communauté aux EAU => 5% de TVA
          $vat_rate = 5;
      }
  }

  $sub_total   = (float)$invoice_details['total_amount'];
  $vat_amount  = $sub_total * ($vat_rate / 100);
  $grand_total = $sub_total + $vat_amount;
?>

<!--title-->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">
            	<i class="mdi mdi-grease-pencil title_icon"></i> <?php echo get_phrase('invoice'); ?>
        	</h4>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">

        <!-- Invoice Logo-->
        <div class="clearfix">
          <div class="float-start mb-3">
            <img src="<?php echo $this->settings_model->get_logo_school($invoice_details['school_id']);; ?>" alt="" height="40">
          </div>
        </div>

        <!-- Invoice Detail-->
        <div class="row">
          <div class="col-sm-6">
            <div class="float-start mt-3">
              <p><b><?php echo get_phrase('hello'); ?>, <?php echo $student_details['name']; ?></b></p>
              <p class="text-muted font-13"><?php echo get_phrase('please_find_below_the_invoice'); ?>.</p>
            </div>

          </div><!-- end col -->
          <div class="col-sm-4 offset-sm-2">
            <div class="mt-3 float-sm-right">
              <p class="font-13"><strong><?php echo get_phrase('invoice_no'); ?>: </strong> &nbsp;&nbsp;&nbsp; <?php echo sprintf('%08d', $invoice_details['id']); ?></p>
              <p class="font-13"><strong><?php echo get_phrase('date'); ?>: </strong> &nbsp;&nbsp;&nbsp; <?php echo date('D, d-M-Y'); ?></p>
              <p class="font-13"><strong><?php echo get_phrase('status'); ?>: </strong>
                <?php if (strtolower($invoice_details['status']) == 'paid'): ?>
                  <span class="badge bg-success "><?php echo get_phrase('paid'); ?></span></p>
                <?php else: ?>
                  <span class="badge bg-danger "><?php echo get_phrase('unpaid'); ?></span></p>
                <?php endif; ?>
            </div>
          </div><!-- end col -->
        </div>
        <!-- end row -->

        <div class="row mt-4">
          <div class="col-sm-4">
            <h6><?php echo get_phrase('billing_details'); ?></h6>
            <address>
              <?php echo $student_details['name']; ?><br>
              <?php echo $student_details['address'] == "" ? '('.get_phrase('address_not_found').')' : $student_details['address']; ?><br>
              <abbr title="Phone">P:</abbr> <?php echo $student_details['phone'] == "" ? '('.get_phrase('phone_number_not_found').')' : $student_details['phone']; ?><br>
            </address>
          </div> <!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
          <div class="col-12">
            <div class="table-responsive">
              <table class="table mt-4">
                <thead>
                  <tr>
                    <th><?php echo get_phrase('product_or_service'); ?></th>
                    <th><?php echo get_phrase('price_excl_vat'); ?></th>
                    <th><?php echo get_phrase('vat'); ?></th>
                    <th><?php echo get_phrase('total_amount'); ?> TTC</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <b><?php echo $invoice_details['title']; ?></b> <br/>
                      <?php echo get_phrase('created_at').' : '.date('D, d-M-Y', $invoice_details['created_at']); ?>
                    </td>
                    <td><?php echo currency($sub_total); ?></td>
                    <td><?php echo $vat_rate > 0 ? $vat_rate.'%' : '-'; ?></td>
                    <td><?php echo currency($grand_total); ?></td>
                  </tr>
                </tbody>
              </table>
            </div> <!-- end table-responsive-->
          </div> <!-- end col -->
        </div>
        <!-- end row -->

          <div class="row">
            <div class="col-sm-6">
              <div class="clearfix pt-3">
                <h6 class="text-muted"></h6>
                <small>

                </small>
              </div>
            </div> <!-- end col -->
            <div class="col-sm-6">
              <div class="float-end mt-3 mt-sm-0">
                <p><b><?php echo get_phrase('sub_total'); ?> :&nbsp;</b>
                  <span class="float-end"><?php echo currency($sub_total); ?></span>
                </p>
                <p>
                  <b><?php echo get_phrase('vat'); ?>
                    <?php echo $vat_rate > 0 ? '(' . $vat_rate . '%)' : ''; ?> :
                  </b>
                  <span class="float-end">
                    <?php echo $vat_rate > 0 ? currency($vat_amount) : currency(0); ?>
                  </span>
                </p>
                <p><b><?php echo get_phrase('grand_total'); ?> : </b>
                  <span class="float-end"><?php echo currency($grand_total); ?></span>
                </p>
                <p><b><?php echo get_phrase('due_amount'); ?> : </b>
                  <span class="float-end"><?php echo currency($grand_total - $invoice_details['paid_amount']); ?></span>
                </p>
                <h3><?php echo currency($grand_total - $invoice_details['paid_amount']); ?></h3>
              </div>
              <div class="clearfix"></div>
            </div> <!-- end col -->
          </div>
          <!-- end row-->

          <div class="d-print-none mt-4">
            <div class="text-end">
              <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> <?php echo get_phrase('print'); ?></a>
              <a href="<?php echo site_url('admin/invoice_pdf/'.$invoice_details['id']); ?>" class="btn btn-success ms-1">
                <i class="mdi mdi-file-pdf"></i> <?php echo get_phrase('download'); ?> PDF
              </a>
            </div>
          </div>
          <!-- end buttons -->

        </div> <!-- end card-body-->
      </div> <!-- end card -->
    </div> <!-- end col-->
  </div>

  <style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card-body, .card-body * {
            visibility: visible;
        }
        .card-body {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 10px;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
            color: white;
            padding: 5px;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
            color: white;
            padding: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
        }
 
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    }
</style>