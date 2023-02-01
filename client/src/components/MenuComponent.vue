<template>
  <el-menu mode="horizontal" :ellipsis="false" router="router" :default-active="active">
    <template v-if="!connected">
      <el-menu-item index="/">PIDLE</el-menu-item>
      <div class="flex-grow" />
      <el-menu-item index="/register">Inscription</el-menu-item>
      <el-menu-item index="/login">Connexion</el-menu-item>
    </template>
    <template v-else>
      <el-menu-item index="/home">PIDLE</el-menu-item>
      <div class="flex-grow" />
      <el-menu-item index="/newLevel">Créer un niveau</el-menu-item>
      <el-menu-item index="/myLevels">Mes niveaux</el-menu-item>
      <el-menu-item index="/rules">Règles</el-menu-item>

      <el-sub-menu index="1">
        <template #title>
          <el-icon><User /></el-icon>
        </template>
        <el-menu-item index="/profile">Mon Profil</el-menu-item>
        <el-menu-item index="/history">Historique</el-menu-item>
        <el-menu-item index="/favoris">Favoris</el-menu-item>
        <el-menu-item index="/logout">Déconnexion</el-menu-item>
      </el-sub-menu>
    </template>
  </el-menu>
</template>

<script lang="ts">
import {Options, Vue} from "vue-class-component";
import {User} from "@element-plus/icons-vue";
import { ref } from "vue";


@Options({
  components: {
    User
  },
})

export default class MenuComponent extends Vue {

  state = ref('')
  active = location.pathname
  get connected() {
    return localStorage.getItem('token') != null
  }

  mounted() {
    if(!this.connected && !['/', '/login', '/register'].includes(location.pathname))
      location.pathname = '/'
    else if (this.connected && ['/', '/login', '/register'].includes(location.pathname))
      location.pathname = '/home'
  }

}
</script>

<style scoped lang="stylus">
.flex-grow
  flex-grow 1

.search
  height 100%
  padding-top 1em
</style>
