<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h3 mb-0 text-gray-800">Loans Management</h2>
      <button class="btn btn-primary" @click="openAddLoanModal">
        <i class="fas fa-plus me-2"></i>Add New Loan
      </button>
    </div>
    <div class="card shadow">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Book</th>
                <th>User</th>
                <th>Loan Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loans.length === 0">
                <td colspan="7" class="text-center p-4">No loans found.</td>
              </tr>
              <tr v-for="loan in loans" :key="loan.id">
                <td>{{ loan.book_title }}</td>
                <td>{{ loan.user_name }}</td>
                <td>{{ formatDate(loan.loan_date) }}</td>
                <td>{{ formatDate(loan.due_date) }}</td>
                <td>{{ formatDate(loan.return_date) }}</td>
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-success': loan.status === 'returned',
                      'bg-warning text-dark': loan.status === 'borrowed',
                      'bg-danger': loan.status === 'overdue',
                    }"
                    >{{ loan.status }}</span
                  >
                </td>
                <td class="text-end">
                  <button
                    v-if="loan.status === 'borrowed'"
                    class="btn btn-sm btn-outline-success me-2"
                    @click="returnLoan(loan.id)"
                    title="Mark as Returned"
                  >
                    <i class="fas fa-check"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteLoan(loan.id)"
                    title="Delete Loan"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Add Loan Modal -->
    <div
      v-if="showLoanModal"
      class="modal-backdrop"
      @click.self="closeLoanModal"
    >
      <div class="modal-dialog modal-dialog-centered animate-fade-in">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Loan</h5>
            <button
              type="button"
              class="btn-close"
              @click="closeLoanModal"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveLoan">
              <div class="mb-3">
                <label for="loanBook" class="form-label">Book</label>
                <select
                  id="loanBook"
                  class="form-select"
                  v-model="loanForm.book_id"
                  required
                >
                  <option disabled value="">Please select a book</option>
                  <option
                    v-for="book in availableBooks"
                    :key="book.id"
                    :value="book.id"
                  >
                    {{ book.title }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="loanUser" class="form-label">User</label>
                <select
                  id="loanUser"
                  class="form-select"
                  v-model="loanForm.user_id"
                  required
                >
                  <option disabled value="">Please select a user</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="loanDate" class="form-label">Loan Date</label>
                  <input
                    type="date"
                    id="loanDate"
                    class="form-control"
                    v-model="loanForm.loan_date"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="dueDate" class="form-label">Due Date</label>
                  <input
                    type="date"
                    id="dueDate"
                    class="form-control"
                    v-model="loanForm.due_date"
                    required
                  />
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button
                  type="button"
                  class="btn btn-secondary me-2"
                  @click="closeLoanModal"
                >
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary">Save Loan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "LoansScreen",
  data() {
    return {
      loans: [],
      books: [],
      users: [],
      showLoanModal: false,
      loanForm: {
        book_id: "",
        user_id: "",
        loan_date: "",
        due_date: "",
      },
    };
  },
  computed: {
    availableBooks() {
      return this.books.filter((book) => book.available_copies > 0);
    },
  },
  methods: {
    async fetchLoans() {
      try {
        const response = await axios.get("http://localhost:8000/api/loans");
        this.loans = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch loans", "error");
      }
    },
    async fetchBooks() {
      try {
        const response = await axios.get("http://localhost:8000/api/books");
        this.books = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch books", "error");
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get("http://localhost:8000/api/users");
        this.users = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch users", "error");
      }
    },
    openAddLoanModal() {
      const loanDate = new Date();
      const dueDate = new Date();
      dueDate.setDate(loanDate.getDate() + 14); // Default 14-day loan period

      this.loanForm = {
        book_id: "",
        user_id: "",
        loan_date: loanDate.toISOString().slice(0, 10),
        due_date: dueDate.toISOString().slice(0, 10),
      };
      this.showLoanModal = true;
    },
    closeLoanModal() {
      this.showLoanModal = false;
    },
    async saveLoan() {
      try {
        await axios.post("http://localhost:8000/api/loans", this.loanForm);
        this.$root.showNotification("Loan added successfully");
        this.showLoanModal = false;
        await this.fetchLoans();
      } catch (error) {
        this.$root.showNotification("Failed to save loan", "error");
      }
    },
    async returnLoan(loanId) {
      if (!confirm("Are you sure you want to mark this book as returned?"))
        return;
      try {
        await axios.put(`http://localhost:8000/api/loans/${loanId}/return`);
        this.$root.showNotification("Book returned successfully");
        await this.fetchLoans();
        await this.fetchBooks(); // Re-fetch books to update available copies
      } catch (error) {
        this.$root.showNotification(
          error.response?.data?.error || "Failed to return book",
          "error"
        );
      }
    },
    async deleteLoan(id) {
      if (confirm("Are you sure you want to delete this loan?")) {
        try {
          await axios.delete(`http://localhost:8000/api/loans/${id}`);
          this.$root.showNotification("Loan deleted successfully");
          await this.fetchLoans();
        } catch (error) {
          this.$root.showNotification("Failed to delete loan", "error");
        }
      }
    },
    formatDate(dateString) {
      if (!dateString) return "—";
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
  },
  async mounted() {
    await this.fetchBooks();
    await this.fetchUsers();
    await this.fetchLoans();
  },
};
</script>
<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: fadeInBg 0.3s ease-out;
}
.table-hover tbody tr:hover {
  background-color: #f8f9fc;
}
.badge.bg-warning {
  color: #6f42c1; /* A darker text for yellow badge */
}
.btn-outline-success {
  color: #1cc88a;
  border-color: #1cc88a;
}
.btn-outline-success:hover {
  background-color: #1cc88a;
  color: #fff;
}
.btn-outline-danger {
  color: #e74a3b;
  border-color: #e74a3b;
}
.btn-outline-danger:hover {
  background-color: #e74a3b;
  color: #fff;
}
@keyframes fadeInBg {
  from {
    background: rgba(0, 0, 0, 0);
  }
  to {
    background: rgba(0, 0, 0, 0.5);
  }
}
.animate-fade-in {
  animation: fadeInModal 0.3s;
}
@keyframes fadeInModal {
  from {
    transform: translateY(40px) scale(0.98);
    opacity: 0;
  }
  to {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}
.modal-dialog {
  max-width: 500px;
  width: 100%;
}
.modal-content {
  background-color: #fff;
  border: none;
  border-radius: 0.5rem;
  padding: 20px;
}
</style>
