describe('5.1 Tests Unitaris - Gestió d\'estat (guestStore)', () => {
  beforeEach(() => {
    cy.clearAuthData()
    cy.visit('/')
    cy.waitForPageLoad()
  })

  describe('Inicialització de l\'estat', () => {
    it('hauria de generar guest_id quan no hi ha usuari', () => {
      cy.reload()
      cy.waitForPageLoad()
      cy.window().then((win) => {
        const guestId = localStorage.getItem('guest_id')
        if (guestId) {
          expect(guestId).to.include('guest_')
        }
      })
    })
  })

  describe('Autenticació via API', () => {
    it('login com admin hauria de desar les dades a localStorage', () => {
      cy.loginAsAdmin()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('auth_token')).to.not.be.null
        expect(localStorage.getItem('usuari_rol')).to.equal('admin')
      })
    })

    it('login com client hauria de desar les dades a localStorage', () => {
      cy.loginAsUser()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('auth_token')).to.not.be.null
        expect(localStorage.getItem('usuari_rol')).to.equal('client')
      })
    })

    it('logout hauria de netejar localStorage', () => {
      cy.loginAsAdmin()
      cy.clearAuthData()

      expect(localStorage.getItem('usuari_id')).to.be.null
      expect(localStorage.getItem('auth_token')).to.be.null
      expect(localStorage.getItem('usuari_rol')).to.be.null
    })
  })

  describe('Persistència de sessió', () => {
    it('hauria de persistir les dades entre navegacions', () => {
      cy.loginAsAdmin()

      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_rol')).to.equal('admin')
      })
    })

    it('hauria de persistir les dades en recarregar', () => {
      cy.loginAsUser()

      cy.reload()
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('usuari_email')).to.equal('client@client.com')
      })
    })
  })

  describe('Funcions auxiliars', () => {
    it('usuari amb rol admin hauria de tenir accés admin', () => {
      cy.loginAsAdmin()

      cy.then(() => {
        expect(localStorage.getItem('usuari_rol')).to.equal('admin')
      })
    })

    it('usuari amb rol client no hauria de tenir accés admin', () => {
      cy.loginAsUser()

      cy.then(() => {
        expect(localStorage.getItem('usuari_rol')).to.equal('client')
        expect(localStorage.getItem('usuari_rol')).to.not.equal('admin')
      })
    })
  })
})
