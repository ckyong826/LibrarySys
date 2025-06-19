<template>
  <div>
    <h2>Library Dashboard</h2>
    <div class="row">
      <div class="col-md-3">
        <div class="card mb-4">
          <div class="card-header">Total Books</div>
          <div class="card-body display-5 text-center">{{ stats.books }}</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-4">
          <div class="card-header">Total Authors</div>
          <div class="card-body display-5 text-center">{{ stats.authors }}</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-4">
          <div class="card-header">Total Users</div>
          <div class="card-body display-5 text-center">{{ stats.users }}</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card mb-4">
          <div class="card-header">Active Loans</div>
          <div class="card-body display-5 text-center">
            {{ stats.activeLoans }}
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-header">Recent Loans</div>
      <div class="card-body">
        <div v-if="recentLoans.length === 0">No recent loans.</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Book</th>
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
              <td>{{ loan.loan_date }}</td>
              <td>{{ loan.due_date }}</td>
              <td>{{ loan.status }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-5">
      <h4>All Books</h4>
      <div class="table-responsive mb-4">
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Published Year</th>
              <th>Available/Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="book in allBooks" :key="book.id">
              <td>{{ book.title }}</td>
              <td>{{ book.author_name }}</td>
              <td>{{ book.isbn }}</td>
              <td>{{ book.published_year }}</td>
              <td>{{ book.available_copies }}/{{ book.total_copies }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h4>All Authors</h4>
      <div class="table-responsive mb-4">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Bio</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="author in allAuthors" :key="author.id">
              <td>{{ author.name }}</td>
              <td>{{ author.bio }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h4>All Users</h4>
      <div class="table-responsive mb-4">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in allUsers" :key="user.id">
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.role }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <h4>All Loans</h4>
      <div class="table-responsive mb-4">
        <table class="table">
          <thead>
            <tr>
              <th>Book</th>
              <th>User</th>
              <th>Loan Date</th>
              <th>Due Date</th>
              <th>Return Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="loan in allLoans" :key="loan.id">
              <td>{{ loan.book_title }}</td>
              <td>{{ loan.user_name }}</td>
              <td>{{ loan.loan_date }}</td>
              <td>{{ loan.due_date }}</td>
              <td>{{ loan.return_date || "-" }}</td>
              <td>{{ loan.status }}</td>
            </tr>
          </tbody>
        </table>
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
      allBooks: [],
      allAuthors: [],
      allUsers: [],
      allLoans: [],
    };
  },
  methods: {
    async fetchStats() {
      try {
        const [booksRes, authorsRes, usersRes, loansRes] = await Promise.all([
          axios.get("http://localhost:8000/api/books"),
          axios.get("http://localhost:8000/api/authors"),
          axios.get("http://localhost:8000/api/users"),
          axios.get("http://localhost:8000/api/loans"),
        ]);
        this.stats.books = booksRes.data.length;
        this.stats.authors = authorsRes.data.length;
        this.stats.users = usersRes.data.length;
        this.stats.activeLoans = loansRes.data.filter(
          (l) => l.status === "borrowed"
        ).length;
        this.recentLoans = loansRes.data.slice(0, 10);
        this.allBooks = booksRes.data;
        this.allAuthors = authorsRes.data;
        this.allUsers = usersRes.data;
        this.allLoans = loansRes.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch dashboard data", "error");
      }
    },
  },
  mounted() {
    this.fetchStats();
  },
};
</script>

<style scoped>
body,
.container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e0eafc 100%);
}
h2 {
  color: #4e73df;
  margin-bottom: 2rem;
  font-weight: 700;
}
.card {
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
  border: none;
  margin-bottom: 2rem;
}
.card-header {
  background: #1cc88a;
  color: #fff;
  border-radius: 16px 16px 0 0;
  font-weight: 600;
}
.card-body {
  background: #fff;
  border-radius: 0 0 16px 16px;
}
.display-5 {
  font-size: 2.5rem;
  font-weight: 700;
}
@media (max-width: 768px) {
  .row {
    flex-direction: column;
  }
  .col-md-6 {
    width: 100%;
    margin-bottom: 1.5rem;
  }
}
</style>
