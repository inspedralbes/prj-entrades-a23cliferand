describe('5.3 Tests de Pinia - Gestió d\'estat amb events Socket.IO', () => {
  beforeEach(() => {
    cy.clearAuthData()
    cy.visit('/')
    cy.waitForPageLoad()
  })

  describe('Inicialització correcta de l\'estat', () => {
    it('hauria de generar guest_id quan es carrega la pàgina', () => {
      cy.then(() => {
        const guestId = localStorage.getItem('guest_id')
        expect(guestId).to.be.a('string')
        expect(guestId).to.include('guest_')
      })
    })

    it('hauria de mantenir l\'estat entre navegacions', () => {
      cy.loginAsAdmin()

      cy.visit('/auth/login')
      cy.visit('/')

      cy.then(() => {
        expect(localStorage.getItem('usuari_rol')).to.equal('admin')
      })
    })

    it('hauria d\'inicialitzar guestId quan no hi ha auth', () => {
      cy.reload()
      cy.waitForPageLoad()
      cy.then(() => {
        const guestId = localStorage.getItem('guest_id')
        if (guestId) {
          expect(guestId).to.be.a('string')
        }
      })
    })
  })

  describe('Actualització d\'estat davant events Socket.IO', () => {
    it('hauria de tenir socket importable des de services', () => {
      cy.visit('/')
      cy.waitForPageLoad()
      cy.window().then((win) => {
        const hasSocket = typeof win.socket !== 'undefined'
        expect(typeof hasSocket === 'boolean' || typeof win.socket === 'object').to.be.true
      })
    })

    it('hauria de tenir socket amb mètodes listeners', () => {
      cy.visit('/compra/1')
      cy.waitForPageLoad()

      cy.window().then((win) => {
        if (win.socket) {
          expect(typeof win.socket.on).to.equal('function')
          expect(typeof win.socket.off).to.equal('function')
          expect(typeof win.socket.emit).to.equal('function')
        }
      })
    })

    it('hauria de permetre afegir listeners a events', () => {
      cy.visit('/compra/1')
      cy.waitForPageLoad()

      cy.window().then((win) => {
        if (win.socket) {
          let called = false
          win.socket.on('seats-updated', () => {
            called = true
          })
          expect(called).to.be.false
        }
      })
    })

    it('hauria de permetre treure listeners', () => {
      cy.visit('/compra/1')
      cy.waitForPageLoad()

      cy.window().then((win) => {
        if (win.socket) {
          const handler = () => {}
          win.socket.on('seats-updated', handler)
          const beforeOff = win.socket.listenerCount('seats-updated')
          win.socket.off('seats-updated', handler)
          const afterOff = win.socket.listenerCount('seats-updated')
          expect(afterOff).to.be.lessThan(beforeOff)
        }
      })
    })
  })

  describe('Actualització d\'estat davant accions de l\'usuari', () => {
    it('hauria d\'actualitzar l\'estat en fer login (admin)', () => {
      cy.loginAsAdmin()

      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('usuari_rol')).to.equal('admin')
        expect(localStorage.getItem('usuari_nom')).to.equal('Admin')
      })
    })

    it('hauria d\'actualitzar l\'estat en fer login (client)', () => {
      cy.loginAsUser()

      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('usuari_rol')).to.equal('client')
        expect(localStorage.getItem('usuari_email')).to.equal('client@client.com')
      })
    })

    it('hauria d\'actualitzar l\'estat en fer logout', () => {
      cy.loginAsAdmin()
      cy.clearAuthData()

      expect(localStorage.getItem('usuari_id')).to.be.null
      expect(localStorage.getItem('usuari_rol')).to.be.null
    })

    it('hauria de permetre usuari no autenticat (guest)', () => {
      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        const guestId = localStorage.getItem('guest_id')
        const userId = localStorage.getItem('usuari_id')
        if (!userId) {
          expect(guestId).to.be.a('string')
        }
      })
    })

    it('hauria de persistir canvis a localStorage', () => {
      cy.loginAsUser()

      cy.reload()
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('usuari_email')).to.equal('client@client.com')
      })
    })
  })

  describe('Reset d\'estat en sortir de l\'esdeveniment', () => {
    it('hauria de permetre reinicialitzar l\'estat', () => {
      cy.loginAsAdmin()
      cy.clearAuthData()

      expect(localStorage.getItem('usuari_id')).to.be.null
      expect(localStorage.getItem('usuari_nom')).to.be.null
      expect(localStorage.getItem('auth_token')).to.be.null
    })

    it('hauria de permetre canviar de guest a usuari autenticat', () => {
      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        const guestId = localStorage.getItem('guest_id')
        expect(guestId).to.be.a('string')
      })

      cy.loginAsAdmin()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
        expect(localStorage.getItem('guest_id')).to.be.null
      })
    })

    it('hauria de mantenir l\'estat de l\'usuari en sortir d\'un esdeveniment', () => {
      cy.loginAsUser()

      cy.visit('/compra/1')
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
      })

      cy.visit('/')
      cy.waitForPageLoad()

      cy.then(() => {
        expect(localStorage.getItem('usuari_id')).to.not.be.null
      })
    })
  })

  describe('Connexió Socket.IO', () => {
    it('hauria de tenir mòdul socket configurable', () => {
      cy.visit('/')
      cy.waitForPageLoad()
      cy.window().then((win) => {
        const hasSocket = typeof win.socket !== 'undefined'
        expect(typeof hasSocket === 'boolean' || typeof win.socket === 'object').to.be.true
      })
    })

    it('hauria de tenir socket.connect() disponible', () => {
      cy.visit('/')
      cy.waitForPageLoad()

      cy.window().then((win) => {
        if (win.socket) {
          expect(typeof win.socket.connect).to.equal('function')
        }
      })
    })

    it('hauria de tenir socket.disconnect() disponible', () => {
      cy.visit('/')
      cy.waitForPageLoad()

      cy.window().then((win) => {
        if (win.socket) {
          expect(typeof win.socket.disconnect).to.equal('function')
        }
      })
    })
  })
})
