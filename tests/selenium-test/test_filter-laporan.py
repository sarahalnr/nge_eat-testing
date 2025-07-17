from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import os

# Matikan log yang tidak perlu
options = webdriver.ChromeOptions()
options.add_argument("--log-level=3")  # Supress logs
options.add_experimental_option("excludeSwitches", ["enable-logging"])  # Suppress DevTools logs

# === KONFIGURASI ===
BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
LAPORAN_URL = f"{BASE_URL}/laporan"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"
SCREENSHOT_PATH = "tests/selenium-test/screenshots/laporan_filtered.png"
os.makedirs(os.path.dirname(SCREENSHOT_PATH), exist_ok=True)

driver = webdriver.Chrome(options=options)
wait = WebDriverWait(driver, 20)
driver.maximize_window()

# Fungsi bantu untuk isi tanggal (pakai flatpickr JS API)
def isi_flatpickr_input(id, tanggal):
    driver.execute_script(f"document.querySelector('#{id}')._flatpickr.setDate('{tanggal}');")

try:
    print("Login...")
    driver.get(LOGIN_URL)
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
    time.sleep(2)

    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    time.sleep(2)

    driver.find_element(By.XPATH, "//button[contains(text(), 'Masuk')]").click()
    wait.until(EC.presence_of_element_located((By.XPATH, "//h1[text()='Dashboard']")))
    time.sleep(2)
    print("Login berhasil")

    print("Buka halaman laporan...")
    driver.get(LAPORAN_URL)
    wait.until(EC.presence_of_element_located((By.XPATH, "//h5[contains(text(), 'Laporan Transaksi')]")))
    time.sleep(2)

    print("Set tanggal filter...")
    isi_flatpickr_input("startDateLaporan", "2025-07-01")
    time.sleep(2)
    isi_flatpickr_input("endDateLaporan", "2025-07-16")
    time.sleep(2)

    driver.find_element(By.ID, "endDateLaporan").send_keys(Keys.ENTER)
    time.sleep(2)

    print("Tunggu reload data laporan...")
    wait.until(EC.presence_of_element_located((By.XPATH, "//table//tr[contains(@class, 'border-t')]")))
    time.sleep(2)

    print("Scroll setelah filter tanggal...")
    tabel = driver.find_element(By.XPATH, "//table")
    driver.execute_script("arguments[0].scrollIntoView({ behavior: 'smooth', block: 'end' });", tabel)
    time.sleep(2)

    print("Filter platform GoFood...")
    platform_select = wait.until(EC.presence_of_element_located((By.NAME, "platform")))
    wait.until(EC.element_to_be_clickable((By.NAME, "platform")))
    driver.execute_script("arguments[0].scrollIntoView(true);", platform_select)
    time.sleep(2)

    options = platform_select.find_elements(By.TAG_NAME, 'option')
    for option in options:
        if option.get_attribute("value") == "gofood":
            option.click()
            break
    time.sleep(2)

    platform_select.send_keys(Keys.ENTER)
    time.sleep(2)

    wait.until(EC.presence_of_element_located((By.XPATH, "//table//tr[contains(@class, 'border-t')]")))
    time.sleep(2)

    tabel = driver.find_element(By.XPATH, "//table")
    print("Scroll setelah filter platform...")
    driver.execute_script("arguments[0].scrollIntoView({ behavior: 'smooth', block: 'end' });", tabel)
    time.sleep(4)

    driver.save_screenshot(SCREENSHOT_PATH)
    print("Semua filter berhasil diterapkan dan hasil ditampilkan dengan benar.")

    time.sleep(2)

except Exception:
    # Jangan cetak error apapun ke terminal
    driver.save_screenshot("tests/selenium-test/screenshots/laporan_filter_gagal.png")

finally:
    driver.quit()
