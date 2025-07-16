<div id="openHapusModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-xl items-center">
    <div class="text-center">
      <i class="fas fa-exclamation-circle text-5xl"></i>
    </div>
    <h2 class="text-xl text-center font-semibold mt-2">Lanjutkan Hapus?</h2>

    <div class="mt-2 text-center">
      <h2 class="text-base text-center mb-4">Anda akan menghapus transaksi ini</h2>
      <button onclick="closeHapusModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 mr-4 rounded">Tutup</button>

      <button id="confirmDeleteBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded" data-id="">
        Hapus
      </button>
    </div>
  </div>
</div>

<script>
  let deleteTransactionId = null;

  function openHapusModal(id) {
    deleteTransactionId = id;
    const modal = document.getElementById('openHapusModal');
    modal.classList.remove('hidden');
    document.getElementById('confirmDeleteBtn').setAttribute('data-id', id);
  }

  function closeHapusModal() {
    document.getElementById('openHapusModal').classList.add('hidden');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');

        fetch(`/gofood/delete/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => {
          if (response.ok) return response.json();
          else throw new Error('Gagal menghapus data');
        })
        .then(data => {
          closeHapusModal();

          alert(data.message || 'Data berhasil dihapus');

          const row = document.querySelector(`button.btn-hapus[transaction-id="${id}"]`)?.closest('tr');
          if (row) row.remove();
        })
        .catch(err => {
          alert('Gagal menghapus data');
          console.error(err);
        });
      });
    }
  });
</script>
