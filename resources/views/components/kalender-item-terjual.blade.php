<!-- resources/views/components/kalender-item-terjual.blade.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="flex items-center gap-2 text-sm">
    <!-- Tanggal Awal -->
    <div class="relative">
        <input id="startDateItemTerjual" type="text" placeholder="Dari" readonly
            class="border border-orange-400 px-3 py-1.5 rounded focus:outline-none cursor-pointer text-sm bg-white" />
    </div>

    <span class="text-gray-500">s/d</span>

    <!-- Tanggal Akhir -->
    <div class="relative">
        <input id="endDateItemTerjual" type="text" placeholder="Sampai" readonly
            class="border border-orange-400 px-3 py-1.5 rounded focus:outline-none cursor-pointer text-sm bg-white" />
    </div>

    <p id="noDataMessageItemTerjual"
        style="position: absolute; top: 100%; right: 0; padding: 4px 12px; border-radius: 4px;
        color: #F44336; font-size: 14px; display: none; white-space: nowrap;">
        Tidak ada transaksi dalam rentang tersebut.
    </p>
</div>

<style>
    .flatpickr-calendar {
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        font-family: 'Poppins', sans-serif;
    }

    .flatpickr-footer {
        display: flex;
        justify-content: flex-end;
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
    }

    .flatpickr-btn.apply {
        background: #4F9CF9;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById("startDateItemTerjual");
        const endInput = document.getElementById("endDateItemTerjual");
        const noDataMessage = document.getElementById("noDataMessageItemTerjual");

        flatpickr(startInput, {
            dateFormat: "Y-m-d",
            onChange: filterRows
        });

        flatpickr(endInput, {
            dateFormat: "Y-m-d",
            onChange: filterRows
        });

        function filterRows() {
            const start = startInput.value;
            const end = endInput.value;
            const rows = document.querySelectorAll("tbody tr[data-tanggal]");
            let hasData = false;

            rows.forEach(row => {
                const rowDate = row.dataset.tanggal;
                if (
                    (!start || rowDate >= start) &&
                    (!end || rowDate <= end)
                ) {
                    row.style.display = '';
                    hasData = true;
                } else {
                    row.style.display = 'none';
                }
            });

            noDataMessage.style.display = hasData ? 'none' : 'block';
        }
    });
</script>
