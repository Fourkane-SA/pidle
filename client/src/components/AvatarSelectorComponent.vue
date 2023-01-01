<template>
  <div class="avatar-selector">
    <div class="selected-avatar">
      <img :src="'/profiles/' + selectedId + '.png'">
    </div>
    <div class="avatar-options">
      <div v-for="id in listId" :key="id">
        <img v-bind:src="'profiles/' + id + '.png'" @click="updateSelectedId(id)">
      </div>
    </div>
  </div>
  <a href="https://fr.vecteezy.com">Avatar Vecteurs par Vecteezy</a>
</template>

<script lang="ts">
import { Prop, Emit } from 'vue-property-decorator'
import {Vue} from "vue-class-component";


export default class AvatarSelector extends Vue {
  @Prop({ required: true }) selectedId!: number
  @Prop({ required: true }) listId!: number[]

  @Emit('update:selectedId')
  updateSelectedId(id: number) {
    return id
  }
}
</script>

<style scoped lang="stylus">
.avatar-selector
  display: flex
  align-items: center
  .selected-avatar
    img
      width: 100px
  .avatar-options
    display: flex
    flex-wrap: wrap
    img
      width: 50px
      cursor: pointer
      border: 2px solid transparent
      border-radius 50%
      &:hover
        border-color: #409eff

</style>
