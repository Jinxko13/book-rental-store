export const formatPrice = (price: number): string => {
  if (!price) return '0 ₫';
  price = Number(price)
  // Format price with thousands separator and VND currency
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price);
};

export const formatDate = (dateString) => {
  if (!dateString) return 'Không có dữ liệu';
  const parsedDate = new Date(dateString);
  if (isNaN(parsedDate.getTime())) {
    return 'Ngày không hợp lệ';
  }
  return parsedDate.toLocaleDateString('vi-VN', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      // hour: '2-digit',
      // minute: '2-digit'
  });
};