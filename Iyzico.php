<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Iyzico
{

    protected $options;
    protected $request;
    function __construct()
    {
        $this->options = new \Iyzipay\Options();
        $this->options->setApiKey("sandbox-DPw7ljIc14ljxNHS1rxpEXXiy5cinjgo");
        $this->options->setSecretKey("sandbox-USMUPx5waPMLZFNeqyGK0j7CLLpzGHBe");
        $this->options->setBaseUrl("https://sandbox-api.iyzipay.com");


    }
    public function taksit($data){

        $request = new \Iyzipay\Request\RetrieveInstallmentInfoRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setBinNumber($data->taksit);                                                                   //kartın ilk 6 hanesı
        $request->setPrice($data->total);                                                                                //taksıtlendırılcek ücret
        $goster=$installmentInfo = \Iyzipay\Model\InstallmentInfo::retrieve($request,$this->options);
        return $goster->getRawResult();

    }
    public function odeme(){
        $data=new stdClass();
        $request = new \Iyzipay\Request\CreatePaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("5890040000000016");
        $request->setPrice("1");                                                                             //taksıtlendırılcek ücret
        $request->setPaidPrice("1.2");                                                                    //Tahsilat tutarının kırılım bazındaki dağılımı. Üye işyeri tarafından mutlaka saklanmalıdır.
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setInstallment(1);                                                                     //taksitbilgisi
        $request->setBasketId("B67832");                                                                   //işyerı tarafından gönderilen sepet id
        $request->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setCardHolderName("John Doe");                                                //kart sahibi ismi
        $paymentCard->setCardNumber("5528790000000008");                                                //kart no
        $paymentCard->setExpireMonth("12");                                                            //sona erme ayı
        $paymentCard->setExpireYear("2030");                                                            //sona erme yili
        $paymentCard->setCvc("123");                                                                         //cvc kodu
        $paymentCard->setRegisterCard(0);                                                         //kaydedilcekmi kaydedilmicekmi
        $request->setPaymentCard($paymentCard);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");                                                                      //alıcıya ait id
        $buyer->setName("John");                                                                   //alıcı ad
        $buyer->setSurname("Doe");                                                                 //alıcı soyad
        $buyer->setGsmNumber("+905350000000");                                                  //telno
        $buyer->setEmail("email@email.com");                                                        //mail
        $buyer->setIdentityNumber("74300864791");                                           //tc kimlik no
        $buyer->setLastLoginDate("2015-10-05 12:43:35");                                   //son giriş tarihi üyenin
        $buyer->setRegistrationDate("2013-04-21 15:12:09");                              //üye kayıt tarihi
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");     //adresi
        $buyer->setIp("85.34.78.112");                                                             //ip si
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");                                                         //fatura adres kodu
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");                // işyerındekı teslimat ismi
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems = array();

        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("0.3");
        $basketItems[0] = $firstBasketItem;

        $secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");
        $basketItems[1] = $secondBasketItem;

        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;
        $request->setBasketItems($basketItems);

        $data->payment = \Iyzipay\Model\Payment::create($request, $this->options);
        prex($data->payment);
    }

    public function calback(){
        if ($_POST['status']=='success'){
            $this->payThree($_POST);
        }



    }

    public function ThreeD($post_pay,$post,$posts){
        //prex($post_pay);
        //3D ÖDEME BASLATMAK
        $data=new stdClass();
        $request = new \Iyzipay\Request\CreatePaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($post_pay->order_id);
        $request->setPrice($post->total);
        $request->setPaidPrice($post_pay->instalment_price);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setInstallment($post_pay->instalment);
        $request->setBasketId($post_pay->order_id);
        $request->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl(site_url('iyzi'));

        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setCardHolderName("John Doe");
        $paymentCard->setCardNumber($posts->number2);
        $paymentCard->setExpireMonth($posts->mount);
        $paymentCard->setExpireYear($posts->year);
        $paymentCard->setCvc($post_pay->cvc);
        $paymentCard->setRegisterCard(0);
        $request->setPaymentCard($paymentCard);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId('1');
        $buyer->setName($post->shiping_name);
        $buyer->setSurname($post->bling_name);
        $buyer->setGsmNumber($post->bling_phone);
        $buyer->setEmail($post->bling_mail);
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($post->bling_adress);
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($post->shiping_address);
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($post->bling_name);
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($post->bling_adress);
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems = array();

        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($post_pay->order_id);
        $firstBasketItem->setName("bindirim");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($post->total);
        $basketItems[0] = $firstBasketItem;




        $request->setBasketItems($basketItems);

        $goster=$data->threedsInitialize = \Iyzipay\Model\ThreedsInitialize::create($request,$this->options);
        $degisken=$goster->getRawResult();
        return json_decode($degisken);

    }
    public function payThree($resultjson){
        //3D ödeme yapmak
        //prex($_POST);

        $data=new stdClass();
        $request = new \Iyzipay\Request\CreateThreedsPaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($resultjson['conversationId']);
        $request->setPaymentId('threeDSHtmlContent');
        $request->setConversationData($resultjson['threeDSHtmlContent']);

        $data->threedsPayment = \Iyzipay\Model\ThreedsPayment::create($request,$this->options);
        $query=(json_decode($data->threedsPayment->GetrawResult()));
        //prex($query->status);
        if ($query->status=='success'){
            echo "Ödeme başarılı";
        }
        else{
            echo "ödeme başarısız";
        }




    }
    public function cancelpay(){
        $request = new \Iyzipay\Request\CreateCancelRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPaymentId("1");
        $request->setIp("85.34.78.112");

        $cancel = \Iyzipay\Model\Cancel::create($request, $this->options);
        prex($cancel);
    }

    public function returnpay(){
        $request = new \Iyzipay\Request\CreateRefundRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPaymentTransactionId("1");
        $request->setPrice("0.5");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setIp("85.34.78.112");

        $refund = \Iyzipay\Model\Refund::create($request,$this->options);
        prex($refund);
    }
    public function querypay(){
        $request = new \Iyzipay\Request\RetrievePaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPaymentId("1");
        $request->setPaymentConversationId("123456789");

        $payment = \Iyzipay\Model\Payment::retrieve($request,$this->options);
        prex($payment);
    }
    public function cardsave(){
        $request = new \Iyzipay\Request\CreateCardRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setEmail("email@email.com");
        $request->setExternalId("external id");

        $cardInformation = new \Iyzipay\Model\CardInformation();
        $cardInformation->setCardAlias("card alias");
        $cardInformation->setCardHolderName("John Doe");
        $cardInformation->setCardNumber("5528790000000008");
        $cardInformation->setExpireMonth("12");
        $cardInformation->setExpireYear("2030");
        $request->setCard($cardInformation);

        $card = \Iyzipay\Model\Card::create($request, $this->options);
        prex($card);
    }
    public function cardpuanquery(){
        $request = new \Iyzipay\Request\RetrieveLoyaltyRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);

        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setCardHolderName("John Doe");
        $paymentCard->setCardNumber("5451030000000000");
        $paymentCard->setExpireMonth("12");
        $paymentCard->setExpireYear("2030");
        $paymentCard->setCvc("123");
        $request->setPaymentCard($paymentCard);

        $loyalty = \Iyzipay\Model\Loyalty::retrieve($request,$this->options);
        prex($loyalty);
    }
    public function startpayform(){
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice("1");
        $request->setPaidPrice("1.2");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName("John");
        $buyer->setSurname("Doe");
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail("email@email.com");
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");

        $request->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("0.3");
        $basketItems[0] = $firstBasketItem;

        $secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");

        $basketItems[1] = $secondBasketItem;
        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;
        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request,$this->options);

    }
    public function resultform(){
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setToken("token");

        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $this->options);
        prex($checkoutForm);
    }
    public function submember(){
        $request = new \Iyzipay\Request\CreateSubMerchantRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setSubMerchantExternalId("S49222");
        $request->setSubMerchantType(\Iyzipay\Model\SubMerchantType::PRIVATE_COMPANY);
        $request->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $request->setTaxOffice("Tax office");
        $request->setLegalCompanyTitle("John Doe inc");
        $request->setEmail("email@submerchantemail.com");
        $request->setGsmNumber("+905350000000");
        $request->setName("John's market");
        $request->setIban("TR180006200119000006672315");
        $request->setIdentityNumber("31300864726");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);

        $subMerchant = \Iyzipay\Model\SubMerchant::create($request,$this->options);
        prex($subMerchant);
    }
    public function Approval(){
        $request = new \Iyzipay\Request\CreateApprovalRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPaymentTransactionId("1");

        $approval = \Iyzipay\Model\Approval::create($request,$this->options);
        prex($approval);
    }
    public function dissaproval(){
        $request = new \Iyzipay\Request\CreateApprovalRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPaymentTransactionId("1");

        $disapproval = \Iyzipay\Model\Disapproval::create($request,$this->options);
        prex($disapproval);
    }
    public function safeeft(){
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice("1");
        $request->setPaidPrice("1.2");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName("John");
        $buyer->setSurname("Doe");
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail("email@email.com");
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");

        $request->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("0.3");
        $basketItems[0] = $firstBasketItem;

        $secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");

        $basketItems[1] = $secondBasketItem;
        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;
        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request,$this->options);
        prex($checkoutFormInitialize);
    }
    public function safequeryeft(){
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setToken("token");

        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $this->options);
        prex($checkoutForm);
    }
    public function iyzicopay(){
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice("1");
        $request->setPaidPrice("1.2");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName("John");
        $buyer->setSurname("Doe");
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail("email@email.com");
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems = array();

        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("0.3");
        $basketItems[0] = $firstBasketItem;

        $secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");
        $basketItems[1] = $secondBasketItem;

        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;
        $request->setBasketItems($basketItems);

        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request,$this->options);
        prex($payWithIyzicoInitialize);
    }
    public function iziycopayend(){
        $request = new \Iyzipay\Request\RetrievePayWithIyzicoRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setToken("token");

        $payWithIyzico = \Iyzipay\Model\PayWithIyzico::retrieve($request, $this->options);
        prex($payWithIyzico);
    }
    public function transferizico(){
        $request = new \Iyzipay\Request\CreateSettlementToBalanceRequest();
        $request->setSubMerchantKey("11654127");
        $request->setCallbackUrl("https://www.callback");
        $request->setPrice("0.1");

        $settlementToBalance = \Iyzipay\Model\SettlementToBalance::create($request,$this->options);
        prex($settlementToBalance);

    }
    public function returniziyco(){
        $request = new \Iyzipay\Request\CreateRefundToBalanceRequest();
        $request->setPaymentId("11654127");
        $request->setCallbackUrl("https://www.callback");

        $refundToBalance = \Iyzipay\Model\RefundToBalance::create($request,$this->options);
        prex($refundToBalance);
    }
    public function securtysavecard(){
        $request = new \Iyzipay\Request\UCSInitializeRequest();
        $request->setConversationId("1234566");
        $request->setLocale("tr");
        $request->setEmail("email@iyzico.com");
        $request->setGsmNumber("+905557378631");


        $result = \Iyzipay\Model\UCSInitialize::create($request,$this->options);
        prex($result);
    }
    public function secrtysavepaycard(){
        $request = new \Iyzipay\Request\CreatePaymentRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice("1");
        $request->setPaidPrice("1.2");
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId("B67832");
        $request->setPaymentChannel(\Iyzipay\Model\PaymentChannel::WEB);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

        $paymentCard = new \Iyzipay\Model\PaymentCard();
        $paymentCard->setUcsToken("sampleToken");
        $paymentCard->setConsumerToken("55287-9000-00000-08");
        $paymentCard->setCardToken("12332-3523-345-234-56456");
        $request->setPaymentCard($paymentCard);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName("John");
        $buyer->setSurname("Doe");
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail("email@email.com");
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $buyer->setIp("85.34.78.112");
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems = array();

        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Binocular");
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("0.3");
        $basketItems[0] = $firstBasketItem;

        $secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");
        $basketItems[1] = $secondBasketItem;

        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;
        $request->setBasketItems($basketItems);

        $payment = \Iyzipay\Model\Payment::create($request,$this->options);
        prex($payment);
    }
}