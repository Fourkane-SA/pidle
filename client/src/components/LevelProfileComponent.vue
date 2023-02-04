<template>
  <div class="content">
    <el-card class="box-card">
      <template #header>
        <h4>{{level.title}}</h4>
        <p>{{ level.description }}</p>
      </template>
      <LevelPreviewComponent :id="id" />
      <p>Joué {{count}} fois</p>
    </el-card>
  </div>

</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import {Prop} from "vue-property-decorator";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import LevelPreviewComponent from "@/components/LevelPreviewComponent.vue";
import {Game} from "@/models/Game";

@Options({
  components: {
    LevelPreviewComponent
  },
})

export default class LevelProfileComponent extends Vue {
  @Prop({ required: true }) id!: number

  level: Level = new Level()
  count: number = 0

  async created() { // Initialise les valeurs à afficher
    this.level = (await axios.get('/levels/' + this.id)).data
    const games: Game[] = (await axios.get('/games')).data
    this.count = games.filter(game => game.levelId === this.level.id).length
  }
}
</script>

<style scoped lang="stylus">
h4
  text-align center
.content
  width 200px
  margin 1em
canvas

.level-preview
  border black solid
  width 100px
  height @width
  margin auto

</style>
