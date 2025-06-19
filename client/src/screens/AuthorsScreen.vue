<template>
  <div>
    <h2>Authors Management</h2>
    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddAuthorModal">
        Add New Author
      </button>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Bio</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="author in authors" :key="author.id">
            <td>{{ author.name }}</td>
            <td>{{ author.bio }}</td>
            <td>
              <button
                class="btn btn-sm btn-warning me-2"
                @click="editAuthor(author)"
              >
                Edit
              </button>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteAuthor(author.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Add/Edit Author Modal -->
    <div v-if="showAuthorModal" class="modal-backdrop" @click.self="closeAuthorModal">
      <div class="modal-dialog animate-fade-in">
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
                <label class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="authorForm.name"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea
                  class="form-control"
                  v-model="authorForm.bio"
                ></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Save</button>
              <button
                type="button"
                class="btn btn-secondary ms-2"
                @click="closeAuthorModal"
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
  },
  mounted() {
    this.fetchAuthors();
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
