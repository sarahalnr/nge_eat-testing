import subprocess
import os

tests = [
    "test_tambah_transaksi.py",
    "test_filter-laporan.py",
    "test_export-laporan.py"
]

# Tentuin folder test yang bener
test_folder = "tests/selenium-test"

for test in tests:
    print(f"\n=== Running {test} ===")
    try:
        # Build path lengkap ke file test
        test_path = os.path.join(test_folder, test)
        subprocess.run(["python", test_path], check=True)
        print(f"{test} selesai dengan sukses!\n")
    except subprocess.CalledProcessError:
        print(f"ERROR di {test}, cek log!\n")
