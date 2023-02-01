<template>
  <h1>PIDLE</h1>
  <div class="levels">
    <div class="buttons">
      <el-button text id="new" @click="orderByDate">Nouveautés</el-button>
      <el-button text id="mostPlayed" @click="orderByPlayed">Les plus jouées</el-button>
    </div>
    <div class="list">
      <LevelListElementComponent v-for="level of levelsOrder" v-bind:key="level.id" :id="level.id"/>
    </div>
  </div>

</template>

<script lang="ts">
import {Options, Vue} from "vue-class-component";
import LevelListElementComponent from "@/components/LevelListElementComponent.vue";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import { Game } from "@/models/Game";

@Options({
  components: {
    LevelListElementComponent
  },
})

export default class HomeLoggedView extends Vue {
  levels: Level[] = []
  levelsOrder: Level[] = []
  userId: number = -1
  async mounted() {
    this.userId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.levels = (await axios.get('/levels')).data
    await this.orderByDate()
  }


  async orderByDate() {
    document.getElementById('new')!.className = "selected"
    document.getElementById('mostPlayed')!.className = "unselected"
    this.levelsOrder = this.levels
        .slice()
        .filter(level => level.published)
        .filter(level => level.userId !== this.userId)
        .sort((a, b) => b.id - a.id )
  }

  async orderByPlayed() {
    document.getElementById('new')!.className = "unselected"
    document.getElementById('mostPlayed')!.className = "selected"
    const games: Game[] = (await axios.get('/games')).data
    this.levelsOrder = this.levels
        .slice()
        .filter(level => level.published)
        .filter(level => level.userId !== this.userId)
        .sort( (a, b)  => {
          const playedA = games.filter(game => game.levelId === a.id).length
          const playedB = games.filter(game => game.levelId === b.id).length
          return  playedB - playedA
        })
  }
}
</script>

<style scoped lang="stylus">
.levels
  .buttons
    border-radius 1em
    width fit-content
    margin auto
    .selected
      background rgba(64, 158, 255, 0.2)
      border-radius 1em
    .unselected
      background transparent
      border-radius 1em
  .list
    outline solid #409eff 3px
    width fit-content
    margin 1em auto
    border-radius 1em

</style>
