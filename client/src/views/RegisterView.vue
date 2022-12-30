<template>
  <div>
    <h1>Inscription</h1>
    <el-steps align-center :active="active">
      <el-step title="Identifiants" />
      <el-step title="Profil" />
    </el-steps>
    <div class="form">
      <template v-if="active === 0">
        <el-form label-width="120px" :label-position="'top'" :rules="rules1" ref="formRef" :model="modelForm">
          <el-form-item label="Nom d'utilisateur" prop="login">
            <el-input v-model="modelForm.login"/>
          </el-form-item>
          <el-form-item label="Mot de passe" prop="password">
            <el-input type="password" v-model="modelForm.password"></el-input>
          </el-form-item>
          <el-form-item label="Confirmer" prop="confirm">
            <el-input type="password" v-model="modelForm.confirm"></el-input>
          </el-form-item>
          <el-form-item label="Adresse mail" prop="email">
            <el-input type="email" v-model="modelForm.email"></el-input>
          </el-form-item>
        </el-form>
        <el-button @click="submit(formRef)"> Suivant</el-button>
      </template>
      <template v-if="active === 1">
        <img v-bind:src="'profiles/' + user.idAvatar + '.png'" width="100">
        <p>Sélectionnez un avatar</p>
        <div style="width: 600px">
          <el-row class="profiles">
          <el-col :span="4" v-for="id in listId">
            <img v-bind:src="'profiles/' + id + '.png'" width="100" @click="updateIdAvatar(id)">
          </el-col>
        </el-row>
        </div>

        <a href="https://fr.vecteezy.com">Avatar Vecteurs par Vecteezy</a>
        <el-divider />
        <el-form :label-position="'top'" :rules="rules2" ref="formRef2" :model="modelForm2">
          <el-form-item class="description" label="Description" prop="description">
            <el-input v-model="modelForm2.description" :rows="3" type="textarea" placeholder="Entrez une description" />
          </el-form-item>
          <el-form-item label="Date de naissance" prop="birth">
            <el-date-picker v-model="modelForm2.birth" type="date" placeholder="Entrez une date"/>
          </el-form-item>
          <el-form-item label="Prenom" prop="firstname">
            <el-input v-model="modelForm2.firstname" />
          </el-form-item>
          <el-form-item label="Nom" prop="lastname">
            <el-input v-model="modelForm2.lastname" />
          </el-form-item>
        </el-form>

        <div class="submit">
          <el-button @click="back"> Retour</el-button>
          <el-button @click="submit(formRef2)"> Valider</el-button>
        </div>
      </template>
      <template v-if="active === 2">
        <p>TODO</p>
        <div class="submit">
          <el-button @click="back"> Retour</el-button>
          <el-button disabled> Inscription</el-button>
        </div>
      </template>
    </div>
  </div>
</template>


<script lang="ts">

import { Vue } from 'vue-class-component'
import {User} from '@/models/User'
import {reactive, ref} from "vue";
import {FormInstance, FormRules } from 'element-plus';

export default class RegisterView extends Vue {
  active: number = 0
  user: User = new User({idAvatar: 0})
  listId = [0,1,2,3,4,5,6,7,8,9,10,11]
  formRef = ref<FormInstance>()
  formRef2 = ref<FormInstance>()
  modelForm = reactive({
    login: '',
    password: '',
    confirm: '',
    email: ''
  })
  modelForm2 = reactive({
    idAvatar: 0,
    description: '',
    birth: '',
    firstname: '',
    lastname: ''
  })
  checkLogin = (rule: any, value: any, callback: any) => {
    const logins = ['login1', "pseudo1"]
    const error = logins.includes(this.modelForm.login) ? new Error("Ce nom d'utilisateur n'est pas disponible") : undefined
    callback(error)
  }
  verifyConfirmPassword = (rule: any, value: any, callback: any) => {
    const { password, confirm } = this.modelForm;
    if(!password || !confirm)
      callback()
    else if(password !== confirm)
      callback(new Error("Les deux mots de passe ne correspondent pas"))
    else
      callback()
  }
  checkEmail = (rule: any, value: any, callback: any) => {
    const emails = ['mail1@mail.com', "second@adress.fr"]
    const error = emails.includes(this.modelForm.email) ? new Error("Cette adresse mail est déjà utilisée") : undefined
    callback(error)
  }
  rules1 = reactive<FormRules>({
    login: [
      {required: true, message: 'Entrez un login', trigger: 'blur'},
      {min: 6, message: "Le nom d'utilisateur doit contenir au moins 6 caractères", trigger: 'blur'},
      {validator: this.checkLogin, trigger: 'blur'}
    ],
    password: [
      {required: true, message: 'Entrez un mot de passe', trigger: 'blur'},
      {min: 6, message: 'Le mot de passe doit contenir au moins 6 caractères', trigger: 'blur'},
      {validator: this.verifyConfirmPassword, trigger: 'blur'}
    ],
    confirm: [
      {required: true, message: 'Confirmer votre mot de passe', trigger: 'blur'},
      {min: 6, message: 'Le mot de passe doit contenir au moins 6 caractères', trigger: 'blur'},
      {validator: this.verifyConfirmPassword, trigger: 'blur'}
    ],
    email: [
      {required: true, message: 'Entrez une adresse mail', trigger: 'blur'},
      {type: "email", message: 'Entrez une adresse mail valide', trigger: 'blur'},
      {validator: this.checkEmail, trigger: 'blur'}
    ]
  })
  rules2 = reactive<FormRules>({
    birth: [
      {required: true, message: 'Entrez une date de naissance', trigger: 'blur'}
    ]
  })

  submit(form: FormInstance) {
    if(!form) return
    form.validate(valid => {
      if(valid) {
        if(this.active === 0) {
          this.active++
          this.user.login = this.modelForm.login
          this.user.email = this.modelForm.email
          this.user.password = this.modelForm.password
        } else {
          this.user.firstname = this.modelForm2.firstname
          this.user.lastname = this.modelForm2.lastname
          this.user.description = this.modelForm2.description
          this.user.birth = this.modelForm2.birth
          this.$router.push('/login')
        }
      }
    })
  }
  back() {
    this.active--
  }
  updateIdAvatar(id: number) {
    this.user.idAvatar = id
  }
}

</script>

<style scoped lang="stylus">
.form
  width fit-content
  min-width 300px
  margin auto
  border solid grey 1px
  border-radius 1em
  padding 3em

a
  color white

.submit
  margin-top 1em

.profiles
  margin-bottom 1em

.description
  margin-top 1em
</style>
