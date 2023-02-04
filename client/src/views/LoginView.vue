<template>
  <h1>Connexion</h1>
  <div class="form">
    <el-form label-width="120px" :label-position="'top'" :rules="rules" ref="formRef" :model="modelForm">
      <el-form-item label="Nom d'utilisateur" prop="login">
        <el-input type="text" v-model="modelForm.login" />
      </el-form-item>
      <el-form-item label="Mot de passe" prop="password">
        <el-input type="password" v-model="modelForm.password" />
      </el-form-item>
    </el-form>
    <el-button @click="login(formRef)">Connexion</el-button>
    <div id="errorMessage"></div>
    <el-alert class="alert" title="La connexion a échoué" type="error" effect="dark" center :description="errorMessage" :closable="false" v-if="error" />
  </div>
</template>

<script lang="ts">
import {Vue} from "vue-class-component";
import {reactive, ref} from "vue";
import {FormInstance, FormRules} from "element-plus";
import axios from "@/plugins/axios";
import router from "@/router";


export default class LoginView extends Vue {
  formRef = ref<FormInstance>()
  modelForm = reactive({
    login: '',
    password: ''
  })
  error: boolean = false
  errorMessage: string = ""
  rules = reactive<FormRules>({
    login: [ // Règles sur le champ login
      {required: true, message: 'Entrez un login', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: "Le nom d'utilisateur doit contenir au moins 6 caractères", trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
    ],
    password: [ // Règles sur le mot de passe
      {required: true, message: 'Entrez un mot de passe', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: 'Le mot de passe doit contenir au moins 6 caractères', trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
    ],
  })
  async login(form: FormInstance) { // Requête l'API pour se connecter si toutes les règles du formulaire sont valides
    this.error = false
    this.errorMessage = ""
    if(await form.validate()) {
      const {login, password} = this.modelForm
      try {
        const token = (await axios.post('/token', {
          login: login,
          password: password
        })).data
        localStorage.setItem('token', token)
        location.pathname = '/home'
      } catch (e : any) {
        this.error = true
        this.errorMessage = e.response.data
      }
    }
  }
}
</script>

<style scoped lang="stylus">
h1
  color #409eff

.form
  width 300px
  margin auto
  padding 3em
  border groove #409eff 3px
  border-radius 1em

.alert
  margin-top 1em
</style>
