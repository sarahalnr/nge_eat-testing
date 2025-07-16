@props(['menu', 'categories', 'platforms'])

<div>
  <h2 class="text-xl font-semibold mb-4">Edit Menu</h2>

  <form action="{{ route('menus.update', $menu->id) }}" method="POST" id="formEditMenu" class="w-full max-w-xl text-sm text-gray-700">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-2 gap-x-6 gap-y-4">
      <div class="col-span-2">
        <label for="name" class="mb-1 block">Nama Menu</label>
        <input type="text" name="name" id="name"
          class="border rounded-sm px-2 py-1 w-full"
          style="border-color: #F58220;"
          placeholder="Contoh: Nasi Ayam Geprek"
          required
          value="{{ old('name', $menu->name) }}">
      </div>

      <div class="col-span-2">
        <label for="category_id" class="mb-1 block">Kategori</label>
        <select name="category_id" id="category_id"
          class="border rounded-sm px-2 py-1 w-full"
          style="border-color: #F58220;"
          required>
          <option value="">-- Pilih Kategori --</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}"
              {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-span-2">
        <h3 class="text-sm font-medium mt-2 mb-1">Harga per Platform</h3>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
          @foreach ($platforms as $platform)
            @php
              $priceObj = $menu->prices->firstWhere('platform_id', $platform->id);
              $priceValue = old('prices.' . $platform->id, $priceObj?->price);
            @endphp
            <div>
              <label class="block text-xs mb-1">{{ $platform->name }}</label>
              <input type="number" name="prices[{{ $platform->id }}]"
                class="border rounded-sm px-2 py-1 w-full"
                style="border-color: #F58220;"
                placeholder="Harga di {{ $platform->name }}"
                required
                value="{{ $priceValue }}">
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="mt-6 flex justify-end space-x-2">
      <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-1.5 rounded">Batal</button>
      <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white px-4 py-1.5 rounded">Simpan</button>
    </div>
  </form>

  <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
</div>
