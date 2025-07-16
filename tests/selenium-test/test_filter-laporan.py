from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import os

# === KONFIGURASI ===
BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
LAPORAN_URL = f"{BASE_URL}/laporan"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"
SCREENSHOT_PATH = "tests/selenium-test/screenshots/laporan_filtered.png"

#folder screenshots
os.makedirs(os.path.dirname(SCREENSHOT_PATH), exist_ok=True)

driver = webdriver.Chrome()
wait = WebDriverWait(driver, 20)  # Tunggu lebih lama: 20 detik
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

    # === AKSES HALAMAN LAPORAN ===
    print("✅ Buka halaman laporan...")
    driver.get(LAPORAN_URL)
    wait.until(EC.presence_of_element_located((By.XPATH, "//h5[contains(text(), 'Laporan Transaksi')]")))

    # === FILTER: tanggal + platform ===
    print("✅ Isi filter tanggal dan platform...")
    wait.until(EC.presence_of_element_located((By.ID, "startDateLaporan"))).click()
    time.sleep(1)  

    driver.find_element(By.ID, "startDateLaporan").send_keys("2025-07-01")
    time.sleep(0.5)
    driver.find_element(By.ID, "endDateLaporan").send_keys("2025-07-16")
    time.sleep(0.5)

    # Pilih platform GoFood
    platform_select = wait.until(EC.presence_of_element_located((By.NAME, "platform")))
    for option in platform_select.find_elements(By.TAG_NAME, 'option'):
        if option.get_attribute("value") == "gofood":
            option.click()
            break

    time.sleep(1)  

    # Klik tombol Filter
    driver.find_element(By.XPATH, "//button[contains(text(), 'Filter')]").click()

    # === VERIFIKASI HASIL ===
    print("✅ Tunggu hasil tabel muncul...")
    wait.until(EC.presence_of_element_located((By.XPATH, "//table//tr[contains(@class, 'border-t')]")))

    # Tunggu sebelum screenshot
    time.sleep(2)

    # Scroll ke elemen tabel 
    tabel = driver.find_element(By.XPATH, "//table")
    driver.execute_script("arguments[0].scrollIntoView({ behavior: 'smooth', block: 'center' });", tabel)
    time.sleep(2)

    # Screenshot hasil
    driver.save_screenshot(SCREENSHOT_PATH)

    print("✅ Filter berhasil!")

except Exception as e:
    print(f"❌ Terjadi kesalahan: {e}")
    driver.save_screenshot("tests/selenium-test/screenshots/laporan_filter_gagal.png")

finally:
    driver.quit()
