<template>
  <div class="level">
    <div class="level-header">
      <h2 class="level-title">{{ level.title }}</h2>
      <div class="level-status">
        <p class="tested" v-if="level.finished">Testé</p>
        <p v-else>Pas encore testé</p>
        <p class="published" v-if="level.published">Publié</p>
        <p v-else>Pas encore publié</p>
      </div>
    </div>
    <div class="level-display">
      <LevelPreviewComponent class="level-preview" :id="id" />
      <div class="level-info">
        <p class="level-description">{{level.description}}</p>
        <p class="level-updated-at">Dernière mise à jour : {{ new Date(level.updated_at).toLocaleString('fr-FR')}} </p>
      </div>
    </div>
    <div class="level-actions">
      <el-button type="primary" @click="editLevel">Modifier</el-button>
      <el-button type="danger" @click="deleteLevel">Supprimer</el-button>
    </div>
  </div>
</template>

<script lang="ts">
import {Options, Vue} from "vue-class-component";
import {Level} from "@/models/Level";
import {Prop} from "vue-property-decorator";
import axios from "@/plugins/axios";
import LevelPreviewComponent from "@/components/LevelPreviewComponent.vue";

@Options({
  components: {
    LevelPreviewComponent
  },
})

export default class MyLevelComponent extends Vue {
  level: Level = new Level()
  @Prop({ required: true }) id!: number

  async mounted() {
    this.level = (await axios.get('/levels/' + this.id)).data
    if(this.level.description.length > 50)
      this.level.description = this.level.description.substring(0, 50) + '...'

  }

  editLevel() {
    this.$router.push('editLevel/' + this.id)
  }

  async deleteLevel() {
    await axios.delete('/levels/' + this.id, {
      headers: {
        'Authorization': localStorage.getItem('token')
      }
    })
  }
}
</script>

<style scoped lang="stylus">
.level
  width 600px
  margin 2em auto
  background rgba(255, 255, 255, 0.2)
  border solid #409eff 1px
  border-radius 1em
  padding 1em
  display flex
  flex-direction column

.level-header
  display flex
  align-items center
  margin-bottom 2em
  h2
    font-size 20px
    color #409eff
    margin-right 1em
  .level-status
    display flex
    align-items center
    p
      font-size 14px
      margin 0 1em
      &.tested
        color #00ff00
      &.published
        color #00e1ff

.level-display
  display flex
  align-items center
  flex-wrap wrap

.level-preview
  width 100px
  height 100px
  margin 20px 0
  canvas
    border 1px solid black

.level-info
  display flex
  flex-direction column
  padding 1em
  margin auto
  .level-description
    font-size 16px
    margin-bottom 10px
    white-space pre-wrap
  .level-updated-at
    font-size 14px
    margin-bottom 10px
    text-align left

.level-actions
  display flex
  justify-content space-between
  margin-top 2em
  button
    font-size 14px

</style>
