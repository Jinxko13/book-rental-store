export default defineNuxtRouteMiddleware(async (to) => {
    const auth = useAuthStore();

    if (!auth.isAuthenticated) {
        return navigateTo('/login');
    }
})
    