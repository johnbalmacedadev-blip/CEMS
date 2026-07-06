import clsx from 'clsx';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import Layout from '@theme/Layout';
import Heading from '@theme/Heading';
import styles from './index.module.css';

const features = [
  {
    title: 'Login & Home',
    description: 'Authenticate, navigate the Home category grid, and open any permitted module.',
    link: '/docs/getting-started/login',
  },
  {
    title: 'All Modules',
    description: 'CAR REPORTS, STAFF REPORTS, PAYMENTS/EXPENSES, and every Home category — with flowcharts.',
    link: '/docs/core/home',
  },
  {
    title: 'Team Chat & API',
    description: 'Live team messaging widget and Scribe API reference for developers.',
    link: '/docs/core/team-chat',
  },
];

function HomepageHeader() {
  const {siteConfig} = useDocusaurusContext();
  return (
    <header className={clsx('hero hero--primary', styles.heroBanner)}>
      <div className="container">
        <Heading as="h1" className="hero__title">
          {siteConfig.title}
        </Heading>
        <p className="hero__subtitle">{siteConfig.tagline}</p>
        <div className={styles.buttons}>
          <Link className="button button--secondary button--lg" to="/docs/intro">
            Read the docs
          </Link>
        </div>
      </div>
    </header>
  );
}

function FeatureCard({title, description, link}) {
  return (
    <div className="col col--4 margin-bottom--lg">
      <div className="card padding--lg" style={{height: '100%'}}>
        <Heading as="h3">{title}</Heading>
        <p>{description}</p>
        <Link to={link}>Learn more →</Link>
      </div>
    </div>
  );
}

export default function Home() {
  const {siteConfig} = useDocusaurusContext();
  return (
    <Layout title={siteConfig.title} description={siteConfig.tagline}>
      <HomepageHeader />
      <main className="container margin-vert--lg">
        <div className="row">
          {features.map((props) => (
            <FeatureCard key={props.title} {...props} />
          ))}
        </div>
      </main>
    </Layout>
  );
}
