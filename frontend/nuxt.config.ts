// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },

  typescript: {
    typeCheck: false,
  },

  css: ['~/assets/css/main.css'],
  
  app: {
    head: {
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
      ]
    }
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  build: {
    transpile: ['chart.js']
  },

  // HAPUS AJA BLOK NITRO INI - nggak perlu
  // nitro: {
  //   preset: 'node-server',
  //   devStorage: {
  //     memory: false
  //   }
  // },

  vite: {
    server: {
      fs: {
        strict: false
      },
      watch: {
        usePolling: false  // ← INI YANG PENTING
      }
    },
    optimizeDeps: {
      include: ['chart.js']
    }
  },


  sourcemap: false
})