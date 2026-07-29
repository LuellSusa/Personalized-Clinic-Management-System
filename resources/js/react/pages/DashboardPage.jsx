import '../../../css/pages/dashboard.css';

const icons = {
    children: <><path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /><path d="M3 19v-1a5 5 0 0 1 10 0v1" /><path d="M16 8a2.5 2.5 0 1 1 0 5" /><path d="M16 14a4 4 0 0 1 4 4v1" /></>,
    calendar: <><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></>,
    check: <><circle cx="12" cy="12" r="9" /><path d="m8 12 2.5 2.5L16 9" /></>,
    history: <><path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.6" /><path d="M4 4v4.6h4.6M12 8v4l2.5 1.5" /></>,
    profile: <><circle cx="12" cy="8" r="4" /><path d="M4.5 21a7.5 7.5 0 0 1 15 0" /></>,
    plus: <><path d="M12 5v14M5 12h14" /></>,
    arrow: <><path d="M5 12h14M14 7l5 5-5 5" /></>,
    menu: <><path d="M4 7h16M4 12h16M4 17h16" /></>,
};

function Icon({ name }) {
    return <svg viewBox="0 0 24 24" aria-hidden="true">{icons[name]}</svg>;
}

function formatDate(value) {
    return new Intl.DateTimeFormat('en-PH', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(new Date(`${value}T00:00:00`));
}

function formatTime(value) {
    const [hour, minute] = value.slice(0, 5).split(':');
    const date = new Date();
    date.setHours(Number(hour), Number(minute));
    return new Intl.DateTimeFormat('en-PH', {
        hour: 'numeric',
        minute: '2-digit',
    }).format(date);
}

function humanize(value) {
    return value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

export default function DashboardPage({ user, profileComplete, kpis, appointments, children, routes, csrfToken }) {
    const firstName = user.firstName || 'there';
    const today = new Intl.DateTimeFormat('en-PH', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
    }).format(new Date());

    const kpiCards = [
        { label: 'Registered children', value: kpis.children, icon: 'children', tone: 'teal' },
        { label: 'Upcoming visits', value: kpis.upcoming, icon: 'calendar', tone: 'blue' },
        { label: 'Confirmed', value: kpis.confirmed, icon: 'check', tone: 'amber' },
        { label: 'Completed visits', value: kpis.completed, icon: 'history', tone: 'plum' },
    ];

    return (
        <div className="dashboard-page">
            <aside className="dashboard-sidebar">
                <a className="dashboard-brand" href={routes.home}>
                    <span>TC</span>
                    <strong>TitaClinic</strong>
                </a>
                <nav aria-label="Dashboard navigation">
                    <a className="active" href={routes.dashboard}><Icon name="menu" />Overview</a>
                    <a href={routes.profile}><Icon name="profile" />Parent profile</a>
                    <a href={routes.children}><Icon name="children" />Children</a>
                    <a href={routes.appointments}><Icon name="calendar" />Appointments</a>
                </nav>
                <div className="dashboard-sidebar-footer">
                    <div className="dashboard-avatar">{user.initials}</div>
                    <div>
                        <strong>{user.fullName}</strong>
                        <span>Parent account</span>
                    </div>
                </div>
            </aside>

            <main className="dashboard-main">
                <header className="dashboard-topbar">
                    <div>
                        <p>{today}</p>
                        <h1>Good day, {firstName}.</h1>
                    </div>
                    <div className="dashboard-topbar-actions">
                        <a className="dashboard-primary-button" href={routes.bookAppointment}>
                            <Icon name="plus" /> Book appointment
                        </a>
                        <form method="POST" action={routes.logout}>
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button type="submit" className="dashboard-logout">Log out</button>
                        </form>
                    </div>
                </header>

                {!profileComplete && (
                    <section className="dashboard-notice">
                        <div>
                            <span>One step left</span>
                            <strong>Complete your parent profile to start managing children and appointments.</strong>
                        </div>
                        <a href={routes.profile}>Complete profile <Icon name="arrow" /></a>
                    </section>
                )}

                <section className="dashboard-kpis" aria-label="Clinic overview">
                    {kpiCards.map((card) => (
                        <article className={`dashboard-kpi dashboard-kpi-${card.tone}`} key={card.label}>
                            <div className="dashboard-kpi-icon"><Icon name={card.icon} /></div>
                            <span>{card.label}</span>
                            <strong>{card.value}</strong>
                            <small>Live clinic data</small>
                        </article>
                    ))}
                </section>

                <div className="dashboard-content-grid">
                    <section className="dashboard-panel dashboard-schedule">
                        <div className="dashboard-panel-heading">
                            <div>
                                <span>Schedule</span>
                                <h2>Upcoming appointments</h2>
                            </div>
                            <a href={routes.appointments}>View all <Icon name="arrow" /></a>
                        </div>

                        {appointments.length ? (
                            <div className="dashboard-appointment-list">
                                {appointments.map((appointment) => (
                                    <article key={appointment.id}>
                                        <div className="dashboard-appointment-date">
                                            <strong>{new Date(`${appointment.date}T00:00:00`).getDate()}</strong>
                                            <span>{new Intl.DateTimeFormat('en-PH', { month: 'short' }).format(new Date(`${appointment.date}T00:00:00`))}</span>
                                        </div>
                                        <div className="dashboard-appointment-info">
                                            <strong>{humanize(appointment.type)}</strong>
                                            <span>{appointment.childName} · {formatDate(appointment.date)}</span>
                                            <small>{formatTime(appointment.startTime)}–{formatTime(appointment.endTime)}{appointment.doctorName ? ` · ${appointment.doctorName}` : ''}</small>
                                        </div>
                                        <span className={`dashboard-status dashboard-status-${appointment.status}`}>
                                            {humanize(appointment.status)}
                                        </span>
                                    </article>
                                ))}
                            </div>
                        ) : (
                            <div className="dashboard-empty">
                                <div><Icon name="calendar" /></div>
                                <strong>No upcoming appointments</strong>
                                <p>Your next scheduled or confirmed visit will appear here.</p>
                                <a href={routes.bookAppointment}>Book an appointment</a>
                            </div>
                        )}
                    </section>

                    <aside className="dashboard-right-column">
                        <section className="dashboard-panel dashboard-quick-actions">
                            <div className="dashboard-panel-heading">
                                <div><span>Shortcuts</span><h2>Quick actions</h2></div>
                            </div>
                            <a href={routes.addChild}>
                                <span><Icon name="plus" /></span>
                                <div><strong>Add a child</strong><small>Create a patient profile</small></div>
                                <Icon name="arrow" />
                            </a>
                            <a href={routes.bookAppointment}>
                                <span><Icon name="calendar" /></span>
                                <div><strong>Book a visit</strong><small>Request a clinic schedule</small></div>
                                <Icon name="arrow" />
                            </a>
                            <a href={routes.profile}>
                                <span><Icon name="profile" /></span>
                                <div><strong>Update profile</strong><small>Contact and emergency details</small></div>
                                <Icon name="arrow" />
                            </a>
                        </section>

                        <section className="dashboard-panel dashboard-children-panel">
                            <div className="dashboard-panel-heading">
                                <div><span>Family</span><h2>Your children</h2></div>
                                <a href={routes.children}>Manage</a>
                            </div>
                            {children.length ? children.map((child) => (
                                <div className="dashboard-child" key={child.id}>
                                    <span>{child.initials}</span>
                                    <div><strong>{child.name}</strong><small>{child.ageLabel}</small></div>
                                    <em>{child.status}</em>
                                </div>
                            )) : (
                                <p className="dashboard-inline-empty">No child profiles added yet.</p>
                            )}
                        </section>
                    </aside>
                </div>
            </main>
        </div>
    );
}
