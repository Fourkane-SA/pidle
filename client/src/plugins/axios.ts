import axios from 'axios'

const instance = axios.create({
    baseURL: 'https://api.pidle.fourkane.me/api'
})

export default instance
