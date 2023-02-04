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
        <AvatarSelector v-bind:selectedId="this.user.idAvatar" v-bind:listId="listId" v-on:update:selectedId="updateIdAvatar" />
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
    </div>
  </div>
</template>


<script lang="ts">

import {Options, Vue } from 'vue-class-component'
import {User} from '@/models/User'
import {reactive, ref} from "vue";
import {FormInstance, FormRules } from 'element-plus';
import axios from "@/plugins/axios";

import AvatarSelector from "@/components/AvatarSelectorComponent.vue";

@Options({
  components: {
    AvatarSelector
  },
})
export default class RegisterView extends Vue {
  active: number = 0 // l'étape actuelle de l'inscription
  user: User = new User({idAvatar: 0})
  usersList: User[] = [] // Contient la liste des utilisateurs
  listId = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15] // Liste des identifiants des avatars
  formRef = ref<FormInstance>()
  formRef2 = ref<FormInstance>()
  modelForm = reactive({ // Modèle du formulaire de l'étape 0
    login: '',
    password: '',
    confirm: '',
    email: ''
  })
  modelForm2 = reactive({ // Modèle du formulaire de l'étape 1
    idAvatar: 0,
    description: '',
    birth: '',
    firstname: '',
    lastname: ''
  })

  async mounted() { // Initialisation des variables
    this.usersList = (await axios.get('/users')).data // Requête de l'API pour initialiser la liste des utilisateurs
  }
  checkLogin = (rule: any, value: any, callback: any) => { // Règles vérifiant que le nom d'utilisateur renseigné n'existe pas
    const logins: string[] = this.usersList.map(user => user.login) // recupère la liste des noms d'utilisateurs
    const error = logins.includes(this.modelForm.login) ? new Error("Ce nom d'utilisateur n'est pas disponible") : undefined // Crée une erreur si le nom d'utilisateur est déjà pris
    callback(error)
  }
  verifyConfirmPassword = (rule: any, value: any, callback: any) => { // Régle vérifiant que le mot de passe renseigné correspond à la confirmation de mot de passe
    const { password, confirm } = this.modelForm; // Recupère le mot de passe et sa confirmation
    if(!password || !confirm)
      callback()
    else if(password !== confirm) // Si les deux champs ont été remplis et sont différents
      callback(new Error("Les deux mots de passe ne correspondent pas")) // Affichage d'une erreur
    else
      callback()
  }
  checkEmail = (rule: any, value: any, callback: any) => { // Règle vérifiant que l'adresse mail n'est pas déjà utilisée
    const emails: string[] = this.usersList.map(user => user.email) // Récupère la liste des adresse mails
    const error = emails.includes(this.modelForm.email) ? new Error("Cette adresse mail est déjà utilisée") : undefined // Crée une erreur si l'adresse mail est déjà pris
    callback(error)
  }
  rules1 = reactive<FormRules>({ // Liste des règles du premier formulaire
    login: [ // Règles sur le champ login
      {required: true, message: 'Entrez un login', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: "Le nom d'utilisateur doit contenir au moins 6 caractères", trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
      {validator: this.checkLogin, trigger: 'blur'} // Vérifie que le nom d'utilisateur n'est pas déjà pris
    ],
    password: [ // Règles sur le mot de passe
      {required: true, message: 'Entrez un mot de passe', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: 'Le mot de passe doit contenir au moins 6 caractères', trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
      {validator: this.verifyConfirmPassword, trigger: 'blur'} // Vérifie que le mot de passe et sa confirmation sont identiques
    ],
    confirm: [ // Règles sur la confirmation du mot de passe
      {required: true, message: 'Confirmer votre mot de passe', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: 'Le mot de passe doit contenir au moins 6 caractères', trigger: 'blur'}, // Vérifie que sa longeuur est d'au moins 6 caractères
      {validator: this.verifyConfirmPassword, trigger: 'blur'} // Vérifie que le mot de passe et sa confirmation sont identiques
    ],
    email: [ // Rèfles sur l'adresse mail
      {required: true, message: 'Entrez une adresse mail', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {type: "email", message: 'Entrez une adresse mail valide', trigger: 'blur'}, // Vérifie que le champ a le format d'une adresse mail
      {validator: this.checkEmail, trigger: 'blur'} // Vérifie que l'adresse mail n'est pas déjà utilisée
    ]
  })
  rules2 = reactive<FormRules>({ // Règle du second formaulaire
    birth: [ // Règle sur la date de naissance
      {required: true, message: 'Entrez une date de naissance', trigger: 'blur'} // Vérifie que le champ n'est pas vide
    ]
  })
  async submit(form: FormInstance) { // Appelé lors de la soumission d'un formulaire
    if(!form) return
    if(await form.validate()) { // Si le formulaire est valide (toutes les règles du formulaire sont respectées).
      if(this.active === 0) { // Si le formulaire validé est le premier
        this.active++ // Incrémentation du numéro de formulaire actif
        this.user.login = this.modelForm.login // Initialisation des champs du formulaire dans l'utilisateur à créer
        this.user.email = this.modelForm.email
        this.user.password = this.modelForm.password
      } else {
        this.user.firstname = this.modelForm2.firstname // Initialisation des champs du formulaire
        this.user.lastname = this.modelForm2.lastname
        this.user.description = this.modelForm2.description
        this.user.birth = this.modelForm2.birth
        const data = {...this.user} // Initialisation des données de la requête avec les attributs de l'utilisateur
        const token = await (await axios.post('/users', data)).data // Requête API pour créer un nouvel utilisateur
        localStorage.setItem('token', token) // Connecte l'utilisateur
        location.pathname = '/rules' // Redirige vers la page affichant les règles
      }
    }
  }
  back() { // Si l'utilisateur appuis sur le bouton de retour
    this.active-- // Affichage de l'étape précédant du formulaire
  }
  updateIdAvatar(id: number) { // Si l'utilisateur clique sur un avatar
    this.user.idAvatar = id // Mise à jour de l'id de l'avatar
  }
}

</script>

<style scoped lang="stylus">
h1
  color #409eff

.form
  width 550px
  margin auto
  border groove #409eff 3px
  border-radius 1em
  padding 3em

a
  color white

.submit
  display flex
  justify-content space-between

.profiles
  margin-bottom 1em

</style>
