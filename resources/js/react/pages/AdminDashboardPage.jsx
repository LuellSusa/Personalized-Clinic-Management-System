import '../../../css/pages/admin-dashboard.css';

const iconPaths = {
    grid: <><rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" /></>,
    users: <><circle cx="9" cy="8" r="3" /><path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 5v1" /></>,
    clock: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></>,
    doctor: <><path d="M9 4h6M12 4v6M8 3v5a4 4 0 0 0 8 0V3M16 13a4 4 0 0 1 4 4v3M20 20h-4" /></>,
    child: <><circle cx="12" cy="9" r="4" /><path d="M5 21a7 7 0 0 1 14 0M8 4l1.5 2M16 4l-1.5 2" /></>,
    calendar: <><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></>,
    arrow: <path d="M5 12h14M14 7l5 5-5 5" />,
};

function AdminIcon({ name }) {
    return <svg viewBox="0 0 24 24" aria-hidden="true">{iconPaths[name]}</svg>;
}

function humanize(value) {
    return value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function dateLabel(value) {
    return new Intl.DateTimeFormat('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
        .format(new Date(`${value}T00:00:00`));
}

export default function AdminDashboardPage({ admin, kpis, pendingUsers, appointments, routes, csrfToken }) {
    const cards = [
        { label: 'All users', value: kpis.users, icon: 'users', tone: 'green' },
        { label: 'Pending approval', value: kpis.pending, icon: 'clock', tone: 'amber' },
        { label: 'Active doctors', value: kpis.doctors, icon: 'doctor', tone: 'blue' },
        { label: 'Active patients', value: kpis.patients, icon: 'child', tone: 'purple' },
        { label: 'Upcoming visits', value: kpis.upcoming, icon: 'calendar', tone: 'coral' },
    ];

    return (
        <div className="admin-react-page">
            <aside className="admin-react-sidebar">
                <a className="admin-react-brand" href={routes.home}><span>TC</span><strong>TitaClinic</strong></a>
                <div className="admin-react-section-label">Workspace</div>
                <nav>
                    <a className="active" href={routes.dashboard}><AdminIcon name="grid" />Overview</a>
                    <a href={routes.users}><AdminIcon name="users" />User access</a>
                </nav>
                <div className="admin-react-user">
                    <span>{admin.initials}</span>
                    <div><strong>{admin.name}</strong><small>Administrator</small></div>
                </div>
            </aside>

            <main className="admin-react-main">
                <header className="admin-react-topbar">
                    <div>
                        <span>Clinic administration</span>
                        <h1>Operations overview</h1>
                        <p>Live account, patient, and appointment activity.</p>
                    </div>
                    <div className="admin-react-actions">
                        <a href={routes.users}>Manage access</a>
                        <form method="POST" action={routes.logout}>
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button type="submit">Log out</button>
                        </form>
                    </div>
                </header>

                <section className="admin-react-kpis" aria-label="Clinic KPIs">
                    {cards.map((card) => (
                        <article key={card.label}>
                            <div className={`admin-react-icon admin-react-icon-${card.tone}`}><AdminIcon name={card.icon} /></div>
                            <span>{card.label}</span>
                            <strong>{card.value}</strong>
                        </article>
                    ))}
                </section>

                <section className="admin-react-grid">
                    <article className="admin-react-panel">
                        <div className="admin-react-panel-title">
                            <div><span>Account queue</span><h2>Pending approvals</h2></div>
                            <a href={routes.users}>Review all <AdminIcon name="arrow" /></a>
                        </div>
                        {pendingUsers.length ? (
                            <div className="admin-react-user-list">
                                {pendingUsers.map((user) => (
                                    <div key={user.id}>
                                        <span className="admin-react-list-avatar">{user.name.split(' ').map((name) => name[0]).slice(0, 2).join('')}</span>
                                        <div><strong>{user.name}</strong><small>{user.email}</small></div>
                                        <time>{dateLabel(user.createdAt)}</time>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="admin-react-empty"><strong>Approval queue is clear</strong><span>New pending accounts will appear here.</span></div>
                        )}
                    </article>

                    <article className="admin-react-panel admin-react-appointments">
                        <div className="admin-react-panel-title">
                            <div><span>Recent activity</span><h2>Appointments</h2></div>
                        </div>
                        {appointments.length ? (
                            <div className="admin-react-table-wrap">
                                <table>
                                    <thead><tr><th>Patient</th><th>Visit</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
                                    <tbody>
                                        {appointments.map((appointment) => (
                                            <tr key={appointment.id}>
                                                <td><strong>{appointment.patient}</strong></td>
                                                <td>{humanize(appointment.type)}</td>
                                                <td>{appointment.doctor}</td>
                                                <td>{dateLabel(appointment.date)}</td>
                                                <td><span className={`admin-react-status status-${appointment.status}`}>{humanize(appointment.status)}</span></td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="admin-react-empty"><strong>No appointment activity</strong><span>New clinic bookings will appear here.</span></div>
                        )}
                    </article>
                </section>
            </main>
        </div>
    );
}
