<!-- resources/views/components/kalender.blade.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Container Kalender -->
<div id="calendarContainer" style="position: relative; display: inline-block;">
    <!-- Kalender Trigger -->
    <input id="datepicker" name="tanggal" type="text" class="hidden" value="{{ request('tanggal') }}">
    <button id="openCalendar" type="button" style="border: 2px solid #F58220;" class="px-4 py-1.5 rounded text-[#333] relative z-10">
        <i class="fas fa-calendar-alt mr-2"></i>Kalender
    </button>

    <p id="noDataMessage"
        style="position: absolute; top: 30px; right: 0;  
       padding: 4px 12px; border-radius: 4px; color: #F44336; font-size: 16px; 
       display: none; pointer-events: none; white-space: nowrap; ">
        Tidak ada transaksi untuk tanggal tersebut.
    </p>
</div>

<style>
    /* styling tetap sama */
    .flatpickr-calendar {
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        font-family: 'Poppins', sans-serif;
    }
    .flatpickr-months { padding: 8px 0; }
    .flatpickr-current-month { font-size: 1.25rem; font-weight: 600; }
    .flatpickr-weekday { color: #999; font-weight: 500; }
    .flatpickr-day {
        border-radius: 8px;
        line-height: 2.5rem;
        height: 2.5rem;
        width: 2.5rem;
        margin: 2px;
    }
    .flatpickr-day.selected { background: #4F9CF9; color: white; }
    .flatpickr-day.today { border: 1px solid #4F9CF9; }
    .flatpickr-footer {
        display: flex;
        justify-content: end;
        gap: 10px;
        padding: 10px 15px;
    }
    .flatpickr-btn {
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid #4F9CF9;
        background: white;
        color: #4F9CF9;
        cursor: pointer;
        transition: 0.2s;
    }
    .flatpickr-btn.apply {
        background: #4F9CF9;
        color: white;
    }
    .flatpickr-btn:hover { opacity: 0.9; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const datepickerInput = document.getElementById("datepicker");
        const calendar = flatpickr(datepickerInput, {
            appendTo: document.body,
            positionElement: document.getElementById("openCalendar"),
            position: "below",
            clickOpens: false,
            allowInput: false,
            dateFormat: "Y-m-d",
            defaultDate: datepickerInput.value || null,
            closeOnSelect: false,

            onReady: function (selectedDates, dateStr, instance) {
                const footer = document.createElement("div");
                footer.className = "flatpickr-footer";

                const cancelBtn = document.createElement("button");
                cancelBtn.className = "flatpickr-btn cancel";
                cancelBtn.textContent = "Batal";
                cancelBtn.onclick = () => {
                    window.location = window.location.pathname; // reset semua filter
                };

                const applyBtn = document.createElement("button");
                applyBtn.className = "flatpickr-btn apply";
                applyBtn.textContent = "Terapkan";
                applyBtn.onclick = () => {
                    const selectedDate = instance.input.value;
                    if (selectedDate) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('tanggal', selectedDate);
                        url.searchParams.delete('page'); // reset pagination
                        window.location = url.toString();
                    }
                };

                footer.appendChild(cancelBtn);
                footer.appendChild(applyBtn);
                instance.calendarContainer.appendChild(footer);
            },

            onChange: function (selectedDates, dateStr, instance) {
                setTimeout(() => {
                    if (instance.isOpen) instance.open();
                }, 0);
            }
        });

        document.getElementById("openCalendar").addEventListener("click", () => {
            calendar.open();
        });
    });
</script>
