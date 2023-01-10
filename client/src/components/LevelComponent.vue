<template>
  <div class="level" ref="konva"/>
</template>

<script lang="ts">
import {Vue} from "vue-class-component";
import Konva from "konva";
import {Text} from "konva/lib/shapes/Text";
import axios from "@/plugins/axios";
import {Level} from "@/models/Level";
import {Emit} from "vue-property-decorator";


export default class LevelComponent extends Vue {
  @Emit('update:level')
  updateLevel(level: Level) {
    return level
  }
  level: Level = new Level({})
  canvas!: Konva.Stage
  layer: Konva.Layer = new Konva.Layer()
  counterX: Text[] = []
  counterY: Text[] = []
  color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
  grid: Array<Array<Konva.Rect>> = []
  async mounted() {
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
    await this.ownerLevel()
    this.initLevel()
  }
  async ownerLevel() {
    const playerId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    if(playerId !== this.level.userId)
      this.level.finished = false
  }
  initLevel() {
    this.canvas = new Konva.Stage({
      container: this.$refs.konva as HTMLDivElement,
      width: 600,
      height: 600
    })
    this.initGrid()
    this.initCounter()
    this.canvas.add(this.layer)
  }

  initGrid() {
    const scale = 500 / this.level.size
    for(let i=0; i<this.level.size; i++) {
      this.grid[i] = []
      for(let j=0; j<this.level.size; j++) {
        this.grid[i][j] = new Konva.Rect({
          x: 100 + i * scale,
          y: 100 + j * scale,
          width: scale,
          height: scale,
          stroke: 'black',
          fill: 'white'
        })
        this.grid[i][j].on('click', () => {
          if(!this.level.finished) {
            if(this.grid[i][j].fill() === 'white')
              this.grid[i][j].fill('black')
            else
              this.grid[i][j].fill('white')
            this.level.finished = this.verifyComplete()
            if(this.level.finished) {
              this.colorLevel()
            }
          }
        })
        this.layer.add(this.grid[i][j])
      }
    }
    if(this.level.finished)
      this.colorLevel()
  }

  initCounter() {
    const scale = 500 / this.level.size
    for(let i=0; i<this.level.size; i++) {
      const countX = new Konva.Text({
        x: 90,
        y: 100 + scale*i + scale/2,
        text: this.initCounterX(i),
        fontSize: 14,
        fill: 'white'
      })
      countX.offsetX(countX.width())
      countX.offsetY(countX.height() / 2)
      this.counterX.push(countX)
      this.layer.add(this.counterX[i])

      const countY = new Konva.Text({
        x: 100 + i*scale + scale/2,
        y: 95,
        text: this.initCounterY(i),
        fontSize: 14,
        fill: 'white'
      })

      countY.offsetX(countY.width() / 2)
      countY.offsetY(countY.height())
      this.counterY.push(countY)
      this.layer.add(this.counterY[i])
    }
  }
  initCounterX(column: number) {
    const values: number[] = []
    const pattern: [] = JSON.parse(this.level.pattern)
    pattern.forEach(line => values.push(line[column]))
    const res = []
    let counter = 0
    values.forEach(val => {
      if(this.color[val] == 'black') {
        counter++
      } else if (counter > 0) {
        res.push(counter)
        counter = 0
      }
    })
    if(counter > 0)
      res.push(counter)
    let text = res.toString().replaceAll(',', ' ')
    if(text === '')
      text = '0'
    return text
  }

  initCounterY(line: number) {
    const pattern: [] = JSON.parse(this.level.pattern)
    const values: [] = pattern[line]
    const res = []
    let counter = 0
    values.forEach(val => {
      if (this.color[val] == 'black') {
        counter++
      } else if (counter > 0) {
        res.push(counter)
        counter = 0
      }
    })
    if (counter > 0)
      res.push(counter)
    let text = res.toString().replaceAll(',', '\n')
    if (text === '')
      text = '0'
    return text
  }

  verifyComplete() {
    let result: number[][] = []
    const pattern: [][] = JSON.parse(this.level.pattern)
    const actual: number[][] = []
    for(let i=0; i<this.level.size; i++) {
      result[i] = (pattern[i].map(val => val === 0 ? 0 : 12))
      actual[i] = []
      for(let j=0; j<this.level.size; j++) {
        actual[i][j] = this.color.findIndex((elem) => elem == this.grid[i][j].fill())
      }
    }
    return JSON.stringify(result) === JSON.stringify(actual)
  }

  async colorLevel() {
    this.updateLevel(this.level)
    const pattern: [][] = JSON.parse(this.level.pattern)
    for(let i=0; i<this.level.size; i++) {
      for(let j=0; j<this.level.size; j++) {
        this.grid[i][j].fill(this.color[pattern[i][j]])
        this.grid[i][j].stroke('')
        await new Promise(resolve => setTimeout(resolve, 50))
      }
    }
  }
}
</script>

<style scoped>

</style>
