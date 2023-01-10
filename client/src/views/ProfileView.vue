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
  <div class="bodyProfile">
    <div class="infosProfile">
      <h2>Statistiques</h2>
      <p>Membre depuis le :
      {{new Date(user.created_at).toLocaleDateString('fr-FR')}}</p>
      <p>Parties jouées : n</p>
      <p>Parties terminées : n</p>
      <p>Parties créées : {{levels.length}}</p>
    </div>
    <div class="listLevels">
      <LevelProfileComponent v-for="level of levels" :id="level.id" />
    </div>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import {User} from "@/models/User";
import axios from "@/plugins/axios";
import LevelProfileComponent from "@/components/LevelProfileComponent.vue";
import {Level} from "@/models/Level";

@Options({
  components: {
    LevelProfileComponent
  },
})

export default class ProfileView extends Vue {
  user?: User = new User({idAvatar: 0})
  levels: Level[] = []
  async created() {
    const id = (await axios.get('/token',{
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.user = (await axios.get('/users/' + id)).data
    const data = (await axios.get('/levels')).data
    this.levels = data.filter((level: Level) => level.userId === this.user!.id)
  }
}
</script>

<style scoped lang="stylus">

.headerProfile
  border #409eff solid 1px
  box-shadow: 4px 3px 10px #0e37ff
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

.bodyProfile
  //border #409eff solid 1px
  border-radius 1em
  box-shadow: 4px 1px 30px #0e37ff
  width 90%
  min-width 300px
  margin 2em auto
  display flex

.infosProfile
  width 40%
  border #409eff solid 1px
  padding 1em

.listLevels
  width 100%
  border #409eff solid 1px
  padding 1em
  display flex
  flex-wrap wrap


</style>
