<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
        <el-form-item label="商品名称" prop="title">
            <el-input v-model="form.title" clearable placeholder="请输入商品名称" />
        </el-form-item>

        <el-form-item label="商品详情" prop="description">
            <el-input v-model="form.description" clearable placeholder="请输入商品详情" />
        </el-form-item>

        <el-form-item label="商品封面图片文件路径" prop="image">
            <el-input v-model="form.image" clearable placeholder="请输入商品封面图片文件路径" />
        </el-form-item>

        <el-form-item label="商品是否正在售卖" prop="on_sale">
            <el-input v-model="form.on_sale" clearable placeholder="请输入商品是否正在售卖" />
        </el-form-item>

        <el-form-item label="商品平均评分" prop="rating">
            <el-input v-model="form.rating" clearable placeholder="请输入商品平均评分" />
        </el-form-item>

        <el-form-item label="销量" prop="sold_count">
            <el-input v-model="form.sold_count" clearable placeholder="请输入销量" />
        </el-form-item>

        <el-form-item label="评价数量" prop="review_count">
            <el-input v-model="form.review_count" clearable placeholder="请输入评价数量" />
        </el-form-item>

        <el-form-item label="SKU 最低价格" prop="price">
            <el-input v-model="form.price" clearable placeholder="请输入SKU 最低价格" />
        </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="visible=false" >取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script>
  import editor from '@/components/scEditor'

  export default {
    emits: ['success', 'closed'],
    components: {
      editor
    },
    data() {
      return {
        mode: "add",
        titleMap: {
          add: '新增商品',
          edit: '编辑商品'
        },
        treeList: [],
        form: {
          
           id: '',
           title: '',
           description: '',
           image: '',
           on_sale: '',
           rating: '',
           sold_count: '',
           review_count: '',
           price: '',
        },
        rules: {
          
        },
        visible: false,
        isSaveing: false,
        
      }
    },
    async created() {
        await this.getDictData();
    },
    methods: {
      //显示
      open(mode='add'){
        this.mode = mode;
        this.visible = true;
        
        return this;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode == 'add') {
              res = await this.$API.shopProducts.save(this.form)
            } else {
              res = await this.$API.shopProducts.update(this.form.id, this.form)
            }
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form, this.mode)
              this.visible = false;
              this.$message.success(res.message)
            }else{
              this.$alert(res.message, "提示", {type: 'error'})
            }
          }
        })
      },

      //表单注入数据
      setData(data){
        
          this.form.id = data.id;
          this.form.title = data.title;
          this.form.description = data.description;
          this.form.image = data.image;
          this.form.on_sale = data.on_sale;
          this.form.rating = data.rating;
          this.form.sold_count = data.sold_count;
          this.form.review_count = data.review_count;
          this.form.price = data.price;
      },

      // 获取字典数据
      getDictData() {
        
      },

      

      
    }
  }
</script>
