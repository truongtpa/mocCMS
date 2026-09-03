<template>
  <LTEContentWrapper>
    <template #content>
      <!-- STATS SUMMARY CARDS -->
      <div class="row mb-3">
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box bg-light elevation-1">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-history"></i></span>
            <div class="info-box-content">
              <span class="info-box-text text-muted">Tổng nhật ký thao tác</span>
              <span class="info-box-number text-dark font-weight-bold">{{ tableLogs.pagination.total || 0 }}</span>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box bg-light elevation-1">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
              <span class="info-box-text text-muted">Bản sao lưu thuộc tính</span>
              <span class="info-box-number text-dark font-weight-bold">{{ tableBackups.pagination.total || 0 }}</span>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box bg-light elevation-1">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-trash-restore"></i></span>
            <div class="info-box-content">
              <span class="info-box-text text-muted">Có thể khôi phục</span>
              <span class="info-box-number text-dark font-weight-bold">{{ countPendingBackup }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <LTECard title="Nhật ký thao tác & Sao lưu hệ thống">
            <template #content>
              <!-- TABS HEADER -->
              <ul class="nav nav-tabs mb-3" id="logTab" role="tablist">
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: activeTab === 'logs' }"
                    href="#"
                    @click.prevent="activeTab = 'logs'"
                  >
                    <i class="fas fa-history mr-1"></i> Nhật ký thao tác
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link"
                    :class="{ active: activeTab === 'backups' }"
                    href="#"
                    @click.prevent="activeTab = 'backups'"
                  >
                    <i class="fas fa-trash-restore mr-1"></i> Sao lưu thuộc tính & Khôi phục
                  </a>
                </li>
              </ul>

              <!-- TAB 1: NHẬT KÝ THAO TÁC -->
              <div v-if="activeTab === 'logs'">
                <div class="row mb-3 align-items-center">
                  <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                      <input
                        type="text"
                        class="form-control"
                        v-model="searchLog"
                        @keyup.enter="getLogs()"
                        placeholder="Nhập từ khóa tìm kiếm nhật ký..."
                      />
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="button" @click="getLogs()">
                          <i class="fas fa-search"></i> Tìm
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" v-model="filterAction" @change="getLogs()">
                      <option value="">-- Tất cả hành động --</option>
                      <option value="THEM">Thêm mới</option>
                      <option value="SUA">Cập nhật / Sửa</option>
                      <option value="XOA">Xóa dữ liệu</option>
                      <option value="KHOI_PHUC">Khôi phục</option>
                      <option value="SAP_XEP">Sắp xếp</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-4 text-md-right">
                    <button class="btn btn-sm btn-default" @click="resetLogFilter">
                      <i class="fas fa-sync-alt mr-1"></i> Làm mới
                    </button>
                  </div>
                </div>

                <AppTable
                  :columns="columnsLogs"
                  :items="tableLogs.list"
                  item-key="id"
                >
                  <template #col-hanh_dong="{ item }">
                    <span class="badge" :class="getBadgeClass(item.hanh_dong)">
                      {{ item.hanh_dong }}
                    </span>
                  </template>

                  <template #actions="{ item }">
                    <button class="btn btn-xs btn-info" @click="xemChiTietLog(item)" title="Xem chi tiết nhật ký">
                      <i class="fas fa-eye"></i> Chi tiết
                    </button>
                  </template>
                </AppTable>

                <!-- PAGINATION LOGS -->
                <div class="d-flex justify-content-between align-items-center mt-3" v-if="tableLogs.pagination.total">
                  <div class="text-muted text-xs">
                    Hiển thị <b>{{ tableLogs.pagination.from || 0 }}</b> - <b>{{ tableLogs.pagination.to || 0 }}</b> trên tổng <b>{{ tableLogs.pagination.total }}</b> bản ghi
                  </div>
                  <ul class="pagination pagination-sm m-0">
                    <li class="page-item" :class="{ disabled: !tableLogs.pagination.prev_page_url }">
                      <a class="page-link" href="#" @click.prevent="getLogs(tableLogs.pagination.prev_page_url)">&laquo;</a>
                    </li>
                    <li class="page-item disabled">
                      <span class="page-link text-dark font-weight-bold">Trang {{ tableLogs.pagination.current_page }} / {{ tableLogs.pagination.last_page }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: !tableLogs.pagination.next_page_url }">
                      <a class="page-link" href="#" @click.prevent="getLogs(tableLogs.pagination.next_page_url)">&raquo;</a>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- TAB 2: SAO LƯU THUỘC TÍNH & KHÔI PHỤC -->
              <div v-if="activeTab === 'backups'">
                <div class="row mb-3 align-items-center">
                  <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                      <input
                        type="text"
                        class="form-control"
                        v-model="searchBackup"
                        @keyup.enter="getBackups()"
                        placeholder="Nhập từ khóa mã/tên thuộc tính đã xóa..."
                      />
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="button" @click="getBackups()">
                          <i class="fas fa-search"></i> Tìm
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-7 text-md-right mt-2 mt-md-0">
                    <button class="btn btn-sm btn-default" @click="getBackups()">
                      <i class="fas fa-sync-alt mr-1"></i> Nạp lại danh sách
                    </button>
                  </div>
                </div>

                <AppTable
                  :columns="columnsBackups"
                  :items="tableBackups.list"
                  item-key="id"
                >
                  <template #col-trang_thai="{ item }">
                    <span v-if="item.trang_thai === 1" class="badge badge-warning">
                      <i class="fas fa-trash-alt"></i> Đã xóa (Có thể khôi phục)
                    </span>
                    <span v-else class="badge badge-success">
                      <i class="fas fa-check-circle"></i> Đã khôi phục
                    </span>
                  </template>

                  <template #actions="{ item }">
                    <button class="btn btn-xs btn-info mr-1" @click="xemChiTietBackup(item)" title="Xem dữ liệu sao lưu">
                      <i class="fas fa-eye"></i> Xem
                    </button>
                    <button
                      v-if="item.trang_thai === 1"
                      class="btn btn-xs btn-success"
                      @click="khoiPhuc(item)"
                      title="Khôi phục thuộc tính này"
                    >
                      <i class="fas fa-undo"></i> Khôi phục
                    </button>
                    <span v-else class="text-muted text-xs">Đã hoàn tác</span>
                  </template>
                </AppTable>

                <!-- PAGINATION BACKUPS -->
                <div class="d-flex justify-content-between align-items-center mt-3" v-if="tableBackups.pagination.total">
                  <div class="text-muted text-xs">
                    Hiển thị <b>{{ tableBackups.pagination.from || 0 }}</b> - <b>{{ tableBackups.pagination.to || 0 }}</b> trên tổng <b>{{ tableBackups.pagination.total }}</b> bản ghi
                  </div>
                  <ul class="pagination pagination-sm m-0">
                    <li class="page-item" :class="{ disabled: !tableBackups.pagination.prev_page_url }">
                      <a class="page-link" href="#" @click.prevent="getBackups(tableBackups.pagination.prev_page_url)">&laquo;</a>
                    </li>
                    <li class="page-item disabled">
                      <span class="page-link text-dark font-weight-bold">Trang {{ tableBackups.pagination.current_page }} / {{ tableBackups.pagination.last_page }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: !tableBackups.pagination.next_page_url }">
                      <a class="page-link" href="#" @click.prevent="getBackups(tableBackups.pagination.next_page_url)">&raquo;</a>
                    </li>
                  </ul>
                </div>
              </div>
            </template>
          </LTECard>
        </div>
      </div>
    </template>
  </LTEContentWrapper>

  <!-- MODAL XEM CHI TIẾT NHẬT KÝ / SAO LƯU -->
  <LTEModal ref="mdChiTiet" :show-save-button="false" size="lg">
    <template v-if="selectedItem">
      <div class="table-responsive">
        <table class="table table-bordered table-sm text-xs">
          <tbody>
            <tr v-for="(val, key) in selectedDetailPairs" :key="key">
              <th style="width: 180px; background-color: #f8f9fa;">{{ key }}</th>
              <td>
                <template v-if="isJsonString(val)">
                  <pre class="bg-light p-2 rounded mb-0 text-xs" style="max-height: 250px; overflow-y: auto;"><code>{{ formatJson(val) }}</code></pre>
                </template>
                <template v-else>
                  {{ val }}
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </LTEModal>
</template>

