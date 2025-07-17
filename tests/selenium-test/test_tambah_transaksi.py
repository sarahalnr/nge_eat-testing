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
GOFOOD_URL = f"{BASE_URL}/gofood"
EMAIL = "admin@gmail.com"
PASSWORD = "admin123"
SCREENSHOT_PATH = "tests/selenium-test/screenshots/tambah_transaksi_gagal.png"
os.makedirs(os.path.dirname(SCREENSHOT_PATH), exist_ok=True)

driver = webdriver.Chrome(options=options)
wait = WebDriverWait(driver, 20)
driver.maximize_window()

try:
    print("Login...")
    driver.get(LOGIN_URL)
    wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(EMAIL)
    time.sleep(2)

    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    time.sleep(2)

    driver.find_element(By.XPATH, '//button[contains(text(), "Masuk")]').click()
    wait.until(EC.presence_of_element_located((By.XPATH, "//h1[text()='Dashboard']")))
    time.sleep(2)
    print("Login berhasil")

    print("Buka halaman GoFood...")
    driver.get(GOFOOD_URL)
    wait.until(EC.element_to_be_clickable(
        (By.XPATH, '//button[.//span[contains(text(), "Tambah Transaksi")]]'))
    ).click()
    time.sleep(2)

    print("Isi form tambah transaksi...")
    wait.until(EC.visibility_of_element_located((By.ID, "formTambahTransaksi")))
    time.sleep(2)

    driver.find_element(By.ID, "tanggal").send_keys("17-07-2025")
    time.sleep(2)

    driver.find_element(By.ID, "waktu").send_keys("13:36")
    time.sleep(2)

    driver.find_element(By.ID, "nama_pelanggan").send_keys("Budi Gacor")
    time.sleep(2)

    driver.find_element(By.ID, "metode_pembayaran").send_keys("Cash")
    time.sleep(2)

    print("Tambah item 1...")
    driver.find_element(By.XPATH, '//button[contains(text(), "+ Tambah Item")]').click()
    wait.until(lambda d: len(d.find_elements(By.CSS_SELECTOR, "#tambahItemsContainer .item-row")) >= 1)
    time.sleep(2)
    item1 = driver.find_elements(By.CSS_SELECTOR, "#tambahItemsContainer .item-row")[-1]

    menu1 = wait.until(lambda d: item1.find_element(By.CSS_SELECTOR, ".menu_name"))
    menu1.send_keys("Rice Bowl Ayam Sambal Geprek -sangat pedas")
    time.sleep(0.5)
    menu1.send_keys(Keys.TAB)
    time.sleep(2)

    jumlah1 = item1.find_element(By.CSS_SELECTOR, ".jumlah")
    jumlah1.clear()
    jumlah1.send_keys("2")
    time.sleep(2)

    print("Tambah item 2...")
    driver.find_element(By.XPATH, '//button[contains(text(), "+ Tambah Item")]').click()
    wait.until(lambda d: len(d.find_elements(By.CSS_SELECTOR, "#tambahItemsContainer .item-row")) == 2)
    time.sleep(2)
    item2 = driver.find_elements(By.CSS_SELECTOR, "#tambahItemsContainer .item-row")[1]

    menu2 = wait.until(lambda d: item2.find_element(By.CSS_SELECTOR, ".menu_name"))
    menu2.send_keys("Ice Tea")
    time.sleep(0.5)
    menu2.send_keys(Keys.TAB)
    time.sleep(2)

    jumlah2 = item2.find_element(By.CSS_SELECTOR, ".jumlah")
    jumlah2.clear()
    jumlah2.send_keys("1")
    time.sleep(2)

    print("Centang status berhasil...")
    checkbox = driver.find_element(By.CSS_SELECTOR, 'input[type="checkbox"][name="status"]')
    driver.execute_script("arguments[0].click();", checkbox)
    time.sleep(2)

    print("Submit form...")
    submit_button = driver.find_element(By.XPATH, '//button[@type="submit" and contains(text(), "Tambah")]')
    submit_button.click()
    time.sleep(3)  # Ini biar submitnya aman, jangan diganti

    print("Berhasil submit transaksi. Tunggu 3 detik buat amannya...")

except Exception:
    driver.save_screenshot(SCREENSHOT_PATH)
    print("Terjadi error! Screenshot disimpan:", SCREENSHOT_PATH)

finally:
    driver.quit()
