<template>
  <div class="block">
    <LevelPreviewComponent :id="level.id"  v-if="level.id !== undefined"/>
    <div class="infos">
      <h3>{{level.title}}</h3>
      <p>Créé par {{user.login}}</p>
    </div>
    <div class="dates">
      <p>
        {{getStatus()}}
        <span v-if="game.completed">
          <br /> Temps : {{getTime()}}s
        </span>
        <br /> Vies : {{game.life}}/3
      </p>

    </div>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import LevelPreviewComponent from "@/components/LevelPreviewComponent.vue"
import {User} from "@/models/User";
import {Level} from "@/models/Level";
import {Game} from "@/models/Game";
import axios from "@/plugins/axios";
import {Prop} from "vue-property-decorator";

@Options({
  components: {
    LevelPreviewComponent
  },
})
export default class GameHistoryComponent extends Vue {

  @Prop({ required: true }) id!: number // Passage de l'id de la partie en paramètre du composant
  user: User = new User()
  level: Level = new Level()
  game: Game = new Game()

  async mounted() { // Requête l'API pour initialiser les variables
    this.game = (await axios.get('/games/' + this.id)).data
    this.level = (await axios.get('/levels/' + this.game.levelId)).data
    this.user = (await axios.get('/users/' + this.game.userId)).data
  }

  getStatus() {
    return this.game.completed ? "Niveau terminé" : "Non terminé"
  }

  getTime() { // Recupère la durée de la partie
    const created = Date.parse(this.game.created_at)
    const finished = Date.parse(this.game.updated_at)
    const duration = finished - created
    return duration / 1000
  }
}
</script>

<style scoped lang="stylus">
.block
  width 80%
  border solid #409eff 3px
  margin auto
  display flex
  justify-content space-between
  .dates
    margin-right 1em
</style>
