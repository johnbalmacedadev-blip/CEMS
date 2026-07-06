// @ts-check
import {themes as prismThemes} from 'prism-react-renderer';

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'Car Empire Management System',
  tagline: 'Documentation for CEMS — inventory, sales, finance, and operations',
  favicon: 'img/favicon.ico',

  future: {
    v4: true,
  },

  url: process.env.DOCUSAURUS_URL || 'https://johnbalmacedadev-blip.github.io',
  baseUrl: process.env.DOCUSAURUS_BASE_URL || '/CEMS/',
  organizationName: 'johnbalmacedadev-blip',
  projectName: 'CEMS',

  onBrokenLinks: 'warn',
  onBrokenMarkdownLinks: 'warn',

  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  markdown: {
    mermaid: true,
  },
  themes: ['@docusaurus/theme-mermaid'],

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          sidebarPath: './sidebars.js',
          routeBasePath: 'docs',
          editUrl: 'https://github.com/johnbalmacedadev-blip/CEMS/tree/main/documentation/',
        },
        blog: false,
        theme: {
          customCss: './src/css/custom.css',
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      image: 'img/docusaurus-social-card.jpg',
      colorMode: {
        defaultMode: 'light',
        respectPrefersColorScheme: true,
      },
      navbar: {
        title: 'CEMS Docs',
        logo: {
          alt: 'Car Empire',
          src: 'img/logo.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'docsSidebar',
            position: 'left',
            label: 'Documentation',
          },
          {
            href: 'https://github.com/johnbalmacedadev-blip/CEMS',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Documentation',
            items: [
              {label: 'Introduction', to: '/docs/intro'},
              {label: 'System Features', to: '/docs/system-features'},
              {label: 'Installation', to: '/docs/getting-started/installation'},
              {label: 'Unit Report', to: '/docs/modules/car-reports/unit-report'},
              {label: 'API Overview', to: '/docs/api/overview'},
            ],
          },
          {
            title: 'Application',
            items: [
              {label: 'Live API docs (Scribe)', href: 'http://localhost:8000/docs'},
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} Car Empire Management System. Built with Docusaurus.`,
      },
      prism: {
        theme: prismThemes.github,
        darkTheme: prismThemes.dracula,
        additionalLanguages: ['bash', 'php', 'json'],
      },
    }),
};

export default config;
