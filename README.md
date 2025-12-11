# MailMoon

Prosty SaaS do kampanii e-mail zbudowany na Laravel 12 (PHP 8.2+). Zawiera wielotenancy per użytkownik, edytor TinyMCE, import kontaktów (CSV/XLS/XLSX/TXT/wklejka) oraz śledzenie otwarć, kliknięć i wypisów.

## Wymagania
- PHP 8.2+
- Composer
- Node.js + npm (do budowy assetów)
- Rozszerzenia PHP: sqlite, openssl, mbstring, dom, zip (dla importu XLS/XLSX)

## Instalacja
1. `composer install`
2. `npm install && npm run build`
3. Skopiuj `.env` (przykład używa SQLite):  
   `cp .env.example .env`
4. Ustaw `APP_URL` na własną domenę (linki śledzące) oraz popraw ścieżkę `DB_DATABASE` do `database/database.sqlite`.
5. Utwórz plik bazy: `touch database/database.sqlite`
6. `php artisan key:generate`
7. Migracje + seeder użytkownika: `php artisan migrate --seed`

## Konfiguracja SMTP (Tożsamości nadawcy)
Dodaj tożsamość w menu „Tożsamości”: nazwa, adres nadawcy, host, port, login, hasło, szyfrowanie (ssl/tls). Hasło jest szyfrowane w bazie. Kampanie wysyłają e-maile przez wskazaną tożsamość, niezależnie od `config/mail.php`.

## Wysyłka kampanii
- Tworzenie/edycja kampanii pozwala na szkic, wysyłkę teraz lub zaplanowanie (statusy: draft, scheduled, sending, sent, failed).  
- Pole `send_interval_seconds` definiuje odstęp (sekundy) pomiędzy każdą wiadomością.
- Automatyczne śledzenie otwarć (piksel), kliknięć (przekierowania) i link wypisu (opcjonalnie).

### Komendy konsolowe
- `php artisan campaigns:send-due` – znajdzie kampanie ze statusem `scheduled` i `scheduled_at <= now()`, wyśle je i zaktualizuje statusy.
- `php artisan campaigns:send-now {campaign_id}` – ręczna wysyłka wybranej kampanii.

## Import kontaktów
- Z poziomu listy kontaktów: upload CSV/XLS/XLSX/TXT lub wklejenie listy (`email` lub `email,first_name,last_name` w każdej linii).
- System pomija duplikaty w obrębie listy (`email`, `contact_list_id`) i zwraca podsumowanie (zaimportowane / duplikaty / błędne wiersze).

## Statystyki
- Dashboard: liczba kampanii, kontaktów, szablonów, alert o braku aktywnej tożsamości oraz ostatnia kampania z KPI.
- Strona kampanii: łączna liczba wiadomości, wysłane, otwarcia (unikalne/łącznie), kliknięcia (unikalne/łącznie), wypisania + wskaźniki procentowe.

## Deployment
- Katalog `public/` jako document root (np. OpenLiteSpeed/typowy hosting PHP).
- Baza SQLite (plik `database/database.sqlite`) – upewnij się, że serwer www ma prawa zapisu.
- Ustaw crona/worker do cyklicznego uruchamiania `php artisan campaigns:send-due` jeśli korzystasz z harmonogramów.
