import '../../../css/pages/landing.css';

const ArrowIcon = () => (
    <svg viewBox="0 0 20 20" aria-hidden="true">
        <path d="M4 10h12M11 5l5 5-5 5" />
    </svg>
);

const HeartIcon = () => (
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z" />
        <path d="M7.5 12h2l1.2-2.5 2.2 5 1.3-2.5h2.3" />
    </svg>
);

export default function LandingPage({ authenticated, routes }) {
    return (
        <div className="landing-page">
            <header className="landing-nav">
                <a className="landing-brand" href={routes.home} aria-label="TitaClinic home">
                    <span className="landing-brand-mark"><HeartIcon /></span>
                    <span>TitaClinic</span>
                </a>

                <nav aria-label="Primary navigation">
                    <a href="#services">Services</a>
                    <a href="#workflow">How it works</a>
                    {authenticated ? (
                        <a className="landing-button landing-button-primary" href={routes.dashboard}>
                            Open dashboard <ArrowIcon />
                        </a>
                    ) : (
                        <>
                            <a href={routes.login}>Log in</a>
                            <a className="landing-button landing-button-primary" href={routes.register}>
                                Create account <ArrowIcon />
                            </a>
                        </>
                    )}
                </nav>
            </header>

            <main>
                <section className="landing-hero">
                    <div className="landing-hero-copy">
                        <span className="landing-kicker">Thoughtful pediatric care, connected</span>
                        <h1>Your child’s care, organized in one calm place.</h1>
                        <p>
                            Keep patient profiles, clinic appointments, and care updates together
                            so families and medical teams can focus on what matters.
                        </p>
                        <div className="landing-actions">
                            <a className="landing-button landing-button-primary landing-button-large" href={authenticated ? routes.dashboard : routes.register}>
                                {authenticated ? 'Go to dashboard' : 'Get started'} <ArrowIcon />
                            </a>
                            {!authenticated && (
                                <a className="landing-button landing-button-secondary landing-button-large" href={routes.login}>
                                    I already have an account
                                </a>
                            )}
                        </div>
                        <div className="landing-trust-row" aria-label="Platform qualities">
                            <span>Admin-approved accounts</span>
                            <span>Role-based access</span>
                            <span>Organized records</span>
                        </div>
                    </div>

                    <div className="landing-hero-visual" aria-label="Clinic dashboard preview">
                        <div className="landing-preview-card">
                            <div className="landing-preview-header">
                                <div>
                                    <span className="landing-preview-label">Next appointment</span>
                                    <strong>Pediatric consultation</strong>
                                </div>
                                <span className="landing-status">Confirmed</span>
                            </div>
                            <div className="landing-calendar-row">
                                <div className="landing-date-tile">
                                    <span>JUL</span>
                                    <strong>30</strong>
                                </div>
                                <div>
                                    <strong>10:30 AM</strong>
                                    <span>with your assigned pediatrician</span>
                                </div>
                            </div>
                            <div className="landing-preview-grid">
                                <div>
                                    <span>Children</span>
                                    <strong>2</strong>
                                </div>
                                <div>
                                    <span>Upcoming</span>
                                    <strong>3</strong>
                                </div>
                                <div>
                                    <span>Completed</span>
                                    <strong>8</strong>
                                </div>
                            </div>
                        </div>
                        <div className="landing-orbit landing-orbit-one" />
                        <div className="landing-orbit landing-orbit-two" />
                    </div>
                </section>

                <section className="landing-services" id="services">
                    <div>
                        <span className="landing-section-number">01</span>
                        <h2>Everything families need before the clinic visit.</h2>
                    </div>
                    <div className="landing-service-list">
                        <article>
                            <span>01</span>
                            <div>
                                <h3>Patient profiles</h3>
                                <p>Keep each child’s essential details organized and available to the family.</p>
                            </div>
                        </article>
                        <article>
                            <span>02</span>
                            <div>
                                <h3>Clear scheduling</h3>
                                <p>Request appointments, select an active doctor, and follow every status update.</p>
                            </div>
                        </article>
                        <article>
                            <span>03</span>
                            <div>
                                <h3>Protected access</h3>
                                <p>New accounts stay pending until a clinic administrator approves them.</p>
                            </div>
                        </article>
                    </div>
                </section>

                <section className="landing-workflow" id="workflow">
                    <span className="landing-section-number">02</span>
                    <h2>A simple path from registration to care.</h2>
                    <div className="landing-steps">
                        <div><strong>1</strong><span>Create a parent account</span></div>
                        <div><strong>2</strong><span>Receive clinic approval</span></div>
                        <div><strong>3</strong><span>Add your child’s profile</span></div>
                        <div><strong>4</strong><span>Book and track appointments</span></div>
                    </div>
                </section>
            </main>

            <footer className="landing-footer">
                <a className="landing-brand" href={routes.home}>
                    <span className="landing-brand-mark"><HeartIcon /></span>
                    <span>TitaClinic</span>
                </a>
                <p>Pediatric care coordination for modern clinics.</p>
            </footer>
        </div>
    );
}
