<template>
  <h1>PIDLE</h1>
  <div class="levels">
    <div class="buttons">
      <el-button text id="new" @click="orderByDate">Nouveautés</el-button>
      <el-button text id="mostPlayed" @click="orderByPlayed">Les plus jouées</el-button>
    </div>
    <div class="list">
      <LevelListElementComponent v-for="level of levels" :id="level.id"/>
    </div>
  </div>

</template>

<script lang="ts">
import {Options, Vue} from "vue-class-component";
import LevelListElementComponent from "@/components/LevelListElementComponent.vue";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";

@Options({
  components: {
    LevelListElementComponent
  },
})

export default class HomeLoggedView extends Vue {

  levels: Level[] = []
  async mounted() {
    const userId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.levels = (await axios.get('/levels')).data
    this.levels = this.levels
        .filter(level => level.published)
        .filter(level => level.userId !== userId)
        .sort((a, b) => a.id > b.id ? -1 : 1)
    this.orderByDate()
  }
  orderByDate() {
    document.getElementById('new')!.className = "selected"
    document.getElementById('mostPlayed')!.className = "unselected"
  }

  orderByPlayed() {
    document.getElementById('new')!.className = "unselected"
    document.getElementById('mostPlayed')!.className = "selected"
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
