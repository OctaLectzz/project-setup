import { defineStore } from 'pinia'
import { server, headers, headersImage } from '/src/boot/axios'

export const useUserStore = defineStore('user', {
  actions: {
    async all() {
      return await server.get('api/user', { headers })
    },

    async dashboard() {
      return await server.get('api/user/dashboard', { headers })
    },

    async show(username) {
      return await server.get(`api/user/${username}`, { headers })
    },

    async create(data) {
      return await server.post('api/user', data, { headers: headersImage })
    },

    async edit(data) {
      return await server.post(`api/user/${data.id}`, data, { headers: headersImage })
    },

    async delete(data) {
      return await server.delete(`api/user/${data}`, { headers })
    }
  }
})
