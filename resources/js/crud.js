// import XLSX from 'xlsx'
// import exportFromJson from 'export-from-json'

export default {
  data() {
    return {
      tableData: [],
      loading: false,
      keyword: '',
      showForm: false,
      showDetail: false,
      formModel: {},
      selectedData: {},
      formErrors: {},
      error: {},
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

      axios.get(this.url, { params })
        .then((r) => {
          this.tableData = r.data.data
          const { from, to, total, per_page, current_page } = r.data
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

    sortChange(c) {
      if (c.prop != this.sort || c.order != this.order) {
        this.sort = c.prop;

        if (c.order == 'ascending') {
          this.order = 'asc'
        } else {
          this.order = 'desc'
        }

        this.getData()
      }
    },

    onCurrentChange(c) {
      this.pagination.current_page = c
      this.getData()
    },

    onSizeChange(s) {
      this.pagination.per_page = s
      this.getData()
    },

    // deleteMany() {
    //   this.$confirm('Anda yakin?', 'Konfirmasi', { type: 'warning' })
    //     .then(() => {
    //       const params = { ids: this.selectedData.map((d) => d.id) }

    //       axios.delete(`${this.url}/deleteMany`, { params })
    //         .then((r) => {
    //           this.$message({
    //             message: r.message,
    //             type: 'success',
    //             showClose: true,
    //           })
    //           this.getData()
    //         })
    //         .catch((e) => {
    //           this.$message({
    //             message: e.response.data.message,
    //             type: 'error',
    //             showClose: true,
    //           })
    //         })
    //     })
    //     .catch((e) => console.log(e))
    // },

    closeForm() {
      this.errors = {}
      this.showForm = false
    },

    deleteData(id) {
      this.$confirm('Anda yakin?', 'Konfirmasi', { type: 'warning' })
        .then(() => {
          axios.delete(`${this.url}/${id}`).then((r) => {
            this.$message({
              message: r.data.message,
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
      this.error = {};
			this.formErrors = {};
			this.formModel = JSON.parse(JSON.stringify(data));
      this.showForm = true;
    },

    saveData() {
      this.loading = true
      axios({
        method: this.formModel.id ? 'put' : 'post',
        url: this.formModel.id ? `${this.url}/${this.formModel.id}` : this.url,
        data: this.formModel,
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
						this.error = {};
						this.formErrors = e.response.data.errors;
					}

					if (e.response.status == 500) {
						this.formErrors = {};
						this.error = e.response.data;
					}
        })
        .finally(() => (this.loading = false))
    },
  },
}
