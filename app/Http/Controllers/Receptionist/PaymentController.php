<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function show($id)
    {
        $appointment = Appointment::with(['patient.patient', 'medicalRecord.prescriptionDetails.medicine', 'invoice'])
            ->where('id', $id)
            ->where('status', 4) // Chờ thanh toán
            ->firstOrFail();

        $invoice = $appointment->invoice;

        if (!$invoice || $invoice->payment_status == 'paid') {
            return redirect()->route('receptionist.appointments.index')->with('error', 'Hóa đơn này không tồn tại hoặc đã được thanh toán.');
        }

        return view('receptionist.appointments.payment', compact('appointment', 'invoice'));
    }

    public function processCash($id)
    {
        $appointment = Appointment::where('id', $id)->where('status', 4)->firstOrFail();
        $invoice = $appointment->invoice;

        if (!$invoice) {
            return back()->with('error', 'Không tìm thấy hóa đơn.');
        }

        $invoice->payment_status = 'paid';
        $invoice->payment_method = 'cash';
        $invoice->save();

        $appointment->status = 5; // Hoàn tất
        $appointment->save();

        return redirect()->route('receptionist.appointments.index')->with('success', 'Thanh toán tiền mặt thành công. Ca khám đã hoàn tất.');
    }

    public function createVnPayPayment(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)->where('status', 4)->firstOrFail();
        $invoice = $appointment->invoice;

        if (!$invoice) {
            return back()->with('error', 'Không tìm thấy hóa đơn.');
        }

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_Returnurl');

        $vnp_TxnRef = $invoice->invoice_number; 
        $vnp_OrderInfo = "ThanhToanHoaDon_" . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = strval((int)($invoice->total_amount * 100));
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; 
        $vnp_IpAddr = "13.160.92.202"; // Fake real IP for VNPAY validation

        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        if ($secureHash == $vnp_SecureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                $invoice = Invoice::where('invoice_number', $request->input('vnp_TxnRef'))->first();
                if ($invoice && $invoice->payment_status != 'paid') {
                    $invoice->payment_status = 'paid';
                    $invoice->payment_method = 'vnpay';
                    $invoice->transaction_id = $request->input('vnp_TransactionNo');
                    $invoice->save();

                    $appointment = Appointment::find($invoice->appointment_id);
                    if ($appointment) {
                        $appointment->status = 5;
                        $appointment->save();
                    }

                    return redirect()->route('receptionist.appointments.index')->with('success', 'Thanh toán VNPAY thành công cho hóa đơn ' . $invoice->invoice_number);
                }
                return redirect()->route('receptionist.appointments.index')->with('success', 'Hóa đơn này đã được thanh toán trước đó.');
            } else {
                return redirect()->route('receptionist.appointments.index')->with('error', 'Giao dịch VNPAY thất bại hoặc bị hủy.');
            }
        } else {
            return redirect()->route('receptionist.appointments.index')->with('error', 'Chữ ký VNPAY không hợp lệ. Giao dịch bị nghi ngờ gian lận!');
        }
    }

    public function printInvoice($id)
    {
        $appointment = Appointment::with(['patient.patient', 'medicalRecord.prescriptionDetails.medicine', 'invoice', 'doctor.user'])
            ->where('id', $id)
            ->where('status', 5) // Đã hoàn tất
            ->firstOrFail();

        $invoice = $appointment->invoice;

        if (!$invoice || $invoice->payment_status != 'paid') {
            return redirect()->route('receptionist.appointments.index')->with('error', 'Hóa đơn chưa được thanh toán.');
        }

        return view('receptionist.appointments.invoice-print', compact('appointment', 'invoice'));
    }
}
