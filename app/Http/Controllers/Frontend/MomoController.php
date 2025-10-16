<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use Illuminate\Http\Request;

class MomoController extends FrontendController
{
    protected $orderRepository;
    protected $orderService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderService $orderService,
    ) {
        parent::__construct();
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    public function momo_return(Request $request)
    {
        $momoConfig = momoConfig();
        $secretKey = $momoConfig['secretKey']; //Put your secret key in there
        $accessKey = $momoConfig['accessKey'];

        if (!empty($_GET)) {
            $rawData = "accessKey=" . $accessKey;
            $rawData .= "&amount=" . $_GET['amount'];
            $rawData .= "&extraData=" . $_GET['extraData'];
            $rawData .= "&message=" . $_GET['message'];
            $rawData .= "&orderId=" . $_GET['orderId'];
            $rawData .= "&orderInfo=" . $_GET['orderInfo'];
            $rawData .= "&orderType=" . $_GET['orderType'];
            $rawData .= "&partnerCode=" . $_GET['partnerCode'];
            $rawData .= "&payType=" . $_GET['payType'];
            $rawData .= "&requestId=" . $_GET['requestId'];
            $rawData .= "&responseTime=" . $_GET['responseTime'];
            $rawData .= "&resultCode=" . $_GET['resultCode'];
            $rawData .= "&transId=" . $_GET['transId'];

            $partnerSignature = hash_hmac("sha256", $rawData, $secretKey);
            $m2signature = $_GET['signature'];

            $order = $this->orderRepository->getOrderByCode($_GET['orderId'], ['products']);

            if($partnerSignature == $m2signature) {
                $payload['payment'] = 'paid';
                $payload['confirm'] = 'confirm';
                $flag = $this->orderService->updatePaymentOnline($payload, $order);
            }

            $momo = [
                'm2signature' => $m2signature,
                'partnerSignature' => $partnerSignature,
                'message' => $_GET['message'],
            ];

            // $order = $this->orderRepository->getOrderByCode($_GET['orderId'], ['products']);
            // SEO and System
            $system = $this->system;
            $seo = [
                'meta_title' => 'Thông tin thanh toán đơn hàng #' . $_GET['orderId'],
                'meta_keyword' => '',
                'meta_description' => '',
                'meta_images' => '',
                'canonical' => write_url('thanh-toan')
            ];

            $template = 'frontend.cart.component.momo';

            return view('frontend.cart.success', compact(
                'seo',
                'system',
                'order',
                'template',
                'momo',
            ));
        }
    }

    public function momo_ipn()
    {
        $momoConfig = momoConfig();
        $secretKey = $momoConfig['secretKey']; //Put your secret key in there
        $accessKey = $momoConfig['accessKey'];

        http_response_code(200); //200 - Everything will be 200 Oke
        if (!empty($_POST)) {
            $response = array();
            try {
                //Checksum
                $rawData = "accessKey=" . $accessKey;
                $rawData .= "&amount=" . $_POST['amount'];
                $rawData .= "&extraData=" . $_POST['extraData'];
                $rawData .= "&message=" . $_POST['message'];
                $rawData .= "&orderId=" . $_POST['orderId'];
                $rawData .= "&orderInfo=" . $_POST['orderInfo'];
                $rawData .= "&orderType=" . $_POST['orderType'];
                $rawData .= "&partnerCode=" . $_POST['partnerCode'];
                $rawData .= "&payType=" . $_POST['payType'];
                $rawData .= "&requestId=" . $_POST['requestId'];
                $rawData .= "&responseTime=" . $_POST['responseTime'];
                $rawData .= "&resultCode=" . $_POST['resultCode'];
                $rawData .= "&transId=" . $_POST['transId'];

                $partnerSignature = hash_hmac("sha256", $rawData, $secretKey);
                $m2signature = $_GET['signature'];
                $errorCode = $_POST["errorCode"];
                // dd($errorCode);
                $payload = [];
                $order = $this->orderRepository->getOrderByCode($orderCode, ['products']);

                if ($m2signature == $partnerSignature) {
                    $payload['payment'] = 'paid';
                    $payload['confirm'] = 'confirm';
                } else {
                    $result = '<div class="alert alert-danger">This transaction could be hacked, please check your signature and returned signature</div>';
                }
            } catch (Exception $e) {
                echo $response['message'] = $e;
            }

            $debugger = array();
            $debugger['rawData'] = $rawHash;
            $debugger['momoSignature'] = $m2signature;
            $debugger['partnerSignature'] = $partnerSignature;

            if ($m2signature == $partnerSignature) {
                $response['message'] = "Received payment result success";
            } else {
                $response['message'] = "ERROR! Fail checksum";
            }
            $response['debugger'] = $debugger;
            echo json_encode($response);
        }
    }
}
