import LoginPage from 'src/pages/Front/LoginPage.vue'
// import IndexPage from 'src/pages/IndexPage.vue'
import SignUpPage from 'src/pages/Front/SignUpPage.vue'
import DashboardPage from 'src/pages/DashboardPage.vue'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: false, isUser: true },
    children: [
      { path: '/', component: LoginPage, name: 'LoginPage' },
      { path: '/sign-up', component: SignUpPage, name: 'SignUpPage' },
    ],
  },
  {
    path: '/user',
    component: () => import('layouts/PortalLayout.vue'),
    meta: { requiresAuth: true },
    children: [{ path: 'dashboard', component: DashboardPage, name: 'DashboardPage' }],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
