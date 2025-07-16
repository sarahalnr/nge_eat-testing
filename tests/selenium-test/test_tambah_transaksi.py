from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

# === KONFIGURASI ===
BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
GOFOOD_URL = f"{BASE_URL}/gofood"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"

driver = webdriver.Chrome()
wait = WebDriverWait(driver, 10)
driver.maximize_window()

try:
    # === LOGIN ===
    print("🔐 Login...")
    driver.get(LOGIN_URL)
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    driver.find_element(By.XPATH, "//button[contains(text(), 'Masuk')]").click()

    wait.until(EC.presence_of_element_located((By.XPATH, "//h1[text()='Dashboard']")))
    print("✅ Login berhasil")

    # === AKSES GOFOOD ===
    print("➡️ Buka halaman transaksi GoFood...")
    driver.get(GOFOOD_URL)
    wait.until(EC.presence_of_element_located((By.XPATH, "//span[contains(text(), 'Transaksi GoFood')]")))

    # Klik tombol Tambah Transaksi
    wait.until(EC.element_to_be_clickable((By.CLASS_NAME, "btn-tambah"))).click()

    # Tunggu sampai modal benar-benar terbuka
    wait.until(EC.visibility_of_element_located((By.NAME, "tanggal")))
    time.sleep(0.5)

    # === ISI FORM UTAMA ===
    print("📝 Isi data transaksi...")
    driver.find_element(By.NAME, "tanggal").send_keys("2025-07-17")
    driver.find_element(By.NAME, "waktu").send_keys("11:45")
    driver.find_element(By.NAME, "nama_pelanggan").send_keys("Pembeli Selenium")
    driver.find_element(By.NAME, "metode_pembayaran").send_keys("Cash")

    # === TAMBAH ITEM ===
    print("➕ Tambah item pesanan...")
    driver.find_element(By.XPATH, "//button[contains(text(), '+ Tambah Item')]").click()
    time.sleep(0.5)

    # Isi menu menggunakan autocomplete
    print("🔍 Isi nama menu...")
    menu_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder='Ketik Nama Menu']")))
    menu_input.click()
    menu_input.send_keys("LITE BBQ Chicken - tidak pedas")
    time.sleep(1)  # beri waktu dropdown muncul

    # Gunakan ARROW_DOWN + ENTER untuk memilih dari dropdown
    menu_input.send_keys(Keys.ARROW_DOWN)
    menu_input.send_keys(Keys.ENTER)
    print("✅ Menu berhasil dipilih")

    # Isi jumlah
    jumlah_input = driver.find_element(By.CSS_SELECTOR, "input[name='items[0][jumlah]']")
    jumlah_input.clear()
    jumlah_input.send_keys("2")

    # Centang status "Sukses / Berhasil"
    driver.find_element(By.NAME, "status").click()

    # Submit form
    print("📤 Kirim form transaksi...")
    submit_button = driver.find_element(By.XPATH, "//button[contains(text(), 'Tambah')]")
    driver.execute_script("arguments[0].click();", submit_button)

    # Verifikasi sukses
    wait.until(EC.presence_of_element_located((By.CLASS_NAME, "alert-success")))
    print("✅ Transaksi berhasil ditambahkan!")
    driver.save_screenshot("transaksi_sukses.png")

except Exception as e:
    print("❌ Error:", str(e))
    driver.save_screenshot("transaksi_gagal.png")

finally:
    driver.quit()
