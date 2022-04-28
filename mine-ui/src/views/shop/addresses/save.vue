<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
        <el-form-item label="省" prop="province">
            <el-input v-model="form.province" clearable placeholder="请输入省" />
        </el-form-item>

        <el-form-item label="市" prop="city">
            <el-input v-model="form.city" clearable placeholder="请输入市" />
        </el-form-item>

        <el-form-item label="区" prop="district">
            <el-input v-model="form.district" clearable placeholder="请输入区" />
        </el-form-item>

        <el-form-item label="具体地址" prop="address">
            <el-input v-model="form.address" clearable placeholder="请输入具体地址" />
        </el-form-item>

        <el-form-item label="邮编" prop="zip">
            <el-input v-model="form.zip" clearable placeholder="请输入邮编" />
        </el-form-item>

        <el-form-item label="联系人姓名" prop="contact_name">
            <el-input v-model="form.contact_name" clearable placeholder="请输入联系人姓名" />
        </el-form-item>

        <el-form-item label="联系人电话" prop="contact_phone">
            <el-input v-model="form.contact_phone" clearable placeholder="请输入联系人电话" />
        </el-form-item>

        <el-form-item label="最后使用时间" prop="last_used_at">
            <el-input v-model="form.last_used_at" clearable placeholder="请输入最后使用时间" />
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
          add: '新增收货地址',
          edit: '编辑收货地址'
        },
        treeList: [],
        form: {
          
           id: '',
           user_id: '',
           province: '',
           city: '',
           district: '',
           address: '',
           zip: '',
           contact_name: '',
           contact_phone: '',
           last_used_at: '',
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
              res = await this.$API.shopAddresses.save(this.form)
            } else {
              res = await this.$API.shopAddresses.update(this.form.id, this.form)
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
          this.form.user_id = data.user_id;
          this.form.province = data.province;
          this.form.city = data.city;
          this.form.district = data.district;
          this.form.address = data.address;
          this.form.zip = data.zip;
          this.form.contact_name = data.contact_name;
          this.form.contact_phone = data.contact_phone;
          this.form.last_used_at = data.last_used_at;
      },

      // 获取字典数据
      getDictData() {
        
      },

      

      
    }
  }
</script>
