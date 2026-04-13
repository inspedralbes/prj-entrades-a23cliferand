describe('5.2 Tests de Rutes', () => {
  beforeEach(() => {
    cy.clearAuthData()
  })

  describe('Rutes públiques', () => {
    it('hauria de carregar la pàgina inicial (cartellera)', () => {
      cy.visit('/')
      cy.waitForPageLoad()
      cy.url().should('eq', Cypress.config().baseUrl + '/')
      cy.get('body').should('be.visible')
    })

    it('hauria de carregar la pàgina de login', () => {
      cy.visit('/auth/login')
      cy.waitForPageLoad()
      cy.url().should('include', '/auth/login')
    })

    it('hauria de carregar la pàgina de registre', () => {
      cy.visit('/auth/register')
      cy.waitForPageLoad()
      cy.url().should('include', '/auth/register')
    })
  })

  describe('Rutes dinàmiques', () => {
    it('hauria de navegar a la pàgina d\'una pel·lícula amb ID vàlid', () => {
      cy.visit('/pelicula/tt1234567')
      cy.waitForPageLoad()
      cy.url().should('include', '/pelicula/tt1234567')
    })

    it('hauria de carregar la pàgina de compra amb ID de sessió', () => {
      cy.visit('/compra/1')
      cy.waitForPageLoad()
      cy.url().should('include', '/compra/1')
    })

    it('hauria de carregar la pàgina de confirmació amb ID de reserva', () => {
      cy.visit('/confirmacio/reserva-123')
      cy.waitForPageLoad()
      cy.url().should('include', '/confirmacio/reserva-123')
    })

    it('hauria de gestionar IDs numèrics a les rutes', () => {
      cy.visit('/compra/42')
      cy.waitForPageLoad()
      cy.url().should('include', '/compra/42')
    })

    it('hauria de gestionar IDs alfanumèrics a les rutes', () => {
      cy.visit('/confirmacio/abc-123-def')
      cy.waitForPageLoad()
      cy.url().should('include', '/confirmacio/abc-123-def')
    })
  })

  describe('Paràmetres d\'URL', () => {
    it('URL amb query params hauria de ser llegida correctament', () => {
      cy.visit('/compra/1?pas=2&seient=A5')
      cy.waitForPageLoad()
      cy.url().should('include', 'pas=2')
      cy.url().should('include', 'seient=A5')
    })

    it('URL amb hash hauria de ser llegit correctament', () => {
      cy.visit('/#section-id')
      cy.url().should('include', '#section-id')
    })
  })

  describe('Redireccions bàsiques - Pàgines protegides', () => {
    it('hauria de redirigir a login quan s\'accedeix a pàgina protegida sense auth', () => {
      cy.visit('/LesMevesEntrades')
      cy.waitForPageLoad()
      cy.url().should('include', '/auth/login')
    })

    it('hauria de permetre accés a pàgina protegida amb login real (client)', () => {
      cy.loginAsUser()
      cy.visit('/LesMevesEntrades')
      cy.waitForPageLoad()
      cy.url().should('include', '/LesMevesEntrades')
    })

    it('hauria de redirigir a home quan s\'accedeix a admin sense permisos', () => {
      cy.visit('/admin')
      cy.waitForPageLoad()
      cy.url().should('not.include', '/admin')
    })

    it('hauria de permetre accés a admin amb login real', () => {
      cy.loginAsAdmin()
      cy.visit('/admin')
      cy.waitForPageLoad()
      cy.url().should('include', '/admin')
    })

    it('hauria de tornar a la pàgina anterior en fer back', () => {
      cy.visit('/')
      cy.visit('/pelicula/tt1234567')
      cy.go('back')
      cy.url().should('eq', Cypress.config().baseUrl + '/')
    })
  })

  describe('Navegació entre rutes', () => {
    it('hauria de navegar a través del botó enrere del navegador', () => {
      cy.visit('/')
      cy.visit('/auth/login')
      cy.visit('/auth/register')
      cy.go('back')
      cy.url().should('include', '/auth/login')
    })

    it('hauria de navegar endavant', () => {
      cy.visit('/')
      cy.visit('/auth/login')
      cy.go('back')
      cy.go('forward')
      cy.url().should('include', '/auth/login')
    })

    it('hauria de recarregar la pàgina mantenint la ruta', () => {
      cy.visit('/pelicula/tt1234567')
      cy.reload()
      cy.url().should('include', '/pelicula/tt1234567')
    })
  })

  describe('Rutes inexistents', () => {
    it('hauria de manejar ruta inexistent', () => {
      cy.visit('/ruta-inexistent-12345', { failOnStatusCode: false })
      cy.get('body').should('be.visible')
    })

    it('hauria de manejar IDs invàlids a les rutes dinàmiques', () => {
      cy.visit('/pelicula/invalid-id-format', { failOnStatusCode: false })
      cy.waitForPageLoad()
      cy.get('body').should('be.visible')
    })
  })
})
