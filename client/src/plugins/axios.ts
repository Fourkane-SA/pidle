import axios from 'axios'

const instance = axios.create({
    baseURL: 'http://157.230.100.33/api'
})

export default instance
