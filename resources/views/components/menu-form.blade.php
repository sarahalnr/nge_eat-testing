@props(['categories', 'platforms'])

<form action="{{ route('menus.store') }}" method="POST" id="formTambahMenu" class="w-full max-w-xl text-sm text-gray-700">
  @csrf

  <div class="grid grid-cols-2 gap-x-6 gap-y-4">
    <div class="col-span-2">
      <label for="name" class="mb-1 block">Nama Menu</label>
      <input type="text" name="name" id="name"
        class="border rounded-sm px-2 py-1 w-full"
        style="border-color: #F58220;"
        placeholder="Contoh: Nasi Ayam Geprek"
        required>
    </div>

    <div class="col-span-2">
      <label for="category_id" class="mb-1 block">Kategori</label>
      <select name="category_id" id="category_id"
        class="border rounded-sm px-2 py-1 w-full"
        style="border-color: #F58220;"
        required>
        <option value="">-- Pilih Kategori --</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-span-2">
      <h3 class="text-sm font-medium mt-2 mb-1">Harga per Platform</h3>
      <div class="grid grid-cols-2 gap-x-4 gap-y-2">
        @foreach ($platforms as $platform)
          <div>
            <label class="block text-xs mb-1">{{ $platform->name }}</label>
            <input type="number" name="prices[{{ $platform->id }}]"
              class="border rounded-sm px-2 py-1 w-full"
              style="border-color: #F58220;"
              placeholder="Harga di {{ $platform->name }}" required>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="mt-6 flex justify-end space-x-2">
    <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-1.5 rounded">Batal</button>
    <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white px-4 py-1.5 rounded">Simpan</button>
  </div>
</form>
