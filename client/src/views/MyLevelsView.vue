<template>
  <h1 v-if="levels.length === 0"> Vous n'avez créé aucun niveau</h1>
  <MyLevelComponent v-for="level in levels" v-bind:id="level.id" />
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import MyLevelComponent from "@/components/MyLevelComponent.vue";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";

@Options({
  components: {
    MyLevelComponent
  },
})

export default class MyLevelsView extends Vue {
  levels: Level[] = []
  async mounted() {
    const userId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    const {data} = await axios.get('/levels')
    this.levels = data.filter((level: Level) => level.userId === userId)
  }
}
</script>

<style scoped>

</style>
