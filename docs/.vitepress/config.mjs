import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "nmrXiv",
  description: "FAIR, consensus-driven NMR data repository and computational platform",
  ignoreDeadLinks: true,
 //base: '/nmrxiv/',

  themeConfig: {
    search: {
      provider: 'local'
    },

    logo:  { 
      light: "/logo.svg",
      dark: "/logo-dark.svg",
      alt : "nmrXiv" 
    },

    siteTitle: "",

    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/introduction/intro.md' },
      { text: 'Guides', link: '/developer-guides/architecture.md' }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Overview', link: '/introduction/intro.md' },
          { text: 'Data',
            items: [
              { text: 'File Formats', link: '/introduction/data/formats.md'},
              { text: 'Ontologies', link: '/introduction/data/ontologies.md'},
              { text: 'Schemas', link: '/introduction/data/schemas.md'},
              { text: 'Exemplary Data', link: '/introduction/data/exemplary-data.md'}
            ]
          }
        ],
      },
      {
        text: 'Submission Guides',
        items: [
          { text: 'Data - Life cycle', link: '/submission-guides/data-lifecycle.md' },
          { text: 'Registration', link: '/submission-guides/registration.md' },
          { text: 'Data Models',
            items: [
              { text: 'Project', link: '/submission-guides/data-model/project.md'},
              { text: 'Sample/Study', link: '/submission-guides/data-model/study.md'},
              { text: 'Spectra Dataset', link: '/submission-guides/data-model/dataset.md'},
              { text: 'Team', link: '/submission-guides/data-model/team.md'},
              { text: 'Sharing', link: '/submission-guides/data-model/sharing.md'}
            ]
          },
          { text: 'Submission Process',
            items: [
              { text: 'OAuth', link: '/developer-guides/configurations/oauth.md'},
              { text: 'Storage', link: '/developer-guides/configurations/storage.md'}
            ]
          },
          { text: 'Spectra', link: '/developer-guides/architecture.md' },
          { text: 'Licenses', link: '/submission-guides/licenses.md' }
        ],
      },
      {
        text: 'Developers Guides',
        items: [
          { text: 'Architecture', link: '/developer-guides/architecture.md' },
          { text: 'Installation',
            items: [
              { text: 'macOS', link: '/developer-guides/installation/mac.md'},
              { text: 'Windows', link: '/developer-guides/installation/windows.md'},
              { text: 'Ubuntu', link: '/developer-guides/installation/ubuntu.md'},
              { text: 'Centos', link: '/developer-guides/installation/centos.md'},
              { text: 'Development Workflow', link: '/developer-guides/installation/development-workflow.md'}
            ]
          },
          { text: 'Configuration',
            items: [
              { text: 'OAuth', link: '/developer-guides/configurations/oauth.md'},
              { text: 'Storage', link: '/developer-guides/configurations/storage.md'}
            ]
          },
          { text: 'Deployment',
            items: [
              { text: 'CI/CD', link: '/developer-guides/deployment/ci-cd.md'},
              { text: 'GKE', link: '/developer-guides/deployment/gke.md'},
              { text: 'Helm', link: '/developer-guides/deployment/helm.md'},
              { text: 'Production', link: '/developer-guides/deployment/production.md'},
              { text: 'Environments', link: '/developer-guides/deployment/environment.md'}
            ]
          },
          { text: 'Code Contribution Guidelines', link: '/developer-guides/code-contribution-guidelines.md' },
          { text: 'API', link: '/developer-guides/api.md' }
        ],
      },
      {
        text: 'Advance Guides',
        items: [
          { text: 'NMRium', link: '/advanced-guides/nmrium/nmrium.md' },  
          { text: 'NMR Repositories Overview',
            items: [
              { text: 'Background', link: '/advanced-guides/nmr-repositories/background.md'},
              { text: 'Data Sanitisation and Missing Values', link: '/advanced-guides/nmr-repositories/sanitisation.md'},
              { text: 'Dimensionality', link: '/advanced-guides/nmr-repositories/dimensionality.md'},
              { text: 'Spectrometer Frequency', link: '/advanced-guides/nmr-repositories/frequency.md'},
              { text: 'Atomic Nuclei', link: '/advanced-guides/nmr-repositories/nuclei.md'},
              { text: 'Temperature', link: '/advanced-guides/nmr-repositories/temperature.md'},
              { text: 'Solvent', link: '/advanced-guides/nmr-repositories/solvent.md'},
              { text: 'Instruments', link: '/advanced-guides/nmr-repositories/instrument.md'},
              { text: 'pH', link: '/advanced-guides/nmr-repositories/ph.md'},
              { text: 'Organism', link: '/advanced-guides/nmr-repositories/organism.md'},
              { text: 'Organism Part', link: '/advanced-guides/nmr-repositories/part.md'},
              { text: 'Variant', link: '/advanced-guides/nmr-repositories/variant.md'}
    
            ]
          },
          { text: 'Spectral Viewing and Processing', link: '/submission-guides/spectra.md' },
          { text: 'Licenses', link: '/submission-guides/licenses.md' }
        ],
      },
      {
        text: 'Community',
        items: [
          { text: 'Training', link: '/community/training.md' },
          { text: 'NMR MIChI Workshops in NFDI4Chem', link: '/community/workshops.md' },
          { text: 'Media Kit', link: '/community/media-kit.md' }
        ],
      },
      {
        text: 'Contribution',
        items: [
          { text: 'nmrXiv App', link: '/contribution/nmrxiv.md' },
          { text: 'nmrXiv Docs', link: '/contribution/nmrxiv-docs.md' },
          { text: 'Contributors and Steering Committee', link: '/contribution//contributors.md' }
        ],
      },
      {
        text: 'Miscellaneous',
        items: [
          { text: 'Tour', link: '/miscellaneous/tour.md' },
          { text: 'Shortcuts', link: '/miscellaneous/shortcuts.md' },
        ],
      },
      {
        text: 'License' , link: 'license.md',
      },
      {
        text: 'FAQ', link: 'FAQs.md'
      }
    ],


    socialLinks: [
      { icon: 'github', link: 'https://github.com/NFDI4Chem/nmrxiv' }
    ],

    footer: {
      message: 'Source code released under the MIT License | Data are provided under the Creative Commons Attribution (aka CC-BY 4.0) <br/> Funded by the <a href="https://www.dfg.de/en/index.jsp" style="color: blue" target="_blank">Deutsche Forschungsgemeinschaft (DFG, German Research Foundation)</a> under the <a href="https://www.nfdi4chem.de/" style="color: blue" target="_blank">National Research Data Infrastructure – NFDI4Chem</a> – Projektnummer <b>441958208.</b>',
      copyright: `© ${new Date().getFullYear()} nmrXiv, Inc. All rights reserved.`
    }
  }
})
