<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">

    <?php display_notifications() ?>

    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url() ?>"><?= $this->language->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li><a href="<?= url('package') ?>"><?= $this->language->package->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= sprintf($this->language->pay->breadcrumb, $data->package->name) ?></li>
            </ol>
        </small>
    </nav>

    <?php if($data->package_id == 'trial'): ?>

        <h1 class="h3"><?= sprintf($this->language->pay->trial->header, $data->package->name) ?></h1>
        <div class="text-muted mb-5"><?= $this->language->pay->trial->subheader ?></div>

        <form action="<?= 'pay/' . $data->package_id ?>" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <?php if($this->user->package_id != 'free' && !$this->user->package_is_expired): ?>
                <div class="alert alert-info" role="alert">
                    <?= $this->language->pay->trial->other_package_not_expired ?>
                </div>
            <?php endif ?>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= sprintf($this->language->pay->trial->trial_start, $data->package->days) ?></button>
            </div>

            <div class="mt-3 text-muted text-center">
                <small>
                    <?= sprintf(
                        $this->language->pay->accept,
                        '<a href="' . $this->settings->terms_and_conditions_url . '">' . $this->language->global->terms_and_conditions . '</a>',
                        '<a href="' . $this->settings->privacy_policy_url . '">' . $this->language->global->privacy_policy . '</a>'
                    ) ?>
                </small>
            </div>
        </form>

    <?php elseif(is_numeric($data->package_id)): ?>

    <?php
    /* Check for extra savings on the prices */
    $annual_price_savings = ceil(($data->package->monthly_price * 12) - $data->package->annual_price);

    ?>
        <h1 class="h3"><?= sprintf($this->language->pay->custom_package->header, $data->package->name) ?></h1>
        <div class="text-muted mb-5"><?= $this->language->pay->custom_package->subheader ?></div>

        <div class="mb-5"><i class="fa fa-fw fa-box-open mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_frequency ?></span></div>

        <form action="<?= 'pay/' . $data->package_id ?>" method="post" enctype="multipart/form-data" role="form">
            <input type="hidden" name="package_id" value="<?= $data->package_id ?>" />
            <input type="hidden" name="monthly_price" value="<?= $data->package->monthly_price ?>" />
            <input type="hidden" name="annual_price" value="<?= $data->package->annual_price ?>" />
            <input type="hidden" name="lifetime_price" value="<?= $data->package->lifetime_price ?>" />
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="row d-flex align-items-stretch">

                <?php if($data->package->monthly_price): ?>
                <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">
                    <input type="radio" id="monthly_price" name="payment_frequency" value="monthly" class="custom-control-input" required="required">

                    <div class="card zoomer h-100">
                        <div class="card-body">

                            <div class="card-title text-center"><?= $this->language->pay->custom_package->monthly ?></div>

                            <div class="mt-3 text-center">
                                <span id="monthly_price_amount" class="custom-radio-box-main-text"><?= $data->package->monthly_price ?></span> <span><?= $this->settings->payment->currency ?></span>
                            </div>

                        </div>
                    </div>
                </label>
                <?php endif ?>

                <?php if($data->package->annual_price): ?>
                <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">
                    <input type="radio" id="annual_price" name="payment_frequency" value="annual" class="custom-control-input" required="required">

                    <div class="card zoomer h-100">
                        <div class="card-body">

                            <div class="card-title text-center"><?= $this->language->pay->custom_package->annual ?></div>

                            <div class="mt-3 text-center">
                                <span id="annual_price_amount" class="custom-radio-box-main-text"><?= $data->package->annual_price ?></span> <span><?= $this->settings->payment->currency ?></span>
                                <div class="text-muted">
                                    <small><?= sprintf($this->language->pay->custom_package->annual_savings, $annual_price_savings, $this->settings->payment->currency) ?></small>
                                </div>
                            </div>

                        </div>
                    </div>
                </label>
                <?php endif ?>

                <?php if($data->package->lifetime_price): ?>
                    <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">
                        <input type="radio" id="lifetime_price" name="payment_frequency" value="lifetime" class="custom-control-input" required="required">

                        <div class="card zoomer h-100">
                            <div class="card-body">

                                <div class="card-title text-center"><?= $this->language->pay->custom_package->lifetime ?></div>

                                <div class="mt-3 text-center">
                                    <span id="lifetime_price_amount" class="custom-radio-box-main-text"><?= $data->package->lifetime_price ?></span> <span><?= $this->settings->payment->currency ?></span>
                                    <div class="text-muted">
                                        <small><?= $this->language->pay->custom_package->lifetime_help ?></small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </label>
                <?php endif ?>

            </div>

            <?php if($this->settings->payment->codes_is_enabled): ?>
                <div class="form-group mt-4">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-fw fa-tags mr-3"></i> <input type="text" name="code" class="form-control form-control-lg" placeholder="<?= $this->language->pay->custom_package->code ?>" />
                    </div>

                    <div class="mt-2"><span id="code_help" class="text-muted"></span></div>
                </div>

            <?php ob_start() ?>
                <script>
                    let timer = null;

                    $('input[name="code"]').on('change paste keyup', event => {

                        let code = $(event.currentTarget).val();
                        let package_id = $('input[name="package_id"]').val();
                        let monthly_price = $('input[name="monthly_price"]').val();
                        let annual_price = $('input[name="annual_price"]').val();
                        let lifetime_price = $('input[name="lifetime_price"]').val();
                        let is_valid = false;

                        clearTimeout(timer);

                        if(code.trim() == '') {
                            $('#monthly_price_amount').html(monthly_price);
                            $('#annual_price_amount').html(annual_price);
                            $('#lifetime_price_amount').html(lifetime_price);
                            $('#code_help').html('');
                            $(event.currentTarget).removeClass('is-invalid').removeClass('is-valid');

                            return;
                        }

                        timer = setTimeout(() => {
                            $.ajax({
                                type: 'POST',
                                url: `${url}pay/code`,
                                data: {code, global_token, package_id},
                                success: data => {

                                    if(data.status == 'success') {
                                        is_valid = true;

                                        /* Set the new discounted price */
                                        let new_monthly_price = nr(monthly_price - (monthly_price * data.details.discount / 100), 2);
                                        let new_annual_price = nr(annual_price - (annual_price * data.details.discount / 100), 2);
                                        let new_lifetime_price = nr(lifetime_price - (lifetime_price * data.details.discount / 100), 2);

                                        $('#monthly_price_amount').html(new_monthly_price);
                                        $('#annual_price_amount').html(new_annual_price);
                                        $('#lifetime_price_amount').html(new_lifetime_price);
                                    } else {
                                        $('#monthly_price_amount').html(monthly_price);
                                        $('#annual_price_amount').html(annual_price);
                                        $('#lifetime_price_amount').html(lifetime_price);
                                    }

                                    $('#code_help').html(data.message);

                                    if(is_valid) {
                                        $(event.currentTarget).addClass('is-valid');
                                        $(event.currentTarget).removeClass('is-invalid');
                                    } else {
                                        $(event.currentTarget).addClass('is-invalid');
                                        $(event.currentTarget).removeClass('is-valid');
                                    }

                                },
                                dataType: 'json'
                            });
                        }, 500);

                    });
                </script>
                <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
            <?php endif ?>

            <div class="mt-8 mb-5"><i class="fa fa-fw fa-money-check-alt mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_processor ?></span></div>

            <?php if(!$this->settings->paypal->is_enabled && !$this->settings->stripe->is_enabled && !$this->settings->paystack->is_enabled): ?>
                <div class="alert alert-info" role="alert">
                    <?= $this->language->pay->custom_package->no_processor ?>
                </div> 
            <?php endif ?>

            <div class="row d-flex align-items-stretch">

                <?php if($this->settings->paystack->is_enabled): ?>
                    <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">

                        <input type="radio" id="paystack_processor" name="payment_processor" value="paystack" class="custom-control-input" required="required">

                        <div class="card zoomer h-100">
                            <div class="card-body">

                                <div class="card-title text-center"><?= $this->language->pay->custom_package->paystack ?></div>

                                <div class="mt-3 text-center">
                                    <span class="custom-radio-box-main-icon">
                                        <svg height="58px" width="58px" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg"><g transform="scale(0.06)" clip-path="url(#clip0)" fill="currentColor"><path d="M548.416 0H31.792C14.306 0 0 14.333 0 31.852v57.333c0 17.519 14.306 31.852 31.792 31.852h515.034c17.486 0 31.792-14.333 31.792-31.852V31.852C580.208 14.333 565.901 0 548.416 0zM548.416 320.111H31.792C14.306 320.111 0 334.444 0 351.963v57.333c0 17.519 14.306 31.852 31.792 31.852h515.034c17.486 0 31.792-14.333 31.792-31.852v-57.333c1.59-17.519-12.717-31.852-30.202-31.852zM322.691 480.963H31.792C14.306 480.963 0 495.296 0 512.815v57.333C0 587.667 14.306 602 31.792 602h290.899c17.486 0 31.792-14.333 31.792-31.852v-57.333c0-17.519-14.306-31.852-31.792-31.852zM580.208 160.852H31.792C14.306 160.852 0 175.185 0 192.703v57.334c0 17.518 14.306 31.852 31.792 31.852h548.416c17.486 0 31.792-14.334 31.792-31.852v-57.334c0-17.518-14.306-31.851-31.792-31.851z"/></g><defs><clipPath id="clip0"><path fill="#fff" d="M0 0h612v602H0z"/></clipPath></defs></svg>
                                    </span>
                                </div>

                            </div>
                        </div>

                    </label>
                <?php endif ?>

                <?php if($this->settings->paypal->is_enabled): ?>
                    <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">

                        <input type="radio" id="paypal_processor" name="payment_processor" value="paypal" class="custom-control-input" required="required">

                        <div class="card zoomer h-100">
                            <div class="card-body">

                                <div class="card-title text-center"><?= $this->language->pay->custom_package->paypal ?></div>

                                <div class="mt-3 text-center">
                                    <span class="custom-radio-box-main-icon"><i class="fab fa-fw fa-paypal"></i></span>
                                </div>

                            </div>
                        </div>

                    </label>
                <?php endif ?>

                <?php if($this->settings->stripe->is_enabled): ?>
                    <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">

                        <input type="radio" id="stripe_processor" name="payment_processor" value="stripe" class="custom-control-input" required="required">

                        <div class="card zoomer h-100">
                            <div class="card-body">

                                <div class="card-title text-center"><?= $this->language->pay->custom_package->stripe ?></div>

                                <div class="mt-3 text-center">
                                    <span class="custom-radio-box-main-icon"><i class="fab fa-fw fa-stripe"></i></span>
                                </div>

                            </div>
                        </div>

                    </label>
                <?php endif ?>

                <?php if($this->settings->offline_payment->is_enabled): ?>
                    <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box">

                        <input type="radio" id="offline_payment_processor" name="payment_processor" value="offline_payment" class="custom-control-input" required="required">

                        <div class="card zoomer h-100">
                            <div class="card-body">

                                <div class="card-title text-center"><?= $this->language->pay->custom_package->offline_payment ?></div>

                                <div class="mt-3 text-center">
                                    <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-university"></i></span>
                                </div>

                            </div>
                        </div>

                    </label>
                <?php endif ?>
            </div>

            <div id="offline_payment_processor_wrapper" style="display: none;">
                <div class="form-group mt-4">
                    <label><?= $this->language->pay->custom_package->offline_payment_instructions ?></label>
                    <div class="card"><div class="card-body"><?= $this->settings->offline_payment->instructions ?></div></div>
                </div>

                <div class="form-group mt-4">
                    <label><?= $this->language->pay->custom_package->offline_payment_proof ?></label>
                    <input type="file" name="offline_payment_proof" accept=".png, .jpg, .jpeg" class="form-control form-control-lg" />
                    <div class="mt-2"><span class="text-muted"><?= $this->language->pay->custom_package->offline_payment_proof_help ?></span></div>
                </div>
            </div>


            <div class="mt-8 mb-5"><i class="fa fa-fw fa-dollar-sign mr-3"></i> <span class="h5 text-muted"><?= $this->language->pay->custom_package->payment_type ?></span></div>

            <div class="row d-flex align-items-stretch">

                <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box" id="one_time_type_label" <?= in_array($this->settings->payment->type, ['one_time', 'both']) ? null : 'style="display: none"' ?>>
                    <input type="radio" id="one_time_type" name="payment_type" value="one_time" class="custom-control-input" required="required">

                    <div class="card zoomer h-100">
                        <div class="card-body">

                            <div class="card-title text-center"><?= $this->language->pay->custom_package->one_time_type ?></div>

                            <div class="mt-3 text-center">
                                <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-hand-holding-usd"></i></span>
                            </div>

                        </div>
                    </div>
                </label>

                <label class="col-12 col-lg mb-3 mb-lg-0 custom-radio-box" id="recurring_type_label" <?= in_array($this->settings->payment->type, ['recurring', 'both']) ? null : 'style="display: none"' ?>>
                    <input type="radio" id="recurring_type" name="payment_type" value="recurring" class="custom-control-input" required="required">

                    <div class="card zoomer h-100">
                        <div class="card-body">

                            <div class="card-title text-center"><?= $this->language->pay->custom_package->recurring_type ?></div>

                            <div class="mt-3 text-center">
                                <span class="custom-radio-box-main-icon"><i class="fa fa-fw fa-sync-alt"></i></span>
                            </div>

                        </div>
                    </div>
                </label>

            </div>

            <div class="text-center mt-6">
                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= $this->language->pay->custom_package->pay ?></button>

                <div class="mt-3 text-muted text-center">
                    <small>
                        <?= sprintf(
                            $this->language->pay->accept,
                            '<a href="' . $this->settings->terms_and_conditions_url . '">' . $this->language->global->terms_and_conditions . '</a>',
                            '<a href="' . $this->settings->privacy_policy_url . '">' . $this->language->global->privacy_policy . '</a>'
                        ) ?>
                    </small>
                </div>
            </div>
        </form>


    <?php if($data->stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            let stripe = Stripe(<?= json_encode($this->settings->stripe->publishable_key) ?>);

            stripe.redirectToCheckout({
                sessionId: <?= json_encode($data->stripe_session->id) ?>,
            }).then((result) => {

                /* Nothing for the moment */

            });
        </script>
    <?php endif ?>

    <?php endif ?>

</div>

<?php ob_start() ?>
<script>
    let payment_type_one_time_enabled = <?= json_encode((bool) in_array($this->settings->payment->type, ['one_time', 'both'])) ?>;
    let payment_type_recurring_enabled = <?= json_encode((bool) in_array($this->settings->payment->type, ['recurring', 'both'])) ?>;

    /* Handlers */
    let check_payment_frequency = () => {
        let payment_frequency = $('[name="payment_frequency"]:checked').val();

        switch(payment_frequency) {
            case 'monthly':

                if(payment_type_one_time_enabled) {
                    $('#one_time_type_label').show();
                } else {
                    $('#one_time_type_label').hide();
                }

                if(payment_type_recurring_enabled) {
                    $('#recurring_type_label').show();
                } else {
                    $('#recurring_type_label').hide();
                }

                break;

            case 'annual':

                if(payment_type_one_time_enabled) {
                    $('#one_time_type_label').show();
                } else {
                    $('#one_time_type_label').hide();
                }

                if(payment_type_recurring_enabled) {
                    $('#recurring_type_label').show();
                } else {
                    $('#recurring_type_label').hide();
                }

                break;

            case 'lifetime':

                /* Show only the one time payment option for the lifetime plan */
                $('#recurring_type_label').hide();
                $('#one_time_type_label').show();

                break;
        }

        $('[name="payment_type"]').filter(':visible:first').click();
    };

    $('[name="payment_frequency"]').on('change', event => {

        check_payment_frequency();

        check_payment_processor();

    });

    /* Handle switch to offline payment */
    let check_payment_processor = () => {
        let payment_processor = $('[name="payment_processor"]:checked').val();

        switch(payment_processor) {
            case 'offline_payment':

                $('#offline_payment_processor_wrapper').show();

                /* Show only the one time payment option for the lifetime plan */
                $('#recurring_type_label').hide();
                $('#one_time_type_label').show();

                break;

            default:

                $('#offline_payment_processor_wrapper').hide();

                break;
        }

        $('[name="payment_type"]').filter(':visible:first').click();

    }

    $('[name="payment_processor"]').on('change', event => {

        check_payment_frequency();

        check_payment_processor();

    });

    /* Select default values */
    $('[name="payment_frequency"]:first').click();
    $('[name="payment_processor"]:first').click();
    $('[name="payment_type"]').filter(':visible:first').click();
    
    // Paystack apyments
    $("form").submit(function(e) {
        let processor = $('[name="payment_processor"]:checked').val();
        if (processor == 'paystack') {
            
            e.preventDefault(e);   

            let script = document.createElement('script');
            script.src = 'https://js.paystack.co/v1/inline.js';
            script.type = 'text/javascript';
            script.onload = script.onreadystatechange = function() {

                var rate = Number("<?=$this->settings->paystack->currency !== $this->settings->payment->currency ? $this->settings->payment->conversion_rate : 0?>");

                var package = $('input[name=package_id]').val();
                var code = $('input[name=code]').val();
                var token = $('input[name=token]').val();
                var type = $('input[name=payment_type]:checked').val(); 
                var frequency = $('input[name=payment_frequency]:checked').val();
                var amount = Number($('input[name="'+frequency+'_price"]').val() * rate).toFixed();
               
                /* Final price */ 
                if ($.inArray("<?=$this->settings->paystack->currency ?>", [ "NGN", "GHS", "GHC" ] ) > -1) {
                    var amount = amount*100;
                }

                var $form = $("<form/>", { action:'<?='pay/' . $data->package_id ?>', method:'post', role:'form'});
                $form.append($("<input>", { type:'hidden', name:'package_id', value:package }));
                $form.append($("<input>", { type:'hidden', name:'code', value:code }));
                $form.append($("<input>", { type:'hidden', name:'token', value:token }));
                $form.append($("<input>", { type:'hidden', name:'payment_type', value:type }));
                $form.append($("<input>", { type:'hidden', name:'payment_processor', value:processor }));
                $form.append($("<input>", { type:'hidden', name:'payment_frequency', value:frequency }));

                var handler = PaystackPop.setup({
                    key: "<?=$this->settings->paystack->publishable_key ?>", // Replace with your public key
                    email: "<?=$this->user->email?>",
                    currency: "<?=$this->settings->paystack->currency ?>",
                    amount: amount,
                    firstname: "<?=$this->user->name?>",
                    lastname: "",
                    // label: "Optional string that replaces customer email"
                    onClose: function(){
                        $('button[type=submit]').removeAttr('disabled'); 
                        $('button[type=submit] .spinner-grow').remove(); 
                    },
                    callback: function(response) {   

                        $form.append($("<input>", { type:'hidden', name:'reference', value:response.reference }));
                        $form.appendTo('body').submit();  
                        
                    }
                });
                  
                handler.openIframe(); 
            }

            let head  = document.getElementsByTagName('head')[0];
            head.appendChild(script);

            $(window).on('popstate', function() {
                handler.close();
            });
        }
    }); 

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>