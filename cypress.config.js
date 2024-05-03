const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: "http://nishant.test/",
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
