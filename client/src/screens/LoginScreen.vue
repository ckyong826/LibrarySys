<template>
  <div class="login-wrapper">
    <div class="card shadow-lg">
      <div class="card-body p-md-5">
        <div class="text-center mb-4">
          <h2 class="card-title">Welcome Back!</h2>
          <p class="text-muted">Login to your LibrarySys account</p>
        </div>
        <form @submit.prevent="login">
          <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-envelope"></i>
              </span>
              <input
                type="email"
                class="form-control"
                id="email"
                v-model="form.email"
                required
                :disabled="loading"
                placeholder="you@example.com"
              />
            </div>
          </div>
          <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-lock"></i>
              </span>
              <input
                type="password"
                class="form-control"
                id="password"
                v-model="form.password"
                required
                :disabled="loading"
                placeholder="••••••••"
              />
            </div>
          </div>
          <div class="d-grid gap-2">
            <button
              type="submit"
              class="btn btn-primary btn-lg"
              :disabled="loading"
            >
              <span
                v-if="loading"
                class="spinner-border spinner-border-sm me-2"
              ></span>
              {{ loading ? "Logging in..." : "Login" }}
            </button>
          </div>
        </form>
        <div class="text-center mt-4">
          <p class="mb-0">
            Don't have an account?
            <router-link to="/register" class="fw-bold">Sign up</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { auth } from "../stores/auth.js";

export default {
  name: "LoginScreen",
  data() {
    return {
      form: {
        email: "",
        password: "",
      },
      loading: false,
    };
  },
  methods: {
    async login() {
      if (!this.validateForm()) return;

      this.loading = true;
      const result = await auth.login(this.form.email, this.form.password);

      if (result.success) {
        this.$root.showNotification(result.message, "success");
        this.$router.push("/");
      } else {
        this.$root.showNotification(result.message, "error");
      }

      this.loading = false;
    },

    validateForm() {
      if (!this.form.email.trim()) {
        this.$root.showNotification("Email is required", "error");
        return false;
      }
      if (!this.form.password) {
        this.$root.showNotification("Password is required", "error");
        return false;
      }
      return true;
    },
  },

  mounted() {
    // Redirect if already logged in
    if (auth.state.isAuthenticated) {
      this.$router.push("/");
    }
  },
};
</script>

<style scoped>
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.card {
  border-radius: 20px;
  border: none;
  width: 100%;
  max-width: 450px;
}
.card-title {
  font-weight: 700;
  color: #333;
}
.input-group-text {
  background-color: #f8f9fa;
  border-right: 0;
}
.form-control {
  border-left: 0;
  padding: 0.75rem 1rem;
}
.form-control:focus {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  border-left: 0;
}
.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  padding: 0.75rem;
  font-weight: 600;
}
.btn-primary:hover {
  background-color: #0069d9;
  border-color: #0062cc;
}
.fw-bold {
  color: #007bff;
}
</style>
