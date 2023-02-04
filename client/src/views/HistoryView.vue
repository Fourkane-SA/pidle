<template>
  <h1>Historique</h1>
  <GameHistoryComponent v-for="game of history" :id="game.id" />
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";

import GameHistoryComponent from "@/components/GameHistoryComponent.vue";
import {Game} from "@/models/Game";
import axios from "@/plugins/axios";
@Options({
  components: {
    GameHistoryComponent
  },
})

export default class HistoryView extends Vue {
  history: Game[] = []

  async mounted() { // Initialise la liste des parties jouées par l'utilisateur connecté
    const idUser = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.history = (await axios.get('/games/byUserId/' + idUser)).data
    this.history = this.history.sort((a, b) => b.id - a.id)
  }
}
</script>

<style scoped>

</style>
