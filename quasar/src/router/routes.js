import LoginPage from 'src/pages/Front/LoginPage.vue'
import IndexPage from 'src/pages/IndexPage.vue'
import SignUpPage from 'src/pages/Front/SignUpPage.vue'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: IndexPage, name: 'IndexPage' },
      { path: '/login', component: LoginPage, name: 'LoginPage' },
      { path: '/sign-up', component: SignUpPage, name: 'SignUpPage' },
    ],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
