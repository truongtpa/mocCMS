<template>
  <div class="row">
    <div class="col-8"></div>
    <div class="col-4">
      <div class="input-group input-group-sm" style="margin-bottom: 12px;">
        <input type="text" class="form-control float-right" v-model="searchQuery" placeholder="Tìm kiếm thông tin ...">
        <div class="input-group-append">
          <button type="submit" class="btn btn-default">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="col-12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th v-for="header in headers" :key="header.key" @click="sortTable(header.key)">
              {{ header.label }}
              <span v-if="sortKey === header.key">
                <i v-if="sortOrder === 'asc'" class="fas fa-sort-amount-up"></i>
                <i v-if="sortOrder === 'desc'" class="fas fa-sort-amount-up-alt"></i>
              </span>
            </th>
            <th v-if="$slots.actions" class="d-flex justify-content-end"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in filteredAndSortedData" :key="item.id">
            <td v-for="header in headers" :key="header.key" v-html="highlight(item[header.key])"></td>
            <td v-if="$slots.actions" class="float-right">
              <slot name="actions" :item="item"></slot>
            </td>
          </tr>
        </tbody>
      </table>
      <hr>
    </div>

    <div class="col-6">
      Có tổng cộng <b>{{ pagination.total }}</b> dòng dữ liệu.
    </div>

    <div class="col-6">
      <!-- Pagination Controls -->
      <ul v-if="pagination.last_page > 1" class="pagination pagination-sm m-0 float-right">
        <!-- Previous Button -->
        <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
          <a class="page-link" href="#" @click.prevent="fetchPage(pagination.prev_page_url)">Trang trước</a>
        </li>

        <!-- Loop through the total pages and display them -->
        <li v-for="page in pagination.last_page" :key="page" class="page-item"
          :class="{ active: pagination.current_page === page }">
          <a class="page-link" href="#" @click.prevent="fetchPage(getPageUrl(page))">{{ page }}</a>
        </li>

        <!-- Next Button -->
        <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
          <a class="page-link" href="#" @click.prevent="fetchPage(pagination.next_page_url)">Trang sau</a>
        </li>
      </ul>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Array,
      required: true
    },
    headers: {
      type: Array,
      required: true
    },
    pagination: {
      type: Object,
      required: true
    },
    fetchPage: {
      type: Function,
      required: true
    },
  },
  data() {
    return {
      searchQuery: '',
      sortKey: '',
      sortOrder: 'asc'
    };
  },
  computed: {
    filteredAndSortedData() {
      let filteredData = this.data.filter(item => {
        return this.headers.some(header => {
          const value = item[header.key];
          return value && value.toString().toLowerCase().includes(this.searchQuery.toLowerCase());
        });
      });

      if (this.sortKey) {
        filteredData.sort((a, b) => {
          let result = 0;
          const valA = a[this.sortKey] || '';
          const valB = b[this.sortKey] || '';
          if (valA < valB) result = -1;
          if (valA > valB) result = 1;
          return this.sortOrder === 'asc' ? result : -result;
        });
      }

      return filteredData;
    }
  },
  methods: {
    sortTable(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }
    },

    highlight(text) {
      if (!this.searchQuery) return text;
      const regex = new RegExp(`(${this.searchQuery})`, 'gi');
      return text ? text.toString().replace(regex, '<span class="highlight">$1</span>') : text;
    },
    getPageUrl(page) {
      let url = this.pagination.path;
      if (url.includes('sinh-vien')) {
        return `${this.pagination.path}page=${page}`;
      }
      return `${this.pagination.path}?page=${page}`;
    }
  }
};
</script>

<style>
.highlight {
  background-color: yellow;
  font-weight: bold;
  border-radius: 2px;
}

.icon-edit {
  font-size: 11px;
  background: #0c84ff;
  color: white;
  padding: 5px 3px 5px 5px;
  border-radius: 50%;
  margin-right: 5px;
  width: 25px;
  height: 25px;
  line-height: 16px;
  text-align: center;
}

.icon-delete {
  font-size: 11px;
  background: red;
  color: white;
  padding: 5px;
  border-radius: 50%;
  margin-right: 5px;
  width: 25px;
  height: 25px;
  line-height: 16px;
  text-align: center;
}

.table td,
.table th {
  padding: .4rem !important;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}
</style>
