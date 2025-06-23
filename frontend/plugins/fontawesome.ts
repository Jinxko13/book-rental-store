// plugins/fontawesome.ts
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
  faTrash,
  faUser,
  faShoppingCart,
  faHeart,
  faHouse,
  faPlus,
  faMinus,
  faEdit,
  faChevronLeft,
  faChevronRight,
} from '@fortawesome/free-solid-svg-icons'

library.add(
  faTrash,
  faUser,
  faShoppingCart,
  faHeart,
  faHouse,
  faPlus,
  faMinus,
  faEdit,
  faChevronLeft,
  faChevronRight
)

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.component('font-awesome-icon', FontAwesomeIcon)
})