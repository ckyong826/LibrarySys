import { createRouter, createWebHistory } from "vue-router";
import DashboardScreen from "../screens/DashboardScreen.vue";
import BooksScreen from "../screens/BooksScreen.vue";
import AuthorsScreen from "../screens/AuthorsScreen.vue";
import LoansScreen from "../screens/LoansScreen.vue";
import LoginScreen from "../screens/LoginScreen.vue";
import RegisterScreen from "../screens/RegisterScreen.vue";
import { auth } from "../stores/auth.js";

const routes = [
  {
    path: "/login",
    name: "login",
    component: LoginScreen,
    meta: { requiresGuest: true },
  },
  {
    path: "/register",
    name: "register",
    component: RegisterScreen,
    meta: { requiresGuest: true },
  },
  {
    path: "/",
    name: "dashboard",
    component: DashboardScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/books",
    name: "books",
    component: BooksScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/authors",
    name: "authors",
    component: AuthorsScreen,
    meta: { requiresAuth: true },
  },
  {
    path: "/loans",
    name: "loans",
    component: LoansScreen,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Route guards
router.beforeEach(async (to, from, next) => {
  // Initialize auth if not done yet
  if (!auth.state.isAuthenticated && localStorage.getItem("token")) {
    await auth.checkAuth();
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth && !auth.state.isAuthenticated) {
    next("/login");
    return;
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && auth.state.isAuthenticated) {
    next("/");
    return;
  }

  next();
});

export default router;
