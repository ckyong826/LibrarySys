<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h3 mb-0 text-gray-800">Authors Management</h2>
      <button class="btn btn-primary" @click="openAddAuthorModal">
        <i class="fas fa-plus me-2"></i>Add New Author
      </button>
    </div>

    <div class="card shadow">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Biography</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="authors.length === 0">
                <td colspan="3" class="text-center p-4">No authors found.</td>
              </tr>
              <tr v-for="author in authors" :key="author.id">
                <td>{{ author.name }}</td>
                <td>{{ truncate(author.bio, 100) }}</td>
                <td class="text-end">
                  <button
                    class="btn btn-sm btn-outline-primary me-2"
                    @click="editAuthor(author)"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteAuthor(author.id)"
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

    <!-- Add/Edit Author Modal -->
    <div
      v-if="showAuthorModal"
      class="modal-backdrop"
      @click.self="closeAuthorModal"
    >
      <div class="modal-dialog modal-dialog-centered animate-fade-in">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editingAuthor ? "Edit Author" : "Add New Author" }}
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="closeAuthorModal"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveAuthor">
              <div class="mb-3">
                <label for="authorName" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="authorName"
                  v-model="authorForm.name"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="authorBio" class="form-label">Biography</label>
                <textarea
                  class="form-control"
                  id="authorBio"
                  rows="4"
                  v-model="authorForm.bio"
                ></textarea>
              </div>
              <div class="d-flex justify-content-end">
                <button
                  type="button"
                  class="btn btn-secondary me-2"
                  @click="closeAuthorModal"
                >
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                  {{ editingAuthor ? "Update" : "Save" }}
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
  name: "AuthorsScreen",
  data() {
    return {
      authors: [],
      showAuthorModal: false,
      editingAuthor: null,
      authorForm: {
        name: "",
        bio: "",
      },
    };
  },
  methods: {
    async fetchAuthors() {
      try {
        const response = await axios.get("http://localhost:8000/api/authors");
        this.authors = response.data;
      } catch (error) {
        this.$root.showNotification("Failed to fetch authors", "error");
      }
    },
    openAddAuthorModal() {
      this.editingAuthor = null;
      this.authorForm = { name: "", bio: "" };
      this.showAuthorModal = true;
    },
    closeAuthorModal() {
      this.showAuthorModal = false;
    },
    editAuthor(author) {
      this.editingAuthor = author;
      this.authorForm = { name: author.name, bio: author.bio };
      this.showAuthorModal = true;
    },
    async saveAuthor() {
      try {
        if (this.editingAuthor) {
          await axios.put(
            `http://localhost:8000/api/authors/${this.editingAuthor.id}`,
            this.authorForm
          );
          this.$root.showNotification("Author updated successfully");
        } else {
          await axios.post(
            "http://localhost:8000/api/authors",
            this.authorForm
          );
          this.$root.showNotification("Author added successfully");
        }
        this.showAuthorModal = false;
        await this.fetchAuthors();
      } catch (error) {
        this.$root.showNotification("Failed to save author", "error");
      }
    },
    async deleteAuthor(id) {
      if (confirm("Are you sure you want to delete this author?")) {
        try {
          await axios.delete(`http://localhost:8000/api/authors/${id}`);
          this.$root.showNotification("Author deleted successfully");
          await this.fetchAuthors();
        } catch (error) {
          this.$root.showNotification("Failed to delete author", "error");
        }
      }
    },
    truncate(text, length) {
      if (text && text.length > length) {
        return text.substring(0, length) + "...";
      }
      return text;
    },
  },
  mounted() {
    this.fetchAuthors();
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
.modal-content {
  background-color: #fff;
  border: none;
  border-radius: 0.5rem;
  padding: 20px;
}
</style>
