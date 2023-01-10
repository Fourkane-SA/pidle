<template>
  <LevelComponent />
</template>

<script>

import {Options, Vue} from "vue-class-component";
import LevelComponent from "@/components/LevelComponent";

@Options({
  components: {
    LevelComponent
  },
})
export default class LevelView extends Vue {}
</script>

<style scoped>

</style>
