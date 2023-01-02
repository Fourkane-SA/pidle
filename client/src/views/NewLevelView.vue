<template>
  <h1>Créer un niveau</h1>
  <LevelEditor />
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import LevelEditor from "@/components/LevelEditorComponent.vue";

@Options({
  components: {
    LevelEditor
  },
})
export default class NewLevelView extends Vue {}
</script>

<style scoped>

</style>
