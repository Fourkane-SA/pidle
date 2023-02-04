<template>
  <div />
</template>

<script lang="ts">

import {Vue} from "vue-class-component";

export default class LogoutView extends Vue {
  mounted() { // Supprime le token du localstorage et redirige vers l'accueil
    localStorage.clear()
    location.pathname = '/'
  }
}
</script>

<style scoped>

</style>
