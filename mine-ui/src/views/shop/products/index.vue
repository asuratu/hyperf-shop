<template>
  <el-container>
    <el-header class="mine-el-header">
      <div class="panel-container">
        <div class="left-panel">
          <el-button
            icon="el-icon-plus"
            v-auth="['shop:products:save']"
            type="primary"
            @click="add"
          >新增</el-button>

          <el-button
            type="danger"
            plain
            icon="el-icon-delete"
            v-auth="['shop:products:delete']"
            :disabled="selection.length==0"
            @click="batchDel"
          >删除</el-button>

        </div>
        <div class="right-panel">
          <div class="right-panel-search">

            <el-input v-model="queryParams.title" placeholder="商品名称" clearable></el-input>

            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
              <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
              <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
            </el-tooltip>

            <el-button type="text" @click="toggleFilterPanel">
              {{ povpoerShow ? '关闭更多筛选' : '显示更多筛选'}}
              <el-icon><el-icon-arrow-down v-if="povpoerShow" /><el-icon-arrow-up v-else /></el-icon>
            </el-button>
          </div>
        </div>
      </div>
      <el-card class="filter-panel" shadow="never">
        <el-form label-width="80px" :inline="true">
          
            <el-form-item label="商品详情" prop="description">
                <el-input v-model="queryParams.description" placeholder="商品详情" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="商品封面图片文件路径" prop="image">
                <el-input v-model="queryParams.image" placeholder="商品封面图片文件路径" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="商品是否正在售卖" prop="on_sale">
                <el-input v-model="queryParams.on_sale" placeholder="商品是否正在售卖" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="商品平均评分" prop="rating">
                <el-input v-model="queryParams.rating" placeholder="商品平均评分" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="销量" prop="sold_count">
                <el-input v-model="queryParams.sold_count" placeholder="销量" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="评价数量" prop="review_count">
                <el-input v-model="queryParams.review_count" placeholder="评价数量" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="SKU 最低价格" prop="price">
                <el-input v-model="queryParams.price" placeholder="SKU 最低价格" clearable></el-input>
            </el-form-item>
        
        </el-form>
      </el-card>
    </el-header>
    
    <el-main class="nopadding">
      <maTable
        ref="table"
        :api="api"
        :column="column"
        :showRecycle="true"
        row-key="id"
        :hidePagination="false"
        @selection-change="selectionChange"
        @switch-data="switchData"
        stripe
        remoteSort
      >
        <el-table-column type="selection" width="50"></el-table-column>

        
        <el-table-column
           label="商品名称"
           prop="title"
        />
        <el-table-column
           label="商品详情"
           prop="description"
        />
        <el-table-column
           label="商品封面图片文件路径"
           prop="image"
        />
        <el-table-column
           label="商品是否正在售卖"
           prop="on_sale"
        />
        <el-table-column
           label="商品平均评分"
           prop="rating"
        />
        <el-table-column
           label="销量"
           prop="sold_count"
        />
        <el-table-column
           label="评价数量"
           prop="review_count"
        />
        <el-table-column
           label="SKU 最低价格"
           prop="price"
        />

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['shop:products:update']"
            >编辑</el-button>

            <el-button
              type="text"
              size="small"
              @click="deletes(scope.row.id)"
              v-auth="['shop:products:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['shop:products:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['shop:products:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>

</template>

<script>
  import saveDialog from './save'

  export default {
    name: 'shop:products',
    components: {
      saveDialog
    },

    async created() {
        await this.getDictData();
    },

    data() {
      return {
        dialog: {
          save: false
        },
        
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.shopProducts.getList,
          recycleList: this.$API.shopProducts.getRecycleList,
        },
        selection: [],
        queryParams: {
            
          title: undefined,
          description: undefined,
          image: undefined,
          on_sale: undefined,
          rating: undefined,
          sold_count: undefined,
          review_count: undefined,
          price: undefined,
        },
        isRecycle: false,
        
      }
    },
    methods: {

      //添加
      add(){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open()
        })
      },

      //编辑
      tableEdit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').setData(row)
        })
      },

      //查看
      tableShow(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('show').setData(row)
        })
      },

      //批量删除
      async batchDel(){
        await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消',
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          if (this.isRecycle) {
            this.$API.shopProducts.realDeletes(ids.join(',')).then(res => {
              if(res.success) {
                this.$message.success(res.message)
                this.$refs.table.upData(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
          } else {
            this.$API.shopProducts.deletes(ids.join(',')).then(res => {
              if(res.success) {
                this.$message.success(res.message)
                this.$refs.table.upData(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
          }
          loading.close();

        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消',
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.shopProducts.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.shopProducts.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.shopProducts.recoverys(id).then(res => {
          this.$message.success(res.message)
          this.$refs.table.upData(this.queryParams)
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      // 选择时间事件
      handleDateChange (values) {
        if (values !== null) {
          this.queryParams.minDate = values[0]
          this.queryParams.maxDate = values[1]
        }
      },

      toggleFilterPanel() {
        this.povpoerShow = ! this.povpoerShow
        document.querySelector('.filter-panel').style.display = this.povpoerShow ? 'block' : 'none'
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      resetSearch() {
        this.queryParams = {
          
          title: undefined,
          description: undefined,
          image: undefined,
          on_sale: undefined,
          rating: undefined,
          sold_count: undefined,
          review_count: undefined,
          price: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      },

      // 获取字典数据
      getDictData() {
        
      },

      // 标签页查询
      handleTabsClick(tab, event) {
      	
      }
    }
  }
</script>
<style>
.el-tabs__item {
	padding: 0 20px;
	height: 44px;
	box-sizing: border-box;
	/* line-height: 40px; */
	display: inline-block;
	list-style: none;
	font-size: 14px;
	font-weight: 500;
	color: var(--el-text-color-primary);
	position: relative;
	margin-top: 10px;
}
</style>