<template>
  <div class="edit-level">
    <h1 class="edit-level-title">{{level.title}}</h1>
    <el-input class="edit-level-description" v-model="level.description" type="textarea" placeholder="Description"></el-input>
    <el-alert class="edit-level-alert" title="Vous devez terminer le niveau pour le publier" effect="dark" type="error" :closable="false" v-if="!level.finished"/>
    <!--<div class="level" ref="konva"/>-->
    <LevelComponent class="level" v-on:update:level="setLevelFinished"/>
    <div class="edit-level-actions">
      <el-button type="primary" @click="saveLevel" :loading="loadingSave">Enregistrer</el-button>
      <el-button type="success" @click="publishLevel" :disabled="!level.finished" :loading="loadingPublish" v-if="!this.level.published">Publier</el-button>
      <el-button type="success" @click="privateLevel" :loading="loadingPublish" v-else>Mettre en privé</el-button>
      <el-button type="danger" @click="deleteLevel">Supprimer</el-button>
    </div>
  </div>
</template>

<script lang="ts">

import {Options, Vue} from "vue-class-component";
import {Level} from "@/models/Level";
import axios from "@/plugins/axios";
import LevelComponent from "@/components/LevelComponent.vue";

@Options({
  components: {
    LevelComponent
  },
})

export default class EditLevelView extends Vue {
  level: Level = new Level()
  color = ['black', 'cyan', 'red', 'maroon', 'green', 'darkgreen', 'blue', 'darkblue', 'indigo', 'mediumblue', 'yellow', 'khaki', 'white', 'magenta', 'darkmagenta', 'olive', 'olivedrab', 'orange', 'violet', 'pink', 'mediumpurple', 'cornflowerblue', 'crimson', 'lightblue']
  loadingSave: boolean = false
  loadingPublish: boolean = false
  async mounted() {
    this.level = (await axios.get('/levels/' + this.$route.params.id)).data
  }

  async saveLevel() {
    this.loadingSave = true
    await axios.patch('/levels/' + this.level.id, {
      description: this.level.description,
      finished: this.level.finished
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
    this.loadingSave = false
  }
  async publishLevel() {
    this.loadingPublish = true
    this.level = (await axios.patch('/levels/' + this.level.id, {
      published: true
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.loadingPublish = false
  }

  async privateLevel() {
    this.loadingPublish = true
    this.level =  (await axios.patch('/levels/' + this.level.id, {
      published: false
    }, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })).data
    this.loadingPublish = false
  }
  async deleteLevel() {
    await axios.delete('/levels/' + this.level.id, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
    this.$router.push('/myLevels')
  }

  setLevelFinished(level: Level) {
    this.level = level
    if(this.level.finished)
      this.saveLevel()
  }
}
</script>

<style scoped lang="stylus">
.edit-level
  display: flex
  flex-direction: column
  align-items: center
  .edit-level-title
    font-size: 32px
    color: #409eff
  .edit-level-description
    width: 600px
    margin: 20px 0
  .edit-level-alert
    width fit-content
    margin auto
  .level
    width: 600px
    height: 600px
  .edit-level-actions
    display: flex
    justify-content: space-between
    width: 600px
    margin-top: 20px

</style>
