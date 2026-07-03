<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Hóa Đơn - {{ $invoice->invoice_number }}</title>
    <!-- Use standard Bootstrap for simple print styles if needed, or pure CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        @media print {
            .invoice-box {
                box-shadow: none;
                border: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table .text-right {
            text-align: right;
        }
        .total-row td {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-style: italic;
            color: #7f8c8d;
        }
        .btn-print {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #27ae60;
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .badge-paid {
            display: inline-block;
            padding: 5px 10px;
            background: #27ae60;
            color: #fff;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print no-print">🖨️ In Hóa Đơn</button>

    <div class="invoice-box">
        <div class="header">
            <h1>PHÒNG KHÁM ĐA KHOA ANTIGRAVITY</h1>
            <p>123 Đường Công Nghệ, Quận AI, TP. Tương Lai</p>
            <p>Điện thoại: 1900 9999 - Email: contact@clinic.com</p>
        </div>

        <h2 style="text-align: center; color: #e74c3c;">BIÊN LAI THU TIỀN VIỆN PHÍ</h2>
        <div style="text-align: center; margin-bottom: 20px;">
            <span class="badge-paid">ĐÃ THANH TOÁN ({{ strtoupper($invoice->payment_method) }})</span>
        </div>

        <div class="info-row">
            <div>
                <p><strong>Họ tên bệnh nhân:</strong> {{ $appointment->patient->name ?? 'N/A' }}</p>
                <p><strong>Số điện thoại:</strong> {{ $appointment->patient->phone ?? 'N/A' }}</p>
                <p><strong>Bác sĩ khám:</strong> {{ $appointment->doctor->title ?? '' }} {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
            </div>
            <div style="text-align: right;">
                <p><strong>Mã Hóa Đơn:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Ngày lập:</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y H:i') }}</p>
                @if($invoice->transaction_id)
                <p><strong>Mã GD:</strong> {{ $invoice->transaction_id }}</p>
                @endif
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Nội dung / Tên thuốc</th>
                    <th>SL</th>
                    <th>Đ.Vị</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <!-- Phí khám -->
                <tr>
                    <td>1</td>
                    <td>Phí khám bệnh (Khám {{ $appointment->doctor->specialty->name ?? 'Đa khoa' }})</td>
                    <td>1</td>
                    <td>Lần</td>
                    <td class="text-right">{{ number_format($invoice->consultation_fee, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($invoice->consultation_fee, 0, ',', '.') }}</td>
                </tr>

                <!-- Tiền thuốc -->
                @php $stt = 2; @endphp
                @if($appointment->medicalRecord && $appointment->medicalRecord->prescriptionDetails->count() > 0)
                    @foreach($appointment->medicalRecord->prescriptionDetails as $pd)
                        <tr>
                            <td>{{ $stt++ }}</td>
                            <td>{{ $pd->medicine->name }}</td>
                            <td>{{ $pd->quantity }}</td>
                            <td>{{ $pd->medicine->unit }}</td>
                            <td class="text-right">{{ number_format($pd->price_at_sale, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($pd->quantity * $pd->price_at_sale, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Tổng tiền -->
                <tr class="total-row">
                    <td colspan="5" class="text-right" style="color: #e74c3c;">TỔNG CỘNG:</td>
                    <td class="text-right" style="color: #e74c3c;">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</td>
                </tr>
            </tbody>
        </table>

        <div class="info-row" style="margin-top: 40px;">
            <div style="text-align: center; width: 50%;">
                <p><strong>Người nộp tiền</strong></p>
                <p style="font-style: italic; color: #7f8c8d; font-size: 12px;">(Ký, ghi rõ họ tên)</p>
                <br><br><br>
                <p>{{ $appointment->patient->name ?? '' }}</p>
            </div>
            <div style="text-align: center; width: 50%;">
                <p><strong>Người thu tiền</strong></p>
                <p style="font-style: italic; color: #7f8c8d; font-size: 12px;">(Ký, ghi rõ họ tên)</p>
                <br><br><br>
                <p>{{ auth()->user()->name ?? 'Lễ tân' }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của Phòng khám!</p>
            <p style="font-size: 11px;">Biên lai này có giá trị thay thế cho Hóa đơn bán lẻ thông thường.</p>
        </div>
    </div>
    
    <script>
        // Tự động mở hộp thoại in khi tải xong trang
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
