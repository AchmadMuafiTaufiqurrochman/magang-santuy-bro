<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SiTukang</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Daftar Akun</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="name" name="name" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Province -->
            <div>
                <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                <select id="province" name="province" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Provinsi</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                </select>
            </div>

            <!-- City -->
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                <select id="city" name="city" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Kota</option>
                    <option value="Sidoarjo">Sidoarjo</option>
                    <option value="Surabaya">Surabaya</option>
                </select>
            </div>

            <!-- District -->
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                <select id="district" name="district" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Button -->
            <div>
                <button type="submit"
                        class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Daftar
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-bold">Login di sini</a>
        </p>

        <!-- Error messages -->
        @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <script>
        // Data kecamatan untuk Sidoarjo & Surabaya
        const districts = {
            Sidoarjo: [
                "Candi", "Buduran", "Sedati", "Taman", "Waru", "Porong", "Krian", "Tanggulangin", "Balongbendo"
            ],
            Surabaya: [
                "Wonokromo", "Rungkut", "Sukolilo", "Tegalsari", "Genteng", "Gubeng", "Kenjeran", "Tambaksari", "Wiyung"
            ]
        };

        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');

        citySelect.addEventListener('change', function() {
            const selectedCity = this.value;
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

            if (districts[selectedCity]) {
                districts[selectedCity].forEach(d => {
                    const option = document.createElement('option');
                    option.value = d;
                    option.textContent = d;
                    districtSelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>
