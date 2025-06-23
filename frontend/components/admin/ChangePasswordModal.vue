<!-- pages/admin/profile.vue -->
<script setup>
reactive({
    fullName: 'Admin User',
    email: 'admin@bookstore.com',
    phone: '0123456789',
    address: '123 Đường Sách, Quận 1, TP. Hồ Chí Minh',
    gender: 'male',
    birthday: null,
  });
  
  const notifications = [
    {
      title: 'Đơn hàng mới',
      description: 'Đơn hàng #12345 đã được tạo',
      time: '1 giờ trước',
      color: '#1890ff',
    },
    {
      title: 'Sách quá hạn',
      description: 'Có 3 đơn hàng quá hạn cần xử lý',
      time: '3 giờ trước',
      color: '#ff4d4f',
    },
    {
      title: 'Cập nhật hệ thống',
      description: 'Hệ thống vừa được cập nhật lên phiên bản mới',
      time: '1 ngày trước',
      color: '#52c41a',
    },
  ];
  
  const activities = [
    {
      icon: LoginOutlined,
      title: 'Đăng nhập hệ thống',
      description: 'Đăng nhập từ IP 192.168.1.1',
      time: 'Hôm nay, 09:30',
      color: 'blue',
    },
    {
      icon: EditOutlined,
      title: 'Cập nhật sách',
      description: 'Cập nhật thông tin sách "Ngôn ngữ lập trình"',
      time: 'Hôm nay, 08:45',
      color: 'green',
    },
    {
      icon: CheckCircleOutlined,
      title: 'Duyệt đơn hàng',
      description: 'Duyệt đơn hàng #12344 của khách hàng Nguyễn Văn A',
      time: 'Hôm qua, 15:20',
      color: 'green',
    },
    {
      icon: BookOutlined,
      title: 'Thêm sách mới',
      description: 'Thêm sách "Lập trình hướng đối tượng" vào hệ thống',
      time: 'Hôm qua, 10:15',
      color: 'blue',
    },
  ];
  
  const handleSubmit = () => {
    console.log('Form submitted', formState);
    // Gọi API để cập nhật thông tin
    alert('Đã lưu thay đổi thành công!');
  };
  </script>