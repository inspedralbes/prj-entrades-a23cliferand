import './commands'

Cypress.on('uncaught:exception', (err, runnable) => {
  if (err.message.includes('ResizeObserver')) {
    return false
  }
})

beforeEach(() => {
  cy.clearLocalStorage()
})
