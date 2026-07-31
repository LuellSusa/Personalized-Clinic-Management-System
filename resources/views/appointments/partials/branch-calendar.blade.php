@php
    $calendarBranch = old('branch', $selectedBranch ?? array_key_first($branches));
    $calendarDate = old('appointment_date', $selectedDate ?? '');
@endphp

<div class="col-12">
    <label class="form-label" for="branch">Clinic branch</label>
    <select name="branch" id="branch" class="form-select" required>
        @foreach ($branches as $key => $branch)
            <option value="{{ $key }}" {{ $calendarBranch === $key ? 'selected' : '' }}>{{ $branch['name'] }}</option>
        @endforeach
    </select>
</div>

<div class="col-12">
    <div class="branch-schedule-summary" id="branch-schedule-summary"></div>
    <input type="hidden" name="appointment_date" id="appointment_date" value="{{ $calendarDate }}">

    <div class="appointment-calendar" data-selected-date="{{ $calendarDate }}">
        <div class="appointment-calendar-header">
            <button type="button" id="calendar-previous" aria-label="Previous month">&#8592;</button>
            <strong id="calendar-month"></strong>
            <button type="button" id="calendar-next" aria-label="Next month">&#8594;</button>
        </div>
        <div class="appointment-calendar-weekdays" aria-hidden="true">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
        </div>
        <div class="appointment-calendar-grid" id="calendar-grid"></div>
        <p class="appointment-calendar-selection" id="calendar-selection">Select an available date.</p>
    </div>
</div>

<script id="branch-schedules" type="application/json">{!! json_encode($branches, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const schedules = JSON.parse(document.getElementById('branch-schedules').textContent);
    const branchInput = document.getElementById('branch');
    const dateInput = document.getElementById('appointment_date');
    const grid = document.getElementById('calendar-grid');
    const monthLabel = document.getElementById('calendar-month');
    const selectionLabel = document.getElementById('calendar-selection');
    const summary = document.getElementById('branch-schedule-summary');
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const parseLocalDate = (value) => {
        if (!value) return null;
        const [year, month, day] = value.split('-').map(Number);
        return new Date(year, month - 1, day);
    };
    const toDateValue = (date) => [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');

    let selectedDate = parseLocalDate(dateInput.value);
    let cursor = selectedDate
        ? new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1)
        : new Date(today.getFullYear(), today.getMonth(), 1);

    const isAvailable = (date) => {
        const isoDay = date.getDay() || 7;
        return date >= today && schedules[branchInput.value].days.includes(isoDay);
    };

    const render = () => {
        const schedule = schedules[branchInput.value];
        summary.innerHTML = `<strong>${schedule.name}</strong><span>${schedule.days_label} · ${schedule.hours}</span>`;
        monthLabel.textContent = cursor.toLocaleDateString('en-PH', { month: 'long', year: 'numeric' });
        grid.innerHTML = '';

        const firstDay = new Date(cursor.getFullYear(), cursor.getMonth(), 1).getDay();
        const daysInMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();

        for (let index = 0; index < firstDay; index += 1) {
            const spacer = document.createElement('span');
            spacer.className = 'calendar-spacer';
            grid.appendChild(spacer);
        }

        for (let day = 1; day <= daysInMonth; day += 1) {
            const date = new Date(cursor.getFullYear(), cursor.getMonth(), day);
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = day;
            button.disabled = !isAvailable(date);
            button.className = 'calendar-day';

            if (selectedDate && toDateValue(date) === toDateValue(selectedDate)) {
                button.classList.add('selected');
            }

            button.addEventListener('click', () => {
                selectedDate = date;
                dateInput.value = toDateValue(date);
                selectionLabel.textContent = `Selected: ${date.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}`;
                render();
            });
            grid.appendChild(button);
        }

        if (selectedDate) {
            selectionLabel.textContent = `Selected: ${selectedDate.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}`;
        }

        document.getElementById('calendar-previous').disabled = cursor <= new Date(today.getFullYear(), today.getMonth(), 1);
    };

    branchInput.addEventListener('change', () => {
        if (selectedDate && !isAvailable(selectedDate)) {
            selectedDate = null;
            dateInput.value = '';
            selectionLabel.textContent = 'Select an available date.';
        }
        render();
    });
    document.getElementById('calendar-previous').addEventListener('click', () => {
        cursor = new Date(cursor.getFullYear(), cursor.getMonth() - 1, 1);
        render();
    });
    document.getElementById('calendar-next').addEventListener('click', () => {
        cursor = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 1);
        render();
    });
    dateInput.form.addEventListener('submit', (event) => {
        if (!dateInput.value) {
            event.preventDefault();
            selectionLabel.textContent = 'Please select an available appointment date.';
            selectionLabel.classList.add('text-danger');
        }
    });

    render();
});
</script>
