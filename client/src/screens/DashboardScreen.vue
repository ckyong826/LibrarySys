<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h3 mb-0 text-gray-800">Dashboard</h2>
    </div>
    <div class="row">
      <!-- Stat Cards -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >
                  Total Books
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ stats.books }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-book fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-success text-uppercase mb-1"
                >
                  Total Authors
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ stats.authors }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-users fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-info text-uppercase mb-1"
                >
                  Active Loans
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ stats.activeLoans }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-warning text-uppercase mb-1"
                >
                  Registered Users
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ stats.users }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-user-friends fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Recent Loans Table -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Recent Loans</h6>
      </div>
      <div class="card-body">
        <div v-if="recentLoans.length === 0" class="text-center p-4">
          No recent loan activity.
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Book Title</th>
                <th>User</th>
                <th>Loan Date</th>
                <th>Due Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loan in recentLoans" :key="loan.id">
                <td>{{ loan.book_title }}</td>
                <td>{{ loan.user_name }}</td>
                <td>{{ formatDate(loan.loan_date) }}</td>
                <td>{{ formatDate(loan.due_date) }}</td>
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-success': loan.status === 'returned',
                      'bg-warning': loan.status === 'borrowed',
                    }"
                    >{{ loan.status }}</span
                  >
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "DashboardScreen",
  data() {
    return {
      stats: {
        books: 0,
        authors: 0,
        users: 0,
        activeLoans: 0,
      },
      recentLoans: [],
    };
  },
  methods: {
    async fetchDashboardData() {
      try {
        const [statsRes, loansRes] = await Promise.all([
          axios.get("http://localhost:8000/api/stats"),
          axios.get(
            "http://localhost:8000/api/loans?_sort=loan_date&_order=desc&_limit=10"
          ),
        ]);

        this.stats = statsRes.data;
        this.recentLoans = loansRes.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch dashboard data", "error");
      }
    },
    formatDate(dateString) {
      if (!dateString) return "N/A";
      const options = { year: "numeric", month: "long", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
  },
  mounted() {
    this.fetchDashboardData();
  },
};
</script>

<style scoped>
.card.border-left-primary {
  border-left: 0.25rem solid #4e73df !important;
}
.text-primary {
  color: #4e73df !important;
}
.card.border-left-success {
  border-left: 0.25rem solid #1cc88a !important;
}
.text-success {
  color: #1cc88a !important;
}
.card.border-left-info {
  border-left: 0.25rem solid #36b9cc !important;
}
.text-info {
  color: #36b9cc !important;
}
.card.border-left-warning {
  border-left: 0.25rem solid #f6c23e !important;
}
.text-warning {
  color: #f6c23e !important;
}
.text-xs {
  font-size: 0.7rem;
}
.text-gray-300 {
  color: #dddfeb !important;
}
.text-gray-800 {
  color: #5a5c69 !important;
}
.shadow {
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
.table-hover tbody tr:hover {
  background-color: #f8f9fc;
}
.badge {
  padding: 0.4em 0.6em;
  font-size: 0.85em;
}
</style>
