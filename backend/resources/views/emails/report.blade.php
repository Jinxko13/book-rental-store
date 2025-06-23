<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo thống kê</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f8f9fa;
            padding: 20px;
        }
        h2 {
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<p>Chào bạn,</p>
<p>Dưới đây là thông tin báo cáo thống kê mới nhất:</p>

<table>
    <tr>
        <th>Tổng số thuê</th>
        <td>{{ $data->total_rentals }}</td>
    </tr>
    <tr>
        <th>Tổng số người dùng</th>
        <td>{{ $data->total_users }}</td>
    </tr>
    <tr>
        <th>Sách phổ biến</th>
        <td>{{ $data->top_books }}</td>
    </tr>
    <tr>
        <th>Doanh thu</th>
        <td>{{ number_format($data->revenue, 2) }} VNĐ</td>
    </tr>
</table>

<div class="footer">
    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
</div>
</body>
</html>
