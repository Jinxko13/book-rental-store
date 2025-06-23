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
            background: linear-gradient(135deg,#f8f8f8 0%,hsl(270, 100.00%, 92.50%) 100%); 
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
<div class="email-container" id="return-template">
    <div class="header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
        <h1>📤 Trả sách thành công</h1>
    </div>
    
    <div class="content">
        <p>Xin chào <strong>{{ $userName ?? 'Khách hàng' }}</strong>,</p>
        
        <div class="alert alert-success">
            <strong>✨ Bạn đã trả sách thành công!</strong><br>
            Cảm ơn bạn đã sử dụng dịch vụ thư viện.
        </div>
        
        @foreach($rentalDetails as $rentalDetail)
        <div class="book-info">
            <div class="book-title">{{ $rentalDetail->book->title }}</div>
            <div class="book-author">{{ $rentalDetail->book->author->name }}</div>
        </div>
        @endforeach
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Ngày trả</div>
                <div class="info-value">{{ $returnDate ?? '10/06/2025' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Thời gian thuê</div>
                <div class="info-value">{{ $rentalDuration ?? '9' }} ngày</div>
            </div>
        </div>
        
        <p>Bạn có muốn khám phá thêm những cuốn sách hay khác?</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $browseUrl ?? '#' }}" class="button">Duyệt sách mới</a>
            <!-- <a href="#" class="button button-secondary">Lịch sử thuê</a> -->
        </div>
        
        <div class="contact-info">
            <strong>💝 Đánh giá dịch vụ:</strong><br>
            Hãy để lại đánh giá để giúp chúng tôi cải thiện dịch vụ!
        </div>
    </div>
    
    <div class="footer">
        <p>Hẹn gặp lại bạn lần sau!</p>
        <p>&copy; {{ date('Y') }} Thư viện sách. All rights reserved.</p>
    </div>
</div>
</body>
</html>