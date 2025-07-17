from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import os

# Matikan log yang tidak perlu
options = webdriver.ChromeOptions()
options.add_argument("--log-level=3")
options.add_experimental_option("excludeSwitches", ["enable-logging"])

# === KONFIGURASI ===
BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
LAPORAN_URL = f"{BASE_URL}/laporan"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"
SCREENSHOT_PATH = "tests/selenium-test/screenshots/unduh_laporan_berhasil.png"
ERROR_SCREENSHOT_PATH = "tests/selenium-test/screenshots/unduh_laporan_gagal.png"
DOWNLOAD_DIR = os.path.abspath("tests/selenium-test/downloads")

os.makedirs(os.path.dirname(SCREENSHOT_PATH), exist_ok=True)
os.makedirs(DOWNLOAD_DIR, exist_ok=True)

prefs = {
    "download.default_directory": DOWNLOAD_DIR,
    "download.prompt_for_download": False,
    "download.directory_upgrade": True,
    "safebrowsing.enabled": True,
}
options.add_experimental_option("prefs", prefs)

driver = webdriver.Chrome(options=options)
wait = WebDriverWait(driver, 20)
driver.maximize_window()

def bersihin_download(exts=[".pdf", ".xlsx"]):
    for f in os.listdir(DOWNLOAD_DIR):
        if any(f.endswith(ext) for ext in exts):
            os.remove(os.path.join(DOWNLOAD_DIR, f))

def tunggu_file(ext=".pdf", timeout=15):
    while timeout > 0:
        files = [f for f in os.listdir(DOWNLOAD_DIR) if f.endswith(ext)]
        if files:
            print(f"File {ext} berhasil diunduh:", files[0])
            return True
        time.sleep(1)
        timeout -= 1
    print(f"File {ext} gagal diunduh!")
    return False

try:
    print("Bersihin folder download dulu...")
    bersihin_download()
    time.sleep(2)

    print("Login...")
    driver.get(LOGIN_URL)
    time.sleep(2)
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
    time.sleep(2)
    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    time.sleep(2)
    driver.find_element(By.XPATH, '//button[contains(text(), "Masuk")]').click()
    time.sleep(2)
    wait.until(EC.presence_of_element_located((By.XPATH, "//h1[text()='Dashboard']")))
    print("Login berhasil")

    print("Buka halaman laporan...")
    driver.get(LAPORAN_URL)
    time.sleep(2)
    wait.until(EC.presence_of_element_located((By.XPATH, "//h5[contains(text(), 'Laporan Transaksi')]")))

    print("Buka modal Unduh Laporan via JS openModal()...")
    driver.execute_script("openModal();")
    time.sleep(2)

    print("Tunggu modal DownloadModal muncul...")
    wait.until(lambda d: 'hidden' not in d.find_element(By.ID, "DownloadModal").get_attribute("class"))
    time.sleep(2)

    print("Klik link 'Unduh PDF'...")
    link_pdf = wait.until(EC.element_to_be_clickable((By.LINK_TEXT, "Unduh PDF")))
    driver.execute_script("arguments[0].scrollIntoView(true);", link_pdf)
    time.sleep(2)
    link_pdf.click()
    assert tunggu_file(".pdf"), "File PDF tidak terunduh!"
    time.sleep(2)

    print("Klik link 'Unduh Excel'...")
    link_excel = wait.until(EC.element_to_be_clickable((By.LINK_TEXT, "Unduh Excel")))
    driver.execute_script("arguments[0].scrollIntoView(true);", link_excel)
    time.sleep(2)
    link_excel.click()
    assert tunggu_file(".xlsx"), "File Excel tidak terunduh!"
    time.sleep(2)

    driver.save_screenshot(SCREENSHOT_PATH)
    print("Semua file berhasil di-download. Screenshot di:", SCREENSHOT_PATH)

except Exception:
    driver.save_screenshot(ERROR_SCREENSHOT_PATH)
    print("Gagal unduh laporan, cek screenshot:", ERROR_SCREENSHOT_PATH)

finally:
    driver.quit()
