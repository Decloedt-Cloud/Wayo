<table id="basic-datatable" class="table table-striped dt-responsive nowrap" width="100%">
    <thead class="thead-dark">
        <tr>
            <th><i class="mdi mdi mdi-barcode thead-icon"></i><?php echo get_phrase('invoice_no'); ?></th>
            <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('student'); ?></th>
            <th class="d-none d-md-table-cell"><i class="mdi mdi-file-document-outline thead-icon"></i><?php echo get_phrase('invoice_title'); ?></th>
            <th class="d-none d-md-table-cell"><i class="mdi mdi-currency-usd thead-icon"></i><?php echo get_phrase('total_amount'); ?></th>
            <th class="d-none d-md-table-cell"><i class="mdi mdi-check-circle-outline thead-icon"></i><?php echo get_phrase('paid_amount'); ?></th>
            <th class="d-none d-md-table-cell"><i class="mdi mdi-checkbox-marked-circle-outline thead-icon"></i><?php echo get_phrase('status'); ?></th>
            <th>
                <span class="d-none d-md-inline"><i class="mdi mdi-dots-vertical thead-icon"></i><?php echo get_phrase('option'); ?></span>
                <span class="d-md-none"><i class="mdi mdi-chevron-down"></i><?php echo get_phrase('details'); ?></span>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status)->result_array();
        foreach ($invoices as $invoice):
            $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
            $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id'])->row_array(); ?>
            <tr>
                <td> <?php echo sprintf('%08d', $invoice['id']); ?> </td>
                <td>
                    <?php echo $student_details['name']; ?> <br>
                    <small> <strong><?php echo get_phrase('class'); ?> :</strong> <?php echo $class_details['name']; ?></small>
                </td>
                <td class="d-none d-md-table-cell"> <?php echo $invoice['title']; ?> </td>
                <td class="d-none d-md-table-cell">
                    <?php echo currency($invoice['total_amount']); ?> <br>
                    <small> <strong> <?php echo get_phrase('created_at'); ?> : </strong> <?php echo date('d-M-Y', $invoice['created_at']); ?> </small>
                </td>
                <td class="d-none d-md-table-cell">
                    <?php echo currency($invoice['paid_amount']); ?> <br>
                    <small>
                        <strong> <?php echo get_phrase('payment_date'); ?> : </strong>
                        <?php if ($invoice['updated_at'] > 0): ?>
                            <?php echo date('d-M-Y', $invoice['updated_at']); ?>
                        <?php else: ?>
                            <?php echo get_phrase('not_found'); ?>
                        <?php endif; ?>

                    </small>
                </td>
                <td class="d-none d-md-table-cell">
                    <?php if (strtolower($invoice['status']) == 'unpaid'): ?>
                        <span class="badge badge-danger-lighten"><?php echo ucfirst($invoice['status']); ?></span>
                    <?php else: ?>
                        <span class="badge badge-success-lighten"><?php echo ucfirst($invoice['status']); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="dropdown text-center d-none d-md-block">
                        <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="<?php echo route('invoice/invoice/'.$invoice['id']); ?>" class="dropdown-item" target="_blank"><?php echo get_phrase('print_invoice'); ?></a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item" onclick="rightModal('<?php echo site_url('modal/popup/invoice/edit/'.$invoice['id']); ?>', '<?php echo get_phrase('update_invoice'); ?>');"><?php echo get_phrase('edit'); ?></a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item" onclick="confirmModal('<?php echo route('invoice/delete/'.$invoice['id']); ?>', showAllInvoices )"><?php echo get_phrase('delete'); ?></a>
                        </div>
                    </div>
                     <!-- Version Mobile: Le bouton "+" -->
                    <button class="btn btn-info btn-sm d-md-none expand-button" type="button">+</button>
                </td>
            </tr>
  <!-- AJOUTÉ: Ligne dépliable, visible uniquement sur mobile au clic -->
            <tr class="expandable-row d-md-none">
 <td colspan="3">
                    <div class="expanded-details">
                        <div class="detail-item">
                            <strong><?php echo get_phrase('invoice_title'); ?>:</strong>
                            <span><?php echo $invoice['title']; ?></span>
                        </div>
                         <div class="detail-item">
                            <strong><?php echo get_phrase('total_amount'); ?>:</strong>
                            <span><?php echo currency($invoice['total_amount']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong><?php echo get_phrase('paid_amount'); ?>:</strong>
                            <span><?php echo currency($invoice['paid_amount']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong><?php echo get_phrase('status'); ?>:</strong>
                            <span>
                                <?php if (strtolower($invoice['status']) == 'unpaid'): ?>
                                    <span class="badge badge-danger-lighten"><?php echo ucfirst($invoice['status']); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-success-lighten"><?php echo ucfirst($invoice['status']); ?></span>
                                <?php endif; ?>
                            </span>
                        </div>

            <div class="detail-item justify-content-center">
                            <!-- Le menu dropdown est ici pour mobile -->
                             <a href="<?php echo route('invoice/invoice/'.$invoice['id']); ?>" class="btn btn-outline-primary btn-sm m-1" target="_blank"><?php echo get_phrase('print'); ?></a>
                             <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm m-1" onclick="rightModal('<?php echo site_url('modal/popup/invoice/edit/'.$invoice['id']); ?>', '<?php echo get_phrase('update_invoice'); ?>');"><?php echo get_phrase('edit'); ?></a>
                             <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm m-1" onclick="confirmModal('<?php echo route('invoice/delete/'.$invoice['id']); ?>', showAllInvoices )"><?php echo get_phrase('delete'); ?></a>
                        </div>
                    </div>
                </td>
            </tr>


        <?php endforeach; ?>
    </tbody>
</table>
<!-- AJOUTER CE CSS ET JAVASCRIPT À VOTRE PAGE SI CE N'EST PAS DÉJÀ FAIT -->
<script>
$(document).ready(function() {
    // S'assure de ne pas attacher l'événement plusieurs fois
    // Utile si ce code est dans un fichier inclus plusieurs fois
    if (typeof window.expandButtonInitialized === 'undefined') {
        $('body').on('click', '.expand-button', function() {
            $(this).closest('tr').next('.expandable-row').toggle();
            $(this).text($(this).text() == '+' ? '-' : '+');
        });
        window.expandButtonInitialized = true;
    }
});
</script>
<style>
    .expandable-row {
        display: none;
    }
    .expanded-details {
        padding: 15px;
        background-color: #f8f9fa;
        border-top: 2px solid #6c757d;
    }
    .expanded-details .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 5px;
        border-bottom: 1px solid #e9ecef;
    }
    .expanded-details .detail-item:last-child {
        border-bottom: none;
        padding-top: 15px;
    }
    .expanded-details strong {
        margin-right: 10px;
    }
</style>