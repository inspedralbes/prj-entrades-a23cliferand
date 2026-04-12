import { defineConfig } from "cypress";

export default defineConfig({
  allowCypressEnv: true,
  viewportWidth: 1280,
  viewportHeight: 720,
  video: false,
  screenshotOnRunFailure: true,
  defaultCommandTimeout: 10000,
  e2e: {
    baseUrl: "http://localhost:3000",
    setupNodeEvents(on, config) {
      return config;
    },
    specPattern: "cypress/e2e/**/*.cy.{js,jsx,ts,tsx}",
    supportFile: "cypress/support/e2e.js",
  },
  env: {
    apiUrl: "http://localhost:8000/api",
    socketUrl: "http://localhost:3001",
  },
});
