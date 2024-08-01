import { defineStore } from 'pinia'
import { server, headers } from '/src/boot/axios'

export const useAuthStore = defineStore('auth', {
  actions: {
    async register(username, name, email, password, confirm_password, avatar, phone_number, domicile, gender, pronouns, bio) {
      const res = await server.post('api/auth/register', { username, name, email, password, confirm_password, avatar, phone_number, domicile, gender, pronouns, bio })

      // localStorage.setItem('email', res.data.data.email)

      return res
    },

    async login(email, password) {
      return await server.post('api/auth/login', { email, password })
    },

    async verify(email, verification_code) {
      const res = await server.post('api/auth/verify-email', { email, verification_code })

      localStorage.setItem('token', res.data.data.token)
      localStorage.setItem('role', res.data.data.user.role)

      return res
    },

    async logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      localStorage.removeItem('email')
      window.location.reload()

      return await server.get('api/auth/logout', { headers })
    },

    async resendVerificationEmail() {
      return await server.post('api/auth/email/resend', {}, { headers })
    }
  }
})
