<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông báo thuê sách</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .email-container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: 600; 
        }
        .content { 
            padding: 30px; 
        }
        .book-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .book-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .book-author {
            color: #6c757d;
            font-style: italic;
        }
        .book-amount {
            color: #6c757d;
            font-style: italic;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            text-align: center;
        }
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff !important;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            text-align: center;
            margin: 10px 5px;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .button-secondary {
            background: #6c757d;
        }
        .fine-amount {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            background: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-radius: 6px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="email-container" id="overdue-template">
    <div class="header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
        <h1>⚠️ Sách quá hạn</h1>
    </div>
    
    <div class="content">
        <p>Xin chào <strong>{{ $userName ?? 'Khách hàng' }}</strong>,</p>
        
        <div class="alert alert-danger">
            <strong>🚨 Sách của bạn đã quá hạn {{ $daysOverdue ?? '3' }} ngày!</strong><br>
            Vui lòng liên hệ thư viện để trả sách ngay.
        </div>
        
        @foreach($rentalDetails as $rentalDetail)
        <div class="book-info">
            <div class="book-title">{{ $rentalDetail->book->title }}</div>
            <div class="book-author">{{ $rentalDetail->book->author->name ?? 'Tác giả không xác định' }}</div>
            <div class="book-amount">{{ $rentalDetail->quantity }} cuốn</div>
        </div>
        @endforeach
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Hạn trả</div>
                <div class="info-value">{{ $dueDate ?? '28/05/2025' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Quá hạn</div>
                <div class="info-value" style="color: #e74c3c;">{{ $daysOverdue ?? '3' }} ngày</div>
            </div>
        </div>
        
        @if(($lateFee ?? 15000) > 0)
        <div class="fine-amount">
            💰 Phí phạt hiện tại: {{ number_format($lateFee) }}đ
        </div>
        @endif
        
        <p><strong>Hậu quả nếu không trả sách:</strong></p>
        <ul>
            <li>Phí phạt sẽ tăng theo từng ngày</li>
            <li>Tài khoản có thể bị tạm khóa</li>
            <li>Không thể thuê sách mới</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $renewUrl ?? '#' }}" class="button">Gia hạn thuê</a>
        </div>
        
        <div class="contact-info" style="background: #ffebee;">
            <strong>🏢 Địa chỉ thư viện:</strong><br>{{ $contactInfo ?? 'Số 123, Đường ABC, Quận XYZ, Hà Nội' }} <br>
            <strong> Email: </strong> {{ $email ?? 'library@example.com' }} 
            <strong>Điện thoại: </strong> {{ $phone ?? '024.1234.5678' }}
        </div>
    </div>
    
    <div class="footer">
        <p>Email tự động từ hệ thống thư viện</p>
        <p>&copy; {{ date('Y') }} Thư viện sách. All rights reserved.</p>
    </div>
</div>
</body>
</html>