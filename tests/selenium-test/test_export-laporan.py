import os
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

# === KONFIGURASI ===
BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
LAPORAN_URL = f"{BASE_URL}/laporan"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"

# === FOLDER UNDUHAN ===
download_dir = os.path.abspath("tests/selenium-test/downloads")
os.makedirs(download_dir, exist_ok=True)

# === SETUP CHROME ===
chrome_options = Options()
chrome_options.add_experimental_option("prefs", {
    "download.default_directory": download_dir,
    "download.prompt_for_download": False,
    "download.directory_upgrade": True,
    "safebrowsing.enabled": True
})
driver = webdriver.Chrome(options=chrome_options)
wait = WebDriverWait(driver, 20)
driver.maximize_window()

try:
    # === LOGIN DULU ===
    print("🔐 Login...")
    driver.get(LOGIN_URL)
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    driver.find_element(By.XPATH, "//button[contains(text(), 'Masuk')]").click()
    wait.until(EC.presence_of_element_located((By.XPATH, "//h1[text()='Dashboard']")))
    print("✅ Login berhasil")

    # === KE HALAMAN LAPORAN ===
    print("➡️ Ke halaman laporan...")
    driver.get(LAPORAN_URL)
    wait.until(EC.presence_of_element_located((By.XPATH, "//h5[contains(text(), 'Laporan Transaksi')]")))

    # === KLIK MODAL UNDUH ===
    print("📥 Klik tombol Unduh Laporan...")
    btn_unduh = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Unduh Laporan')]")))
    btn_unduh.click()

    # === TUNGGU MODAL MUNCUL DAN KLIK PDF ===
    time.sleep(1)  # Biar modal muncul
    print("⬇️ Klik link PDF...")
    wait.until(EC.element_to_be_clickable((By.LINK_TEXT, "PDF"))).click()
    time.sleep(4)

    # === KLIK ULANG MODAL DAN KLIK EXCEL ===
    btn_unduh = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Unduh Laporan')]")))
    btn_unduh.click()
    time.sleep(1)
    print("⬇️ Klik link Excel...")
    wait.until(EC.element_to_be_clickable((By.LINK_TEXT, "Excel"))).click()
    time.sleep(4)

    # === SIMPAN SCREENSHOT ===
    screenshot_path = "tests/selenium-test/screenshots/export_ok.png"
    driver.save_screenshot(screenshot_path)
    print("✅ Sukses. Screenshot disimpan di:", screenshot_path)

except Exception as e:
    print("❌ Error:", e)
    driver.save_screenshot("tests/selenium-test/screenshots/export_gagal.png")

finally:
    driver.quit()
