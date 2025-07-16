<div id="filterModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-xl">
        <h2 class="text-xl font-semibold mb-2">Filter Transaksi</h2>
        <p class="text-sm text-gray-700 mb-4">Pilih informasi yang anda perlukan</p>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1">ID Pesanan</label>
                <input type="text" class="w-full border-2 rounded px-2 py-1" style="border-color: #F58220;">
            </div>
            <div>
                <label class="block text-sm mb-1">Kategori</label>
                <select id="kategoriSelect" class="w-full border-2 rounded px-2 py-1 appearance-none bg-white" style="border-color: #F58220;">
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="grabfood">GrabFood</option>
                    <option value="gofood">GoFood</option>
                    <option value="shopeefood">ShopeeFood</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input id="semuaTipePembayaran" type="checkbox" class="mr-2"> Semua tipe pembayaran
            </label>
            <div class="border-2 rounded p-4 mt-2 shadow-sm" style="border-color: #F58220;">
                <div class="grid grid-cols-2 gap-2">
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> Gopay</label>
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> Kartu Debit</label>
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> GrabFood</label>
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> Ovo</label>
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> ShopeeFood</label>
                    <label><input type="checkbox" class="mr-2 payment-checkbox"> Qris</label>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input id="semuaStatusPembayaran" type="checkbox" class="mr-2"> Semua status pembayaran
            </label>
        </div>

        <div class="flex justify-between items-center">
            <span class="font-bold text-gray-700 cursor-pointer hover:underline" id="resetFilters">Atur Ulang</span>
            <div class="space-x-2">
                <button id="closeFilterModal" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">Batal</button>
                <button id="applyFilters" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Terapkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to open the modal
    function openModal() {
        document.getElementById('filterModal').classList.remove('hidden');
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('filterModal').classList.add('hidden');
    }

    // Event listener for closing the modal when clicking the "Batal" button
    document.getElementById('closeFilterModal').addEventListener('click', closeModal);

    // Reset all filter values including dropdown
    document.getElementById('resetFilters').addEventListener('click', () => {
        // Reset text input
        document.querySelector('input[type="text"]').value = '';
        
        // Reset dropdown to first option
        const kategoriSelect = document.getElementById('kategoriSelect');
        kategoriSelect.selectedIndex = 0;
        
        // Reset all checkboxes
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // Toggle all payment checkboxes when "Semua tipe pembayaran" is clicked
    document.getElementById('semuaTipePembayaran').addEventListener('change', function() {
        const paymentCheckboxes = document.querySelectorAll('.payment-checkbox');
        paymentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Event listener for applying the filters when clicking the "Terapkan" button
    document.getElementById('applyFilters').addEventListener('click', closeModal);
</script>