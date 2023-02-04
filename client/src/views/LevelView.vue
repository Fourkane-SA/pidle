<template>
  <div class="levelComponent">
    <LevelComponent />
  </div>
  <div class="levelInfos">
    <div>
      <img :src="'/profiles/' + user.idAvatar + '.png'" width="60">
      <p>{{ user.login }}</p>
    </div>
    <div class="titleDescription">
      <h2>{{ level.title }}</h2>
      <p>{{ level.description }}</p>
    </div>
    <div class="favoris">
      <Star style="width: 40px" v-if="!favoris" @click="clickFavoris"/>
      <StarFilled style="width: 40px " v-if="favoris" @click="clickFavoris"/>
      {{countLikes}}
    </div>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import LevelComponent from "@/components/LevelComponent.vue";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import {User} from "@/models/User";
import { Star, StarFilled } from "@element-plus/icons-vue";
import { Favoris } from "@/models/favoris";

@Options({
  components: {
    LevelComponent,
    Star,
    StarFilled
  },
})
export default class LevelView extends Vue {
  favoris = false
  level: Level = new Level()
  user: User = new User()
  countLikes: number = 0
  async mounted() { // Initialise les différentes variables en requetant l'API
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
    this.user = (await axios.get('/users/' + this.level.userId)).data
    const idUser = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    const favorisList: Favoris[] = (await axios.get('/favoris/byUserId/' + idUser)).data
    let fav = favorisList.filter(fav => fav.levelId === this.level.id)
    if(fav.length > 0) {
      this.favoris = fav[0].isLiked
    }
    this.countLikes = (await axios.get('/favoris/count/' + this.level.id)).data
  }
  async clickFavoris() { // Ajoute / supprime le niveau des favoris de l'utilisateur connecté
    const fav: Favoris = (await axios.patch('/favoris/' + this.level.id,null, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.favoris = fav.isLiked
    this.countLikes = (await axios.get('/favoris/count/' + this.level.id)).data
  }
}
</script>

<style scoped lang="stylus">
.levelComponent
  width fit-content
  margin 2em auto
  border-radius 1em
  padding 1em
  background rgba(64, 158, 255, 0.2)

.levelInfos
  border #409eff solid 1px
  border-radius 1em
  padding 1em
  width 80%
  margin auto
  display flex
  .titleDescription
    width 100%
</style>
