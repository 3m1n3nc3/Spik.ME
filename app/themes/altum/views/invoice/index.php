<?php defined('ALTUMCODE') || die() ?>

<div class="container mt-5 d-flex justify-content-center">
    <div class="col-12 col-lg-10">

        <div class="d-print-none d-flex justify-content-between mb-5">
            <a href="<?= url('account-payments') ?>" class="text-muted" data-toggle="tooltip" title="<?= $this->language->global->go_back_button ?>"><i class="fa fa-fw fa-arrow-left"></i></a>

            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-fw fa-sm fa-print"></i> <?= $this->language->invoice->print ?></button>
        </div>

        <div class="card border-0">
            <div class="card-body p-5">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <?php if($this->settings->logo != ''): ?>
                        <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'logo/' . $this->settings->logo ?>" class="img-fluid navbar-logo invoice-logo" alt="<?= $this->language->global->accessibility->logo_alt ?>" />
                    <?php else: ?>
                        <h1><?= $this->settings->title ?></h1>
                    <?php endif ?>

                    <div class="d-flex flex-column">
                        <h3><?= $this->language->invoice->invoice ?></h3>

                        <table>
                            <tbody>
                            <tr>
                                <td class="font-weight-bold pr-3"><?= $this->language->invoice->invoice_nr ?>:</td>
                                <td><?= $data->payment->id ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold pr-3"><?= $this->language->invoice->invoice_date ?>:</td>
                                <td><?= \Altum\Date::get($data->payment->date, 1) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <h5><?= $this->language->invoice->vendor->header ?></h5>

                            <table>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->name ?>:</td>
                                    <td><?= $this->settings->business->name ?></td>
                                </tr>

                                <?php if(!empty($this->settings->business->address)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->address ?>:</td>
                                        <td><?= $this->settings->business->address ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->city)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->city ?>:</td>
                                        <td><?= $this->settings->business->city ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->county)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->county ?>:</td>
                                        <td><?= $this->settings->business->county ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->zip)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->zip ?>:</td>
                                        <td><?= $this->settings->business->zip ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->country)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->country ?>:</td>
                                        <td><?= $this->settings->business->country ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->email)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->email ?>:</td>
                                        <td><?= $this->settings->business->email ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->phone)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->language->invoice->vendor->phone ?>:</td>
                                        <td><?= $this->settings->business->phone ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->tax_type) && !empty($this->settings->business->tax_id)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->settings->business->tax_type ?>:</td>
                                        <td><?= $this->settings->business->tax_id ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->custom_key_one) && !empty($this->settings->business->custom_value_one)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->settings->business->custom_key_one ?>:</td>
                                        <td><?= $this->settings->business->custom_value_one ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($this->settings->business->custom_key_two) && !empty($this->settings->business->custom_value_two)): ?>
                                    <tr>
                                        <td class="font-weight-bold pr-3"><?= $this->settings->business->custom_key_two ?>:</td>
                                        <td><?= $this->settings->business->custom_value_two ?></td>
                                    </tr>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 col-md-6">
                            <h5><?= $this->language->invoice->customer->header ?></h5>

                            <table>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold pr-3"><?= $this->language->invoice->customer->name ?>:</td>
                                    <td><?= $this->user->name ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold pr-3"><?= $this->language->invoice->customer->email ?>:</td>
                                    <td><?= $this->user->email ?></td>
                                </tr>
                                </tbody>
                            </table>

                            <textarea class="form-control mt-3" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th><?= $this->language->invoice->table->item ?></th>
                            <th class="text-right"><?= $this->language->invoice->table->amount ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= sprintf($this->language->invoice->table->item_value, $data->payment->package->name, $data->payment_frequency) ?></td>
                            <td class="text-right"><?= $data->payment->amount . ' ' . $data->payment->currency ?></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="d-flex flex-column">
                                <span class="font-weight-bold"><?= $this->language->invoice->table->total ?></span>
                                <small><?= sprintf($this->language->invoice->table->paid_via, $data->payment->processor) ?></small>
                            </td>
                            <td class="text-right font-weight-bold"><?= $data->payment->amount . ' ' . $data->payment->currency ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
