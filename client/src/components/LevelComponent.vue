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
import {Game} from "@/models/Game";


export default class LevelComponent extends Vue {
  @Emit('update:level')
  updateLevel(level: Level) {
    return level
  }
  level: Level = new Level()
  canvas!: Konva.Stage
  layer: Konva.Layer = new Konva.Layer()
  counterX: Text[] = []
  counterY: Text[] = []
  color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
  grid: Array<Array<Konva.Rect>> = []
  lifeRect: Array<Konva.Rect> = []
  life: number = 3
  game: Game = new Game()
  async mounted() {
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
    const userId = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    if(!await this.ownerLevel()) {
      const history: Game[] = (await axios.get('/games/byUserId/' + userId)).data
      if(history.length > 0) {
        if(history[history.length - 1].completed) {
          this.level.finished = true
          this.game = history[history.length - 1]
        }
      }
      if(!this.game.completed) {
        this.game = (await axios.post('games', {
          'levelId': this.level.id
        }, {
          headers: {
            'Authorization': localStorage.getItem('token')
          }
        })).data
      }
    }
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
    return playerId === this.level.userId
  }
  initLevel() {
    this.canvas = new Konva.Stage({
      container: this.$refs.konva as HTMLDivElement,
      width: 600,
      height: 600
    })
    this.initGrid()
    this.initCounter()
    this.initLife()
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
            if(this.grid[i][j].fill() === 'white') {
              this.grid[i][j].fill('black')
              this.verifyColumn(i)
              this.verifyLine(j)
              this.verifyValid(i, j)
              this.level.finished = this.verifyComplete()
              if(this.level.finished) {
                this.colorLevel()
              }
            }
          }
        })
        this.layer.add(this.grid[i][j])
      }
    }
    for(let i=0; i<this.level.size; i++) {
      this.verifyLine(i)
      this.verifyColumn(i)
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

  initLife() {
    for(let i=0; i<this.life; i++) {
      const rect = new Konva.Rect({
        x: 5+i*35,
        y: 20,
        width: 25,
        height: 25,
        fill: 'red',
        stroke: 'black'
      })
      this.lifeRect.push(rect)
      this.layer.add(rect)
    }
  }

  verifyColumn(column: number) {
    const pattern: [][] = JSON.parse(this.level.pattern)
    const columnList = this.grid[column]
            .map(rect => rect.fill())
            .map(val => val === 'white'? 1 : 0)
    const columnPattern = pattern[column].map(val => val === 0 ? 0 : 1)
    if (JSON.stringify(columnList) === JSON.stringify(columnPattern)) {
      for(let i=0; i<this.level.size; i++) {
        if(columnList[i] === 1)
          this.grid[column][i].fill('grey')
      }
    }
  }

  verifyLine(line: number) {
    const pattern: [][] = JSON.parse(this.level.pattern)
    const lineListActual = []
    const lineListPattern = []
    for(let i=0; i<this.level.size; i++) {
      const actual = this.grid[i][line].fill() === 'black' ? 0 : 1
      lineListActual.push(actual)
      const patternValue = pattern[i][line] === 0 ? 0 : 1
      lineListPattern.push(patternValue)
    }
    if(JSON.stringify(lineListPattern) === JSON.stringify(lineListActual))
      for(let i=0; i<this.level.size; i++) {
        if(lineListActual[i] === 1)
          this.grid[i][line].fill('grey')
      }
  }

  async verifyValid(i: number, j: number) {
    const pattern: [][] = JSON.parse(this.level.pattern)
    if(pattern[i][j] !== 0) {
      this.grid[i][j].fill('red')
      this.life--;
      this.game = (await axios.patch('/games/' + this.game.id, {
        life: this.life
      }, {
        headers: {
          'Authorization': localStorage.getItem('token')
        }
      })).data
      this.lifeRect[this.life].fill('')
      if(this.life === 0) {
        this.grid.forEach(column => column.forEach(rect => rect.destroy()))
        this.lifeRect.forEach(rect => rect.destroy())
        this.counterX.forEach(text => text.destroy())
        this.counterY.forEach(text => text.destroy())
        this.game = (await axios.patch('games/' + this.game.id, {
          completed: false,
          end: true
        }, {
          headers: {
            'Authorization': localStorage.getItem('token')
          }
        })).data
        const text = new Konva.Text({
          text: "Vous avez perdu!",
          x: 300,
          y: 300,
          fontSize: 70,
          fill: 'white'
        })
        text.offsetX(text.width()/2)
        text.offsetY(text.height()/2)
        this.layer.add(text)
      }
    }
  }

  verifyComplete() {
    let result: number[][] = []
    const pattern: [][] = JSON.parse(this.level.pattern)
    const actual: number[][] = []
    for(let i=0; i<this.level.size; i++) {
      result[i] = (pattern[i].map(val => val === 0 ? 0 : 1))
      actual[i] = []
      for(let j=0; j<this.level.size; j++) {
        actual[i][j] = this.grid[i][j].fill() === 'black' ? 0 : 1 // this.color.findIndex((elem) => elem == this.grid[i][j].fill())
      }
    }
    return JSON.stringify(result) === JSON.stringify(actual)
  }

  async colorLevel() {
    this.game = (await axios.patch('/games/' + this.game.id, {
      completed: true,
      end: true
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.updateLevel(this.level)
    const pattern: [][] = JSON.parse(this.level.pattern)
    for(let i=0; i<this.level.size; i++) {
      for(let j=0; j<this.level.size; j++) {
        this.grid[i][j].fill(this.color[pattern[i][j]])
        this.grid[i][j].stroke(this.grid[i][j].fill())
        await new Promise(resolve => setTimeout(resolve, 50))
      }
    }
  }
}
</script>
