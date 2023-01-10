<template>
  <div class="content">
    <h1>Règles</h1>
    <p>Pidle est un site permettant à des utilisateurs de créer et partager leurs niveaux de Picross.
    </p>
    <h2>Comment jouer ?</h2>
    <p>
      Le Picross est un jeu de reflexion dans laquelle l'objectif est de retrouver les cases noires d'une image.
      Afin d'aider le joueur à retrouver cette image, il dispose d'informations sur la taille de chacune des blocs noirs se suivant sur chaque ligne et chaque colonne
      Par exemple, si l'indice indiqué sur une ligne est <strong> 2 1 </strong>, cette ligne contiendra 2 cases noires de suite puis 1 case noire
      Une fois toutes les cases noires découvertes, le dessin coloré se révélera.
    </p>
    <h2>Comment créer un niveau ?</h2>
    <p>Afin de créer un niveau, il suffit d'appuyer sur le bouton <strong> Créer un niveau </strong> dans le menu.
      Il faut ensuite renseigner un titre et une description du niveau ainsi que la dimension de l'image (comprise entre 5 et 15).
      Ensuite, une page permettant la création du dessin s'affichera. C'est dans cette page que l'image correspondant au niveau est créée.
      Seuls les cases noires seront prises en compte lors d'une partie cependant, il est toujours possible d'ajouter d'autres couleurs à l'image qui seront révélés à la fin de la partie.</p>

  </div>
</template>

<script>
export default {
  name: "RulesView"
}
</script>

<style scoped lang="stylus">
.content
  border solid #409eff 2px
  border-radius 1em
  background rgba(64, 158, 255, 0.2)
  width 80%
  padding 1em
  margin 2em auto
  p, h2
    text-align justify
    margin 1em
</style>
