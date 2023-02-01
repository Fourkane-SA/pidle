<template>
  <el-container>
    <el-header>
      <h1>Bienvenue sur Pidle</h1>
    </el-header>
    <el-main style="width: 70%; margin: auto;" >
      <h2>Qu'est-ce que le Picross ?</h2>
      <p style="text-align: justify; ">Le Picross est un jeu de logique consistant à remplir une grille en utilisant des chiffres comme indices pour
        déterminer la couleur des cellules. Les chiffres indiquent combien de cellules consécutives doivent être
        remplies sur la même ligne ou colonne. Une fois que toutes les cellules ont été remplies correctement, une image
        cachée apparaît.</p>
      <h2>Créez vos propres parties</h2>
      <p style="text-align: justify; ">Avec Pidle, vous pouvez créer vos propres parties de Picross grâce à un éditeur de niveaux pour créer la
        grille. Une fois votre grille créée, vous pourrez jouer à votre propre partie ou la partager avec vos amis !</p>
      <div>
        <img src="editeur.png" width="400" height="400" alt="Illustration de la création d'une partie" />
        <p style="font-style: italic;">Editeur de niveaux</p>
      </div>
      <h2>Jouez à des parties différentes</h2>
      <p style="text-align: justify; ">Avec Pidle, vous pouvez jouer à différentes parties de Picross créées par vous-même ou par d'autres
        utilisateurs. Chaque partie est illustrée par une image qui apparaîtra une fois que vous avez rempli
        correctement la grille.</p>
      <div>
        <img src="partie.png" width="400" height="400" alt="Illustration d'une partie en cours" />
        <p style="font-style: italic;">Une partie en cours</p>
      </div>
    </el-main>
  </el-container>
</template>
<script lang="ts">
import { Vue } from 'vue-class-component';

export default class HomeView extends Vue { }
</script>
