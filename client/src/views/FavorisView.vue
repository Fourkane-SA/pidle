<template>
  <h1>Vos niveaux favoris</h1>
  <div class="list">
    <LevelListElementComponent v-for="level of levels" :id="level.id"/>
    <p v-if="levels.length === 0">
      Vous n'avez aucun niveau favoris
    </p>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import {Level} from "@/models/Level";
import LevelListElementComponent from "@/components/LevelListElementComponent.vue";
import {Favoris} from "@/models/favoris";
import axios from "@/plugins/axios";

@Options({
  components: {
    LevelListElementComponent
  },
})

export default class FavorisView extends Vue {
  levels: Level[] = []

  async mounted() {
    const userId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    const favoris: Favoris[] = (await axios.get('/favoris/byUserId/' + userId)).data
    favoris.forEach(fav => {
      const level = new Level()
      level.id = fav.levelId
      this.levels.push(level)
    })
  }
}
</script>

<style scoped lang="stylus">
.list
  width fit-content
  margin 1em auto
  border-radius 1em
</style>
