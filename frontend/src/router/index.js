import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import ProductListView from '../views/products/ProductListView.vue'
import ProductFormView from '../views/products/ProductFormView.vue'
import CustomerListView from '../views/customers/CustomerListView.vue'
import CustomerFormView from '../views/customers/CustomerFormView.vue'
import OrderListView from '../views/orders/OrderListView.vue'
import OrderCreateView from '../views/orders/OrderCreateView.vue'
import OrderDetailView from '../views/orders/OrderDetailView.vue'
import { authClient } from '../lib/auth-client'
import { openAuthModal } from '../lib/authModal'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/products', name: 'products.index', component: ProductListView },
    {
      path: '/products/create',
      name: 'products.create',
      component: ProductFormView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/products/:id/edit',
      name: 'products.edit',
      component: ProductFormView,
      props: true,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/customers',
      name: 'customers.index',
      component: CustomerListView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/customers/create',
      name: 'customers.create',
      component: CustomerFormView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/customers/:id/edit',
      name: 'customers.edit',
      component: CustomerFormView,
      props: true,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    { path: '/orders', name: 'orders.index', component: OrderListView, meta: { requiresAuth: true } },
    { path: '/orders/create', name: 'orders.create', component: OrderCreateView, meta: { requiresAuth: true } },
    {
      path: '/orders/:id',
      name: 'orders.show',
      component: OrderDetailView,
      props: true,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach(async (to) => {
  if (!to.meta.requiresAuth) return true

  const { data: session } = await authClient.getSession()

  if (!session) {
    openAuthModal('login')
    return { path: '/login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && session.user.role !== 'admin') {
    return { path: '/' }
  }

  return true
})

export default router
