<template>
  <div class="level-preview" ref="canvas" />
</template>

<script lang="ts">

import {Vue} from "vue-class-component";
import {Prop} from "vue-property-decorator";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import Konva from "konva";
import {Game} from "@/models/Game";

export default class LevelPreviewComponent extends Vue { // Composant permettant d'afficher un apercu de niveau
  @Prop({ required: true }) id!: number

  level: Level = new Level()

  async mounted() {
    this.level = (await axios.get('/levels/' + this.id)).data // Recupération du niveau
    const canvas = new Konva.Stage({ // Création du canevas
      container: this.$refs.canvas as HTMLDivElement,
      width: 100,
      height: 100
    })
    const layer = new Konva.Layer()
    const pattern = JSON.parse(this.level.pattern) // Recupération du patterne à dessiner
    const scale = 100 / this.level.size // Echelle d'une case
    if(await this.hideImage()) { // Si l'aperçu du niveau ne peut pas être affiché
      const rect = new Konva.Rect({ // Affichage d'un fond blanc sur le canevas
        x: 0,
        y: 0,
        width: 100,
        height: 100,
        fill: 'white'
      })
      const text = new Konva.Text({ // Affichage d'un ? sur le canevas
        text: "?",
        fontSize: 100,
        x: 50,
        y: 50
      })
      text.offsetX(text.width() / 2)
      text.offsetY(text.height() / 2)
      layer.add(rect)
      layer.add(text)
    } else { // Si l'aperçu du niveau peut êter affiché
      const color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
      for(let i=0; i < this.level.size; i++) {
        for(let j=0; j < this.level.size; j++) { // Parcourt de toutes les cases du niveau
          const rect = new Konva.Rect({ // Colorie une case
            x: i*scale,
            y: j*scale,
            width: scale,
            height: scale,
            fill: color[pattern[i][j]], // Recupère la couleur de la case
            stroke: color[pattern[i][j]]
          })
          layer.add(rect) // Ajout du rectangle dans le layer
        }
      }
    }
    canvas.add(layer) // Ajout du layer dans le canevas
  }

  async hideImage() { // Retourne vrai si le niveau n'appartient pas à l'utilisateur connecté et l'utilisateur n'a pas terminé ce niveau
    const idUser: number = (await axios.get('/token', {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    const games: Game[] = (await axios.get('/games/byUserId/' + idUser )).data
    return this.level.userId !== idUser && games
        .filter(game => game.levelId === this.level.id)
        .filter(game => game.completed)
        .length === 0
  }
}
</script>

<style scoped>

</style>