<script>
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue'
import LTECard from '@/components/controls/LTECard.vue'
import AppTable from '@/components/controls/AppTable.vue'
import LTEModal from '@/components/controls/LTEModal.vue'

export default {
  name: 'NhatKyVaSaoLuu',
  components: {
    LTEContentWrapper,
    LTECard,
    AppTable,
    LTEModal
  },
  data() {
    return {
      activeTab: 'logs',
      searchLog: '',
      searchBackup: '',
      filterAction: '',
      selectedItem: null,
      selectedDetailPairs: {},
      columnsLogs: [
        { key: 'ngay_tao', label: 'Thời gian', width: '150px' },
        { key: 'ten_dang_nhap', label: 'Người thực hiện', width: '160px' },
        { key: 'hanh_dong', label: 'Hành động', width: '160px' },
        { key: 'bang_tac_dong', label: 'Bảng tác động', width: '130px' },
        { key: 'mo_ta', label: 'Chi tiết mô tả' },
        { key: 'ip_address', label: 'Địa chỉ IP', width: '110px' }
      ],
      columnsBackups: [
        { key: 'ngay_xoa', label: 'Thời gian xóa', width: '150px' },
        { key: 'nguoi_xoa', label: 'Người xóa', width: '170px' },
        { key: 'ma_truong', label: 'Mã thuộc tính', width: '140px' },
        { key: 'ten_truong', label: 'Tên thuộc tính', width: '190px' },
        { key: 'trang_thai', label: 'Trạng thái', width: '180px' }
      ],
      tableLogs: {
        pagination: {},
        list: []
      },
      tableBackups: {
        pagination: {},
        list: []
      }
    }
  },
  computed: {
    countPendingBackup() {
      if (!this.tableBackups.list) return 0
      return this.tableBackups.list.filter((b) => b.trang_thai === 1).length
    }
  },
  mounted() {
    this.getLogs()
    this.getBackups()
  },
  methods: {
    getLogs(url) {
      const targetUrl = typeof url === 'string' ? url : window.route('DynamicObjectController.getDsNhatKy')
      const params = {
        s: this.searchLog,
        hanh_dong: this.filterAction
      }
      this.$axios.get(targetUrl, { params }).then((res) => {
        if (res.data && res.data.status === 200) {
          const rData = res.data.data
          this.tableLogs.list = rData.data.map((item) => ({
            raw: item,
            id: item.id,
            ngay_tao: this.$func ? this.$func.formatDate(item.ngay_tao) : item.ngay_tao,
            ten_dang_nhap: item.ten_dang_nhap || 'Hệ thống',
            hanh_dong: item.hanh_dong,
            bang_tac_dong: item.bang_tac_dong || '-',
            mo_ta: item.mo_ta,
            ip_address: item.ip_address || '-'
          }))
          this.tableLogs.pagination = rData
        }
      })
    },
    resetLogFilter() {
      this.searchLog = ''
      this.filterAction = ''
      this.getLogs()
    },
    getBackups(url) {
      const targetUrl = typeof url === 'string' ? url : window.route('DynamicObjectController.getDsSaoLuuThuocTinh')
      this.$axios.get(targetUrl, { params: { s: this.searchBackup } }).then((res) => {
        if (res.data && res.data.status === 200) {
          const rData = res.data.data
          this.tableBackups.list = rData.data.map((item) => ({
            raw: item,
            id: item.id,
            ngay_xoa: this.$func ? this.$func.formatDate(item.ngay_xoa) : item.ngay_xoa,
            nguoi_xoa: item.nguoi_xoa || 'Admin',
            ma_truong: item.ma_truong,
            ten_truong: item.ten_truong,
            trang_thai: item.trang_thai
          }))
          this.tableBackups.pagination = rData
        }
      })
    },
    async khoiPhuc(backupItem) {
      if (!confirm(`Bạn có chắc chắn muốn khôi phục thuộc tính "${backupItem.ten_truong}" cùng toàn bộ dữ liệu đã nhập trước đây không?`)) {
        return
      }

      const url = window.route('DynamicObjectController.khoiPhucThuocTinh', { id: backupItem.id })
      await this.$axios.post(url).then((res) => {
        if (res.data && res.data.status === 200) {
          if (this.$func && this.$func.toastSuccess) {
            this.$func.toastSuccess(res.data.message)
          }
          this.getBackups()
          this.getLogs()
        } else {
          if (this.$func && this.$func.toastError) {
            this.$func.toastError(res.data)
          }
        }
      })
    },
    xemChiTietLog(item) {
      const raw = item.raw || item
      this.selectedItem = raw
      this.selectedDetailPairs = {
        'Mã định danh nhật ký': raw.id,
        'Thời gian thực hiện': item.ngay_tao,
        'Tài khoản người thực hiện': raw.ten_dang_nhap || 'Hệ thống',
        'Địa chỉ IP': raw.ip_address || 'N/A',
        'Hành động': raw.hanh_dong,
        'Bảng tác động': raw.bang_tac_dong || 'N/A',
        'Mã bản ghi tác động': raw.id_ban_ghi || raw.ban_ghi_id || 'N/A',
        'Mô tả chi tiết': raw.mo_ta,
        'Dữ liệu trước thao tác (Dữ liệu cũ)': raw.du_lieu_cu || 'Không có (Thao tác thêm mới)',
        'Dữ liệu sau thao tác (Dữ liệu mới)': raw.du_lieu_moi || 'Không có'
      }
      this.$refs.mdChiTiet.$data.title = 'Chi tiết Nhật ký Thao tác #' + raw.id
      this.$refs.mdChiTiet.openModal()
    },
    xemChiTietBackup(item) {
      const raw = item.raw || item
      this.selectedItem = raw
      let valueCount = 0
      try {
        const valArr = JSON.parse(raw.values_data || '[]')
        valueCount = Array.isArray(valArr) ? valArr.length : 0
      } catch (e) {
        valueCount = 0
      }

      this.selectedDetailPairs = {
        'Mã định danh bản sao lưu': raw.id,
        'Tên thuộc tính đã xóa': raw.ten_truong,
        'Mã thuộc tính': raw.ma_truong,
        'Người thực hiện xóa': raw.nguoi_xoa || 'Admin',
        'Thời gian xóa': item.ngay_xoa,
        'Số lượng giá trị đã lưu': valueCount + ' bản ghi sinh viên',
        'Trạng thái': raw.trang_thai === 1 ? 'Đã xóa (Có thể khôi phục)' : 'Đã khôi phục',
        'Cấu hình thuộc tính (field_data)': raw.field_data,
        'Dữ liệu đã nhập (values_data)': raw.values_data
      }
      this.$refs.mdChiTiet.$data.title = 'Chi tiết Bản Sao lưu Thuộc tính #' + raw.id
      this.$refs.mdChiTiet.openModal()
    },
    getBadgeClass(action) {
      if (!action) return 'badge-secondary'
      if (action.includes('XOA')) return 'badge-danger'
      if (action.includes('KHOI_PHUC')) return 'badge-success'
      if (action.includes('THEM')) return 'badge-info'
      if (action.includes('SUA')) return 'badge-warning'
      return 'badge-secondary'
    },
    isJsonString(val) {
      if (!val || typeof val !== 'string') return false
      const trimmed = val.trim()
      return (trimmed.startsWith('{') && trimmed.endsWith('}')) || (trimmed.startsWith('[') && trimmed.endsWith(']'))
    },
    formatJson(val) {
      try {
        return JSON.stringify(JSON.parse(val), null, 2)
      } catch (e) {
        return val
      }
    }
  }
}
</script>

<style scoped>
.nav-tabs .nav-link {
  color: #495057;
  font-weight: 600;
}
.nav-tabs .nav-link.active {
  color: #007bff;
  border-bottom: 2px solid #007bff;
}
.info-box {
  border-radius: 6px;
}
</style>
