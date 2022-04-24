<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
        <el-form-item label="密码" prop="password">
            <el-input v-model="form.password" clearable placeholder="请输入密码" />
        </el-form-item>

        <el-form-item label="手机" prop="phone">
            <el-input v-model="form.phone" clearable placeholder="请输入手机" />
        </el-form-item>

        <el-form-item label="用户邮箱" prop="email">
            <el-input v-model="form.email" clearable placeholder="请输入用户邮箱" />
        </el-form-item>

        <el-form-item label="用户头像" prop="avatar">
            <el-input v-model="form.avatar" clearable placeholder="请输入用户头像" />
        </el-form-item>

        <el-form-item label="状态 (0正常 1停用)" prop="status">
            <el-input v-model="form.status" clearable placeholder="请输入状态 (0正常 1停用)" />
        </el-form-item>

        <el-form-item label="最后登陆IP" prop="login_ip">
            <el-input v-model="form.login_ip" clearable placeholder="请输入最后登陆IP" />
        </el-form-item>

        <el-form-item label="最后登陆时间" prop="login_time">
            <el-input v-model="form.login_time" clearable placeholder="请输入最后登陆时间" />
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
          add: '新增用户',
          edit: '编辑用户'
        },
        treeList: [],
        form: {
          
           id: '',
           username: '',
           password: '',
           phone: '',
           email: '',
           avatar: '',
           status: '',
           login_ip: '',
           login_time: '',
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
              res = await this.$API.shopUsers.save(this.form)
            } else {
              res = await this.$API.shopUsers.update(this.form.id, this.form)
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
          this.form.username = data.username;
          this.form.password = data.password;
          this.form.phone = data.phone;
          this.form.email = data.email;
          this.form.avatar = data.avatar;
          this.form.status = data.status;
          this.form.login_ip = data.login_ip;
          this.form.login_time = data.login_time;
      },

      // 获取字典数据
      getDictData() {
        
      },

      

      
    }
  }
</script>
