<template>
  <div class="content">
    <p><strong> LOGIN</strong></p>
    <AvatarSelector v-bind:selectedId="user.idAvatar" v-bind:listId="listId" v-on:update:selectedId="updateIdAvatar" />
    <el-form label-position="top">
      <el-form-item label="Description" prop="description">
        <el-input type="textarea" v-model="user.description" />
      </el-form-item>
      <el-form-item label="Date de naissance" prop="birth">
        <el-date-picker type="date" v-model="user.birth"/>
      </el-form-item>
      <el-form-item label="Prénom">
        <el-input v-model="user.firstname" />
      </el-form-item>
      <el-form-item label="Nom">
        <el-input v-model="user.lastname" />
      </el-form-item>
    </el-form>
    <div class="button">
      <router-link to="/profile" class="link">
        <el-button type="danger" plain> Annuler</el-button>
      </router-link>
      <el-button type="success" plain @click="submit" v-bind:loading="loading">Modifier</el-button>
    </div>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import AvatarSelector from "@/components/AvatarSelectorComponent.vue";
import {User} from "@/models/User";
import axios from "@/plugins/axios";

@Options({
  components: {
    AvatarSelector
  },
})

export default class EditProfileView extends Vue {
  description: string = ''
  birth: string = ''
  firstname: string = ''
  lastname: string = ''
  listId = [0,1,2,3,4,5,6,7,8,9,10,11, 12, 13, 14, 15]
  idAvatar: number = 0
  loading: boolean = false
  user: User = new User({})

  async created() {
    const login = (await axios.get('/token/whoami',{
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.user = (await axios.get('/users/' + login)).data
  }

  updateIdAvatar(id: number) {
    this.user.idAvatar = id
  }

  async submit() {
    this.loading = true
    const {login, description, birth, firstname, lastname, idAvatar} = this.user
    await axios.patch('/users/' + login, {
      description: description,
      birth: birth,
      firstname: firstname,
      lastname: lastname,
      idAvatar: idAvatar
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
    this.loading = false
  }
}
</script>

<style scoped lang="stylus">

.content
  width: 550px
  border: 1px solid #409eff
  border-radius: 1em
  margin: 1em auto
  padding: 1em

.button
  display flex
  justify-content space-between

.link
  color white
  text-decoration none
</style>
