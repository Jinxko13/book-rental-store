import { ref } from 'vue'

export const useMenuState = () => {
  const selectedKeys = ref<string[]>(['1'])
  
  const setSelectedKey = (key: string) => {
    selectedKeys.value = [key]
  }
  
  return {
    selectedKeys,
    setSelectedKey
  }
}