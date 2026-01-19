import '../css/app.css';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

import App from './App.vue';

// Set up Axios defaults
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['Accept'] = 'application/json';

// Get CSRF token from cookie or meta tag
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : null;
};

// Set initial CSRF token
const csrfToken = getCsrfToken();
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Add request interceptor to ensure CSRF token is always fresh
axios.interceptors.request.use(function (config) {
    const token = getCsrfToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Add response interceptor to handle CSRF token errors
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch - reload the page to get a fresh token
            console.error('CSRF token mismatch, reloading page...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

// Define routes
const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('./views/Home.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('./views/auth/Login.vue')
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('./views/auth/Register.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard', 
    component: () => import('./views/Dashboard.vue')
  },
  {
    path: '/clients',
    name: 'clients',
    component: () => import('./views/clients/Index.vue')
  },
  {
    path: '/clients/create',
    name: 'clients.create',
    component: () => import('./views/clients/Create.vue')
  },
  {
    path: '/clients/:id/edit',
    name: 'clients.edit',
    component: () => import('./views/clients/Edit.vue')
  },
  {
    path: '/clients/:id',
    name: 'clients.show',
    component: () => import('./views/clients/Show.vue')
  },
  {
    path: '/budgets',
    name: 'budgets',
    component: () => import('./views/budgets/Index.vue')
  },
  {
    path: '/budgets/create',
    name: 'budgets.create',
    component: () => import('./views/budgets/Create.vue')
  },
  {
    path: '/budgets/:id/edit',
    name: 'budgets.edit',
    component: () => import('./views/budgets/Edit.vue')
  },
  {
    path: '/budgets/:id',
    name: 'budgets.show',
    component: () => import('./views/budgets/Show.vue')
  }
];

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes
});

// Create Vue app
const app = createApp(App);

// Make axios available globally
app.config.globalProperties.$http = axios;

// Use router
app.use(router);

// Mount the app
app.mount('#app');