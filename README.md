# 👑 GOLD CRYPTO  - İnvestisiya Paneli (Simulyasiya)

Bu layihə, istifadəçilərin kriptovalyuta qiymətlərini izləyə biləcəyi, qeydiyyatdan keçərək virtual balansla ticarət edə biləcəyi bir **FinTech** veb tətbiqi simulyasiyasıdır. Layihə həmçinin kiber-təhlükəsizlik təhsili məqsədilə daxilində qəsdən saxlanılmış **SQL Injection** zəifliyini nümayiş etdirir.

## 🚀 Texnologiyalar
* **Backend:** PHP 8.x
* **Frontend:** HTML5, CSS3 (Modern Dark UI), JavaScript (Live Charts)
* **Database:** MySQL / MariaDB
* **Server:** XAMPP / Apache

## 🛠️ Əsas Funksiyalar
* **Dinamik Qeydiyyat və Giriş:** İstifadəçilər yeni hesab yarada və mövcud hesablarına daxil ola bilərlər.
* **Virtual Balans:** Hər yeni istifadəçiyə başlanğıc üçün **$10,000** balans verilir.
* **Canlı Kripto Bazarı:** 8 fərqli aktiv (BTC, ETH, SOL və s.) üzrə real vaxt simulyasiyalı qiymətlər.
* **Ticarət Modulu:** İstifadəçilər balanslarından istifadə edərək "AL" (Buy) əməliyyatları icra edə bilərlər.
* **Sessiya İdarəetməsi:** Cookie əsaslı giriş-çıxış (Logout) sistemi.

---

## 🛡️ Kiber-Təhlükəsizlik Hesabatı: SQL Injection (Zəiflik Analizi)

Bu layihənin giriş panelində **Authentication Bypass** (Girişin yan keçilməsi) xətası mövcuddur.

### 🔍 Zəiflik Nədir?
Sistem istifadəçi tərəfindən daxil edilən məlumatları birbaşa SQL sorğusuna göndərir (Sanitization olunmur). Bu, hücumçunun verilənlər bazası sorğusunu manipulyasiya etməsinə şərait yaradır.

### 📉 Hədəf Kod Parçası:
```php
$user = $_POST['username'];
$pass = $_POST['password'];
$sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
```

### ⚔️ İstismar (Exploit) Metodu:
Giriş panelində "İstifadəçi Adı" xanasına aşağıdakı payload daxil edildikdə:
> **Payload:** `admin' OR '1'='1' #`

**SQL Sorğusu belə dəyişir:**
```sql
SELECT * FROM users WHERE username = 'admin' OR '1'='1' #' AND password = '...'
```
* `'1'='1'` həmişə **DOĞRU (TRUE)** olduğu üçün `WHERE` şərti ödənilir.
* `#` işarəsi sorğunun qalan hissəsini (şifrə yoxlanışını) şərhə (comment) çevirir və ləğv edir.
* Nəticədə hücumçu **şifrəni bilmədən** sistemə "admin" olaraq daxil olur.

### 🔐 Həll Yolu (Prevention):
Zəifliyi aradan qaldırmaq üçün `Prepared Statements` (Hazırlanmış Sorğular) istifadə edilməlidir:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
```

---

## 📸 Ekran Görüntüləri
* **Giriş Ekranı:** Modern qızılı və qara dizayn.
* **Dashboard:** Canlı artan qrafiklər və balans paneli.
* **Ticarət:** Aktivlərin bir kliklə alınması.

## 🛠️ Quraşdırma
1.  Faylları `htdocs/invest_sayti/` qovluğuna kopyalayın.
2.  `invest_db` adlı verilənlər bazası yaradın.
3.  `users` cədvəlini SQL skripti ilə əlavə edin.
4.  Brauzerdə `localhost/invest_sayti/index.php` ünvanına keçid edin.

---

**Qeyd:** Bu layihə yalnız təhsil məqsədlidir.

---

