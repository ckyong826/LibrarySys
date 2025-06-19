<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mt-5">
          <div class="card-header">
            <h4 class="text-center">Login to AgroTrack</h4>
          </div>
          <div class="card-body">
            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="form.email"
                  required
                  :disabled="loading"
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  required
                  :disabled="loading"
                />
              </div>
              <div class="d-grid gap-2">
                <button
                  type="submit"
                  class="btn btn-primary"
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
            <div class="text-center mt-3">
              <p>
                Don't have an account?
                <router-link to="/register">Register here</router-link>
              </p>
            </div>
          </div>
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
body,
.container {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
}
.card {
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
  border: none;
}
.card-header {
  background: #4e73df;
  color: #fff;
  border-radius: 18px 18px 0 0;
  text-align: center;
}
.form-control:focus {
  border-color: #4e73df;
  box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
}
.btn-primary {
  background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
  border: none;
  transition: background 0.3s, box-shadow 0.3s;
  box-shadow: 0 2px 8px rgba(78, 115, 223, 0.08);
}
.btn-primary:hover,
.btn-primary:focus {
  background: linear-gradient(90deg, #1cc88a 0%, #4e73df 100%);
  box-shadow: 0 4px 16px rgba(28, 200, 138, 0.12);
}
.text-center a {
  color: #1cc88a;
  text-decoration: underline;
  transition: color 0.2s;
}
.text-center a:hover {
  color: #4e73df;
}
</style>
