import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '../views/LoginView.vue'
import HomeLoggedView from '../views/HomeLoggedView.vue'
// @ts-ignore
import LogoutView from '../views/LogoutView.vue'
// @ts-ignore
import ProfileView from '../views/ProfileView.vue'
import EditProfileView from "@/views/editProfile.vue";
import NewLevelView from "@/views/NewLevelView.vue";
import MyLevelsView from "@/views/MyLevelsView.vue"
import EditLevelView from "@/views/EditLevelView.vue";
import RulesView from "@/views/RulesView.vue"
import LevelView from "@/views/LevelView.vue"
import HistoryView from "@/views/HistoryView.vue"

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  {
    path: '/home',
    name: 'homeConnected',
    component: HomeLoggedView
  },
  {
    path: '/logout',
    name: 'logout',
    component: LogoutView
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfileView
  },
  {
    path: '/editProfile',
    name: 'editprofile',
    component: EditProfileView
  },
  {
    path: '/newLevel',
    name: 'newLevel',
    component: NewLevelView
  },
  {
    path: '/myLevels',
    name: 'myLevels',
    component: MyLevelsView
  },
  {
    path: '/editLevel/:id',
    name: 'editLevel',
    component: EditLevelView
  },
  {
    path: '/rules',
    name: 'rules',
    component: RulesView
  },
  {
    path: '/level/:id',
    name: 'level',
    component: LevelView
  },
  {
    path: '/history',
    name: 'history',
    component: HistoryView
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
