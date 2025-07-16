<div id="BerhasilEditModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-md shadow-md p-6 w-full max-w-xl flex flex-col items-center">
    
    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
      <i class="fas fa-check text-black text-2xl"></i>
    </div>
    
    <h2 class="text-xl font-semibold mb-2 text-center w-full" style="border-color: #C0C0C0;">Berhasil!</h2>
    
    <div class="mt-2 text-center">
      <h2 class="text-base mb-4">Transaksi berhasil diedit</h2>
      <button id="closeBerhasilEditModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">
        OK
      </button>
    </div>
    
  </div>
</div>


<script>
    // Function to open the modal
    function openModal() {
        document.getElementById('BerhasilEditModal').classList.remove('hidden');
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('BerhasilEditModal').classList.add('hidden');
    }

    // Event listener for closing the modal when clicking the "Batal" button
    document.getElementById('closeBerhasilEditModal').addEventListener('click', closeModal);

    // Modal Berhasil Download
    document.getElementById('openBerhasilEditModal').addEventListener('click', function () {
        document.getElementById('BerhasilEditModal').classList.remove('hidden');
    });

    function closeAllModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
    }

    document.getElementById('closeBerhasilEditModal').addEventListener('click', closeAllModals);
</script>
