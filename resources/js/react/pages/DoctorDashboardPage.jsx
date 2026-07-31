import { useEffect, useMemo, useState } from 'react';
import '../../../css/pages/doctor-dashboard.css';

const paths = {
    grid: <><rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" /></>,
    calendar: <><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></>,
    today: <><circle cx="12" cy="12" r="9" /><path d="M8 12h8M12 8v8" /></>,
    check: <><circle cx="12" cy="12" r="9" /><path d="m8 12 2.5 2.5L16 9" /></>,
    history: <><path d="M4 12a8 8 0 1 0 2.4-5.7L4 8.7" /><path d="M4 4v4.7h4.7" /></>,
    arrow: <path d="M5 12h14M14 7l5 5-5 5" />,
};

function Icon({ name }) {
    return <svg viewBox="0 0 24 24" aria-hidden="true">{paths[name]}</svg>;
}

function humanize(value) {
    return value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function localDateValue(date) {
    return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-');
}

export default function DoctorDashboardPage({ doctor, kpis, initialMonth, selectedDate, calendarBookings: initialBookings, appointments, branches, routes, csrfToken, flash }) {
    const [month, setMonth] = useState(initialMonth);
    const [calendarBookings, setCalendarBookings] = useState(initialBookings);

    useEffect(() => {
        if (month === initialMonth) {
            setCalendarBookings(initialBookings);
            return undefined;
        }

        const controller = new AbortController();
        fetch(`${routes.calendar}?month=${month}`, { signal: controller.signal, headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((data) => setCalendarBookings(data.bookings))
            .catch((error) => {
                if (error.name !== 'AbortError') console.error('Unable to refresh calendar.', error);
            });

        return () => controller.abort();
    }, [month, initialMonth, initialBookings, routes.calendar]);

    useEffect(() => {
        const timer = window.setInterval(() => {
            fetch(`${routes.calendar}?month=${month}`, { headers: { Accept: 'application/json' } })
                .then((response) => response.json())
                .then((data) => setCalendarBookings(data.bookings));
        }, 30000);

        return () => window.clearInterval(timer);
    }, [month, routes.calendar]);

    const monthDate = useMemo(() => {
        const [year, monthNumber] = month.split('-').map(Number);
        return new Date(year, monthNumber - 1, 1);
    }, [month]);

    const calendarDays = useMemo(() => {
        const days = [];
        const startDay = monthDate.getDay();
        const dayCount = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
        for (let index = 0; index < startDay; index += 1) days.push(null);
        for (let day = 1; day <= dayCount; day += 1) days.push(new Date(monthDate.getFullYear(), monthDate.getMonth(), day));
        return days;
    }, [monthDate]);

    const changeMonth = (offset) => {
        const next = new Date(monthDate.getFullYear(), monthDate.getMonth() + offset, 1);
        setMonth(`${next.getFullYear()}-${String(next.getMonth() + 1).padStart(2, '0')}`);
    };

    const cards = [
        { label: "Today's bookings", value: kpis.today, icon: 'today', tone: 'teal' },
        { label: 'Upcoming', value: kpis.upcoming, icon: 'calendar', tone: 'blue' },
        { label: 'Confirmed', value: kpis.confirmed, icon: 'check', tone: 'amber' },
        { label: 'Completed', value: kpis.completed, icon: 'history', tone: 'purple' },
    ];

    return (
        <div className="doctor-react-page">
            <aside className="doctor-react-sidebar">
                <a className="doctor-react-brand" href={routes.home}><span>TC</span><strong>TitaClinic</strong></a>
                <nav>
                    <a className="active" href={routes.dashboard}><Icon name="grid" />Dashboard</a>
                    <a href="#doctor-calendar"><Icon name="calendar" />Calendar</a>
                </nav>
                <div className="doctor-react-profile"><span>{doctor.initials}</span><div><strong>Dr. {doctor.name}</strong><small>Doctor account</small></div></div>
            </aside>

            <main className="doctor-react-main">
                <header className="doctor-react-header">
                    <div><span>Clinical workspace</span><h1>Doctor dashboard</h1><p>Review bookings and manage patient attendance.</p></div>
                    <form method="POST" action={routes.logout}>
                        <input type="hidden" name="_token" value={csrfToken} />
                        <button type="submit">Log out</button>
                    </form>
                </header>

                {flash?.success && <div className="doctor-react-flash success">{flash.success}</div>}
                {flash?.error && <div className="doctor-react-flash error">{flash.error}</div>}

                <section className="doctor-react-kpis" aria-label="Appointment KPIs">
                    {cards.map((card) => (
                        <article key={card.label}>
                            <div className={`doctor-react-kpi-icon tone-${card.tone}`}><Icon name={card.icon} /></div>
                            <span>{card.label}</span><strong>{card.value}</strong><small>Live booking data</small>
                        </article>
                    ))}
                </section>

                <section className="doctor-react-calendar-panel" id="doctor-calendar">
                    <div className="doctor-react-panel-heading">
                        <div><span>Schedule calendar</span><h2>{monthDate.toLocaleDateString('en-PH', { month: 'long', year: 'numeric' })}</h2></div>
                        <div className="doctor-react-month-controls">
                            <button type="button" onClick={() => changeMonth(-1)} aria-label="Previous month">&#8592;</button>
                            <button type="button" onClick={() => changeMonth(1)} aria-label="Next month">&#8594;</button>
                        </div>
                    </div>
                    <div className="doctor-react-weekdays"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
                    <div className="doctor-react-calendar-grid">
                        {calendarDays.map((date, index) => {
                            if (!date) return <span className="doctor-react-calendar-spacer" key={`spacer-${index}`} />;
                            const value = localDateValue(date);
                            const booking = calendarBookings[value];
                            return (
                                <a
                                    href={`${routes.dashboard}?date=${value}#doctor-bookings`}
                                    className={`${booking ? 'has-bookings' : ''} ${value === selectedDate ? 'selected' : ''}`}
                                    key={value}
                                    title={booking ? `${booking.count} booking${booking.count === 1 ? '' : 's'} on this date` : 'No bookings'}
                                >
                                    <span>{date.getDate()}</span>
                                    {booking && <strong>{booking.count} booked</strong>}
                                </a>
                            );
                        })}
                    </div>
                    <p className="doctor-react-calendar-legend"><span /> Green dates have one or more patient bookings. Calendar data refreshes every 30 seconds.</p>
                </section>

                <section className="doctor-react-bookings" id="doctor-bookings">
                    <div className="doctor-react-panel-heading">
                        <div><span>Selected date</span><h2>{new Date(`${selectedDate}T00:00:00`).toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</h2></div>
                    </div>
                    {appointments.length ? (
                        <div className="doctor-react-booking-grid">
                            {appointments.map((appointment) => {
                                const branch = branches[appointment.branch];
                                const actions = appointment.status === 'scheduled'
                                    ? { confirmed: 'Confirm', cancelled: 'Cancel' }
                                    : appointment.status === 'confirmed'
                                        ? { completed: 'Complete', no_show: 'No-show', cancelled: 'Cancel' }
                                        : {};
                                return (
                                    <article key={appointment.id}>
                                        <div className="doctor-react-booking-top"><h3>{appointment.patient}</h3><span className={`status-${appointment.status}`}>{humanize(appointment.status)}</span></div>
                                        <dl>
                                            <div><dt>Visit</dt><dd>{humanize(appointment.type)}</dd></div>
                                            <div><dt>Parent</dt><dd>{appointment.parent}</dd></div>
                                            <div><dt>Branch</dt><dd>{branch?.name ?? 'Clinic branch'}</dd></div>
                                            <div><dt>Clinic hours</dt><dd>{branch?.hours ?? 'Not available'}</dd></div>
                                            <div><dt>Reason</dt><dd>{appointment.reason || 'Not provided'}</dd></div>
                                        </dl>
                                        <div className="doctor-react-booking-actions">
                                            {Object.entries(actions).map(([status, label]) => (
                                                <form method="POST" action={`${routes.appointmentStatusBase}/${appointment.id}/status`} key={status}>
                                                    <input type="hidden" name="_token" value={csrfToken} />
                                                    <input type="hidden" name="_method" value="PATCH" />
                                                    <input type="hidden" name="status" value={status} />
                                                    <input type="hidden" name="redirect_date" value={selectedDate} />
                                                    <button className={status === 'cancelled' ? 'danger' : ''}>{label}</button>
                                                </form>
                                            ))}
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                    ) : <div className="doctor-react-empty">No appointments booked for this date.</div>}
                </section>
            </main>
        </div>
    );
}
