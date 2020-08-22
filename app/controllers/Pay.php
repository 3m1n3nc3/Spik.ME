<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Logger;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Package;
use Altum\Models\User;
use Altum\Response;
use Exception;
use PayPal\Api\Agreement;
use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Plan;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Common\PayPalModel;
use PayPal\Converter\FormatConverter;

class Pay extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect();
        } 

        $this->package_id = isset($this->params[0]) ? $this->params[0] : false;
        $this->return_type = isset($_GET['return_type']) && in_array($_GET['return_type'], ['success', 'cancel']) ? $_GET['return_type'] : null;
        $this->payment_processor = isset($_GET['payment_processor']) && in_array($_GET['payment_processor'], ['paypal', 'stripe', 'paystack', 'offline_payment']) ? $_GET['payment_processor'] : null;

        /* Make sure it is either the trial / free package or normal packages */
        switch($this->package_id) {

            case 'free':

                /* Get the current settings for the free package */
                $this->package = $this->settings->package_free;

                break;

            case 'trial':

                /* Get the current settings for the trial package */
                $this->package = $this->settings->package_trial;

                break;

            default:

                $this->package_id = (int) $this->package_id;

                /* Check if package exists */
                if(!$this->package = (new Package(['settings' => $this->settings]))->get_package_by_id($this->package_id)) {
                    redirect('package');
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$this->package->status) {
            redirect('package');
        }

        /* More checks depending on the user package and what it has been chosen */
        if($this->package_id == 'free') {
            if($this->user->package_id == 'free') {
                $_SESSION['info'][] = $this->language->pay->free->free_already;
            } else {
                $_SESSION['info'][] = $this->language->pay->free->other_package_not_expired;
            }

            redirect('package');
        }

        elseif($this->package_id == 'trial') {
            if($this->user->package_trial_done) {
                $_SESSION['info'][] = $this->language->pay->trial->trial_done;
                redirect('package');
            }
        }

        /* Form submission processing */
        /* Make sure that this only runs on user click submit post and not on callbacks / webhooks */
        if(!empty($_POST) && !$this->return_type) {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            switch($this->package_id) {

                case 'free':

                    redirect('pay/' . $this->package_id);

                    break;

                case 'trial':

                    if($this->user->package_trial_done) {
                        redirect('pay/' . $this->package_id);
                    }

                    break;

                default:

                    $_POST['payment_frequency'] = Database::clean_string($_POST['payment_frequency']);
                    $_POST['payment_processor'] = Database::clean_string($_POST['payment_processor']);
                    $_POST['payment_type'] = Database::clean_string($_POST['payment_type']);

                    /* Make sure the chosen option comply */
                    if(!in_array($_POST['payment_frequency'], ['monthly', 'annual', 'lifetime'])) {
                        redirect('pay/' . $this->package_id);
                    }

                    if(!in_array($_POST['payment_processor'], ['paypal', 'stripe', 'paystack', 'offline_payment'])) {
                        redirect('pay/' . $this->package_id);
                    } else {

                        /* Make sure the payment processor is active */
                        switch($_POST['payment_processor']) {
                            case 'paypal':

                                if(!$this->settings->paypal->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;

                            case 'stripe':

                                if(!$this->settings->stripe->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;

                            case 'paystack':

                                if(!$this->settings->paystack->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;

                            case 'offline_payment':

                                if(!$this->settings->offline_payment->is_enabled) {
                                    redirect('pay/' . $this->package_id);
                                }

                                break;
                        }

                    }

                    if(!in_array($_POST['payment_type'], ['one_time', 'recurring'])) {
                        redirect('pay/' . $this->package_id);
                    }

                    /* Lifetime */
                    if($_POST['payment_frequency'] == 'lifetime') {
                        $_POST['payment_type'] = 'one_time';
                    }

                    /* Offline payment */
                    if($_POST['payment_processor'] == 'offline_payment') {
                        $_POST['payment_type'] = 'one_time';
                    }

                    break;
            }

            if(empty($_SESSION['error'])) {

                switch($this->package_id) {

                    case 'trial':

                        /* Determine the expiration date of the package */
                        $package_expiration_date = (new \DateTime())->modify('+' . $this->package->days . ' days')->format('Y-m-d H:i:s');
                        $package_settings = json_encode($this->settings->package_trial->settings);

                        Database::$database->query("UPDATE `users` SET `package_id` = 'trial', `package_settings` = '{$package_settings}', `package_expiration_date` = '{$package_expiration_date}', `package_trial_done` = '1' WHERE `user_id` = {$this->user->user_id}");

                        /* Success message and redirect */
                        $this->redirect_pay_thank_you();

                        break;

                    default:

                        /* Check for code usage */
                        $this->code = false;

                        if($this->settings->payment->codes_is_enabled && isset($_POST['code'])) {

                            $_POST['code'] = Database::clean_string($_POST['code']);

                            $this->code = $this->database->query("SELECT `code_id`, `code`, `discount` FROM `codes` WHERE (`package_id` IS NULL OR `package_id` = '{$this->package_id}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

                            if($this->code && Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $this->code->code_id])) {
                                redirect('pay/' . $this->package_id);
                            }
                        }

                        switch($_POST['payment_processor']) {
                            case 'paypal':

                                $this->paypal_create();

                                break;

                            case 'stripe':

                                $stripe_session = $this->stripe_create();

                                break;

                            case 'paystack':

                                $stripe_session = $this->paystack_create();

                                break;

                            case 'offline_payment':

                                $stripe_session = $this->offline_payment_process();

                                break;
                        }

                        break;

                }

            }

        }

        /* Include the detection of paypal callbacks processing */
        $this->paypal_process();

        /* Include the detection of stripe callbacks processing */
        $this->stripe_process();

        /* Include the detection of paystack callbacks processing */
        $this->paystack_process();

        /* Prepare the View */
        $data = [
            'package_id'    => $this->package_id,
            'package'       => $this->package,
            'stripe_session' => $stripe_session ?? false
        ];

        $view = new \Altum\Views\View('pay/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function paypal_create() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Payment details */
        $product = $this->package->name;
        $price = (float) $this->package->{$_POST['payment_frequency'] . '_price'};
        $shipping = 0;
        $code = '';

        /* Check for code usage */
        if($this->code) {

            /* Calculate the new price */
            $price = $price - ($price * $this->code->discount / 100);

            $code = $this->code->code;

        }

        /* Make sure the price is right depending on the currency */
        $price = in_array($this->settings->payment->currency, ['JPY', 'TWD', 'HUF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '');

        /* Generate the generic return url parameters */
        $return_url_parameters = function($return_type) use($code, $price) {
            return '&return_type=' . $return_type . '&payment_processor=' . $_POST['payment_processor'] . '&payment_frequency=' . $_POST['payment_frequency'] . '&payment_type=' . $_POST['payment_type']  . '&code=' . $code . '&price=' . $price;
        };

        switch($_POST['payment_type']) {
            case 'one_time':

                /* Payment experience */
                $flowConfig = new FlowConfig();
                $flowConfig->setLandingPageType('Billing');
                $flowConfig->setUserAction('commit');
                $flowConfig->setReturnUriHttpMethod('GET');

                $presentation = new Presentation();
                $presentation->setBrandName($this->settings->payment->brand_name);

                $inputFields = new InputFields();
                $inputFields->setAllowNote(true)
                    ->setNoShipping(1)
                    ->setAddressOverride(0);

                $webProfile = new WebProfile();
                $webProfile->setName($this->settings->payment->brand_name . uniqid())
                    ->setFlowConfig($flowConfig)
                    ->setPresentation($presentation)
                    ->setInputFields($inputFields)
                    ->setTemporary(true);

                /* Create the experience profile */
                try {
                    $createdProfileResponse = $webProfile->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }

                }

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item = new Item();
                $item->setName($product)
                    ->setCurrency($this->settings->payment->currency)
                    ->setQuantity(1)
                    ->setPrice($price);

                $itemList = new ItemList();
                $itemList->setItems([$item]);

                $amount = new Amount();
                $amount->setCurrency($this->settings->payment->currency)
                    ->setTotal($price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setInvoiceNumber(uniqid());

                $redirectUrls = new RedirectUrls();
                $redirectUrls->setReturnUrl(url('pay/' . $this->package_id . $return_url_parameters('success')))
                    ->setCancelUrl(url('pay/' . $this->package_id . $return_url_parameters('cancel')));

                $payment = new Payment();
                $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirectUrls)
                    ->setTransactions([$transaction])
                    ->setExperienceProfileId($createdProfileResponse->getId());

                try {
                    $payment->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                $payment_url = $payment->getApprovalLink();

                header('Location: ' . $payment_url);

                break;

            case 'recurring':

                $plan = new Plan();
                $plan->setName($product)
                    ->setDescription($product)
                    ->setType('fixed');

                /* Set billing plan definitions */
                $payment_definition = new PaymentDefinition();
                $payment_definition->setName('Regular Payments')
                    ->setType('REGULAR')
                    ->setFrequency($_POST['payment_frequency'] == 'monthly' ? 'Month' : 'Year')
                    ->setFrequencyInterval('1')
                    ->setCycles($_POST['payment_frequency'] == 'monthly' ? '12' : '5')
                    ->setAmount(new Currency(['value' => $price, 'currency' => $this->settings->payment->currency]));


                /* Set merchant preferences */
                $merchant_preferences = new MerchantPreferences();
                $merchant_preferences->setReturnUrl(url('pay/' . $this->package_id . $return_url_parameters('success')))
                    ->setCancelUrl(url('pay/' . $this->package_id . $return_url_parameters('cancel')))
                    ->setAutoBillAmount('yes')
                    ->setInitialFailAmountAction('CONTINUE')
                    ->setMaxFailAttempts('0')
                    ->setSetupFee(new Currency(['value' => $price, 'currency' => $this->settings->payment->currency]));

                $plan->setPaymentDefinitions([$payment_definition]);
                $plan->setMerchantPreferences($merchant_preferences);

                /* Create the plan */
                try {
                    $plan = $plan->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '1';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Make sure to activate the plan */
                try {
                    $patch = new Patch();
                    $value = new PayPalModel('{"state":"ACTIVE"}');
                    $patch->setOp('replace')
                        ->setPath('/')
                        ->setValue($value);
                    $patchRequest = new PatchRequest();
                    $patchRequest->addPatch($patch);
                    $plan->update($patchRequest, $paypal);
                    $plan = Plan::get($plan->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '2';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Start creating the agreement */
                $agreement = new Agreement();
                $agreement->setName($product)
                    ->setDescription($this->user->user_id . '###' . $this->package_id . '###' . $_POST['payment_frequency'] . '###' . $code . '###' . time())
                    ->setStartDate((new \DateTime())->modify($_POST['payment_frequency'] == 'monthly' ? '+30 days' : '+1 year')->format(DATE_ISO8601));

                /* Set the plan id to the agreement */
                $agreement_plan = new Plan();
                $agreement_plan->setId($plan->getId());
                $agreement->setPlan($agreement_plan);

                /* Add Payer */
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $agreement->setPayer($payer);

                /* Create the agreement */
                try {
                    $agreement = $agreement->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '3';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                $payment_url = $agreement->getApprovalLink();

                header('Location: ' . $payment_url);

                break;
        }


    }

    private function paypal_process() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Return confirmation processing */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor == 'paypal' && isset($_GET['payment_frequency'], $_GET['code'])) {
            $payment_frequency = $_GET['payment_frequency'];
            $code = $_GET['code'];

            /* Return confirmation processing one time payment */
            if(isset($_GET['paymentId'], $_GET['PayerID'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);


                $payment_id = $_GET['paymentId'];
                $payer_id = $_GET['PayerID'];
                $payment_type = 'one_time';

                $subscription_id = '';
                $payment_subscription_id =  '';

                try {
                    $payment = Payment::get($payment_id, $paypal);

                    $payer_info = $payment->getPayer()->getPayerInfo();
                    $payer_email = $payer_info->getEmail();
                    $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();

                    $payment_total = $payment->getTransactions()[0]->getAmount()->getTotal();
                    $payment_currency = $payment->getTransactions()[0]->getAmount()->getCurrency();

                    /* Execute the payment */
                    $execute = new PaymentExecution();
                    $execute->setPayerId($payer_id);

                    $result = $payment->execute($execute, $paypal);

                    /* Get status after execution */
                    $payment_status = $payment->getState();

                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Make sure the transaction is not already existing */
                if(Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'paypal'])) {
                    redirect('pay/' . $this->package_id);
                }

                /* Make sure the payment is approved */
                if($payment_status != 'approved') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->package_id);
                }

                /* Make sure the chosen option comply */
                if(!in_array($payment_frequency, ['monthly', 'annual', 'lifetime'])) {
                    redirect('pay/' . $this->package_id);
                }

                // COMMENTED BECAUSE PRICES OF A PLAN MIGHT CHANGE BUT YOU STILL HAVE TO ACCEPT PAYMENTS FROM OLDER PRICES
                /* Make sure the paid amount equals to the current price of the plan */
//                if ($this->package->{$payment_frequency . '_price'} != $payment_total) {
//                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
//                    redirect('pay/' . $this->package_id);
//                }

                /* Unsubscribe from the previous plan if needed */
                if(!empty($this->user->payment_subscription_id) && $this->user->payment_subscription_id != $payment_subscription_id) {
                    try {
                        (new User(['settings' => $this->settings, 'user' => $this->user]))->cancel_subscription();
                    } catch (\Exception $exception) {

                        /* Output errors properly */
                        if (DEBUG) {
                            echo $exception->getCode() . '-' . $exception->getMessage();

                            die();
                        } else {
                            $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                            redirect('pay/' . $this->package_id);
                        }

                    }
                }

                /* Make sure the code exists */
                $codes_code = Database::get('*', 'codes', ['code' => $code, 'type' => 'discount']);

                if($codes_code) {
                    $code = $codes_code->code;

                    /* Check if we should insert the usage of the code or not */
                    if(!Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $codes_code->code_id])) {
                        /* Update the code usage */
                        $this->database->query("UPDATE `codes` SET `redeemed` = `redeemed` + 1 WHERE `code_id` = {$codes_code->code_id}");

                        /* Add log for the redeemed code */
                        Database::insert('redeemed_codes', [
                            'code_id'   => $codes_code->code_id,
                            'user_id'   => $this->user->user_id,
                            'date'      => \Altum\Date::$date
                        ]);

                        Logger::users($this->user->user_id, 'codes.redeemed_code=' . $codes_code->code);
                    }
                }

                /* Add a log into the database */
                Database::insert(
                    'payments',
                    [
                        'user_id' => $this->user->user_id,
                        'package_id' => $this->package_id,
                        'processor' => 'paypal',
                        'type' => $payment_type,
                        'frequency' => $payment_frequency,
                        'code' => $code,
                        'email' => $payer_email,
                        'payment_id' => $payment_id,
                        'subscription_id' => $subscription_id,
                        'payer_id' => $payer_id,
                        'name' => $payer_name,
                        'amount' => $payment_total,
                        'currency' => $payment_currency,
                        'date' => \Altum\Date::$date
                    ]
                );

                /* Update the user with the new package */
                switch ($payment_frequency) {
                    case 'monthly':
                        $package_expiration_date = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
                        break;

                    case 'annual':
                        $package_expiration_date = (new \DateTime())->modify('+12 months')->format('Y-m-d H:i:s');
                        break;

                    case 'lifetime':
                        $package_expiration_date = (new \DateTime())->modify('+100 years')->format('Y-m-d H:i:s');
                        break;
                }

                Database::update(
                    'users',
                    [
                        'package_id' => $this->package_id,
                        'package_settings' => json_encode($this->package->settings),
                        'package_expiration_date' => $package_expiration_date
                    ],
                    [
                        'user_id' => $this->user->user_id
                    ]
                );

                /* Send notification to the user */
                /* Prepare the email */
                $email_template = get_email_template(
                    [],
                    $this->language->global->emails->user_payment->subject,
                    [
                        '{{PACKAGE_EXPIRATION_DATE}}' => \Altum\Date::get($package_expiration_date, 2),
                        '{{USER_PACKAGE_LINK}}' => url('account-package'),
                        '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                    ],
                    $this->language->global->emails->user_payment->body
                );

                send_mail(
                    $this->settings,
                    $this->user->email,
                    $email_template->subject,
                    $email_template->body
                );

                /* Send notification to admin if needed */
                if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                    send_mail(
                        $this->settings,
                        explode(',', $this->settings->email_notifications->emails),
                        sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'paypal', $payment_total, $payment_currency),
                        sprintf($this->language->global->emails->admin_new_payment_notification->body, $payment_total, $payment_currency)
                    );

                }

                /* Success message and redirect */
                $this->redirect_pay_thank_you();

            }

            /* Return confirmation processing recurring payment */
            if (isset($_GET['token'], $_GET['payment_type'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                $token = $_GET['token'];
                $agreement = new \PayPal\Api\Agreement();
                $payment_type = 'recurring';

                try {
                    $agreement->execute($token, $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Get details about the executed agreement */
                try {
                    $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->package_id);

                    }
                }

                /* Get the needed details from the agreement */
                $agreement_status = $agreement->getState();

                /* Make sure the payment is approved */
                if($agreement_status != 'Active' && $agreement_status != 'Pending') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->package_id);
                }

                /* Success message and redirect */
                $this->redirect_pay_thank_you();

            }

        }


        /* Return confirmation processing if failed */
        if($this->return_type && $this->return_type == 'paypal-cancel') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->package_id);
        }

    }

    private function stripe_create() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

        /* Payment details */
        $product = $this->package->name;
        $price = $this->package->{$_POST['payment_frequency'] . '_price'};
        $shipping = 0;
        $code = '';

        /* Check for code usage */
        if($this->code) {

            /* Calculate the new price */
            $price = $price - ($price * $this->code->discount / 100);

            $code = $this->code->code;

        }
 
        /* Final price */
        $stripe_formatted_price = in_array($this->settings->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 2, '.', '') : number_format($price, 2, '.', '') * 100;

        /* Generate the generic return url parameters */
        $return_url_parameters = function($return_type) use($code, $price) {
            return '&return_type=' . $return_type . '&payment_processor=' . $_POST['payment_processor'] . '&payment_frequency=' . $_POST['payment_frequency'] . '&payment_type=' . $_POST['payment_type']  . '&code=' . $code . '&price=' . $price;
        };

        switch($_POST['payment_type']) {
            case 'one_time':

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'name' => $product,
                        'description' => $_POST['payment_frequency'],
                        'amount' => $stripe_formatted_price,
                        'currency' => $this->settings->payment->currency,
                        'quantity' => 1,
                    ]],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'package_id' => $this->package_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'code' => $code
                    ],
                    'success_url' => url('pay/' . $this->package_id . $return_url_parameters('success')),
                    'cancel_url' => url('pay/' . $this->package_id . $return_url_parameters('cancel')),
                ]);

                break;

            case 'recurring':

                /* Try to get the product related to the package */
                try {
                    $stripe_product = \Stripe\Product::retrieve($this->package_id);
                } catch (\Exception $exception) {
                    /* The product probably does not exist */
                }

                if(!isset($stripe_product)) {
                    /* Create the product if not already created */
                    $stripe_product = \Stripe\Product::create([
                        'id'    => $this->package_id,
                        'name'  => $product,
                        'type'  => 'service',
                    ]);
                }

                /* Generate the plan id with the proper parameters */
                $stripe_plan_id = $this->package_id . '_' . $_POST['payment_frequency'] . '_' . $stripe_formatted_price . '_' . $this->settings->payment->currency;

                /* Check if we already have a payment plan created and try to get it */
                try {
                    $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
                } catch (\Exception $exception) {
                    /* The plan probably does not exist */
                }

                /* Create the plan if it doesnt exist already */
                if(!isset($stripe_plan)) {
                    try {
                        $stripe_plan = \Stripe\Plan::create([
                            'amount' => $stripe_formatted_price,
                            'interval' => $_POST['payment_frequency'] == 'monthly' ? 'month' : 'year',
                            'product' => $stripe_product->id,
                            'currency' => $this->settings->payment->currency,
                            'id' => $stripe_plan_id,
                        ]);
                    } catch (\Exception $exception) {
                        $_SESSION['error'][] = $exception->getMessage();
                        redirect('pay/' . $this->package_id);
                    }
                }

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'subscription_data' => [
                        'items' => [
                            ['plan' => $stripe_plan->id]
                        ],
                        'metadata' => [
                            'user_id' => $this->user->user_id,
                            'package_id' => $this->package_id,
                            'payment_frequency' => $_POST['payment_frequency'],
                            'code' => $code
                        ],
                    ],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'package_id' => $this->package_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'code' => $code
                    ],
                    'success_url' => url('pay/' . $this->package_id . $return_url_parameters('success')),
                    'cancel_url' => url('pay/' . $this->package_id . $return_url_parameters('cancel')),
                ]);

                break;
        }

        return $stripe_session;

    }

    private function paystack_create() { 
        $code = '';
        $rate = 0;

        /* Check for code usage */
        if($this->code) {

            /* Calculate the new price */
            $price = $price - ($price * $this->code->discount / 100);

            $code = $this->code->code;

        }

        if ($this->settings->paystack->currency !== $this->settings->payment->currency) {
            $rate = $this->settings->payment->conversion_rate;
        }

        $price = $this->package->{$_POST['payment_frequency'] . '_price'} * $rate;
        $plan  = $this->package->settings->{$_POST['payment_frequency'] . '_plan_id'};

        $paystack = new \Yabacon\Paystack($this->settings->paystack->secret_key);
        if (isset($_POST['reference'])) {

            /* Generate the generic return url parameters */
            $return_url_parameters = function($return_type) use($code, $price) {
                return '&return_type=' . $return_type . '&payment_processor=' . $_POST['payment_processor'] . '&payment_frequency=' . $_POST['payment_frequency'] . '&payment_type=' . $_POST['payment_type']  . '&code=' . $code . '&price=' . $price . '&ref=' . $_POST['reference'];
            };

            $verify_pay = false;

            // Try to verify the payment
            try
            {
                $verify_pay = $paystack->transaction->verify([
                    'reference'=>$_POST['reference'], // unique to transactions
                ]);
            } catch(\Yabacon\Paystack\Exception\ApiException $e) { 
                $_SESSION['error'][] = $e->getMessage();
                redirect('pay/' . $this->package_id); 
            }

            if ($verify_pay && $verify_pay->data->status === 'success') 
            {
                $currency =  $verify_pay->data->currency;

                try
                {
                    // Create a subscription 
                    if ($_POST['payment_type'] == 'recurring' && $verify_pay->data->channel === 'card') {
                        $paystack->subscription->create([
                            'customer' => $verify_pay->data->customer->customer_code, 
                            'plan'=> $plan,
                            'authorization' => $verify_pay->data->authorization->authorization_code,  
                        ]);
                    }
                } catch(\Yabacon\Paystack\Exception\ApiException $e) { 
                    // silent
                }
                redirect('pay/' . $this->package_id . $return_url_parameters('success') . '&currency=' . $currency); 
            } elseif ($verify_pay) {
                $_SESSION['error'][] = '<b>'.$verify_pay->data->message. ':</b> '.$verify_pay->message; 
                redirect('pay/' . $this->package_id);
            }
        }
    }

    private function stripe_process() {

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor == 'stripe') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel' && $this->payment_processor == 'stripe') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->package_id);
        }

    }

    private function paystack_process() { 

        $payment_frequency = isset($_GET['payment_frequency']) ? $_GET['payment_frequency'] : 'monthly';   
        $payment_currency = isset($_GET['currency']) ? $_GET['currency'] : $this->settings->paystack->currency;   
        $payment_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : 'one_time';
        $ref = isset($_GET['ref']) ? $_GET['ref'] : '';
        $code = isset($_GET['code']) ? $_GET['code'] : '';

        $rate =  0;

        if ($this->settings->paystack->currency !== $this->settings->payment->currency) {
            $rate = $this->settings->payment->conversion_rate;
        }

        /* Payment details */
        $price = isset($_GET['price']) ? $_GET['price'] : $this->package->{$payment_frequency . '_price'} * $rate; 

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor == 'paystack') { 

            /* Make sure the chosen option comply */
            if(!in_array($payment_frequency, ['monthly', 'annual', 'lifetime'])) {
                redirect('pay/' . $this->package_id);
            }
 
            /* Add a log into the database */
            Database::insert(
                'payments',
                [
                    'user_id' => $this->user->user_id,
                    'package_id' => $this->package_id,
                    'processor' => 'paystack',
                    'type' => $payment_type,
                    'frequency' => $payment_frequency,
                    'code' => $code,
                    'email' => $this->user->email,
                    'payment_id' => $ref,
                    'subscription_id' => '',
                    'payer_id' => $this->user->user_id,
                    'name' => $this->user->name,
                    'amount' => $price,
                    'currency' => $payment_currency,
                    'date' => \Altum\Date::$date
                ]
            );

            /* Update the user with the new package */
            switch ($payment_frequency) {
                case 'monthly':
                    $package_expiration_date = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
                    break;

                case 'annual':
                    $package_expiration_date = (new \DateTime())->modify('+12 months')->format('Y-m-d H:i:s');
                    break;

                case 'lifetime':
                    $package_expiration_date = (new \DateTime())->modify('+100 years')->format('Y-m-d H:i:s');
                    break;
            }

            Database::update(
                'users',
                [
                    'package_id' => $this->package_id,
                    'package_settings' => json_encode($this->package->settings),
                    'package_expiration_date' => $package_expiration_date
                ],
                [
                    'user_id' => $this->user->user_id
                ]
            );

            /* Send notification to the user */
            /* Prepare the email */
            $email_template = get_email_template(
                [],
                $this->language->global->emails->user_payment->subject,
                [
                    '{{PACKAGE_EXPIRATION_DATE}}' => \Altum\Date::get($package_expiration_date, 2),
                    '{{USER_PACKAGE_LINK}}' => url('account-package'),
                    '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                ],
                $this->language->global->emails->user_payment->body
            );

            send_mail(
                $this->settings,
                $this->user->email,
                $email_template->subject,
                $email_template->body
            );

            /* Send notification to admin if needed */
            if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                send_mail(
                    $this->settings,
                    explode(',', $this->settings->email_notifications->emails),
                    sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'paypal', $payment_total, $payment_currency),
                    sprintf($this->language->global->emails->admin_new_payment_notification->body, $payment_total, $payment_currency)
                );

            }

            /* Success message and redirect */
            $this->redirect_pay_thank_you(); 
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel' && $this->payment_processor == 'paystack') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->package_id);
        }
    }


    private function offline_payment_process() {

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor == 'offline_payment') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Payment details */
        $price = $this->package->{$_POST['payment_frequency'] . '_price'};
        $code = '';

        /* Check for code usage */
        if($this->code) {

            /* Calculate the new price */
            $price = $price - ($price * $this->code->discount / 100);

            $code = $this->code->code;

        }

        /* Other vars */
        $payment_id = md5($this->user->user_id . $this->package_id . $_POST['payment_type'] . $_POST['payment_frequency'] . $this->user->email . Date::$date);
        $file_allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $offline_payment_proof = (!empty($_FILES['offline_payment_proof']['name']));

        /* Generate the generic return url parameters */
        $return_url_parameters = function($return_type) use($code, $price) {
            return '&return_type=' . $return_type . '&payment_processor=' . $_POST['payment_processor'] . '&payment_frequency=' . $_POST['payment_frequency'] . '&payment_type=' . $_POST['payment_type']  . '&code=' . $code . '&price=' . $price;
        };

        /* Error checks */
        if(!$offline_payment_proof) {
            $_SESSION['error'][] = $this->language->pay->error_message->offline_payment_proof_missing;
            redirect('pay/' . $this->package_id);
        }

        $offline_payment_proof_file_name = $_FILES['offline_payment_proof']['name'];
        $offline_payment_proof_file_extension = explode('.', $offline_payment_proof_file_name);
        $offline_payment_proof_file_extension = strtolower(end($offline_payment_proof_file_extension));
        $offline_payment_proof_file_temp = $_FILES['offline_payment_proof']['tmp_name'];

        if(!in_array($offline_payment_proof_file_extension, $file_allowed_extensions)) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_file_type;
            redirect('pay/' . $this->package_id);
        }

        if(!is_writable(UPLOADS_PATH . 'offline_payment_proofs/')) {
            $_SESSION['error'][] = sprintf($this->language->global->error_message->directory_not_writable, UPLOADS_PATH . 'offline_payment_proofs/');
            redirect('pay/' . $this->package_id);
        }

        /* Generate new name for offline_payment_proof */
        $offline_payment_proof_new_name = $payment_id . '.' . $offline_payment_proof_file_extension;

        /* Upload the original */
        move_uploaded_file($offline_payment_proof_file_temp, UPLOADS_PATH . 'offline_payment_proofs/' . $offline_payment_proof_new_name);

        /* Add a log into the database */
        Database::insert(
            'payments',
            [
                'user_id' => $this->user->user_id,
                'package_id' => $this->package_id,
                'processor' => 'offline_payment',
                'type' => $_POST['payment_type'],
                'frequency' => $_POST['payment_frequency'],
                'code' => $code,
                'email' => $this->user->email,
                'payment_id' => $payment_id,
                'subscription_id' => '',
                'payer_id' => $this->user->user_id,
                'name' => $this->user->name,
                'amount' => $price,
                'currency' => $this->settings->payment->currency,
                'payment_proof' => $offline_payment_proof_new_name,
                'status' => 0,
                'date' => Date::$date
            ]
        );

        /* Send notification to admin if needed */
        if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

            send_mail(
                $this->settings,
                explode(',', $this->settings->email_notifications->emails),
                sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'offline_payment', $price, $this->settings->payment->currency),
                sprintf($this->language->global->emails->admin_new_payment_notification->body, $price, $this->settings->payment->currency)
            );

        }

        redirect('pay/' . $this->package_id . $return_url_parameters('success'));

    }


    /* Ajax to check if discount codes are available */
    public function code() {
        Authentication::guard();

        if(!Csrf::check('global_token')) {
            die();
        }

        if(!$this->settings->payment->is_enabled || !$this->settings->payment->codes_is_enabled) {
            die();
        }

        if(empty($_POST)) {
            die();
        }

        $_POST['package_id'] = !$_POST['package_id'] ? null : (int) $_POST['package_id'];
        $_POST['code'] = Database::clean_string($_POST['code']);


        /* Make sure the discount code exists */
        $code = $this->database->query("SELECT * FROM `codes` WHERE (`package_id` IS NULL OR `package_id` = '{$_POST['package_id']}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

        if(!$code) {
            Response::json($this->language->pay->error_message->code_invalid, 'error');
        }

        if(Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $code->code_id])) {
            Response::json($this->language->pay->error_message->code_used, 'error');
        }


        Response::json(sprintf($this->language->pay->success_message->code, '<strong>' . $code->discount . '%</strong>'), 'success', ['discount' => $code->discount]);
    }

    /* Simple url generator to return the thank you page */
    private function redirect_pay_thank_you() {
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return $key != 'altum';
        }, ARRAY_FILTER_USE_KEY);

        $thank_you_url_parameters = '&package_id=' . $this->package_id;
        $thank_you_url_parameters .= '&user_id=' . $this->user->user_id;

        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $thank_you_url_parameters .= '&unique_transaction_identifier=' . md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        redirect('pay-thank-you?' . $thank_you_url_parameters);
    }

}
