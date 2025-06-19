<template>
  <div>
    <h2>Loans Management</h2>
    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddLoanModal">
        Add New Loan
      </button>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Book</th>
            <th>User</th>
            <th>Loan Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="loan in loans" :key="loan.id">
            <td>{{ loan.book_title }}</td>
            <td>{{ loan.user_name }}</td>
            <td>{{ loan.loan_date }}</td>
            <td>{{ loan.due_date }}</td>
            <td>{{ loan.return_date || "-" }}</td>
            <td>{{ loan.status }}</td>
            <td>
              <button
                v-if="loan.status !== 'returned'"
                class="btn btn-sm btn-success me-2"
                @click="returnLoan(loan)"
              >
                Return
              </button>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteLoan(loan.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Add Loan Modal -->
    <div v-if="showLoanModal" class="modal-backdrop" @click.self="closeLoanModal">
      <div class="modal-dialog animate-fade-in">
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
                <label class="form-label">Book</label>
                <select
                  class="form-control"
                  v-model="loanForm.book_id"
                  required
                >
                  <option v-for="book in books" :key="book.id" :value="book.id">
                    {{ book.title }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">User</label>
                <select
                  class="form-control"
                  v-model="loanForm.user_id"
                  required
                >
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Loan Date</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="loanForm.loan_date"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="loanForm.due_date"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary">Save</button>
              <button
                type="button"
                class="btn btn-secondary ms-2"
                @click="closeLoanModal"
              >
                Cancel
              </button>
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
        return_date: null,
        status: "borrowed",
      },
    };
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
      this.loanForm = {
        book_id: this.books.length ? this.books[0].id : "",
        user_id: this.users.length ? this.users[0].id : "",
        loan_date: new Date().toISOString().slice(0, 10),
        due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)
          .toISOString()
          .slice(0, 10),
        return_date: null,
        status: "borrowed",
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
    async returnLoan(loan) {
      try {
        const updated = {
          ...loan,
          return_date: new Date().toISOString().slice(0, 10),
          status: "returned",
        };
        await axios.put(`http://localhost:8000/api/loans/${loan.id}`, updated);
        this.$root.showNotification("Book returned successfully");
        await this.fetchLoans();
      } catch (error) {
        this.$root.showNotification("Failed to return book", "error");
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
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.45);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: fadeInBg 0.3s;
}
@keyframes fadeInBg {
  from { background: rgba(0,0,0,0); }
  to { background: rgba(0,0,0,0.45); }
}
.animate-fade-in {
  animation: fadeInModal 0.3s;
}
@keyframes fadeInModal {
  from { transform: translateY(40px) scale(0.98); opacity: 0; }
  to { transform: translateY(0) scale(1); opacity: 1; }
}
.modal-dialog {
  max-width: 500px;
  width: 100%;
}
</style>
