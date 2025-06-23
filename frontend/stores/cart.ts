// stores/product.ts
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useProductStore = defineStore('product', () => {
  const products = ref<Array<{
    id: number
    title: string
    price: number
    quantity: number
    image?: string
    maxQuantity: number
  }>>([])
  const checkedId=ref([])
  const number=ref(0)
  const totalQuantity = computed(() =>
    products.value.reduce((sum, p) => sum + p.quantity, 0)
  )
  const totalPrice = computed(() =>
    products.value.reduce((sum, p) => sum + p.quantity * p.price, 0)
  )
  let initialized = false;

  function initializeFromLocalStorage() {
    try {
      const cartData = localStorage.getItem('cart')
      console.log(cartData)
      if (cartData) {
        const parsedCart = JSON.parse(cartData)
        products.value = parsedCart
        console.log(products.value)
        updateNumber()
      }
    } catch (error) {
      console.error('Lỗi khi load dữ liệu từ localStorage:', error)
      products.value = []
    }
  }

  function syncToLocalStorage() {
    try {
      if (!initialized) {
        initialized = true;
        return;
      }
      localStorage.setItem('cart', JSON.stringify(products.value))
    } catch (error) {
      console.error('Lỗi khi lưu vào localStorage:', error)
    }
  }

  function updateNumber() {
    number.value=products.value.reduce((sum, p) => sum + p.quantity, 0)
  }

  watch(
    products,
    () => {
      syncToLocalStorage()
      updateNumber()
    },
    { deep: true }
  )

  function addToCart(product: {
    id: number
    title: string
    price: number
    quantity: number
    image?: string
    maxQuantity: number
  }) {
    const existing = products.value.find(
      item => item.id === product.id
    )
    if (existing) {
      if ((existing.quantity + product.quantity) <= existing.maxQuantity) {
        existing.quantity += product.quantity
        return ;
      }
    } else {
      if (product.quantity <= product.maxQuantity) {
        products.value.push(product)
        return ;
      }
    }
    
    throw new Error('Số lượng sách vượt quá số lượng trong kho');
  }

  function removeProduct(id: number) {
    products.value = products.value.filter(p => p.id !== id)
    if (checkedId.value.includes(id)) {
      checkedId.value = checkedId.value.filter(p => p !== id)
    }
  }

  function updateQuantity(id: number, quantity: number) {
    const item = products.value.find(p => p.id === id)
    if (item && quantity >= 0) {
      item.quantity = quantity
    }
    if (item.quantity > item.maxQuantity) {
      item.quantity = item.maxQuantity
    }
  }

  function clearCheckedProducts() {
    products.value = products.value.filter(p => !checkedId.value.includes(p.id))
    checkedId.value = []
  }

  function updateCheckedId(id: number) {
    if (checkedId.value.includes(id)) {
      checkedId.value = checkedId.value.filter(p => p !== id)
    } else {
      checkedId.value.push(id)
    }
  }

  function getCheckedProducts() {
    return products.value.filter(p => checkedId.value.includes(p.id))
  }

  return {
    products,
    totalQuantity,
    totalPrice,
    addToCart,
    updateQuantity,
    removeProduct,
    clearCheckedProducts,
    checkedId,
    updateCheckedId,
    number,
    updateNumber,
    getCheckedProducts,
    initializeFromLocalStorage,
  }
})
