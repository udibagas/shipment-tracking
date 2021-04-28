import XLSX from 'xlsx'
import exportFromJson from 'export-from-json'

export default {
  data() {
    return {
      tableData: [],
      loading: false,
      keyword: '',
      showForm: false,
      form: {},
      selectedData: [],
      errors: {},
      filters: {},
      sort: 'name',
      order: 'asc',
      pagination: {
        from: 0,
        to: 0,
        total: 0,
        per_page: 20,
        current_page: 1,
      },
    }
  },

  mounted() {
    this.getData()
  },

  methods: {
    getData() {
      const params = {
        keyword: this.keyword,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        paginated: true,
        ...this.filters,
      }

      this.loading = true

      this.$axios
        .$get(this.url, { params })
        .then((r) => {
          this.tableData = r.data
          const { from, to, total, per_page, current_page } = r
          this.pagination = {
            from,
            to,
            total,
            per_page: Number(per_page),
            current_page,
          }
        })
        .catch((e) => {
          this.$message({
            message: e.reponse.data.message,
            type: 'error',
            showClose: true,
          })
        })
        .finally(() => (this.loading = false))
    },

    refreshData() {
      this.keyword = ''
      this.pagination.current_page = 1
      this.getData()
    },

    onCurrentChange(c) {
      this.pagination.current_page = c
      this.getData()
    },

    onSizeChange(s) {
      this.pagination.per_page = s
      this.getData()
    },

    onSelectionChange(selection) {
      this.selectedData = selection
      console.log(selection)
    },

    deleteMany() {
      this.$confirm('Anda yakin?', 'Konfirmasi', { type: 'warning' })
        .then(() => {
          const params = { ids: this.selectedData.map((d) => d.id) }

          this.$axios
            .$delete(`${this.url}/deleteMany`, { params })
            .then((r) => {
              this.$message({
                message: r.message,
                type: 'success',
                showClose: true,
              })
              this.getData()
            })
            .catch((e) => {
              this.$message({
                message: e.response.data.message,
                type: 'error',
                showClose: true,
              })
            })
        })
        .catch((e) => console.log(e))
    },

    closeForm() {
      this.errors = {}
      this.showForm = false
    },

    deleteData(id) {
      this.$confirm('Anda yakin?', 'Konfirmasi', { type: 'warning' })
        .then(() => {
          this.$axios.$delete(`${this.url}/${id}`).then((r) => {
            this.$message({
              message: r.message,
              type: 'success',
              showClose: true,
            })
            this.getData()
          })
        })
        .catch((e) => {
          this.$message({
            message: e.response.data.message,
            type: 'error',
            showClose: true,
          })
        })
    },

    openForm(data) {
      this.form = JSON.parse(JSON.stringify(data))
      this.showForm = true
    },

    saveData() {
      this.loading = true
      this.$axios({
        method: this.form.id ? 'put' : 'post',
        url: this.form.id ? `${this.url}/${this.form.id}` : this.url,
        data: this.form,
      })
        .then((r) => {
          this.$message({
            message: r.data.message,
            type: 'success',
            showClose: true,
          })
          this.closeForm()
          this.getData()
        })
        .catch((e) => {
          if (e.response.status == 422) {
            this.errors = e.response.data.errors
          }

          this.$message({
            message: e.response.data.message,
            type: 'error',
            showClose: true,
          })
        })
        .finally(() => (this.loading = false))
    },

    exportData(fileName = 'data', exportType = 'xls') {
      const params = { keyword: this.keyword }
      this.loading = true

      this.$axios
        .$get(`${this.url}/export`, { params })
        .then((data) => {
          exportFromJson({
            data,
            fileName,
            exportType,
          })
        })
        .catch((e) => {
          this.$message({
            message: e.response.data.message,
            type: 'error',
            showClose: true,
          })
        })
        .finally(() => (this.loading = false))
    },

    importData() {
      const el = document.createElement('input')
      el.type = 'file'

      el.addEventListener('change', (event) => {
        const file = event.target.files[0]
        const reader = new FileReader()

        reader.onload = (e) => {
          var data = e.target.result
          data = new Uint8Array(data)
          var workbook = XLSX.read(data, { type: 'array' })
          // see the result, caution: it works after reader event is done.
          var res = XLSX.utils
            .sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {
              header: 1,
            })
            .filter((r) => !!r[0]) // cuma yg ada datanya

          // remove header
          res.splice(0, 1)

          // PISAH INI SESUAI TABLE
          var dataToImport = res.map((r) => {
            return {
              number: r[1],
              name: r[2] || '',
              remark: r[3] || '',
            }
          })

          console.log('raw data: ', dataToImport.length)

          this.loading = true
          this.$axios
            .$post(`${this.url}/import`, { rows: dataToImport })
            .then((r) => {
              this.$message({
                message: r.message,
                type: 'success',
                showClose: true,
              })
              this.getData()
            })
            .catch((e) => {
              this.$message({
                message: e.response.data.message,
                type: 'error',
                showClose: true,
              })
            })
            .finally(() => (this.loading = false))
        }

        reader.readAsArrayBuffer(file)
      })

      el.click()
    },
  },
}
