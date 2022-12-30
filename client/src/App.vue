<template>
  <MenuComponent/>
  <router-view/>
</template>

<style lang="stylus">
#app
  font-family Avenir, Helvetica, Arial, sans-serif
  -webkit-font-smoothing antialiased
  -moz-osx-font-smoothing grayscale
  text-align center
  margin-top 60px
</style>
<script>
import MenuComponent from "@/components/MenuComponent";
export default {
  components: {MenuComponent}
}
</script>
