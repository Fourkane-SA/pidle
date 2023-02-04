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


export default class LevelComponent extends Vue { // Composant permettant de réaliser une partie
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
    if(!await this.ownerLevel()) { // Si la personne qui joue à ce niveau n'est pas celui qui l'a créé
      let history: Game[] = (await axios.get('/games/byUserId/' + userId)).data // Recupère l'historique des parties créées par l'utilisateur
      history = history
          .filter(game => game.userId === userId)
          .filter(game => game.levelId === this.level.id)
      if(history.length > 0) { // Vérifie si l'utilisateur a déjà réalisé ce niveau
        if(history[history.length - 1].completed) { // Si dans sa derniere tentative, il a terminé ce niveau
          this.level.finished = true // Le niveau est terminé
          this.game = history[history.length - 1]
        }
      }
      if(!this.game.completed) { // Si l'utilisateur n'a pas déjà terminé ce niveau
        this.game = (await axios.post('games', { // Création d'une nouvelle partie
          'levelId': this.level.id
        }, {
          headers: {
            'Authorization': localStorage.getItem('token')
          }
        })).data
      }
    }
    this.initLevel() // Initialise le niveau
  }
  async ownerLevel() { // Vérifie si l'utilisateur connecté est le propriétaire du niveau
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
    this.canvas = new Konva.Stage({ // Création du canva
      container: this.$refs.konva as HTMLDivElement,
      width: 600,
      height: 600
    })
    this.initGrid() // Initialise la grille
    this.initCounter() // Initialise les indices sur les lignes et les colonnes
    this.initLife() // Initialise le nombre de vies
    this.canvas.add(this.layer) // Ajout du layer dans le canevas
  }

  initGrid() { // Permet d'initialiser la grille
    const scale = 500 / this.level.size // Echelle d'une case
    for(let i=0; i<this.level.size; i++) {
      this.grid[i] = []
      for(let j=0; j<this.level.size; j++) {
        this.grid[i][j] = new Konva.Rect({ // Création d'une case de grille
          x: 100 + i * scale,
          y: 100 + j * scale,
          width: scale,
          height: scale,
          stroke: 'black',
          fill: 'white'
        })
        this.grid[i][j].on('click', () => { // Gestion d'evenement au click sur la case
          if(!this.level.finished) { // Si le niveau n'est pas terminé
            if(this.grid[i][j].fill() === 'white') { // Si la case n'a pas déjà été cliqué
              this.grid[i][j].fill('black') // Colore la case en noir
              this.verifyColumn(i) // Si la colonne est correcte, colore les cases blanches en gris
              this.verifyLine(j) // Si la ligne est correcte, colore les cases blanches en gris
              this.verifyValid(i, j) // Si la case cliqué par l'utilisateur est incorrecte, colorie en rouge
              this.level.finished = this.verifyComplete() // Vérifie si le niveau est correct
              if(this.level.finished) { // Si le niveau est terminé
                this.colorLevel() // Affiche l'imag complète avec ses couleurs
              }
            }
          }
        })
        this.layer.add(this.grid[i][j]) // Ajout de la grille dans le layer
      }
    }
    for(let i=0; i<this.level.size; i++) { // Vérifie les lignes et les colonnes complètes une fois que la grille a été initialisée
      this.verifyLine(i)
      this.verifyColumn(i)
    }
    if(this.level.finished) // Cette condition est vraie si l'utilisateur a déjà terminé ce niveau
      this.colorLevel()
  }

  initCounter() { // Initialise les indices pour chaque lignes et chaque colonne
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
  initCounterX(column: number) { // Retourne le patterne à afficher sur une colonne
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

  initCounterY(line: number) { // Retourne le patterne à afficher sur une ligne
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

  initLife() { // Initialise l'affichage du nombre de vie
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

  verifyColumn(column: number) { // Compare la colonne en cours avec celle du patterne du niveau
    const pattern: [][] = JSON.parse(this.level.pattern)
    const columnList = this.grid[column]
            .map(rect => rect.fill())
            .map(val => val === 'white'? 1 : 0)
    const columnPattern = pattern[column].map(val => val === 0 ? 0 : 1)
    if (JSON.stringify(columnList) === JSON.stringify(columnPattern)) { // Si les cases noires de la colonne renseignée par l'utilisateur correspond à celle du niveau
      for(let i=0; i<this.level.size; i++) {
        if(columnList[i] === 1)
          this.grid[column][i].fill('grey') // Remplace les cases blanches en gris pour indiquer que la colonne est correcte
      }
    }
  }

  verifyLine(line: number) { // Compare la ligne en cours avec celle du patterne du niveau
    const pattern: [][] = JSON.parse(this.level.pattern)
    const lineListActual = []
    const lineListPattern = []
    for(let i=0; i<this.level.size; i++) {
      const actual = this.grid[i][line].fill() === 'black' ? 0 : 1
      lineListActual.push(actual)
      const patternValue = pattern[i][line] === 0 ? 0 : 1
      lineListPattern.push(patternValue)
    }
    if(JSON.stringify(lineListPattern) === JSON.stringify(lineListActual)) // Si les cases noires de la ligne renseignée par l'utilisateur correspond à celle du niveau
      for(let i=0; i<this.level.size; i++) {
        if(lineListActual[i] === 1)
          this.grid[i][line].fill('grey') // Remplace les cases blanches en gris pour indiquer que la ligne est correcte
      }
  }

  async verifyValid(i: number, j: number) { // Vérifie si la case cliqué est valide
    const pattern: [][] = JSON.parse(this.level.pattern)
    if(pattern[i][j] !== 0) { // Si la case cliquée n'est pas correcte
      this.grid[i][j].fill('red') // Colorie la case en rouge
      this.life--; // Enleve une vie
      this.game = (await axios.patch('/games/' + this.game.id, { // Met à jour le nombre de vie dans la partie
        life: this.life
      }, {
        headers: {
          'Authorization': localStorage.getItem('token')
        }
      })).data
      this.lifeRect[this.life].fill('')
      if(this.life === 0) { // Si le nombre de vies est égale à 0
        this.grid.forEach(column => column.forEach(rect => rect.destroy())) // Suppression de tous les elements dessinées
        this.lifeRect.forEach(rect => rect.destroy())
        this.counterX.forEach(text => text.destroy())
        this.counterY.forEach(text => text.destroy())
        this.game = (await axios.patch('games/' + this.game.id, { // Mise à jour de l'état de la partie
          completed: false,
          end: true
        }, {
          headers: {
            'Authorization': localStorage.getItem('token')
          }
        })).data
        const text = new Konva.Text({ // Affichage d'un texte informant que la partie est terminée
          text: "Vous avez perdu!",
          x: 300,
          y: 300,
          fontSize: 70,
          fill: 'white'
        })
        text.offsetX(text.width()/2)
        text.offsetY(text.height()/2)
        this.layer.add(text) // Ajoutb du texte dans le layer
      }
    }
  }

  verifyComplete() { // Vérifie si le niveau est finie
    let result: number[][] = []
    const pattern: [][] = JSON.parse(this.level.pattern)
    const actual: number[][] = []
    for(let i=0; i<this.level.size; i++) {
      result[i] = (pattern[i].map(val => val === 0 ? 0 : 1))
      actual[i] = []
      for(let j=0; j<this.level.size; j++) {
        actual[i][j] = this.grid[i][j].fill() === 'black' ? 0 : 1
      }
    }
    return JSON.stringify(result) === JSON.stringify(actual)
  }

  async colorLevel() { // Colorie le niveau
    if(!await this.ownerLevel()) { // Mis à jour du status de la partie
      this.game = (await axios.patch('/games/' + this.game.id, {
        completed: true,
        end: true
      }, {
        headers: {
          'Authorization': localStorage.getItem('token')
        }
      })).data
    }
    this.updateLevel(this.level)
    const pattern: [][] = JSON.parse(this.level.pattern)
    for(let i=0; i<this.level.size; i++) {
      for(let j=0; j<this.level.size; j++) { // Colorie une case de niveau
        this.grid[i][j].fill(this.color[pattern[i][j]])
        this.grid[i][j].stroke(this.grid[i][j].fill())
        await new Promise(resolve => setTimeout(resolve, 50)) // Attente de 50 ms avant de colorier la prochaine case
      }
    }
  }
}
</script>
