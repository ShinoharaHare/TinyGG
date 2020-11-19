import axios, { AxiosStatic } from 'axios'

declare global {
    const axios: AxiosStatic
    interface Window {
        axios: AxiosStatic
    }
}
axios.defaults.validateStatus = (status) => status >= 200 && status <= 600
window.axios = axios
