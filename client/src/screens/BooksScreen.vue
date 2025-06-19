<template>
  <div>
    <h2>Books Management</h2>
    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddBookModal">
        Add New Book
      </button>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Published Year</th>
            <th>Available/Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books" :key="book.id">
            <td>{{ book.title }}</td>
            <td>{{ book.author_name }}</td>
            <td>{{ book.isbn }}</td>
            <td>{{ book.published_year }}</td>
            <td>{{ book.available_copies }}/{{ book.total_copies }}</td>
            <td>
              <button
                class="btn btn-sm btn-warning me-2"
                @click="editBook(book)"
              >
                Edit
              </button>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteBook(book.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Add/Edit Book Modal -->
    <div
      v-if="showBookModal"
      class="modal-backdrop"
      @click.self="closeBookModal"
    >
      <div class="modal-dialog animate-fade-in">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editingBook ? "Edit Book" : "Add New Book" }}
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="closeBookModal"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveBook">
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="bookForm.title"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Author</label>
                <select
                  class="form-control"
                  v-model="bookForm.author_id"
                  required
                >
                  <option
                    v-for="author in authors"
                    :key="author.id"
                    :value="author.id"
                  >
                    {{ author.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">ISBN</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="bookForm.isbn"
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Published Year</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="bookForm.published_year"
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Available Copies</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="bookForm.available_copies"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Total Copies</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="bookForm.total_copies"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary">Save</button>
              <button
                type="button"
                class="btn btn-secondary ms-2"
                @click="closeBookModal"
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
  name: "BooksScreen",
  data() {
    return {
      books: [],
      authors: [],
      showBookModal: false,
      editingBook: null,
      bookForm: {
        title: "",
        author_id: "",
        isbn: "",
        published_year: "",
        available_copies: 1,
        total_copies: 1,
      },
    };
  },
  methods: {
    async fetchBooks() {
      try {
        const response = await axios.get("http://localhost:8000/api/books");
        this.books = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch books", "error");
      }
    },
    async fetchAuthors() {
      try {
        const response = await axios.get("http://localhost:8000/api/authors");
        this.authors = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch authors", "error");
      }
    },
    openAddBookModal() {
      this.editingBook = null;
      this.bookForm = {
        title: "",
        author_id: this.authors.length ? this.authors[0].id : "",
        isbn: "",
        published_year: "",
        available_copies: 1,
        total_copies: 1,
      };
      this.showBookModal = true;
    },
    closeBookModal() {
      this.showBookModal = false;
    },
    editBook(book) {
      this.editingBook = book;
      this.bookForm = {
        title: book.title,
        author_id: book.author_id,
        isbn: book.isbn,
        published_year: book.published_year,
        available_copies: book.available_copies,
        total_copies: book.total_copies,
      };
      this.showBookModal = true;
    },
    async saveBook() {
      try {
        if (this.editingBook) {
          await axios.put(
            `http://localhost:8000/api/books/${this.editingBook.id}`,
            this.bookForm
          );
          this.$root.showNotification("Book updated successfully");
        } else {
          await axios.post("http://localhost:8000/api/books", this.bookForm);
          this.$root.showNotification("Book added successfully");
        }
        this.showBookModal = false;
        await this.fetchBooks();
      } catch (error) {
        this.$root.showNotification("Failed to save book", "error");
      }
    },
    async deleteBook(id) {
      if (confirm("Are you sure you want to delete this book?")) {
        try {
          await axios.delete(`http://localhost:8000/api/books/${id}`);
          this.$root.showNotification("Book deleted successfully");
          await this.fetchBooks();
        } catch (error) {
          this.$root.showNotification("Failed to delete book", "error");
        }
      }
    },
  },
  async mounted() {
    await this.fetchAuthors();
    await this.fetchBooks();
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
  background: rgba(0, 0, 0, 0.45);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: fadeInBg 0.3s;
}
@keyframes fadeInBg {
  from {
    background: rgba(0, 0, 0, 0);
  }
  to {
    background: rgba(0, 0, 0, 0.45);
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
</style>
