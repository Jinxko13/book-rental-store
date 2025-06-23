import { UserRole, STAFF_ROLES } from '~/utils/roles';

export default defineNuxtRouteMiddleware(async (to) => {
  if (!to.path.startsWith('/admin')) return;
  const authStore = useAuthStore();
  if (authStore.isSessionExpired) {
    await authStore.logout(true);
    return;
  }
  if (!STAFF_ROLES.includes(authStore.user?.role)) {
    return await navigateTo('/');
  }
});
