import FileDropZone from './FileDropZone.vue'

describe('<FileDropZone />', () => {
  it('renders', () => {
    // see: https://on.cypress.io/mounting-vue
    cy.mount(FileDropZone)
  })
})