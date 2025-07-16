<!-- Modal Detail Transaksi -->
<div id="transactionDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-xl">
    <h2 class="text-xl font-semibold mb-4 border-b pb-2" style="border-color: #F58220;">Detail Transaksi</h2>

    <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700">
      <div>
        <p class="mb-1">Tanggal</p>
        <input type="text" id="detail-tanggal" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;">
      </div>
      <div>
        <p class="mb-1">Waktu</p>
        <input type="text" id="detail-waktu" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;">
      </div>
      <div>
        <p class="mb-1">ID Pesanan</p>
        <input type="text" id="detail-id_pesanan" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;">
      </div>
      <div>
        <p class="mb-1">Status</p>
        <input type="text" id="detail-status" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm font-semibold text-green-600" style="border-color: #F58220;">
      </div>
      <div>
        <p class="mb-1">Nama Pelanggan</p>
        <input type="text" id="detail-nama" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;">
      </div>
      <div>
        <p class="mb-1">Metode Pembayaran</p>
        <input type="text" id="detail-metode" readonly class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;">
      </div>
    </div>

    <!-- Baris Item dan Total -->
    <div class="flex gap-4 mt-4">
      <div class="w-1/2">
        <p class="mb-1 font-medium">Item Pesanan</p>
        <ul class="list-disc list-inside text-gray-700" id="detail-item-list">
          <!-- Item pesanan akan ditambahkan lewat JS -->
        </ul>
      </div>
      <div class="w-1/2">
        <p class="mb-1 font-medium">Total</p>
        <p id="detail-total-value" class="text-base font-semibold text-gray-800">Rp 0</p>
      </div>
    </div>

    <div class="mt-6 text-right">
      <button onclick="closeDetailModal()" class="bg-red-700 hover:bg-red-800 text-white px-4 py-1.5 rounded shadow">
        Tutup
      </button>
    </div>
  </div>
</div>

<!-- Script Modal Detail -->
<script>
  function openTransactionDetailModal(transactionData) {
    const modal = document.getElementById('transactionDetailModal');

    // Isi detail
    document.getElementById('detail-tanggal').value = transactionData.tanggal || '';
    document.getElementById('detail-waktu').value = transactionData.waktu || '';
    document.getElementById('detail-id_pesanan').value = transactionData.id_pesanan || '';
    document.getElementById('detail-status').value = transactionData.status ? 'Sukses' : 'Gagal';
    document.getElementById('detail-nama').value = transactionData.nama_pelanggan || '';
    document.getElementById('detail-metode').value = transactionData.metode_pembayaran || '';

    // Proses item_pesanan yang berupa string
    const itemList = document.getElementById('detail-item-list');
    const totalValue = document.getElementById('detail-total-value');
    itemList.innerHTML = '';

    const items = transactionData.item_pesanan || '';
    const itemArray = items.split(',').map(i => i.trim()).filter(i => i.length > 0);

    if (itemArray.length > 0) {
      itemArray.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item;
        itemList.appendChild(li);
      });
    } else {
      const li = document.createElement('li');
      li.textContent = 'Tidak ada item pesanan.';
      itemList.appendChild(li);
    }

    // Tampilkan total (jika tersedia)
    const total = parseFloat(transactionData.total || 0);
    totalValue.textContent = `Rp ${total.toLocaleString('id-ID')}`;

    // Tampilkan modal
    modal.classList.remove('hidden');
  }

  function closeDetailModal() {
    document.getElementById('transactionDetailModal').classList.add('hidden');
  }
</script>
