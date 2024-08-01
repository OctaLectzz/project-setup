export default ({ router }) => {
  router.beforeEach((to, from, next) => {
    // Check if the page requires authentication
    if (to.meta.requiresAuth) {
      const role = localStorage.getItem('role')

      // Check if the user has the required role to access the page
      if (role && to.meta.roles.includes(role)) {
        // If you have the appropriate role, continue navigation
        next()
      } else {
        // If it does not have the appropriate role, redirect to another page or display an error message.
        if (to.path !== '/') {
          next('/')
        } else {
          // Avoid redirection loop, if already on "/", just let it pass
          next()
        }
      }
    } else {
      // If the page does not require authentication, continue navigation
      next()
    }
  })
}
