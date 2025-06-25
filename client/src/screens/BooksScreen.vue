<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h3 mb-0 text-gray-800">Books Management</h2>
      <button class="btn btn-primary" @click="openAddBookModal">
        <i class="fas fa-plus me-2"></i>Add New Book
      </button>
    </div>

    <div class="card shadow">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Published Year</th>
                <th class="text-center">Availability</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="books.length === 0">
                <td colspan="6" class="text-center p-4">No books found.</td>
              </tr>
              <tr v-for="book in books" :key="book.id">
                <td>{{ book.title }}</td>
                <td>{{ book.author_name }}</td>
                <td>{{ book.isbn }}</td>
                <td>{{ book.published_year }}</td>
                <td class="text-center">
                  <span class="badge" :class="getAvailabilityClass(book)">
                    {{ book.available_copies }} / {{ book.total_copies }}
                  </span>
                </td>
                <td class="text-end">
                  <button
                    class="btn btn-sm btn-outline-primary me-2"
                    @click="editBook(book)"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteBook(book.id)"
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

    <!-- Add/Edit Book Modal -->
    <div
      v-if="showBookModal"
      class="modal-backdrop"
      @click.self="closeBookModal"
    >
      <div class="modal-dialog modal-dialog-centered animate-fade-in">
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
                <label for="bookTitle" class="form-label">Title</label>
                <input
                  type="text"
                  id="bookTitle"
                  class="form-control"
                  v-model="bookForm.title"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="bookAuthor" class="form-label">Author</label>
                <select
                  id="bookAuthor"
                  class="form-select"
                  v-model="bookForm.author_id"
                  required
                >
                  <option disabled value="">Please select an author</option>
                  <option
                    v-for="author in authors"
                    :key="author.id"
                    :value="author.id"
                  >
                    {{ author.name }}
                  </option>
                </select>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="bookIsbn" class="form-label">ISBN</label>
                  <input
                    type="text"
                    id="bookIsbn"
                    class="form-control"
                    v-model="bookForm.isbn"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="bookPublishedYear" class="form-label"
                    >Published Year</label
                  >
                  <input
                    type="number"
                    id="bookPublishedYear"
                    class="form-control"
                    v-model="bookForm.published_year"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="bookTotalCopies" class="form-label"
                    >Total Copies</label
                  >
                  <input
                    type="number"
                    id="bookTotalCopies"
                    class="form-control"
                    v-model="bookForm.total_copies"
                    required
                    min="0"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="bookAvailableCopies" class="form-label"
                    >Available Copies</label
                  >
                  <input
                    type="number"
                    id="bookAvailableCopies"
                    class="form-control"
                    v-model="bookForm.available_copies"
                    required
                    min="0"
                  />
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button
                  type="button"
                  class="btn btn-secondary me-2"
                  @click="closeBookModal"
                >
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                  {{ editingBook ? "Update" : "Save" }}
                </button>
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
        author_id: "",
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
    getAvailabilityClass(book) {
      if (!book.total_copies) return "bg-secondary";
      const availability = book.available_copies / book.total_copies;
      if (availability === 0) {
        return "bg-danger";
      } else if (availability < 0.3) {
        return "bg-warning text-dark";
      } else {
        return "bg-success";
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
  background: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: fadeInBg 0.3s;
}
.table-hover tbody tr:hover {
  background-color: #f8f9fc;
}
.btn-outline-primary {
  color: #4e73df;
  border-color: #4e73df;
}
.btn-outline-primary:hover {
  background-color: #4e73df;
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
