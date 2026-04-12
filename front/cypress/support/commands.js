const API_URL = 'http://localhost:8000/api'

Cypress.Commands.add('initPiniaStore', () => {
  cy.clearLocalStorage()
  cy.visit('/')
})

Cypress.Commands.add('mockSocketEvents', () => {
  cy.window().then((win) => {
    if (win.socket) {
      win.socket.removeAllListeners('seats-updated')
      win.socket.removeAllListeners('seats-released')
      win.socket.removeAllListeners('seat-refresh')
      win.socket.removeAllListeners('connect')
      win.socket.removeAllListeners('disconnect')
    }
  })
})

Cypress.Commands.add('loginAsAdmin', () => {
  cy.clearAuthData()
  cy.request({
    method: 'POST',
    url: `${API_URL}/auth/login`,
    body: {
      email: 'admin@admin.com',
      password: 'Jupiter1',
    },
    failOnStatusCode: false,
  }).then((response) => {
    if (response.status === 200 && response.body.token) {
      const { token, user } = response.body
      localStorage.setItem('usuari_id', String(user.id))
      localStorage.setItem('auth_token', token)
      localStorage.setItem('usuari_nom', user.nom)
      localStorage.setItem('usuari_email', user.email)
      localStorage.setItem('usuari_rol', user.rol)
    }
  })
})

Cypress.Commands.add('loginAsUser', () => {
  cy.clearAuthData()
  cy.request({
    method: 'POST',
    url: `${API_URL}/auth/login`,
    body: {
      email: 'client@client.com',
      password: 'Jupiter1',
    },
    failOnStatusCode: false,
  }).then((response) => {
    if (response.status === 200 && response.body.token) {
      const { token, user } = response.body
      localStorage.setItem('usuari_id', String(user.id))
      localStorage.setItem('auth_token', token)
      localStorage.setItem('usuari_nom', user.nom)
      localStorage.setItem('usuari_email', user.email)
      localStorage.setItem('usuari_rol', user.rol)
    }
  })
})

Cypress.Commands.add('clearAuthData', () => {
  localStorage.removeItem('usuari_id')
  localStorage.removeItem('auth_token')
  localStorage.removeItem('usuari_nom')
  localStorage.removeItem('usuari_email')
  localStorage.removeItem('usuari_rol')
  localStorage.removeItem('guest_id')
})

Cypress.Commands.add('waitForPageLoad', () => {
  cy.get('body').should('be.visible')
  cy.wait(1000)
})
