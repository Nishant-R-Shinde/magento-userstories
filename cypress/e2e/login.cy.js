describe('Register page tests', () => {
  beforeEach(() => {
    cy.visit('/customer/account/create/')
  })
  it('correct info', () => {
    cy.get('div.field-name-firstname').within(() => {
      cy.get('input').type("test12")  
    })
    cy.get('div.field-name-lastname').within(() => {
      cy.get('input').type("test12")  
    })
    cy.get('#email_address').type("test12@gmail.com")
    cy.get('#password-strength-meter-label').should('contain.text', 'No Password')
    cy.get('#password').type('Test1234')
    cy.get('#password-confirmation').type('Test1234')
    cy.get('#send2').click()
    cy.wait(5000)
    cy.contains(/Thank you for registering with Main Website Store./i).should('exist')

  })
  it("same credentials", () => {
    cy.get('div.field-name-firstname').within(() => {
      cy.get('input').type("test12")  
    })
    cy.get('div.field-name-lastname').within(() => {
      cy.get('input').type("test12")  
    })
    cy.get('#email_address').type("test12@gmail.com")
    cy.get('#password-strength-meter-label').should('contain.text', 'No Password')
    cy.get('#password').type('Test1234')
    cy.get('#password-confirmation').type('Test1234')
    cy.get('#send2').click()
    cy.contains(/There is already an account with this email address./i).should('exist')
  })
})