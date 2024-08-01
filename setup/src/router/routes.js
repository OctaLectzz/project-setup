const routes = [
  // Home
  {
    path: '/',
    component: () => import('/src/layouts/MainLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('/src/pages/landing/IndexPage.vue') },

      // Not Found
      { path: '/:catchAll(.*)*', component: () => import('/src/pages/ErrorNotFound.vue') }
    ]
  },

  // Dashboard
  {
    path: '/dashboard',
    component: () => import('/src/layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true, roles: ['Admin'] },
    children: [
      { path: 'home', name: 'dashboard.home', component: () => import('/src/pages/dashboard/IndexDashboard.vue') },
      { path: 'profile', name: 'dashboard.profile', component: () => import('/src/components/ProfileView.vue') },
      { path: 'changepassword', name: 'dashboard.changepassword', component: () => import('/src/components/ChangePasswordView.vue') },
      { path: 'user', name: 'dashboard.user', component: () => import('/src/pages/dashboard/user/IndexUser.vue') },
      { path: 'setting', name: 'dashboard.setting', component: () => import('/src/pages/dashboard/setting/IndexSetting.vue') }
    ]
  }
]

export default routes
