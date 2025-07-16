<!-- Modal Unduh -->
<div id="DownloadModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-xl">
    <h2 class="text-xl text-center font-semibold mb-4 border-b pb-2 text-gray-700" style="border-color: #C0C0C0;">Unduh Laporan Transaksi</h2>

    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
      <a href="{{ route('laporan.download.pdf', request()->all()) }}" 
         class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm text-center block w-full sm:w-auto">
         Unduh PDF
      </a>
      <a href="{{ route('laporan.download.excel', request()->all()) }}" 
         class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm text-center block w-full sm:w-auto">
         Unduh Excel
      </a>
    </div>

    <div class="text-center mt-6">
      <button id="closeDownloadModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">Tutup</button>
    </div>
  </div>
</div>

<!-- Script Modal -->
<script>
    // Buka modal
    function openModal() {
        document.getElementById('DownloadModal').classList.remove('hidden');
    }

    // Tutup modal
    function closeModal() {
        document.getElementById('DownloadModal').classList.add('hidden');
    }

    // Event close
    document.getElementById('closeDownloadModal').addEventListener('click', closeModal);
</script>
