<!-- Tanpa tombol, hanya input tanggal -->
<div class="flex items-center gap-2 text-sm">
    <div class="relative">
        <input id="startDateLaporan" name="start_date" type="text" placeholder="Dari" readonly
            class="border border-orange-400 px-3 py-1.5 rounded focus:outline-none cursor-pointer bg-white text-sm"
            value="{{ request('start_date') }}">
    </div>

    <span class="text-gray-500">s/d</span>

    <div class="relative">
        <input id="endDateLaporan" name="end_date" type="text" placeholder="Sampai" readonly
            class="border border-orange-400 px-3 py-1.5 rounded focus:outline-none cursor-pointer bg-white text-sm"
            value="{{ request('end_date') }}">
    </div>

    @if(request('platform'))
        <input type="hidden" name="platform" value="{{ request('platform') }}">
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#startDateLaporan", { dateFormat: "Y-m-d" });
        flatpickr("#endDateLaporan", { dateFormat: "Y-m-d" });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<style>
    .flatpickr-calendar {
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        font-family: 'Poppins', sans-serif;
    }
</style>
@endpush
