<!DOCTYPE html>
<html>
<head>
    <title>Thông báo tồn kho thấp</title>
</head>
<body>
<h1>Thông báo: Sách tồn kho dưới mức tối thiểu</h1>

<p>Hệ thống phát hiện các sách sau có số lượng tồn kho dưới mức tối thiểu ({{ $minQuantity }}):</p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Tên sách</th>
        <th>Số lượng hiện tại</th>
        <th>Số lượng tối thiểu</th>
    </tr>
    </thead>
    <tbody>
    @foreach($books as $book)
        <tr>
            <td>{{ $book->id }}</td>
            <td>{{ $book->title }}</td>
            <td>{{ $book->quantity }}</td>
            <td>{{ $minQuantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>Vui lòng kiểm tra và nhập thêm hàng kịp thời.</p>
</body>
</html>
