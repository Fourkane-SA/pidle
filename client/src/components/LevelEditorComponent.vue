<template>
  <div class="form" v-if="!confirm">
    <el-form label-position="top" :rules="rules" ref="formRef" :model="modelForm">
      <el-form-item label="Titre" prop="title">
        <el-input v-model="modelForm.title" />
      </el-form-item>
      <el-form-item label="Description" prop="description">
        <el-input type="textarea" v-model="modelForm.description" />
      </el-form-item>
      <el-form-item label="Dimensions">
        <el-input-number :min="5" :max="15" :value-on-clear="5" v-model="level.size"/>
      </el-form-item>
    </el-form>
    <el-button @click="verifieForm(formRef)">Créer</el-button>
  </div>
  <div class="konva" ref="konva" />
  <div v-if="confirm" style="margin: 1em">
    <el-button @click="publish" v-bind:loading="loading"> Créer le niveau</el-button>
    <h2>{{level.title}}</h2>
    <p>{{level.description}}</p>
  </div>
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import Konva from 'konva'
import Layer = Konva.Layer;
import Rect = Konva.Rect;
import { Text } from "konva/lib/shapes/Text";
import {ElMessage, FormInstance, FormRules} from "element-plus";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import {reactive, ref} from "vue";

export default class LevelEditorComponent extends Vue {

  canvas!: Konva.Stage
  layer: Layer = new Konva.Layer()
  color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
  selectedColorRect: Rect | undefined
  grid: Array<Array<number>> = []
  counterX: Text[] = []
  counterY: Text[] = []
  confirm = false
  loading: boolean = false
  level: Level = new Level({
    title: '',
    description: '',
    size: 5
  })

  rules = reactive<FormRules>({
    title: [ // Règles sur le champ login
      {required: true, message: 'Entrez un titre', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: "Le nom d'utilisateur doit contenir au moins 6 caractères", trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
    ],
    description: [ // Règles sur le mot de passe
      {required: true, message: 'Entrez une description', trigger: 'blur'}, // Vérifie que le champ n'est pas vide
      {min: 6, message: 'La description doit contenir au moins 6 caractères', trigger: 'blur'}, // Vérifie que sa longueur est d'au moins 6 caractères
    ],
  })
  formRef = ref<FormInstance>()
  modelForm = reactive({
    title: '',
    description: ''
  })


  async verifieForm(form: FormInstance) { // Permet de vérifier la validité du formulaire
    if(await form.validate()) {
      this.level.title = this.modelForm.title
      this.level.description = this.modelForm.description
      this.initCanvas()
    }
  }

  initCanvas() { // Initialise le canevas
    this.confirm = true
    this.canvas = new Konva.Stage({
      container: this.$refs.konva as HTMLDivElement,
      width: 800,
      height: 600
    })
    this.initSelectedColor() // Initialise la selection de couleurs
    this.initGrid() // Initialise la grille
    this.initCounter() // Initialise l'affichage des indices
    this.canvas.add(this.layer)
  }

  initSelectedColor() { // Initialise la selection de couleurs
    for(let i=0; i<this.color.length/2; i++) {
      const rect1 = new Konva.Rect({ // Affichage d'un selecteur de couleur
        x: 20,
        y: i*40+20,
        width: 40,
        height: 40,
        fill: this.color[i],
        stroke: 'black'
      })
      rect1.on('click', () => { // Mise à jour de la couleur selectionnée au clic
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

    this.selectedColorRect = new Konva.Rect({ // Initialise la couleur selectionnée à noire
      x: 40,
      y: 520,
      width: 40,
      height: 40,
      fill: 'black',
      stroke: '#409eff',
      cornerRadius: 20
    })

    this.layer.add(this.selectedColorRect)
  }

  initGrid() {
    const scale = 400/this.level.size // Echelle d'une case de la grille
    for(let i=0; i<this.level.size; i++) {
      this.grid[i] = []
      for(let j=0; j<this.level.size; j++) { // Initialisation de la grille
        this.grid[i][j] = 12
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
          this.grid[i][j] = this.color.findIndex((elem) => elem === rect.fill())
          console.log(this.grid)
          this.updateCounterY(i)
          this.updateCounterX(j)
        })
        this.layer.add(rect)
      }
    }
  }

  initCounter() {
    const scale = 400/this.level.size // Echelle d'une case de la grille
    for(let i=0; i<this.level.size; i++) { // Initialise les patternes de chaques lignes et colonnes à zéro
      const countY = new Konva.Text({
        x: 250 + i*scale + scale/2,
        y: 120,
        text: '0',
        fontSize: 14,
        fill: 'white'
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
        fill: 'white'
      })

      countX.offsetX(countX.width())
      countX.offsetY(countX.height() / 2)

      this.counterX.push(countX)
      this.layer.add(this.counterX[i])

    }
  }

  updateCounterX(column: number) { // Mise à jour du patterne d'une colonne
    const values: number[] = []
    this.grid.forEach(line => values.push(line[column]))
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
    this.counterX[column].text(text)
    this.counterX[column].offsetX(this.counterX[column].width())
  }
  updateCounterY(line: number) { // Mise à jour du patterne d'une ligne
    const values = this.grid[line]
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
    let text = res.toString().replaceAll(',', '\n')
    if(text === '')
      text = '0'
    this.counterY[line].text(text)
    this.counterY[line].offsetY(this.counterY[line].height())
  }

  async publish() {
    this.loading = true
    let count = 0 // Initialisation du compteur de case noire
    this.grid.forEach(l => { // Pour chaque ligne de la grille
      count += l.filter(v => this.color[v] === 'black').length // On ajoute au compteur le nombre de fois qu'il y a une case noire
    })
    if(count < 4) {
      ElMessage('Il faut un minimum de 4 cases noircies')
    } else {
      this.level.pattern = JSON.stringify(this.grid)
      const {title, description, size, pattern} = this.level
      this.level = (await axios.post('/levels', {
        title: title,
        description: description,
        size: size,
        pattern: pattern
      }, {
        headers: {
          'Authorization': localStorage.getItem('token')
        }
      })).data
      this.$router.push('/editLevel/' + this.level.id)
    }
    this.loading = false
  }

}
</script>

<style scoped lang="stylus">
.konva
  margin auto
  width fit-content
  border-radius 1em

.form
  width 500px
  margin auto
</style>
