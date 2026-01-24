# Phase 6.4: Server Optimization

## الهدف
تحسين إعدادات الخادم (PHP OPcache، HTTP/2، Gzip، Nginx/Apache) لتحسين الأداء.

---

## 1. OPcache (PHP)

### إعدادات مُوصى بها في `php.ini`

```ini
[opcache]
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.validate_timestamps=1
opcache.revalidate_freq=2
opcache.save_comments=1
```

- **الإنتاج**: يُفضّل `opcache.validate_timestamps=0` بعد النشر، وإعادة تشغيل PHP-FPM عند التحديثات.
- **التحقق**: `php -i | grep opcache` أو `opcache_get_status()` في كود تجريبي.

---

## 2. HTTP/2

- تفعيل HTTP/2 في Nginx أو Apache حسب الوثائق الرسمية.
- **Nginx**: `listen 443 ssl http2;` ضمن `server { ... }`.
- **Apache**: تفعيل `mod_http2` وإعداد `Protocols h2 http/1.1`.

---

## 3. Gzip Compression

### Nginx

```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied any;
gzip_types text/plain text/css application/json application/javascript application/xml;
gzip_comp_level 6;
```

### Apache (`mod_deflate`)

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css application/json application/javascript
</IfModule>
```

التحقق: `curl -H "Accept-Encoding: gzip" -I https://example.com` والتحقق من `Content-Encoding: gzip`.

---

## 4. Nginx – Laravel (مثال)

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /var/www/khezana/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 60;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

استبدل `php8.2-fpm` ومَسار الـ socket حسب بيئتك.

---

## 5. الاختبار

- **OPcache**: `php artisan tinker` ثم `var_export(opcache_get_status(false));`
- **Gzip**: `curl -H "Accept-Encoding: gzip" -I https://your-domain`
- **HTTP/2**: أدوات مثل Chrome DevTools (Network → Protocol) أو `curl -I --http2`.

---

**التاريخ**: يناير 2026
