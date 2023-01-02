<template>
  <div class="form" v-if="!confirm">
    <el-form label-position="top">
      <el-form-item label="Titre">
        <el-input v-model="title" />
      </el-form-item>
      <el-form-item label="Description">
        <el-input type="textarea" v-model="description" />
      </el-form-item>
      <el-form-item label="Dimensions">
        <el-input-number :min="5" :max="15" :value-on-clear="5" v-model="size"/>
      </el-form-item>
    </el-form>
    <el-button @click="initCanvas">Créer</el-button>
  </div>
  <div class="konva" ref="konva" />
  <div v-if="confirm" style="margin: 1em">
    <el-button @click="publish"> Créer le niveau</el-button>
    <h2>{{title}}</h2>
    <p>{{description}}</p>
  </div>
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import Konva from 'konva'
import Layer = Konva.Layer;
import Rect = Konva.Rect;
import { Text } from "konva/lib/shapes/Text";

export default class LevelEditorComponent extends Vue {

  canvas!: Konva.Stage
  layer: Layer = new Konva.Layer()
  color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
  selectedColorRect: Rect | undefined
  size: number = 5
  grid: Array<Array<string>> = []
  counterX: Text[] = []
  counterY: Text[] = []
  confirm = false
  title: string = ''
  description: string = ''

  mounted() {
    //this.initCanvas()
  }

  initCanvas() {
    this.confirm = true
    this.canvas = new Konva.Stage({
      container: this.$refs.konva as HTMLDivElement,
      width: 800,
      height: 600
    })
    this.initSelectedColor()
    this.initGrid()
    this.initCounter()
    this.canvas.add(this.layer)
  }

  initSelectedColor() {
    for(let i=0; i<this.color.length/2; i++) {
      const rect1 = new Konva.Rect({
        x: 20,
        y: i*40+20,
        width: 40,
        height: 40,
        fill: this.color[i],
        stroke: 'black'
      })
      rect1.on('click', () => {
        this.selectedColorRect!.fill(this.color[i])
      })
      this.layer.add(rect1)
      const rect2 = new Konva.Rect({
        x: 60,
        y: i*40+20,
        width: 40,
        height: 40,
        fill: this.color[i+this.color.length/2],
        stroke: 'black'
      })
      rect2.on('click', () => {
        this.selectedColorRect!.fill(this.color[i+this.color.length/2])
      })
      this.layer.add(rect2)
    }

    this.selectedColorRect = new Konva.Rect({
      x: 40,
      y: 520,
      width: 40,
      height: 40,
      fill: 'black',
      stroke: 'bkack'
    })

    this.layer.add(this.selectedColorRect)
  }

  initGrid() {
    const scale = 400/this.size // Echelle d'une case de la grille
    for(let i=0; i<this.size; i++) {
      this.grid[i] = []
      for(let j=0; j<this.size; j++) {
        this.grid[i][j] = 'white'
        const rect = new Konva.Rect({
          x: 250 + i*scale,
          y: 125 + j*scale,
          width: scale,
          height: scale,
          fill: 'white',
          stroke: 'black'
        })
        rect.on('click', () => {
          rect.fill(this.selectedColorRect?.fill()!)
          this.grid[i][j] = rect.fill()
          this.updateCounterY(i)
          this.updateCounterX(j)
        })
        this.layer.add(rect)
      }
    }
  }

  initCounter() {
    const scale = 400/this.size // Echelle d'une case de la grille
    for(let i=0; i<this.size; i++) {
      const countY = new Konva.Text({
        x: 250 + i*scale + scale/2,
        y: 120,
        text: '0',
        fontSize: 14,
      })

      countY.offsetX(countY.width() / 2)
      countY.offsetY(countY.height())

      this.counterY.push(countY)
      this.layer.add(this.counterY[i])

      const countX = new Konva.Text({
        x: 245,
        y: 125 + scale*i + scale/2,
        text: '0',
        fontSize: 14,
      })

      countX.offsetX(countX.width())
      countX.offsetY(countX.height() / 2)

      this.counterX.push(countX)
      this.layer.add(this.counterX[i])

    }
  }

  updateCounterX(column: number) {
    const values: string[] = []
    this.grid.forEach(line => values.push(line[column]))
    const res = []
    let counter = 0
    values.forEach(val => {
      if(val == 'black') {
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
    this.counterX[column].text(text)
    this.counterX[column].offsetX(this.counterX[column].width())
  }
  updateCounterY(line: number) {
    const values = this.grid[line]
    const res = []
    let counter = 0
    values.forEach(val => {
      if(val == 'black') {
        counter++
      } else if (counter > 0) {
        res.push(counter)
        counter = 0
      }
    })
    if(counter > 0)
      res.push(counter)
    let text = res.toString().replaceAll(',', '\n')
    if(text === '')
      text = '0'
    this.counterY[line].text(text)
    this.counterY[line].offsetY(this.counterY[line].height())
  }

  publish() {
    console.log(this.title, this.description, this.grid)
  }

}
</script>

<style scoped lang="stylus">
.konva
  margin auto
  width fit-content
  border-radius 1em
  background grey

.form
  width 500px
  margin auto
</style>
