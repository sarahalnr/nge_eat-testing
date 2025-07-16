@props(['menus', 'platforms'])

<!-- Inject menu & platform ke JavaScript -->
<script>
  const menuList = @json($menus);
  const platformList = @json($platforms);
</script>

<!-- Modal Edit Transaksi -->
<div id="transactionEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-3xl max-h-[90vh] overflow-auto">
    <h2 class="text-xl font-semibold mb-4 border-b pb-2" style="border-color: #F58220;">Edit Transaksi</h2>

    <form id="formEditTransaksi" method="POST">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700">
        <div>
          <label for="edit_tanggal" class="mb-1 block">Tanggal</label>
          <input id="edit_tanggal" name="tanggal" type="date" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
        <div>
          <label for="edit_waktu" class="mb-1 block">Waktu</label>
          <input id="edit_waktu" name="waktu" type="time" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
        <div>
          <label for="edit_id_pesanan" class="mb-1 block">ID Pesanan</label>
          <input id="edit_id_pesanan" name="id_pesanan" type="text"
                 class="border rounded-sm px-2 py-1 w-full bg-gray-100 shadow-sm"
                 style="border-color: #F58220;"
                 readonly>
        </div>
        <div>
          <label for="edit_nama_pelanggan" class="mb-1 block">Nama Pelanggan</label>
          <input id="edit_nama_pelanggan" name="nama_pelanggan" type="text" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
      </div>

      <!-- Items -->
      <div class="mt-4 mb-2">
        <h3 class="font-medium mb-2">Detail Pesanan</h3>
        <div id="editItemsContainer" class="space-y-4"></div>
        <button type="button" onclick="addEditItemRow()" class="mt-2 px-3 py-1.5 bg-blue-600 text-white rounded shadow hover:bg-blue-700">+ Tambah Item</button>
      </div>

    <div class="grid grid-cols-2 gap-4 mt-4">
    <div>
        <label for="edit_metode_pembayaran" class="mb-1 block">Metode Pembayaran</label>
        <input
            id="edit_metode_pembayaran"
            name="metode_pembayaran"
            type="text"
            class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm"
            style="border-color: #F58220;"
            required="required"></div>
    </div>


      <div class="mt-4">
        <label class="inline-flex items-center space-x-2 mt-2">
          <input type="checkbox" name="status" value="1" class="form-checkbox text-green-600" />
          <span>Sukses / Berhasil</span>
        </label>
      </div>

      <div class="mt-6 flex justify-between items-center">
        <a href="#" onclick="resetEditTransaksiModal(); return false;" class="text-sm text-blue-600 underline">Atur Ulang</a>
        <div>
          <button type="button" onclick="closeEditModal()" class="bg-red-700 hover:bg-red-800 text-white px-4 py-1.5 rounded shadow mr-2">Batal</button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded shadow">Simpan</button>
        </div>
      </div>

      <input type="hidden" name="item_pesanan" id="item_pesanan_input">
    </form>
  </div>
</div>

<script>
  let editItemIndex = 0;

  function resetEditTransaksiModal() {
    document.getElementById('formEditTransaksi').reset();
    document.getElementById('editItemsContainer').innerHTML = '';
    editItemIndex = 0;
  }

  function addEditItemRow(item = {}) {
  const container = document.getElementById('editItemsContainer');
  const menuOptions = menuList.map(menu => `<option value="${menu.id}" ${item.menu_id == menu.id ? 'selected' : ''}>${menu.name}</option>`).join('');
  
  // ✅ Pindahkan definisi ke sini
  const gofoodPlatformId = platformList.find(p => p.name.toLowerCase().includes('gofood'))?.id || '';
  const platformId = item.platform_id || gofoodPlatformId;

  const row = document.createElement('div');
  row.classList.add('grid', 'grid-cols-4', 'gap-2', 'items-end', 'item-row');
  row.innerHTML = `
    <div class="col-span-2">
      <label class="block text-xs mb-1">Menu</label>
      <select name="items[${editItemIndex}][menu_id]" class="menu_id border px-2 py-1 w-full rounded-sm" style="border-color: #F58220;" required>
        <option value="">-- Pilih Menu --</option>
        ${menuOptions}
      </select>
    </div>

    <input type="hidden" name="items[${editItemIndex}][platform_id]" class="platform_id" value="${platformId}">

    <div>
      <label class="block text-xs mb-1">Jumlah</label>
      <input type="number" name="items[${editItemIndex}][jumlah]" value="${item.jumlah || 1}" class="jumlah border px-2 py-1 w-full rounded-sm" style="border-color: #F58220;" min="1" required>
    </div>

    <div class="flex items-end gap-2">
      <button type="button" class="btn-hapus-item text-red-600 border border-red-600 px-3 py-1 rounded hover:bg-red-600 hover:text-white transition">Hapus</button>
    </div>

    <input type="hidden" class="harga_item" name="items[${editItemIndex}][harga]" value="${item.harga || ''}">
    <input type="hidden" class="subtotal_item" name="items[${editItemIndex}][subtotal]" value="${item.subtotal || ''}">
  `;

  container.appendChild(row);
  attachEventsToEditRow(row, platformId); // ✅ pastikan platformId dikirim ke sini
  editItemIndex++;
}


  function attachEventsToEditRow(row, platformId) {
    const menuSelect = row.querySelector('.menu_id');
    const jumlahInput = row.querySelector('.jumlah');
    const hargaInput = row.querySelector('.harga_item');
    const subtotalInput = row.querySelector('.subtotal_item');
    const btnHapus = row.querySelector('.btn-hapus-item');

    btnHapus?.addEventListener('click', () => row.remove());

    const updateSubtotal = () => {
      const menuId = menuSelect.value;
      const jumlah = parseInt(jumlahInput.value) || 1;

      if (menuId && platformId) {
        fetch(`/get-price?menu_id=${menuId}&platform_id=${platformId}`)
          .then(res => res.json())
          .then(data => {
            const harga = parseFloat(data.price || 0);
            hargaInput.value = harga;
            subtotalInput.value = harga * jumlah;
          });
      } else {
        hargaInput.value = '';
        subtotalInput.value = '';
      }
    };

    menuSelect.addEventListener('change', updateSubtotal);
    jumlahInput.addEventListener('input', updateSubtotal);
  }

  function showEditModal(data) {
    document.getElementById('edit_tanggal').value = data.tanggal;
    document.getElementById('edit_waktu').value = data.waktu;
    document.getElementById('edit_id_pesanan').value = data.id_pesanan;
    document.getElementById('edit_nama_pelanggan').value = data.nama_pelanggan;
    document.getElementById('edit_metode_pembayaran').value = data.metode_pembayaran;
    document.querySelector('#formEditTransaksi [name="status"]').checked = data.status == 1;

    document.getElementById('editItemsContainer').innerHTML = '';
    editItemIndex = 0;
    if (Array.isArray(data.items)) {
      data.items.forEach(item => addEditItemRow(item));
    }

    document.getElementById('transactionEditModal').classList.remove('hidden');
  }
</script>
