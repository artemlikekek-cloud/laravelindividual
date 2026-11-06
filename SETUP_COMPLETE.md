# ✅ Установка PHP с PostgreSQL завершена!

## Что сделано:

1. ✅ Установлен PHP 8.3.13 в `C:\php83`
2. ✅ Настроен `php.ini` с расширениями PostgreSQL
3. ✅ Расширения `pdo_pgsql` и `pgsql` загружены
4. ✅ PHP добавлен в PATH
5. ✅ `.env` настроен для PostgreSQL

## Что осталось сделать:

### 1. Установить PostgreSQL сервер

Скачайте и установите PostgreSQL:
https://www.postgresql.org/download/windows/

**Важно при установке:**
- Запомните пароль для пользователя `postgres`
- Порт по умолчанию: `5432` (оставьте как есть)

### 2. Создать базу данных

После установки PostgreSQL:

Откройте **SQL Shell (psql)** или **pgAdmin** и выполните:

```sql
CREATE DATABASE sto_db;
```

### 3. Обновить пароль в `.env` (если нужно)

Если вы установили другой пароль для PostgreSQL, измените в `.env`:

```env
DB_PASSWORD=ваш_пароль
```

### 4. Запустить миграции

**ВАЖНО: Откройте НОВОЕ окно PowerShell** (чтобы PATH обновился)

Затем выполните:

```bash
cd "c:\Users\Tema\Desktop\individual project\project"
php artisan config:clear
php artisan migrate:fresh --seed
```

### 5. Запустить сервер

```bash
php artisan serve
```

Откройте: http://localhost:8000

---

## Проверка установки

Запустите в новом окне PowerShell:

```bash
php -v
```

Должно показать: **PHP 8.3.13**

```bash
php -m | findstr pgsql
```

Должно показать:
```
pdo_pgsql
pgsql
```

---

## Если что-то не работает

1. **Закройте ВСЕ окна PowerShell и откройте новое** - PATH обновляется только в новых сессиях
2. Проверьте что PostgreSQL сервер запущен
3. Проверьте данные подключения в `.env`

---

**Готово! PostgreSQL настроен и готов к работе!** 🚀
