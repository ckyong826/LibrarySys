<template>
  <div>
    <!-- Notification Component -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="toast show"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div
          class="toast-header"
          :class="{
            'bg-success text-white': notification.type === 'success',
            'bg-danger text-white': notification.type === 'error',
          }"
        >
          <strong class="me-auto">{{
            notification.type === "success" ? "Success" : "Error"
          }}</strong>
          <button
            type="button"
            class="btn-close btn-close-white"
            @click="
              notifications = notifications.filter(
                (n) => n.id !== notification.id
              )
            "
          ></button>
        </div>
        <div class="toast-body">
          {{ notification.message }}
        </div>
      </div>
    </div>

    <!-- Loading Indicators -->
    <div v-if="loading" class="position-fixed top-50 start-50 translate-middle">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Navigation (only show when authenticated) -->
    <nav
      v-if="auth.state.isAuthenticated"
      class="navbar navbar-expand-lg navbar-dark bg-success"
    >
      <div class="container">
        <router-link class="navbar-brand" to="/">LibrarySys</router-link>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Dashboard</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/books">Books</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/authors">Authors</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/loans">Loans</router-link>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="navbarDropdown"
                role="button"
                data-bs-toggle="dropdown"
              >
                {{ auth.state.user?.name || "User" }}
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="#" @click="logout">Logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import { auth } from "./stores/auth.js";

export default {
  name: "App",
  data() {
    return {
      notifications: [],
      loading: false,
      ws: null,
      auth,
    };
  },
  methods: {
    showNotification(message, type = "success") {
      const id = Date.now();
      this.notifications.push({ id, message, type });
      setTimeout(() => {
        this.notifications = this.notifications.filter((n) => n.id !== id);
      }, 3000);
    },

    async logout() {
      await auth.logout();
      this.showNotification("Logged out successfully", "success");
      this.$router.push("/login");
    },

    setupWebSocket() {
      if (!auth.state.isAuthenticated) return;

      this.ws = new WebSocket("ws://localhost:8080");

      this.ws.onopen = () => {
        console.log("WebSocket connected");
      };

      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        this.handleWebSocketMessage(data);
      };

      this.ws.onclose = () => {
        console.log("WebSocket disconnected");
        setTimeout(() => this.setupWebSocket(), 5000);
      };

      this.ws.onerror = (error) => {
        console.error("WebSocket error:", error);
      };
    },

    handleWebSocketMessage(data) {
      switch (data.type) {
        case "sensor_reading":
          this.$root.$emit("sensor-reading", data);
          break;
        case "actuator_status":
          this.$root.$emit("actuator-status", data);
          break;
        default:
          console.log("Unknown message type:", data.type);
      }
    },
  },

  async mounted() {
    // Initialize auth
    auth.init();

    // Setup WebSocket if authenticated
    if (auth.state.isAuthenticated) {
      this.setupWebSocket();
    }

    // Watch for auth state changes
    this.$watch(
      () => auth.state.isAuthenticated,
      (newVal) => {
        if (newVal) {
          this.setupWebSocket();
        } else if (this.ws) {
          this.ws.close();
          this.ws = null;
        }
      }
    );
  },

  beforeUnmount() {
    if (this.ws) {
      this.ws.close();
    }
  },
};
</script>

<style>
body,
#app,
html {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
}
.navbar {
  border-radius: 16px;
  margin-top: 1.5rem;
  box-shadow: 0 4px 24px rgba(78, 115, 223, 0.08);
  background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%) !important;
}
.navbar-brand {
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: 1px;
}
.nav-link {
  transition: color 0.2s, background 0.2s;
  border-radius: 8px;
  margin-right: 0.5rem;
}
.nav-link.router-link-exact-active,
.nav-link:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #fff !important;
}
.dropdown-menu {
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(78, 115, 223, 0.08);
}
.toast {
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(28, 200, 138, 0.1);
  min-width: 260px;
  font-size: 1rem;
}
.toast-header {
  border-radius: 12px 12px 0 0;
}
.toast-body {
  border-radius: 0 0 12px 12px;
}
.btn-close {
  filter: invert(1);
}
.container {
  margin-top: 2.5rem !important;
  margin-bottom: 2.5rem;
}
@media (max-width: 768px) {
  .navbar {
    border-radius: 0;
    margin-top: 0.5rem;
  }
  .container {
    margin-top: 1rem !important;
    margin-bottom: 1rem;
  }
}
</style>
