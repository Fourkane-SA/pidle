<template>
  <el-card class="box-card">
    <template #header>
      <div class="card-header">
        <strong>{{ level.title }}</strong>
        <router-link class="link" :to='"/level/" + id '>
          <el-button class="el-button" text>Jouer</el-button>
        </router-link>
      </div>
    </template>
    <div class="body">
      <LevelPreviewComponent :id="id" />
      <p class="text"> {{level.description}}</p>
    </div>
    <div class="infos">
      <p>Créé par <strong>{{ user.login }}</strong> le <strong> {{new Date(level.updated_at).toLocaleDateString('fr-FR')}}</strong></p>
      <p>Joué {{count}} fois</p>
    </div>
  </el-card>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import LevelPreviewComponent from "@/components/LevelPreviewComponent.vue";
import {Prop} from "vue-property-decorator";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import { User } from "@/models/User";
import { Game } from "@/models/Game";

@Options({
  components: {
    LevelPreviewComponent
  },
})
export default class LevelListElementComponent extends Vue {
  @Prop({ required: true }) id!: number

  level: Level = new Level()
  user: User = new User()
  count: number = 0
  async created() {
    this.level = (await axios.get('/levels/' + this.id)).data
    this.user = (await axios.get('/users/' + this.level.userId)).data
    let games: Game[] = (await axios.get('/games')).data
    this.count = games.filter(g => g.levelId === this.id).length
  }
}
</script>

<style scoped lang="stylus">
.card-header
  display flex
  justify-content space-between
  align-items center
  strong
    color #409eff


.text
  font-size 14px

.box-card
  width 480px

canvas
  border solid black 1px


.body
  display flex
  p
    margin-left 1em

.infos
  text-align left
  display flex
  justify-content space-between

  p
    font-size 12px

.link
  color white
  text-decoration none
</style>
