<template>
  <el-menu mode="horizontal" :ellipsis="false" router="router" :default-active="test">
    <el-menu-item index="/">PIDLE</el-menu-item>
    <div class="flex-grow" />
    <el-menu-item index="/register">Inscription</el-menu-item>
    <el-menu-item index="/login">Connexion</el-menu-item>
  </el-menu>
</template>

<script lang="ts">
import {Vue} from "vue-class-component";

export default class MenuComponent extends Vue {
  test: string = location.pathname
}
</script>

<style scoped lang="stylus">
.flex-grow
  flex-grow 1

</style>
