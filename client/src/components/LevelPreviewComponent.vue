<template>
  <div class="level-preview" ref="canvas" />
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import {Prop} from "vue-property-decorator";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import Konva from "konva";

export default class LevelPreviewComponent extends Vue {
  @Prop({ required: true }) id!: number

  level: Level = new Level()

  async mounted() {
    this.level = (await axios.get('/levels/' + this.id)).data
    const canvas = new Konva.Stage({
      container: this.$refs.canvas as HTMLDivElement,
      width: 100,
      height: 100
    })
    const layer = new Konva.Layer()
    const pattern = JSON.parse(this.level.pattern)
    const scale = 100 / this.level.size
    if(await this.hideImage()) {
      const rect = new Konva.Rect({
        x: 0,
        y: 0,
        width: 100,
        height: 100,
        fill: 'white'
      })
      const text = new Konva.Text({
        text: "?",
        fontSize: 100,
        x: 50,
        y: 50
      })
      text.offsetX(text.width() / 2)
      text.offsetY(text.height() / 2)
      layer.add(rect)
      layer.add(text)
    } else {
      const color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
      for(let i=0; i < this.level.size; i++) {
        for(let j=0; j < this.level.size; j++) {
          const rect = new Konva.Rect({
            x: i*scale,
            y: j*scale,
            width: scale,
            height: scale,
            fill: color[pattern[i][j]],
            stroke: color[pattern[i][j]]
          })
          layer.add(rect)
        }
      }
    }
    canvas.add(layer)
  }

  async hideImage() {
    const idUser: number = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    return this.level.userId !== idUser
  }
}
</script>

<style scoped>

</style>
