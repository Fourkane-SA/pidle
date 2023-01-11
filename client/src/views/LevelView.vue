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
      <p>{{ level.description }}</p> <!--Ajouter un bouton favoris quelque part-->
    </div>
    <div class="favoris">
      <Star style="width: 40px" v-if="!favoris" @click="clickFavoris"/>
      <StarFilled style="width: 40px " v-if="favoris" @click="clickFavoris"/>
    </div>
  </div>
  <div class="comments">
    TODO commentaires
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import LevelComponent from "@/components/LevelComponent";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import {User} from "@/models/User";
import { Star, StarFilled } from "@element-plus/icons-vue";

@Options({
  components: {
    LevelComponent,
    Star,
    StarFilled
  },
})
export default class LevelView extends Vue {
  favoris = false
  level: Level = new Level({})
  user: User = new User({})
  async mounted() {
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
    this.user = (await axios.get('/users/' + this.level.userId)).data
  }
  clickFavoris() {
    this.favoris = !this.favoris
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
