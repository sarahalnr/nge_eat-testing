@props(['menus', 'platforms'])

<!-- Inject menu & platform ke JavaScript -->
<script>
  const menuList = @json($menus);
  const platformList = @json($platforms);
</script>

<!-- Datalist untuk autocomplete menu -->
<datalist id="menuDatalist">
  @foreach ($menus as $menu)
    <option data-id="{{ $menu->id }}" value="{{ $menu->name }}">
  @endforeach
</datalist>

<!-- Modal Tambah Transaksi -->
<div id="transactionTambahModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-3xl max-h-[90vh] overflow-auto">
    <h2 class="text-xl font-semibold mb-4 border-b pb-2" style="border-color: #F58220;">Tambah Transaksi</h2>

    <form id="formTambahTransaksi" action="{{ route('grabfood.store') }}" method="POST">
      @csrf

      <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700">
        <div>
          <label for="tanggal" class="mb-1 block">Tanggal</label>
          <input id="tanggal" name="tanggal" type="date" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
        <div>
          <label for="waktu" class="mb-1 block">Waktu</label>
          <input id="waktu" name="waktu" type="time" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
        <div>
          <label for="id_pesanan" class="mb-1 block">ID Pesanan</label>
          <input id="id_pesanan" name="id_pesanan" type="text"
          class="border rounded-sm px-2 py-1 w-full bg-gray-100 shadow-sm"
          style="border-color: #F58220;"
          value="{{ $generatedId ?? '' }}"
          readonly>
        </div>

        <div>
          <label for="nama_pelanggan" class="mb-1 block">Nama Pelanggan</label>
          <input id="nama_pelanggan" name="nama_pelanggan" type="text" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
      </div>

      <!-- Dynamic Items -->
      <div class="mt-4 mb-2">
        <h3 class="font-medium mb-2">Detail Pesanan</h3>
        <div id="tambahItemsContainer" class="space-y-4"></div>
        <button type="button" onclick="addTambahItemRow()" class="mt-2 px-3 py-1.5 bg-blue-600 text-white rounded shadow hover:bg-blue-700">+ Tambah Item</button>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-4">
        <div>
          <label for="metode_pembayaran" class="mb-1 block">Metode Pembayaran</label>
          <input id="metode_pembayaran" name="metode_pembayaran" type="text" class="border rounded-sm px-2 py-1 w-full bg-white shadow-sm" style="border-color: #F58220;" required>
        </div>
      </div>

      <div class="mt-4">
        <label class="inline-flex items-center space-x-2 mt-2">
          <input type="checkbox" name="status" value="1" class="form-checkbox text-green-600" />
          <span>Sukses / Berhasil</span>
        </label>
      </div>

      <div class="mt-6 flex justify-between items-center">
        <a href="#" onclick="resetTambahTransaksiModal(); return false;" class="text-sm text-blue-600 underline">Atur Ulang</a>
        <div>
          <button type="button" onclick="closeTambahModal()" class="bg-red-700 hover:bg-red-800 text-white px-4 py-1.5 rounded shadow mr-2">Batal</button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded shadow">Tambah</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  let itemIndex = 0;

  function resetTambahTransaksiModal() {
    const form = document.getElementById('formTambahTransaksi');
    form.reset();
    document.getElementById('tambahItemsContainer').innerHTML = '';
    itemIndex = 0;
    addTambahItemRow();
  }

  function closeTambahModal() {
    document.getElementById('transactionTambahModal').classList.add('hidden');
  }

  function addTambahItemRow() {
    const container = document.getElementById('tambahItemsContainer');
    const grabPlatform = platformList.find(p => p.name.toLowerCase().includes('grab'));
    const platformId = grabPlatform ? grabPlatform.id : '';

    const row = document.createElement('div');
    row.classList.add('grid', 'grid-cols-5', 'gap-2', 'items-end', 'item-row');
    row.innerHTML = `
      <div class="col-span-2">
        <label class="block text-xs mb-1">Menu</label>
        <input list="menuDatalist" class="menu_name border px-2 py-1 w-full rounded-sm" style="border-color: #F58220;" required placeholder="Ketik Nama Menu">
        <input type="hidden" name="items[${itemIndex}][menu_id]" class="menu_id_hidden">
      </div>

      <input type="hidden" name="items[${itemIndex}][platform_id]" class="platform_id" value="${platformId}">

      <div>
        <label class="block text-xs mb-1">Jumlah</label>
        <input type="number" name="items[${itemIndex}][jumlah]" value="1" class="jumlah border px-2 py-1 w-full rounded-sm" style="border-color: #F58220;" min="1" required>
      </div>

      <div class="flex items-end gap-2">
        <button type="button" class="btn-hapus-item text-red-600 border border-red-600 px-3 py-1 rounded hover:bg-red-600 hover:text-white transition">
          Hapus
        </button>
      </div>

      <input type="hidden" class="harga_item" name="items[${itemIndex}][harga]" value="">
      <input type="hidden" class="subtotal_item" name="items[${itemIndex}][subtotal]" value="">
    `;

    container.appendChild(row);
    attachEventsToRow(row, platformId);
    itemIndex++;
  }

  function attachEventsToRow(row, fixedPlatformId = null) {
    const menuInput = row.querySelector('.menu_name');
    const menuHidden = row.querySelector('.menu_id_hidden');
    const jumlahInput = row.querySelector('.jumlah');
    const btnHapus = row.querySelector('.btn-hapus-item');

    const platformId = fixedPlatformId || row.querySelector('.platform_id')?.value;

    btnHapus?.addEventListener('click', () => {
      row.remove();
    });

    menuInput.addEventListener('input', () => {
      const selected = menuList.find(m => m.name.toLowerCase() === menuInput.value.toLowerCase());
      menuHidden.value = selected ? selected.id : '';
      updateSubtotal(row);
    });

    jumlahInput.addEventListener('input', () => updateSubtotal(row));
  }

  function updateSubtotal(row) {
    const menuId = row.querySelector('.menu_id_hidden')?.value;
    const jumlah = parseInt(row.querySelector('.jumlah')?.value) || 1;
    const hargaInput = row.querySelector('.harga_item');
    const subtotalInput = row.querySelector('.subtotal_item');
    const platformId = row.querySelector('.platform_id')?.value;

    if (menuId && platformId) {
      fetch(`/get-price?menu_id=${menuId}&platform_id=${platformId}`)
        .then(res => res.json())
        .then(data => {
          const harga = parseFloat(data.price || 0);
          const subtotal = harga * jumlah;
          hargaInput.value = harga;
          subtotalInput.value = subtotal;
        });
    } else {
      hargaInput.value = '';
      subtotalInput.value = '';
    }
  }
</script>
