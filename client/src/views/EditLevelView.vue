<template>
  <div class="edit-level">
    <h1 class="edit-level-title">{{level.title}}</h1>
    <el-input class="edit-level-description" v-model="level.description" type="textarea" placeholder="Description"></el-input>
    <el-alert class="edit-level-alert" title="Vous devez terminer le niveau pour le publier" effect="dark" type="error" :closable="false" v-if="!level.finished"/>
    <div class="level" ref="konva"/>
    <div class="edit-level-actions">
      <el-button type="primary" @click="saveLevel" :loading="loadingSave">Enregistrer</el-button>
      <el-button type="success" @click="publishLevel" :disabled="!level.finished" :loading="loadingPublish" v-if="!this.level.published">Publier</el-button>
      <el-button type="success" @click="privateLevel" :loading="loadingPublish" v-else>Mettre en privé</el-button>
      <el-button type="danger" @click="deleteLevel">Supprimer</el-button>
    </div>
  </div>
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import Konva from "konva";
import {Text} from "konva/lib/shapes/Text";

export default class EditLevelView extends Vue {
  level: Level = new Level({})
  canvas!: Konva.Stage
  layer: Konva.Layer = new Konva.Layer()
  counterX: Text[] = []
  counterY: Text[] = []
  loadingSave: boolean = false
  loadingPublish: boolean = false
  grid: Array<Array<Konva.Rect>> = []
  async mounted() {
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
    this.initLevel()
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
              this.saveLevel()
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
    const values: string[] = []
    const pattern: [] = JSON.parse(this.level.pattern)
    pattern.forEach(line => values.push(line[column]))
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
    return text
  }

  initCounterY(line: number) {
    const pattern: [] = JSON.parse(this.level.pattern)
    const values: [] = pattern[line]
    const res = []
    let counter = 0
    values.forEach(val => {
      if (val == 'black') {
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
    let result: string[][] = []
    const pattern: [][] = JSON.parse(this.level.pattern)
    const actual: string[][] = []
    for(let i=0; i<this.level.size; i++) {
      result[i] = (pattern[i].map(val => val === 'black' ? 'black' : 'white'))
      actual[i] = []
      for(let j=0; j<this.level.size; j++) {
        actual[i][j] = this.grid[i][j].fill()
      }
    }
    return JSON.stringify(result) === JSON.stringify(actual)
  }

  async colorLevel() {
    const pattern: [][] = JSON.parse(this.level.pattern)
    for(let i=0; i<this.level.size; i++) {
      for(let j=0; j<this.level.size; j++) {
        this.grid[i][j].fill(pattern[i][j])
        await new Promise(resolve => setTimeout(resolve, 50))
      }
    }
  }

  async saveLevel() {
    this.loadingSave = true
    await axios.patch('/levels/' + this.level.id, {
      description: this.level.description,
      finished: this.level.finished
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
    this.loadingSave = false
  }
  async publishLevel() {
    this.loadingPublish = true
    this.level = (await axios.patch('/levels/' + this.level.id, {
      published: true
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.loadingPublish = false
  }

  async privateLevel() {
    this.loadingPublish = true
    this.level =  (await axios.patch('/levels/' + this.level.id, {
      published: false
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.loadingPublish = false
  }
  async deleteLevel() {
    await axios.delete('/levels/' + this.level.id, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
    this.$router.push('/myLevels')
  }
}
</script>

<style scoped lang="stylus">
.edit-level
  display: flex
  flex-direction: column
  align-items: center
  .edit-level-title
    font-size: 32px
    color: #409eff
  .edit-level-description
    width: 600px
    margin: 20px 0
  .edit-level-alert
    width fit-content
    margin auto
  .level
    width: 600px
    height: 600px
  .edit-level-actions
    display: flex
    justify-content: space-between
    width: 600px
    margin-top: 20px

</style>
