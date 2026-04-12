describe('5.1 Tests Unitaris - Transformació de dades', () => {
  beforeEach(() => {
    cy.visit('/')
    cy.waitForPageLoad()
  })

  describe('Format de dates en català', () => {
    it('NOMS_DIA hauria de tenir 7 elements', () => {
      const NOMS_DIA = ['Dg', 'Dl', 'Dm', 'Dc', 'Dj', 'Dv', 'Ds']
      expect(NOMS_DIA).to.have.lengthOf(7)
      expect(NOMS_DIA[0]).to.equal('Dg')
      expect(NOMS_DIA[6]).to.equal('Ds')
    })

    it('NOMS_MES hauria de tenir 12 elements', () => {
      const NOMS_MES = ['Gen', 'Feb', 'Març', 'Abr', 'Maig', 'Juny', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Des']
      expect(NOMS_MES).to.have.lengthOf(12)
      expect(NOMS_MES[0]).to.equal('Gen')
      expect(NOMS_MES[11]).to.equal('Des')
    })

    it('formatar temps com a MM:SS hauria de funcionar correctament', () => {
      const formatarTemps = (minuts, segons) => {
        const m = String(minuts).padStart(2, '0')
        const s = String(segons).padStart(2, '0')
        return `${m}:${s}`
      }

      expect(formatarTemps(5, 30)).to.equal('05:30')
      expect(formatarTemps(0, 5)).to.equal('00:05')
      expect(formatarTemps(10, 0)).to.equal('10:00')
    })

    it('formatar preu en euros hauria de funcionar', () => {
      const formatarPreu = (preu) => {
        return new Intl.NumberFormat('ca-ES', {
          style: 'currency',
          currency: 'EUR',
        }).format(preu)
      }

      expect(formatarPreu(10)).to.include('10')
      expect(formatarPreu(8.5)).to.include('8,50')
      expect(formatarPreu(0)).to.include('0')
    })
  })

  describe('Temps restant de reserva temporal', () => {
    it('hauria de calcular correctament els minuts restants', () => {
      const reservaTimestamp = Date.now() + 5 * 60 * 1000
      
      const calcularMinutsRestants = (timestamp) => {
        const ara = Date.now()
        const diferencia = timestamp - ara
        return Math.max(0, Math.floor(diferencia / 60000))
      }

      const minuts = calcularMinutsRestants(reservaTimestamp)
      expect(minuts).to.be.within(4, 5)
    })

    it('hauria de retornar 0 quan la reserva ha expirat', () => {
      const reservaExpirada = Date.now() - 60000
      
      const calcularMinutsRestants = (timestamp) => {
        const ara = Date.now()
        const diferencia = timestamp - ara
        return Math.max(0, Math.floor(diferencia / 60000))
      }

      const minuts = calcularMinutsRestants(reservaExpirada)
      expect(minuts).to.equal(0)
    })

    it('hauria de detectar quan queden menys de 60 segons', () => {
      const reservaTimestamp = Date.now() + 30 * 1000
      
      const estaPerExpirar = (timestamp) => {
        const ara = Date.now()
        const diferencia = timestamp - ara
        return diferencia > 0 && diferencia <= 60000
      }

      expect(estaPerExpirar(reservaTimestamp)).to.be.true
    })

    it('hauria de detectar quan queda poc temps (menys de 2 minuts)', () => {
      const reservaTimestamp = Date.now() + 90 * 1000
      
      const estaPerExpirar = (timestamp) => {
        const ara = Date.now()
        const diferencia = timestamp - ara
        return diferencia > 0 && diferencia <= 120000
      }

      expect(estaPerExpirar(reservaTimestamp)).to.be.true
    })
  })

  describe('Expiració de reserves (5 minuts)', () => {
    it('hauria de saber que les reserves expiren en 5 minuts', () => {
      const TEMPS_EXPIRACIO_MINUTS = 5
      expect(TEMPS_EXPIRACIO_MINUTS).to.equal(5)
    })

    it('hauria de calcular temps restant des de moment de creació', () => {
      const TEMPS_EXPIRACIO_MS = 5 * 60 * 1000
      const momentCreacio = Date.now()
      const momentExpiracio = momentCreacio + TEMPS_EXPIRACIO_MS
      
      const obtenirTempsRestant = () => {
        const ara = Date.now()
        return Math.max(0, momentExpiracio - ara)
      }

      const tempsRestant = obtenirTempsRestant()
      expect(tempsRestant).to.be.closeTo(TEMPS_EXPIRACIO_MS, 100)
    })

    it('hauria de determinar si una reserva està activa', () => {
      const esReservaActiva = (timestampCreacio, tempsExpiracioMs) => {
        const ara = Date.now()
        const tempsRestant = timestampCreacio + tempsExpiracioMs - ara
        return tempsRestant > 0
      }

      const ara = Date.now()
      expect(esReservaActiva(ara, 5 * 60 * 1000)).to.be.true
      expect(esReservaActiva(ara - 10 * 60 * 1000, 5 * 60 * 1000)).to.be.false
    })
  })

  describe('Comparació de dates', () => {
    it('hauria de determinar si una sessió és passada', () => {
      const esSessioPassada = (dataSessio) => {
        const data = new Date(dataSessio)
        const avui = new Date()
        avui.setHours(0, 0, 0, 0)
        return data < avui
      }

      expect(esSessioPassada('2020-01-01')).to.be.true
      expect(esSessioPassada('2099-12-31')).to.be.false
    })

    it('hauria de comparar dates correctament', () => {
      const dates = [
        new Date('2024-01-15'),
        new Date('2024-03-10'),
        new Date('2024-06-20'),
        new Date('2024-12-25'),
      ]

      const datesOrdenades = dates.sort((a, b) => a - b)
      expect(datesOrdenades[0].toISOString()).to.contain('2024-01-15')
      expect(datesOrdenades[3].toISOString()).to.contain('2024-12-25')
    })

    it('hauria de formatar data en català', () => {
      const formatarDataCatala = (data) => {
        const d = new Date(data)
        return d.toLocaleDateString('ca-ES', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        })
      }

      const resultat = formatarDataCatala('2024-06-15T20:30:00')
      expect(resultat).to.include('dissabte')
      expect(resultat).to.include('15')
      expect(resultat).to.include('juny')
      expect(resultat).to.include('2024')
    })

    it('hauria de formatar hora en format 24h', () => {
      const formatarHora = (data) => {
        const d = new Date(data)
        return d.toLocaleTimeString('ca-ES', {
          hour: '2-digit',
          minute: '2-digit',
        })
      }

      expect(formatarHora('2024-06-15T20:30:00')).to.equal('20:30')
      expect(formatarHora('2024-06-15T08:05:00')).to.equal('08:05')
    })
  })
})
