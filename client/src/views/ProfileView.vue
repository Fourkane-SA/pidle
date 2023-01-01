<template>
  <div class="headerProfile">
    <img :src="'profiles/' + user.idAvatar + '.png'">
    <p><strong>{{ user.login }}</strong></p>
    <p>{{user.description}}</p>
    <router-link class="link" to="/editProfile">
      <el-button>Modifier</el-button>
    </router-link>
    <p>0 abonnées | 0 abonnements</p>
  </div>
  <div>
    TODO
  </div>
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import {User} from "@/models/User";
import axios from "@/plugins/axios";

export default class ProfileView extends Vue {
  user?: User = new User({idAvatar: 0})
  async created() {
    const login = (await axios.get('/token/whoami',{
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.user = (await axios.get('/users/' + login)).data
  }
}
</script>

<style scoped lang="stylus">

.headerProfile
  border #409eff solid 1px
  width 70%
  max-width 600px
  min-width 300px
  margin 1em auto
  border-radius 1em
  padding-left 1em
  padding-right 1em

img
  width 100px

.link
  color white
  text-decoration none

</style>
